<?php

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
        //print '<li>'. $feed_uri;
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
                if($featured->content->appdataUrl){
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
        $urlArray = array_unique($storyUrl); //filtering duplicate values.

        foreach ($urlArray as $key => $storyLink) {
            $this->storyFeed = $this->getFeedContents($storyLink);
            $itemElements = json_decode($this->storyFeed);

            if (count($itemElements) == 0)
                return false;
            $text = '';
            $count = $count + 1;
            $feedDef['priority'] = $count;

            $feedDef['decodedJson'] = $itemElements;
            $feedDef['pub_date'] = $itemElements->data->context->updated;
            if ($itemElements->data->context->authors[0]->name) {
                $feedDef['author'] = $itemElements->data->context->authors[0]->name;
            } else {
                $feedDef['author'] = $itemElements->data->context->fields->open_author;
            }

            $feedDef['title'] = $itemElements->data->context->title;
            $feedDef['uri'] = $itemElements->data->context->href;
            $feedDef['assetid'] = $itemElements->data->context->href;
            if ($itemElements->data->context->fields->leadtext != '') {
                $text .= $itemElements->data->context->fields->leadtext; //Lead text if available.
            }
            $storyArray = $itemElements->data->context->fields->body;

            foreach ($storyArray as $key => $storyValue) {
                $p = $storyValue->type;

                if (isset($storyValue->children)) {
                    foreach ($storyValue->children as $k => $paragraph) {
                        $counter = count((array) $paragraph);
                        if ($counter > 2) {
                            $formatType = $paragraph->type;

                            if ($formatType == 'a') {
                                if (isset($paragraph->href))
                                    $href = $paragraph->href;
                                if (isset($paragraph->attr))
                                    $attr = $paragraph->attr;
                            }

                            /////////////// Related article bof ////////////////

                            if ($formatType == 'li') {
                                foreach ($paragraph->children as $relatedChild) {

                                    foreach ($relatedChild as $value) {
                                        foreach ($value as $value) {
                                            foreach ($value as $value) {
                                                foreach ($value as $value) {
                                                    $anchor = $value->type;
                                                    $href = $value->href;
                                                    $attr = $value->attr;
                                                    foreach ($value as $value) {
                                                        foreach ($value as $value) {
                                                            if ($anchor == 'a') {
                                                                if ($value->text && $p) {
                                                                    $text .= '<' . $p . '><li><b><' . $anchor . ' href ="' . $href . '" target="' . $attr . '">' . $value->text . '</' . $anchor . '></b></li></' . $p . '>';
                                                                } elseif($value->text) {
                                                                    $text .= '<li><b><' . $anchor . ' href ="' . $href . '" target="' . $attr . '">' . $value->text . '</' . $anchor . '></b></li>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if (isset($relatedChild->children[1]->relation->content->title)) {
                                        if ($p) {
                                            $text .= '<' . $p . '><li><b><a href=""> ' . $relatedChild->children[1]->relation->content->title . '</a></b></li></' . $p . '>';
                                        } else {
                                            $text .= '<li><b><a href=""> ' . $relatedChild->children[1]->relation->content->title . '</a></b></li>';
                                        }
                                    } else {
                                        foreach ($relatedChild->children as $childrens) {


                                            $anchor = $childrens->type;
                                            $href = $childrens->href;
                                            $attr = $childrens->attr;
                                            $relatedTitle = $childrens->children[0]->text;

                                            if (trim($relatedTitle)) {
                                                if ($p) {
                                                    $text .= '<' . $p . '><li><b><' . $anchor . ' href="' . $href . '" target ="' . $attr . '">' . $relatedTitle . '</' . $anchor . '></b></li></' . $p . '>';
                                                } else {
                                                    $text .= '<li><b><' . $anchor . ' href="' . $href . '" target ="' . $attr . '">' . $relatedTitle . '</' . $anchor . '></b></li>';
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            /*                             * ****** Scribble embed bof ************** */
                            //Getting Streak value
                            if ($formatType == 'b') {

                                foreach ($paragraph->children as $streak) {

                                    if ($streak->type == 'a') {

                                        foreach ($streak as $media) {

                                            foreach ($media as $mediaContent) {

                                                $poster = $this->keyframeImage($mediaContent);

                                                if ($mediaContent->type == 'Video' || $mediaContent->type == 'video') {
                                                    foreach ($mediaContent->mediaInfo as $videos) {

                                                        foreach ($videos as $video) {

                                                            if ($video->width <= 900 && $video->width != '') {
                                                                $text .= '<' . $p . '><' . $streak->type . '><' . $mediaContent->type . ' width="' . $video->width . '" height="' . $video->height . '" controls poster="' . $poster . '"><source src="' . $video->uri . '" type="' . $video->mimeType . '"></' . $mediaContent->type . '></' . $p . '>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($streak->type == 'u') {

                                        foreach ($streak->children as $streakValue) {

                                            if ($streakValue->href != '' && $streakValue->type == 'a') {
                                                $archivelink = $streakValue->href;
                                                $archiveAnchor = $streakValue->type;
                                                $archiveAttr = $streakValue->attr;
                                                foreach ($streakValue as $archive) {

                                                    foreach ($archive as $archiveList) {
                                                        $text .= '<' . $p . '><' . $formatType . '><' . $streak->type . '><' . $archiveAnchor . ' href="' . $archivelink . '" target="' . $archiveAttr . '">' . $archiveList->text . '</' . $archiveAnchor . '></' . $streak->type . '></' . $formatType . '></' . $p . '>';
                                                    }
                                                }
                                            }

                                            //geting streak value
                                            $text .= '<' . $formatType . '><' . $streak->type . '>' . $streakValue->text . '</' . $streak->type . '></' . $formatType . '>';
                                        }
                                    } else {
                                        ////////////////
                                        foreach ($streak->children as $streakValue) {

                                            $a = $streak->type;
                                            $href = $streak->href;
                                            $attr = $streak->attr;
                                            if ($streakValue->type == 'text') {
                                                $cmpval = substr_compare(strtolower($streakValue->text),'embed:',0,6);
                                                //geting streak value
                                                if ($p) {
                                                    if($cmpval !=0)
                                                    $text .= '<' . $p . '><' . $formatType . '><' . $a . ' href="' . $href . '"  target="' . $attr . '">' . $streakValue->text . '</' . $a . '></' . $formatType . '></' . $p . '>';
                                                } else {
                                                    if($cmpval !=0)
                                                    $text .= '<' . $formatType . '><' . $a . ' href="' . $href . '"  target="' . $attr . '">' . $streakValue->text . '</' . $a . '></' . $formatType . '>';
                                                }
                                            }
                                        }
                                        ////////////////
                                    }
                                }
                            }

                            if ($formatType == 'tbody') {
                                $text .= '<table border="0">';

                                foreach ($paragraph->children as $tchild) {
                                    $tr = $tchild->type;
                                    foreach ($tchild as $titems) {
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
                                $text .= '</table>';
                            }

                            /*                             * ********** external embed */////

                            foreach ($paragraph->children as $key => $val) {
                                //Facebook embed
                                $text .= $val->relation->content->fields->embed;
                            }


                            if ($paragraph->relation->content->type == 'ExternalEmbed') {
                                $text .= $paragraph->relation->content->fields->embed;
                                break;
                            }




                            /*                             * ******* Scribble embed eof ****************** */
                            if ($paragraph->relation->content->type == 'Picture') {
                                //28/11/2019
                                $pictureCaption = $paragraph->relation->content->fields->caption;
                                $picture = $paragraph->relation->content->fields->landscape390->href_full;
                                $pictureWidth = $paragraph->relation->content->fields->landscape390->width;
                                $text .= '<' . $p . '><' . $formatType . ' src="' . $picture . '" width="' . $pictureWidth . '"><small>' . $pictureCaption . '</small></' . $p . '>';
                            }

                            /////////////// Related article eof added on 21/11/2019 ////////////////
                            foreach ($paragraph->children as $child) {

                                if ($child->type == 'text' && strtolower($paragraph->relation->content->type) !='video') {

                                    if (isset($child->text)) {
                                         $cmpval = stripos($child->text, 'embed:');

                                       if($href !=''){


                                        if ($p) {
                                            if($cmpval ===false)
                                            $text .= '<' . $p . '><' . $formatType . ' href="' . $href . '"  target= "' . $attr . '"/> ' . $child->text . ' </' . $formatType . '></' . $p . '>';
                                        } else {
                                            if($cmpval ===false)
                                            $text .= '<' . $formatType . ' href="' . $href . '"  target= "' . $attr . '"/> ' . $child->text . ' </' . $formatType . '>';
                                        }
                                        } else {
                                            if ($p) {
                                                if($cmpval ===false)
                                            $text .= '<' . $p . '>' . $child->text . '</' . $p . '>';
                                        } else {
                                            if($cmpval ===false)
                                            $text .=  $child->text ;
                                        }
                                            
                                        }
                                    }
                                }
                            }
                        } else {
                            if (isset($paragraph->text)) {
                                if ($formatType == 'h1' || $formatType == 'h2' || $formatType == 'h3') {
                                    $text .= '<' . $formatType . '>' . $paragraph->text . '</' . $formatType . '>';
                                } else {

                                    if ($p) {

                                        $text .= '<' . $p . '>' . $paragraph->text . '</' . $p . '>';
                                    } else {

                                        $text .= $paragraph->text;
                                    }
                                }
                            }
                        }
                    }
                } elseif (isset($storyValue->type) && $storyValue->type == 'text') {
                    if ($p) {
                        $text .= '<' . $p . '>' . $storyValue->text . '</' . $p . '>';
                    } else {
                        $text .= $storyValue->text;
                    }
                }

                $feedDef['content'] = $text;
                $feedDef['description'] = strip_tags(substr($text, 0, 250));
            }

            $stories[] = $feedDef;
        }

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
                    $media['caption'] = $value->caption;
                    $articleMedia[] = $media;
                } elseif ($value->content->type == 'Video') {
                    $media['type'] = strtolower($value->content->type);
                    $media['duration'] = $value->content->mediaInfo->duration;
                    $media['media'] = $value->content->mediaInfo->videos[0]->uri;
                    $media['caption'] = $value->content->keyframeImages[0]->content->fields->caption;
                    $media['thumbnail'] = $value->content->keyframeImages[0]->content->fields->landscape390->href_full;
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
                    $related['caption'] = $relatedMedia->caption;
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
            }elseif ($childs->children[0]->relation->content->mediaInfo) {
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
