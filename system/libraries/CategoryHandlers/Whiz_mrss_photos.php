<?php

class Whiz_mrss_photosHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $storyListFeedContents = '';
	var $gallery_limit = '';


	var $feedDef = array(
		'title'=>'',
		'type'=>'gallery',
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
        'comments_url'=>'',
		'share_url' =>''
	);

	function __construct($category) {
		$this->feed_uri = $category['source_uri'];
		$this->keep_order = $category['orderflag'];
		$this->gallery_limit = $category['story_limit'];
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
		
		log_message('debug','Getting feed contents with URI:'.$feed_uri);
		// create curl resource
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,0); //
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
       	curl_setopt($ch, CURLOPT_VERBOSE, 1);
		// $output contains the output string
		$feedContents = curl_exec($ch);
		if(curl_errno($ch)){
           // echo 'Curl error: ' . curl_error($ch);
        }
		// close curl resource to free up system resources
		curl_close($ch);
		return $feedContents;
		
	}



////////////////////////// added by ashok 08/10/2010 //////////////////////////////
	//TO CHECK AND CONVERT CHARSET TO UTF-8 //
	function convert_charSet($string){
		if ( true === mb_check_encoding ($string, "UTF-8") )
			{
				return $string;
			}
			else
			{
				$str = mb_convert_encoding($string, "UTF-8" );
				return $str;
			}
		
	}

	/*
	 * This will take a feed uri and fetch the list of stories
	 */
	function getStoryList() { 
		
		$this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->storyListFeedContents=='') return false;

		$xml = new XMLReader();

		/////////////////// USING METHOD CONVERT CHAR TO CONFIRM UTF-8
		$xml->xml($this->convert_charSet($this->storyListFeedContents));
		//////////////////////////////////////////////

		###$xml->xml($this->storyListFeedContents);
		
		$xml->read(); 
		$rssRoot = $xml->expand();
		
		$rssChildren = $rssRoot->childNodes;
		$channel = null;
		foreach($rssChildren as $child) {
			if(!is_null($channel)) continue;
			if($child->nodeName == 'channel') {
				$channel = $child;
			}
		}
		$children = $channel->childNodes;
		$stories = array();
		$count = 0;
		foreach($children as $child) {
			if($child->nodeName == 'item') {
				$itemElements = $child->childNodes;
				$feedDef = $this->getFeedDef();
				foreach($itemElements as $element) {
					switch($element->nodeName) {
						case "title":
							$feedDef[$element->nodeName] = $element->nodeValue;
						
							if($this->keep_order){
							$count =$count+1;
							$feedDef['priority'] = $count;
							}
						
							break;

						case "description":
							$feedDef[$element->nodeName] = preg_replace('/<.*?>/'," ",substr($element->nodeValue,0,300));
                            //$feedDef[$element->nodeName] = substr(strip_tags($element->nodeValue),0,300);
                            $feedDef['content'] = $element->nodeValue;
							$feedDef['media_caption'] = $element->nodeValue; //content_media.

							break;

						case "link":
							$feedDef['uri'] = $element->nodeValue;
							$feedDef['share_url'] = $element->nodeValue;
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

						
//////////////////////////////////BOF EXTRA IMAGE(MEDIA) MANIPULATION IN STORY 
					
						   case "media:thumbnail":
							   $length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
									$feedDef['thumbnail'] = $element->attributes->item($i)->value;
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
								if($element->attributes->item($i)->name == 'url'){
									//$feedDef['icon_uri'] = $element->attributes->item($i)->value;
									$feedDef['icon_uri'] = '';
									$feedDef['media'] = $element->attributes->item($i)->value; //content_media
									
								}
							}
							break;
						case "content:encoded":
							$feedDef['content'] = $element->nodeValue;
						break;
						case "enclosure":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
									$feedDef['icon_uri'] = $element->attributes->item($i)->value;
							}
							break;

						case "pubDate":
						if(substr($element->nodeValue, -2)=='UT'){
							$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime(str_replace('UT', 'UTC', $element->nodeValue)));
						}elseif(substr($element->nodeValue, -2)=='ut'){
							$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime(str_replace('ut', 'UTC', $element->nodeValue)));
						}else{
							$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
						}
						
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
	function refreshContent($category_id='') { 
		if($category_id == '') return false;

		global $CI;
		$stories = $this->getStoryList(); 

		if(!is_array($stories) || sizeof($stories) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$stories);
			return false;
		}
		//STORY MEDIA MANIPULATION.
		foreach($stories as &$content){
			$type[] = 'photo';
			$media[] = $content['media'];
			$thumbnail[]= $content['thumbnail'];
			$caption[] = $content['media_caption'];
			unset ($content['media_type']);
			unset ($content['media']);
			unset ($content['thumbnail']);
			unset ($content['media_caption']);
			
		}
		foreach($stories as $key => $story) {
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$story['title']);
			$CI->db->where('uri',$story['uri']);
			$result = $CI->db->get('content');
			$storyEntry = $result->row_array();
			
			if(sizeof($storyEntry) == 0) {
				
				$story['category_id'] = $category_id;
				$result = $CI->db->insert('content', $story);
				$story['id'] = $CI->db->insert_id();
				log_message('debug', 'Story Doesnt Exist, Adding....:'.$story['id']);
			} else {
				$story['id'] = $storyEntry['id'];
				log_message('debug','Story Exist, Attempting to Upate....:'.$story['id']);
				$CI->db->where('id',$story['id']);
				$CI->db->update('content', $story);
			}
	///////////////////////////bof image(MEDIA) ingetion //////////////////////////////
			////////////////////////////////////////
				
			$CI->db->where('content_id', $story['id']);
			$CI->db->where('category_id', $category_id);
			$mResult = $CI->db->get('content_media');
			$mediaResult = $mResult->row_array();
			if(sizeof($mediaResult) == 0){ 
			///////////////////////////////////////
			
            if($media[$key]){ 
               
                    $pictureData = array(
                        'content_id' => $story['id'],
						'category_id' => $category_id,
                        'type' => $type[$key],
                        'thumbnail' => $thumbnail[$key],
                        'media' => $media[$key],
                        'caption' => $caption[$key],
                    );
                    $result = $CI->db->insert('content_media',$pictureData);
                    $pictureInsertId = $CI->db->insert_id();

              
            }
			}else{
				if($media[$key]){ 
               
                    $pictureData = array(
                        'content_id' => $story['id'],
						'category_id' => $category_id,
                        'type' => $type[$key],
                        'thumbnail' => $thumbnail[$key],
                        'media' => $media[$key],
                        'caption' => $caption[$key],
                    );
                    $CI->db->where('id', $mediaResult['id']); 
                    $CI->db->update('content_media',$pictureData);
              
            }
			}
			///////////////////eof image(MEDIA) ingetion //////////////////
		}
	}
}