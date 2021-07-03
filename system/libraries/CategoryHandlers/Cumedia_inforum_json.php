<?php
//date_default_timezone_set('America/Chicago');

error_reporting(0);

class Cumedia_inforum_jsonHandler {

    var $raw_feed_content = '';
    var $xml_parser = null;
    var $feed_uri = '';
    var $storyListFeedContents = '';
    var $storyFeed = '';
    var $feedDef = array(
        'title' => '',
        'type' => 'story',
        'paid' => 1,
        'priority' => 0,
        'assetid' => 0,
        'description' => '',
        'content' => '',
        'icon_uri' => '',
        'caption' => '',
        'uri' => '',
        'pub_date' => '',
        'author' => '',
    );

    function __construct($category) {
        $this->feed_uri = $category['source_uri'];
    }

    function __destruct() {
        //nothing yet
    }

    function getFeedDef() {
        return $this->feedDef;
    }

    function setCategoryVars($category) {
        $category['source_uri'] = $this->feed_uri;
        return $category;
    }

    function getUri() {
        
    }

    function getFeedContents($feed_uri) {
       // print '<li>'. $feed_uri;
        log_message('debug', 'Getting feed contents with URI:' . $feed_uri);

        // create curl resource
        $ch = curl_init();
        // set url

        curl_setopt($ch, CURLOPT_URL, $feed_uri);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0); //
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        $feedContents = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // close curl resource to free up system resources
        curl_close($ch);
        //print $feedContents;
        return $feedContents;
    }

    /*
     * This will take a feed uri and fetch the list of stories
     */

    function getStoryLink() {
        $this->storyListFeedContents = $this->getFeedContents($this->feed_uri);





        if ($this->storyListFeedContents == '')
            return false;


        $itemElements = json_decode($this->storyListFeedContents);
        //geting path from feed_uri(source_uri).

        $path = parse_url($this->feed_uri, PHP_URL_PATH);
        $pathPart = explode('/', $path);
        $categoryName = $pathPart[2];

        if (count($itemElements) == 0)
            return false;
        $stories = array();
        $count = 0;
        $feedDef = $this->getFeedDef();
        $storyUrl = array();
        //geting end api url for single story detail from category json.

        if (isset($itemElements->data->latestInSection->items)) {
            foreach ($itemElements->data->latestInSection->items as $element) {
                $links = 'https://api.forum.cue.cloud' . $element->appdataUrl;
                $storyUrl[] = $links;
            }
        } else {

            foreach ($itemElements->data as $key => $sections) {
                //if ->data->latest_paidusercontent_milestones[3rd part is category name.
                //the category name can be get from 2d component from feed url.

                if ($key == 'latest_paidusercontent_' . $categoryName) {

                    foreach ($sections->items as $section) {
                        $links = 'https://api.forum.cue.cloud' . $section->appdataUrl;
			$storyUrl[] = $links;
                    }
                }
            }
        }

        //getting Section front stories url bof

        if (isset($itemElements->data->context->rootGroup->featured)) {
            foreach ($itemElements->data->context->rootGroup->featured as $featured) {
                if ($featured->content->appdataUrl) {
                    $links = 'https://api.forum.cue.cloud' . $featured->content->appdataUrl;
                    $storyUrl[] = $links;
                }
            }
        }

        if (isset($itemElements->data->newsSections)) {
            foreach ($itemElements->data->newsSections as $newsSections) {
                foreach ($newsSections->item->section->page->latest as $newsUrl) {
                    $links = 'https://api.forum.cue.cloud' . $newsUrl->appdataUrl;
                    $storyUrl[] = $links;
                }
            }
        }



        // getting section front stories url eof
//    
 print '<pre>';
 print_r($storyUrl);
//unset($storyUrl);
//$storyUrl[]='https://api.forum.cue.cloud/postbulletin/obituaries/obits/6556716-Frederic-%E2%80%9CJudd%E2%80%9D-Kirklin/appdata';
        $urlArray_link = array_unique($storyUrl); //filtering duplicate values.
	foreach ($urlArray_link as $key => $LinkValue) {
            if(strlen($LinkValue) > 28) $urlArray[] = $LinkValue; 
        }	

        foreach ($urlArray as $key => $storyLink) {
            $this->storyFeed = $this->getFeedContents($storyLink);
            $itemElements = json_decode($this->storyFeed);

            if (count($itemElements) == 0)
                return false;
            $text = '';
            $count = $count + 1;
            $feedDef['priority'] = $count;

            $feedDef['decodedJson'] = $itemElements;
            //convering date from utc to cst            
            $feedDef['pub_date'] = date('Y-m-d H:i:s', strtotime($itemElements->data->context->updated));
            //$feedDef['pub_date'] = $itemElements->data->context->updated;
            if ($itemElements->data->context->fields->open_author) {
                    $feedDef['author'] = $itemElements->data->context->fields->open_author;
                } else {
                  $feedDef['author'] = $itemElements->data->context->authors[0]->name;
            }

            $feedDef['title'] = $itemElements->data->context->title;
            $feedDef['uri'] = $itemElements->data->context->href;
            $feedDef['assetid'] = $itemElements->data->context->href;
            if ($itemElements->data->context->fields->leadtext != '') {
                $text .= $itemElements->data->context->fields->leadtext; //Lead text if available.
            }
            $storyArray = $itemElements->data->context->fields->body;

            foreach ($storyArray as $key => $storyValue) {

                if ($storyValue->type == 'p') {
                    $text .= $this->story($storyValue);
                } elseif ($storyValue->type == 'h1' || $storyValue->type == 'h2' || $storyValue->type == 'h3') {
                    $text .= $this->storyHeadings($storyValue);
                } elseif ($storyValue->type == 'table') {
                    $text .= $this->storyTable($storyValue);
                    # code...
                } elseif ($storyValue->type == 'ul') {

                    $text .= $this->relatedStory($storyValue); // not working.
                    $text .= $this->storyList($storyValue);
                } elseif ($storyValue->type == 'b') {
                    //$text .= $this->storyStreak($storyValue); // gettting streak
                    //print_r($storyValue->text);
                    # code...
                } else {
                    $text .= $this->storyContents($storyValue);
                }
            }
            $feedDef['content'] = $text;
            $feedDef['description'] = strip_tags(substr($text, 0, 250));
            $stories[] = $feedDef;
        }
        //$stories[] = $feedDef;
        // print $text;
//print_r($feedDef);
        return $stories;
    }

    /*
     * 
     */

    function keyframeImage($keyframeImage) {

        foreach ($keyframeImage->keyframeImages as $content) {

            foreach ($content as $fields) {

                foreach ($fields->fields->landscape320 as $picture) {
                    return $picture;
                }
            }
        }
    }

    function refreshContent($category_id = '') {
        if ($category_id == '')
            return false;

        global $CI;
        //refresh the top level gallery entries first

        $stories = $this->getStoryLink();

        if (!is_array($stories) || sizeof($stories) == 0) {
            log_message('error', 'ERROR WITH STORY RESULTS:' . $stories);
            return false;
        }

        $media = array();
        foreach ($stories as $key => $story) {

            $story['media'] = $this->storyMedia($story['decodedJson']);

            unset($story['decodedJson']);
            $media = $story['media'];
            unset($story['media']);

            $CI->db->where('category_id', $category_id);
            $CI->db->where('title', $story['title']);
            $CI->db->where('uri', $story['uri']);
            $result = $CI->db->get('content');
            $storyEntry = $result->row_array();

            if (sizeof($storyEntry) == 0) {

                $story['category_id'] = $category_id;
                $result = $CI->db->insert('content', $story);
                $story['id'] = $CI->db->insert_id();
                $this->insrtMedia($category_id, $story['id'], $media);
                log_message('debug', 'Story Doesnt Exist, Adding....:' . $story['id']);
            } else {

                $story['id'] = $storyEntry['id'];
                log_message('debug', 'Story Exist, Attempting to Upate....:' . $story['id']);
                $CI->db->where('id', $story['id']);
                $CI->db->update('content', $story);
                $this->insrtMedia($category_id, $story['id'], $media);
            }
        }
    }

    function insrtMedia($catid, $storyid, $media) {

        global $CI;
        if (isset($media)) {
            foreach ($media as $key => $value) {
                $pictureData = array(
                    'content_id' => $storyid,
                    'category_id' => $catid,
                    'media_order' => $key,
                    'type' => strtolower($value['type']),
                    'thumbnail' => $value['thumbnail'],
                    'media' => $value['media'],
                    'caption' => $value['caption'],
                );

                $result = $CI->db->insert('content_media', $pictureData);
                $pictureInsertId = $CI->db->insert_id();
            }
        }
    }

    function storyMedia($itemElements) {
        $articleMedia = array();
        $media = array();
        $related = array();
        if (isset($itemElements->data->context->topMedia)) {
            foreach ($itemElements->data->context->topMedia as $value) {

                if ($value->content->type == 'Picture' && $value->content->fields->landscape390->href_full != '') {
                    $media['type'] = 'photo';
                    $media['media'] = $value->content->fields->landscape390->href_full; //work
                    $media['thumbnail'] = $value->content->fields->landscape320->href_full; //work
                    $media['caption'] = $value->content->fields->caption.' '.$value->content->fields->photographer;
                    $articleMedia[] = $media;
                } elseif ($value->content->type == 'Video') {
                    $media['type'] = strtolower($value->content->type);
                    $media['duration'] = $value->content->mediaInfo->duration;
                    $media['media'] = $value->content->mediaInfo->videos[0]->uri;
                    $media['caption'] = $value->content->keyframeImages[0]->content->fields->caption;
                    $media['thumbnail'] = $value->content->keyframeImages[0]->content->fields->landscape390->href_full;
                    $articleMedia[] = $media;
                } elseif ($value->content->type == 'JWPlayerVideo') {
                
                
                    $media['type'] = 'video';
                    //$media['duration'] = $value->content->mediaInfo->duration;
                    $media['media'] = trim('https://content.jwplatform.com/videos/'.$value->content->fields->jwplayerVideoId.'-xujEMwLT.mp4');
                    $media['caption'] = $value->content->title;
                    $media['thumbnail'] = 'https://content.jwplatform.com/v2/media/'.$value->content->fields->jwplayerVideoId.'/poster.jpg?width=480';
                    $articleMedia[] = $media;
                    
                }
                 
            }
        }

        if (isset($itemElements->data->context->relatedMedia)) {
            foreach ($itemElements->data->context->relatedMedia as $relatedMedia) {

                if ($relatedMedia->content->type == 'Picture' && $relatedMedia->content->fields->landscape390->href_full != '') {
                    $related['type'] = 'photo';

                    $related['media'] = $relatedMedia->content->fields->landscape390->href_full; //work
                    $related['thumbnail'] = $relatedMedia->content->fields->landscape320->href_full; //work
                    $related['caption'] = $relatedMedia->content->fields->caption.' '.$relatedMedia->content->fields->photographer;
                    $articleMedia[] = $related;
                }elseif ($relatedMedia->content->type == 'Video') {
                    $related['type'] = strtolower($relatedMedia->content->type);
                    $related['duration'] = $relatedMedia->content->mediaInfo->duration;
                    $related['media'] = $relatedMedia->content->mediaInfo->videos[0]->uri;
                    $related['caption'] = $relatedMedia->content->keyframeImages[0]->content->fields->caption;
                    $related['thumbnail'] = $relatedMedia->content->keyframeImages[0]->content->fields->landscape390->href_full;
                    $articleMedia[] = $related;
                }elseif ($relatedMedia->content->type == 'JWPlayerVideo') {
                
                    $related['type'] = 'video';
                    //$media['duration'] = $value->content->mediaInfo->duration;
                    $related['media'] = trim('https://content.jwplatform.com/videos/'.$relatedMedia->content->fields->jwplayerVideoId.'-xujEMwLT.mp4');
                    $related['caption'] = $relatedMedia->content->fields->caption;
                    $related['thumbnail'] = 'https://content.jwplatform.com/v2/media/'.$relatedMedia->content->fields->jwplayerVideoId.'/poster.jpg?width=480';
                    $articleMedia[] = $related;
                    
                }
            }
        }

//related Image /Gallery in story bof ///

        foreach ($itemElements->data->context->fields->body as $body) {
            if (isset($body->children[0]->relation->content->relatedImages)) {
                foreach ($body->children[0]->relation->content->relatedImages as $galleryPicture) {
                    $gallery['type'] = 'photo';
                    $gallery['media'] = $galleryPicture->content->fields->landscape390->href_full; //work
                    $gallery['thumbnail'] = $galleryPicture->content->fields->landscape320->href_full; //work
                    $gallery['caption'] = $galleryPicture->content->fields->caption;
                    $articleMedia[] = $gallery;
                }
            }
        }
        //related image /Gallery in story eof ///
        //relatedImages
        if (isset($itemElements->data->context->relatedImages)) {
            foreach ($itemElements->data->context->relatedImages as $relatedImages) {

                if ($relatedImages->content->type == 'Picture' && $relatedImages->content->fields->landscape390->href_full != '') {
                    $related_images['type'] = 'photo';

                    $related_images['media'] = $relatedImages->content->fields->landscape390->href_full; //work
                    $related_images['thumbnail'] = $relatedImages->content->fields->landscape320->href_full; //work
                    $related_images['caption'] = $relatedImages->caption;
                    $articleMedia[] = $related_images;
                }
            }
        }

        /// bof video ///

        foreach ($itemElements->data->context->fields->body as $key => $childs) {

            if ($childs->type == 'p') {

                foreach ($childs->children as $mediaChild) {
                    foreach ($mediaChild as $childRelation) {
                        foreach ($childRelation->content as $childContent) {
                            foreach ($childContent as $mediaThumb) {
                                if ($mediaThumb->content->type != 'Picture')
                                    break;

                                $video['caption'] = $mediaThumb->caption;
                                $video['thumbnail'] = $mediaThumb->content->fields->landscape390->href_full;
                            }
                        }
                    }
                }

                foreach ($childs->children as $mediaChild) {
                    foreach ($mediaChild as $childRelation) {
                        foreach ($childRelation->content as $childContent) {
                            $video['media'] = $childContent->videos[0]->uri;
                            $video['type'] = 'video';
                            if ($video['media'] != '')
                                $articleMedia[] = $video;
                        }
                    }
                }
            } elseif ($childs->children[0]->relation->content->mediaInfo) {
                $video['media'] = $childs->children[0]->relation->content->mediaInfo->videos[0]->uri;
                $video['thumbnail'] = $childs->children[0]->relation->content->keyframeImages[0]->content->fields->landscape390->href_full;
                $video['duration'] = $childs->children[0]->relation->content->mediaInfo->duration;
                $video['type'] = $childs->children[0]->relation->content->type;
                $video['caption'] = $childs->children[0]->relation->content->title;
                if ($video['media'] != '')
                    $articleMedia[] = $video;
            }
        }

        /// eof video ///

        return $articleMedia;
    }

    function story($body) {


        $paraType = $body->type;
        $text .= '<' . $paraType . '>';

        foreach ($body as $key => $children) {

            if ($key == 'children') {

                foreach ($children as $key => $childs) {

                    if ($childs->type == 'text') {
                        $text .= $childs->text;
                    } elseif ($childs->type == 'br') {
                        $text .= '<br>' . $childs->text . '</br>';
                    } elseif ($childs->type == 'img') {
                        $text .= $this->storyImage($childs);
                    } elseif ($childs->type == 'i') {
                        $text .= $this->italicType($childs);
                    } elseif ($childs->type == 'b') {

                        $text .= $this->storyArchive($childs);

                        $text .= '<b>' . $this->storyStreak($childs) . '</b>';
                        $text .= $this->storyContents($childs);
                    } elseif ($childs->type == 'a') {

                        $text .= $this->storyLinks($childs);
                        $text .= $this->storyContents($childs);
                        
                    }
                }

                $text .= '</' . $paraType . '>';
                
            }
        }


        return $text;
    }

