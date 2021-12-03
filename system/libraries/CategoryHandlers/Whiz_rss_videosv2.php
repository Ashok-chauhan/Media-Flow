<?php

class Whiz_rss_videosv2Handler
{
    var $raw_feed_content = '';
    var $xml_parser = null;
    var $feed_uri = '';
    var $keep_order = '';
    var $storyListFeedContents = '';

    var $feedDef = array(
        'title'=>'',
        'type'=>'video',
		'subtype'=>'',
        'paid'=> 1,
        'priority'=> 0,
        'assetid'=> 0,
        'description'=>'',
        'content'=>'',
        'icon_uri'=>'',
        'caption'=>'',
        'uri'=>'',
        'pub_date'=>'',
        'author'=>'',
        'media_type'=>array(),
        'thumbnail'=>array(),
        'media'=>array(),
        'media_caption'=>array(),

        'comments_url'=>''
    );

    function __construct($category)
    {
        $this->feed_uri = $category['source_uri'];
        $this->keep_order = $category['orderflag'];
    }

    function __destruct()
    {
        //nothing yet
    }

    function getFeedDef()
    {
        return $this->feedDef;
    }

    function setCategoryVars($category)
    {
        $category['source_uri'] = $this->feed_uri;
        return $category;
    }

    function getUri()
    {

    }

    function getFeedContents($feed_uri)
    {
        log_message('debug', 'Getting feed contents with URI:' . $feed_uri);
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $feed_uri);
		curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0); //
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        // $output contains the output string
        $feedContents = curl_exec($ch);
        if (curl_errno($ch)) {
            //echo 'Curl error: ' . curl_error($ch);
        }
        // close curl resource to free up system resources
        curl_close($ch);

        return $feedContents;
    }


