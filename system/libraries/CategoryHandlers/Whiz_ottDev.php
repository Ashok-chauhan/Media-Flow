<?php
error_reporting(E_ALL);
class Whiz_ottDevHandler
{
    var $raw_feed_content = '';
    var $xml_parser = null;
    var $feed_uri = '';
    var $keep_order = '';
    var $storyListFeedContents = '';
	var $share_uri = '';
	var $sharing = false;
	var $sharingTV = false;
	var $catId = '';

    var $feedDef = array(
        'title'=>'',
        'type'=>'video',
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
        'comments_url'=>'',
		'video_group'=>'vod',
    );

    function __construct($category)
    {
		global $CI;
        $this->feed_uri = trim($category['source_uri']);
        $this->keep_order = $category['orderflag'];
		$this->catId = $category['id'];
		
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
		//Validating xml bof.
		/*
		* stopped on 3/9/2015
		if(!$xml->expand()){
			xmlvalidator($this->catId);
			} 
			*/
		// eof validation.

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
                          // $feedDef['uri'] = $element->nodeValue;
                           	break;
						case "enclosure":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
									$feedDef['icon_uri'] = urldecode($element->attributes->item($i)->value);
							}
							break;

                        case "guid":
                            $feedDef['assetid'] = $element->nodeValue;
							 break;
						case "wn:clipid":
							$clipId = $element->nodeValue; //to generate share url fo tv.
							break;
						case "paid":
							$feedDef['paid'] = $element->nodeValue;
							break;

						case "media:group":  
						 foreach ($element->childNodes as $mediaGroup) {
							 switch ($mediaGroup->nodeName) {
								case "media:content":
									 $length = $mediaGroup->attributes->length;
								
										for ($i = 0; $i < $length; ++$i) {
											if ($mediaGroup->attributes->item($i)->name == 'url')
												if(substr(parse_url($mediaGroup->attributes->item($i)->value, PHP_URL_PATH),-4) ==='.mp4')
													$feedDef['uri'] = $mediaGroup->attributes->item($i)->value;
																					
													 if ($mediaGroup->attributes->item($i)->name == 'medium')
													 $feedDef['type'] = $mediaGroup->attributes->item($i)->value;
													 
												}
                                     // break 2; 
									  }
									}
								break;

						case "media:thumbnail":
							   //$feedDef['icon_uri'] = $element->attributes->item(0)->value;
								$length = $element->attributes->length;
									for($i = 0; $i < $length; ++$i){
										if($element->attributes->item($i)->name =='url')
										$feedDef['icon_uri'] = urldecode($element->attributes->item($i)->value);
										}
                                break;
						case "media:credit":
							  $feedDef['author'] = $element->nodeValue;
							  break;

						case "media:content":  
							$mtype = array();
							 $length = $element->attributes->length;
								 for ($i = 0; $i < $length; ++$i) {
									
									if ($element->attributes->item($i)->name == 'url' && $element->attributes->item(0)->value =='video' ){
										$feedDef['uri'] = $element->attributes->item($i)->value;
										$ext = pathinfo($element->attributes->item($i)->value);
												 if($ext['extension']=='mp4'){
													$feedDef['video_type'] = 'video/mp4';
												 }else{
													 $feedDef['video_type']= 'application/octet-stream';
												 }
												}
									if($element->attributes->item($i)->name == 'url' && $element->attributes->item(0)->value =='image'){
										$feedDef['icon_uri'] = urldecode($element->attributes->item($i)->value);
									}
									
								 }

								
									
				 foreach ($element->childNodes as $mediaElement) {
							 switch ($mediaElement->nodeName) {
									case "media:title":
                                        $feedDef['title'] = $mediaElement->nodeValue;
                                        break;
									case "media:thumbnail":
                                        $feedDef['icon_uri'] = urldecode($mediaElement->attributes->item(0)->value);
                                        break;
                                    case "media:text":
                                        $feedDef['caption'] = $mediaElement->nodeValue;
                                        break;
									case "media:credit":
                                        $feedDef['author'] = $mediaElement->nodeValue;
                                        break;
									case "media:comments":
                                        $feedDef['comments_url'] = $mediaElement->nodeValue;
                                        break;
                                } 
							 }
         	  
                            break;


                        case "pubDate":
                            $feedDef['pub_date'] = date('Y-m-d H:i:s', strtotime($element->nodeValue));
                             break;
                        
                    }
                }
											  
				
                $stories[] = $feedDef;
            }
        }
		//print '<pre>';
		//print_r($stories);
		//print '</pre>';
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
        if (!is_array($stories) || sizeof($stories) == 0) {
            log_message('error', 'ERROR WITH STORY RESULTS:' . $stories);
            return false;
        }

        

        foreach ($stories as $key => $story) {

            $CI->db->where('category_id', $category_id);
            $CI->db->where('title', $story['title']);
            $CI->db->where('uri', $story['uri']);
			$CI->db->where('pub_date', $story['pub_date']); 
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
    function feed_order_zero($cid){
        global $CI;
        //$catq("Select publication_id from category where id={$category_id}");
        $CI->db->where('id', $cid);
        $result = $CI->db->get('category');
        $storyEntry = $result->row_array();

        ##$sql=("UPDATE content SET priority=0 where category_id={$storyEntry['id']}  ORDER BY pub_date DESC LIMIT 100 ");
        $sql = ("UPDATE content SET priority=0 where category_id={$storyEntry['id']} AND priority > 0");
        $CI->db->query($sql);
    }


	function get_cc($video_url){
        $entry_id = $this->getInbetweenStrings('entry_id\/','\/',$video_url);
        if(count($entry_id)<=0){return '';}
		$xml_url = 'http://cdnapi.kaltura.com//api_v3/index.php?service=caption_captionasset&action=list&filter%3AentryIdEqual='.$entry_id[0];
		$xml_array = simplexml_load_file($xml_url);
		if(!isset($xml_array->result->objects->item->id)){ return ''; }
        $captions_url = 'http://cdnbakmi.kaltura.com/api_v3/index.php/service/caption_captionAsset/action/serve/captionAssetId/'.$xml_array->result->objects->item->id;
       return $captions_url;
       
    }
    
    function getInbetweenStrings($start, $end, $str){
        $matches = array();
        $regex = "/$start([a-zA-Z0-9_]*)$end/";
        preg_match_all($regex, $str, $matches);
        return $matches[1];
    }


}