//Related articale
    function relatedStory($paragraph) {
        if ($paragraph->type == 'li' || $paragraph->type == 'ul') {
            foreach ($paragraph->children as $relatedChild) {

                 $li = $relatedChild->type;
                foreach ($relatedChild as $childrens) {
                    foreach ($childrens as $value) {
                        $anchor = $value->type;
                        $href = $value->href;
                        $attr = $value->attr;

                        foreach ($value as $value) {
                            
                           
                             
                                if($value->type ==='a' && $value->href !=''){
                                    $text .= '<p><'.$paragraph->type.'><'.$li.'><b><a href="'.$value->href.'" target="'.$value->attr.'">'.$value->children[0]->text.'</a></b></'.$li.'></'.$paragraph->type.'></p>';
                                }

                                if(is_object($value)){
                                   
                                     foreach ($value->children as $key => $relatedValue) {
                                        if($relatedValue->type ==='a' && $relatedValue->href !=''){
                                          
                                            $text .= '<p><'.$paragraph->type.'><'.$li.'><b><a href="'.$relatedValue->href.'" target="'.$relatedValue->attr.'">'.$relatedValue->children[0]->text.'</a></b></'.$li.'></'.$paragraph->type.'></p>';
                                            
                                             }
                                        }   

                                } 
                                

                            foreach ($value as $value) {

                                if ($value->text && $href != '') {
                                    $text .= '<li><' . $anchor . ' href ="' . $href . '" target="' . $attr . '">' . $value->text . '</' . $anchor . '>';
                                } else {
                                    //$text .= $value->text;
                                }
                            }
                        }
             //Related stories without explicite link bof ///                    
                                $bold = $value->type;
                                foreach ($value as $key => $related) {
                                   foreach ($related as $key => $relations) {
                                    $relAnchor = $relations->type;
                                    foreach ($relations as $key => $relate) {
                                
                                    $fnsArticle = $relate->content->type;
                                   
                                    if($fnsArticle == 'FNSArticle' || $fnsArticle == 'Story'){
                                       
                                        $relTitle = '<'.$bold.'>'.$relate->content->title.'</'.$bold.'>';
                                        $appdataUrl = $relate->content->homeSection->appdataUrl;
                                       $relUrl = $this->fnsArticle($appdataUrl, $relate->content->title);
                                       if($relUrl){
                                        $text .='<p><li><'.$relAnchor. ' href="'.$relUrl.'" target="_blank"> '.$relTitle.'</'.$relAnchor.'></li></p>';
                                       }else{
                                           $text .='<p><li>'.$relTitle.'</li></p>';
                                       }
                                    }
                                }
                            }
                        }

         //Related stories without explicite link eof ///               
                        /**
                        foreach ($childrens as $key => $value) {

                            if ($value->type == 'text') {
                                $text .= $value->text . '</li>';
                            }
                        }
                         * 
                         */

                        //// in story image
                        if ($anchor == 'img') {
                            $caption = $value->relation->content->fields->caption;
                            $image = $value->relation->content->fields->landscape390->href_full;
                            $text .= '<p><img src="' . $image . '"/>';
                            $text .= '<br/><small>' . $caption . '</small></p>';
                        }
                    }
                }

                if (isset($relatedChild->children[1]->relation->content->title)) {

                    $text .= '<' . $p . '><li><b><a href=""> ' . $relatedChild->children[1]->relation->content->title . '</a></b></li></' . $p . '>';
                }
            }
        }

        return $text;
        
    }