////////////////////////// added by ashok 08/10/2010 //////////////////////////////
    //TO CHECK AND CONVERT CHARSET TO UTF-8 //
    function convert_charSet($string)
    {
        if (true === mb_check_encoding($string, "UTF-8")) {
            //echo '<li>valid (' . $encoding . ') encoded byte stream!!!!<br />';
            return $string;
        }
        else {
            $str = mb_convert_encoding($string, "UTF-8");
            return $str;
        }

    }

    /*
      * This will take a feed uri and fetch the list of stories
      */
    function getStoryList()
    {
        $this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
        if ($this->storyListFeedContents == '') return false;

        $xml = new XMLReader();

        ////////////////////////////////////////////// USING METHOD CONVERT CHAR TO CONFIRM UTF-8
        $xml->xml($this->convert_charSet($this->storyListFeedContents));
        //////////////////////////////////////////////

        ###$xml->xml($this->storyListFeedContents);
        $xml->read();

        $rssRoot = $xml->expand();
        $rssChildren = $rssRoot->childNodes;
        $channel = null;
        foreach ($rssChildren as $child) {

            if (!is_null($channel)) continue;
            if ($child->nodeName == 'channel') {
                $channel = $child;
            }
        }

        $children = $channel->childNodes;
        $stories = array();
        $count = 0;
        foreach ($children as $child) {

            if ($child->nodeName == 'item') {
                $itemElements = $child->childNodes;

                $feedDef = $this->getFeedDef();
                foreach ($itemElements as $element) {

                    switch ($element->nodeName) {
                        case "title":
                            $feedDef[$element->nodeName] = $element->nodeValue;
                            if($this->keep_order){
                                $count =$count+1;
                                $feedDef['priority'] = $count;
                            }
                            break;

                        case "description":
                            $feedDef[$element->nodeName] = $element->nodeValue;
                            //if content are not availble in feed or meta desc.are used as content
                            $feedDef['content'] = $element->nodeValue;
                            break;
                        case "link":
                            $feedDef['uri'] = $element->nodeValue;
                            break;

                        case "guid":
                            $feedDef['assetid'] = $element->nodeValue;
                            break;
						case "paid":
							$feedDef['paid'] = $element->nodeValue;
						break;

                        case "caption":
                            $feedDef['caption'] = $element->nodeValue;
                            break;

                        case "meta":
                            // if meta=fultext (CA) available override previcous content element.
                            $feedDef['content'] = $element->nodeValue;

                            $metaItem = $element->childNodes;
                            foreach ($metaItem as $meta) {

                                switch ($meta->nodeName) {
                                    case "url":
                                        $feedDef['icon_uri'] = $meta->nodeValue;
                                        break;
                                    case "caption":
                                        $feedDef['caption'] = $meta->nodeValue;
                                        break;
                                }
                            }
                            break;
//////////////////////////////////BOF EXTRA IMAGE(MEDIA) MANIPULATION IN STORY
                        case "multimedia":
                            $contentMedia = $element->childNodes;
                            foreach ($contentMedia as $storyMedia) {
                                if ($storyMedia->nodeName == 'media') {

                                    $length = $storyMedia->attributes->length;
                                    for ($i = 0; $i < $length; ++$i) {
                                        #if ($storyMedia->attributes->item($i)->name == 'type')
										if ($storyMedia->attributes->item($i)->value == 'video'){
                                            $feedDef['media_type'][] = $storyMedia->attributes->item($i)->value;
										
                                   # }
                                    $mediaElements = $storyMedia->childNodes;

                                    foreach ($mediaElements as $element) {
										
                                        switch ($element->nodeName) {
                                            case "thumbnail":
                                                //$feedDef['thumbnail'][] = $element->nodeValue;
												$feedDef['icon_uri'] = $element->nodeValue;
                                                break;
                                            case "link":
                                                $feedDef['uri'] = $element->nodeValue;
											    break;
                                            case "caption":
                                               // $feedDef['media_caption'][] = $element->nodeValue;
											   $feedDef['caption'] = $element->nodeValue;
                                                break;
                                        }

                                    }
									}
									
								}


                                }

                            }
                            break;
///////////////////// EOF EXTRA IMAGE(MEDIA) MANIPULATIN //////////////
                        case "content":
                            // if Node content is avialable all the above should be  voerride.
                            $feedDef['content'] = $element->nodeValue;
                            break;
                        case "media:content":
                            $length = $element->attributes->length;
                            for ($i = 0; $i < $length; ++$i) {
                                if ($element->attributes->item($i)->name == 'url')
                                    $feedDef['icon_uri'] = $element->attributes->item($i)->value;
                            }
                            break;
                        case "content:encoded":
                            $feedDef['content'] = $element->nodeValue;
                            break;
						case "media:keywords":
							$feedDef['subtype'] = $element->nodeValue;
							break;
                        case "enclosure":
                            $length = $element->attributes->length;
                            for ($i = 0; $i < $length; ++$i) {
                                if ($element->attributes->item($i)->name == 'url')
                                    $feedDef['icon_uri'] = $element->attributes->item($i)->value;
                            }
                            break;
                        case "pubDate":
                            $feedDef['pub_date'] = date('Y-m-d H:i:s', strtotime($element->nodeValue));
                           					
                            break;
                        case "author":
                            $feedDef['author'] = $element->nodeValue;
                            break;
                        case "comments":

                            $feedDef['comments_url'] = $element->nodeValue;
                            break;

                    }
                }

                $stories[] = $feedDef;
            }
        }

        return $stories;
    }

    /*
      *
      */
    function refreshContent($category_id = '')
    {

        if ($category_id == '') return false;

        global $CI;
        $stories = $this->getStoryList();
//print_r($stories);exit;
        if (!is_array($stories) || sizeof($stories) == 0) {
            log_message('error', 'ERROR WITH STORY RESULTS:' . $stories);
            return false;
        }

        //STORY MEDIA MANIPULATION.
        foreach ($stories as &$content) {
            $type[] = $content['media_type'];
            $media[] = $content['media'];
            $thumbnail[] = $content['thumbnail'];
            $caption[] = $content['media_caption'];
            unset ($content['media_type']);
            unset ($content['media']);
            unset ($content['thumbnail']);
            unset ($content['media_caption']);
        }

        foreach ($stories as $key => $story) {

            $CI->db->where('category_id', $category_id);
            $CI->db->where('title', $story['title']);
            $CI->db->where('uri', $story['uri']);
            $result = $CI->db->get('content');
            $storyEntry = $result->row_array();

            if (sizeof($storyEntry) == 0) {
                $story['category_id'] = $category_id;
                $result = $CI->db->insert('content', $story);
                $story['id'] = $CI->db->insert_id();
                log_message('debug', 'Story Doesnt Exist, Adding....:' . $story['id']);
            } else {
                $story['id'] = $storyEntry['id'];
                log_message('debug', 'Story Exist, Attempting to Upate....:' . $story['id']);
                $CI->db->where('id', $story['id']);
                $CI->db->update('content', $story);
            }
            ///////////////////////////bof image(MEDIA) ingetion //////////////////////////////
            ////////////////////////////////////////

            $CI->db->where('content_id', $story['id']);
            $CI->db->where('category_id', $category_id);
            $mResult = $CI->db->get('content_media');
            $mediaResult = $mResult->row_array();
            if (sizeof($mediaResult) == 0) {
                ///////////////////////////////////////
                if ($media[$key]) {

                    foreach ($media[$key] as $k => $med) {
                        $pictureData = array(
                            'content_id' => $story['id'],
                            'category_id' => $category_id,
                            'type' => $type[$key][$k],
                            'thumbnail' => $thumbnail[$key][$k],
                            'media' => $med,
                            'caption' => $caption[$key][$k],
                        );

                        $result = $CI->db->insert('content_media', $pictureData);
                        $pictureInsertId = $CI->db->insert_id();

                    }

                }
            } else {

                if ($media[$key]) {

                    foreach ($media[$key] as $k => $med) {
                        $pictureData = array(
                            'content_id' => $story['id'],
                            'category_id' => $category_id,
                            'type' => $type[$key][$k],
                            'thumbnail' => $thumbnail[$key][$k],
                            'media' => $med,
                            'caption' => $caption[$key][$k],
                        );
                        $CI->db->where('id', $mediaResult['id']);
                        $CI->db->update('content_media', $pictureData);


                    }

                }

            }
            ///////////////////eof image(MEDIA) ingetion //////////////////

        }
    }


    /*
     * feed_order will keep the order of incomming rss feed .
     * count will stored in priority field currently this is
     * applicabel to Commercial Appeal publication.
     * param count
     */
//////  feed_order  NOT USING  07/07/2012 ///////

    function feed_order($count)
    {
        global $CI;
        $path = explode('/', $_SERVER['PATH_INFO']);
        $number = 0;
        if ($path[2] == 'refreshpub') {
            if ($path[3] == 20) {
                $number = $count;
            }
        } else {
            $catid = $path[3];
            $CI->db->where('id', $catid);
            $result = $CI->db->get('category');
            $rset = $result->row_array();
            //if($rset['publication_id'] == 20){
            if ($rset['orderflag'] == 1) {

                $number = $count;
            }
        }
        return $number;
    }

    /*
    * feed_order_zero will update priority field value to zero(0).
    * latest 30 stories will updated to 0.
    * this is currently applicable to Commercial Appeal publication.
    * param :  category id
    */

/////// feed_order_zero is NOT USING  07/07/2012 ///////////////////

    function feed_order_zero($cid)

    {
        //echo '<br>...setting feed order to zero';
        global $CI;
        //$catq("Select publication_id from category where id={$category_id}");
        $CI->db->where('id', $cid);
        $result = $CI->db->get('category');
        $storyEntry = $result->row_array();

        ##$sql=("UPDATE content SET priority=0 where category_id={$storyEntry['id']}  ORDER BY pub_date DESC LIMIT 100 ");

        $sql = ("UPDATE content SET priority=0 where category_id={$storyEntry['id']} AND priority > 0");

        $CI->db->query($sql);
        //return $story;
    }
	//Check video for Closed Caption. *NOT IN USE *
	protected function checkCC($uri){
		$findme = 'anvato';
		$pos = strpos($uri, $findme);
			if($pos !== false){
			$ccurl = str_replace(".mp4", ".m3u8", $uri);
			return $ccurl;
			}else{
			return $uri;
			}
	}

	// Convert all the video of .mp4 to .m3u8 *NOT IN USE *
	protected function mp4Tom3u8($uri){
		if(substr($uri, -4) === ".mp4"){
			$m3u8 = str_replace(".mp4", ".m3u8", $uri);
			return $m3u8;
		}else{
			return $uri;
		}
	}


}