//Story streak
    function storyStreak($paragraph) {
        //Getting Streak value

        foreach ($paragraph->children as $streak) {

            $text .= '<b>' . $streak->text . '</b>'; //sometime it only bold.

            if ($streak->type == 'a') {

                foreach ($streak as $media) {

                    foreach ($media as $mediaContent) {

                        //$poster = $this->keyframeImage($mediaContent);

                        if ($mediaContent->type == 'Video' || $mediaContent->type == 'video') {
                            foreach ($mediaContent->mediaInfo as $videos) {

                                foreach ($videos as $video) {

                                    //if ($video->width <= 900 && $video->width != '') {
                                    if ($video->uri != '') {
                                        $text .= '<iframe src="' . $video->uri . '" ></iframe>';
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            if ($streak->type == 'u') {

                foreach ($streak->children as $streakValue) {

                    //geting streak value
                    $text .= '<' . $streak->type . '>' . $streakValue->text . '</' . $streak->type . '>';
                }
            } else {

                ////////////////
                foreach ($streak->children as $streakValue) {

                    $a = $streak->type;
                    $href = $streak->href;
                    $attr = $streak->attr;
                    if ($href != '') {
                        if ($streakValue->type == 'text') {

                            $cmpval = substr_compare(strtolower($streakValue->text), 'embed:', 0, 5);
                            if ($cmpval != 0) {
                                $text .= '<' . $a . ' href="' . $href . '"  target="' . $attr . '">' . $streakValue->text . '</' . $a . '>';
                            }
                        }
                    }
                }
            }
        }


        return $text;
    }

// story table.
    function storyTable($paragraph) {

        $text .= '<table border="0">';
        foreach ($paragraph->children as $key => $para) {
            $text .= '<' . $para->type . '>'; //tbody  
            foreach ($para as $tchild) {
                foreach ($tchild as $key => $childs) {
                    $tr = $childs->type;

                    foreach ($childs as $titems) {
                        $text .= '<' . $tr . '>';
                        foreach ($titems as $tdtext) {
                            $td = $tdtext->type;

                            foreach ($tdtext->children as $tdvalue) {

                                $text .= '<' . $td . '>' . $tdvalue->text . '</' . $td . '>';
                            }
                        }
                        $text .= '</' . $tr . '>';
                    }
                }
            }
            $text .= '</' . $para->type . '>'; //eof tbody
        }
        $text .= '</table>';

        return $text;
    }

// story list
    function storyList($paragraph) {
        $text .= '<' . $paragraph->type . '>'; // ul (unorder list)
        foreach ($paragraph as $key => $childrens) {
            foreach ($childrens as $key => $children) {

                $li = $children->type;
                foreach ($children as $key => $childs) {
                   
                    foreach ($childs as $key => $child) {
                       if($child->type =='a') return false;
                       
                        if($child->type =='p'){
                            $typePare = $child->type;
                            foreach ($child as $key => $child) {
                       
                                foreach ($child as $key => $child) {
                       
                                    if($child->type =='b'){
                                            $b = $child->type;
                                            foreach ($child as $key => $child) {
                                           
                                            foreach ($child as $key => $child) {
                                                if(trim($child->text) !=''){
                                                $text .= '<p><li><b>'.$child->text.'</b>';
                                                }
                                            }
                                        }
                                    }else{
                                         //$text .= $child->text.'</li></p>';
                                         $text .= '<'.$typePare.'><'.$li.'>'.$child->text.'</'.$li.'></'.$typePare.'>';
                                    
                                }
                                }
                            }

                        }elseif ($child->text && trim($child->text) !=''){
                            $text .= '<' . $li . '>' . $child->text . '</' . $li . '>';
                        }
                    
                    }
                }
            }
        }

        $text .= '</' . $paragraph->type . '>'; //closing ul
        return $text;
    }

    function storyLinks($paragraph) {

        $anchor = $paragraph->type;
        $href = $paragraph->href;
        $attr = $paragraph->attr;
        $text .= '<' . $anchor . ' href="' . $href . '" target="' . $attr . '">';

        foreach ($paragraph as $key => $childrens) {
            foreach ($childrens as $key => $children) {
                if ($children->type == 'text' && trim($href) != '') {
                    $text .= $children->text;
                }

                if($children->type !='text' && trim($href) !=''){
                    $text .='<'.$children->type.'>';
                    foreach($children as $key => $childNode){
                        foreach($childNode as $child){
                          if($child->type == 'text'){
                            $text .= $child->text;
                        }
                     }
                    }
                    $text .= '<'.$children->type.'/>';
                }
            }
        }
        $text .= '</' . $anchor . '>';
        if ($text)
            return $text;
    }

    function storyHeadings($paragraph) {
        // h1,h2,h3 etc.
        $heading = $paragraph->type;
        foreach ($paragraph as $key => $children) {
            foreach ($children as $key => $childs) {
                $text .= '<' . $heading . '>' . $childs->text . '</' . $heading . '>';
            }
        }
        if (trim($text))
            return $text;
    }

//misleneous.
    function storyContents($paragraph) {
        /*         * ********** external embed */////


        foreach ($paragraph->children as $key => $val) {
            //Facebook embed
            $text .= $val->relation->content->fields->embed;
            //print $text;
        }

        if ($paragraph->children) {
            foreach ($paragraph->children as $key => $childs) {
                foreach ($childs->children as $key => $childrens) {
                    if ($childrens->relation) {
                        $text .= $childrens->relation->content->fields->embed;
                    }
                }
            }
        }




        if ($paragraph->relation->content->type == 'ExternalEmbed') {
            $text .= $paragraph->relation->content->fields->embed;
            //print $text;
            //break;
        }




        /*         * ******* Scribble embed eof ****************** */
        if ($paragraph->relation->content->type == 'Picture') {
            //28/11/2019
            $pictureCaption = $paragraph->relation->content->fields->caption;
            $picture = $paragraph->relation->content->fields->landscape390->href_full;
            $pictureWidth = $paragraph->relation->content->fields->landscape390->width;
            $text .= '<' . $p . '><' . $formatType . ' src="' . $picture . '" width="' . $pictureWidth . '"><small>' . $pictureCaption . '</small></' . $p . '>';
        }
        
        return $text;
    }

// called in story function bof //
    function storyImage($childs) {
        $caption = $childs->relation->content->fields->caption;
        $href = $childs->relation->content->fields->landscape390->href_full;
        $img = '<img src="' . $href . '" style="width:auto;">';
        $img .= '<div><small>' . $caption . '</small></div>';
        return $img;
    }

    function italicType($childs) {
        $formatType = $childs->type;
        foreach ($childs as $key => $children) {
            foreach ($children as $key => $kids) {
                $text .= $kids->text;
            }
        }
        $textValue = '<' . $formatType . '>' . $text . '</' . $formatType . '>';
        return $textValue;
    }

    function storyArchive($childs) {
        $formatType = $childs->type;

        foreach ($childs as $key => $children) {

            foreach ($children as $key => $kids) {
                $formatUnderline = $kids->type;

                foreach ($kids as $key => $kid) {
                    foreach ($kid as $key => $kiden) {
                        $anchor = $kiden->type;
                        $href = $kiden->href;
                        $attr = $kiden->attr;
                        foreach ($kiden as $key => $kidu) {
                            foreach ($kidu as $key => $gkid) {
                                if ($anchor != 'Video' && $href != '') {

                                    $textValue .= '<p><' . $formatType . '><' . $formatUnderline . '><' . $anchor . ' href="' . $href . '" target="' . $attr . '">' . $gkid->text . '</' . $anchor . '></' . $formatUnderline . '></' . $formatType . '></p>';
                                }
                            }
                        }
                    }
                }
            }
        }

        return $textValue;
    }
    
    
    function fnsArticle($appdataUrl, $title){
        $baseUrl = 'https://api.forum.cue.cloud';
        $resource = $baseUrl.$appdataUrl;
        $apiResponse = $this->getFeedContents($resource);
        $data = json_decode($apiResponse);
        
        foreach ($data->data->latestInSection->items as $key => $item) {
            if($item->title == $title){
                return $item->href;
            }
        }
    }

//called in story function eof.
// excluding unwanted characters from title CURRENTLY NOT IN USE MAY BE USE IN FUTURE.
// not in use.
    function unhtmlentities($string) {
        // replace numeric entities
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
        $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
        // replace literal entities
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }

}
