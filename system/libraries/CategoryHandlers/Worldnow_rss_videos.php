<?php
class Worldnow_rss_videosHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $videoListFeedContents = '';
	var $label = '';
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
		'video_type'=>'',
		'video_group'=>'vod'
        
	);

	function __construct($category) {
		$this->feed_uri = $category['source_uri'];
		$this->keep_order = $category['orderflag'];
		$this->label = $category['label'];
		$this->catId = $category['id'];
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
		curl_setopt($ch, CURLOPT_URL, trim($feed_uri));
		curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,0); //
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        // $output contains the output string
		$feedContents = curl_exec($ch);
       /* if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }*/
        
		// close curl resource to free up system resources
		curl_close($ch);
       // print $feedContents;
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

	///////////////////////////////////////////////////////////////////////////////

	/*
	 * This will take a feed uri and fetch the list of videos
	 */
	function getVideoList() { 
		
		$this->videoListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->videoListFeedContents=='') return false;

		$xml = new XMLReader();

		////////////////////////////////////////////// USING METHOD CONVERT CHAR TO CONFIRM UTF-8
		$xml->xml($this->convert_charSet($this->videoListFeedContents));
		//////////////////////////////////////////////
		#######$xml->xml($this->videoListFeedContents);
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
		$videos = array();
		$count = 0;
		foreach($children as $child) {
			if($child->nodeName == 'item') {
				$itemElements = $child->childNodes;
				$feedDef = $this->getFeedDef();
				foreach($itemElements as $element) {
					switch($element->nodeName) {
						case "title":
							$feedDef[$element->nodeName] = $element->nodeValue;
						// keep order of worldnow video comming in mrss feed.
							if($this->keep_order){
							$count =$count+1;
							$feedDef['priority'] = $count;
						}
						///////////////////
							break;
						case "description":
							$feedDef[$element->nodeName] = strip_tags($element->nodeValue);
							break;

						case "guid":
							$feedDef['assetid'] = $element->nodeValue;
							break;
						case "paid":
							$feedDef['paid'] = $element->nodeValue;
						break;
						case "media:text":
							$feedDef['caption'] = $element->nodeValue;
							break;
						case "media:content":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								$mediaContent[$element->attributes->item($i)->name]=$element->attributes->item($i)->value;
								//if($element->attributes->item($i)->name == 'url')
								//	$feedDef['uri'] = $element->attributes->item($i)->value;
							}
							

							if($mediaContent['medium'] == 'image' || $mediaContent['type'] == 'image/jpeg') $feedDef['icon_uri'] = urldecode($mediaContent['url']);
								if($mediaContent['medium'] == 'video'){ 
									$feedDef['uri'] = $mediaContent['url'];
									if(substr(parse_url($mediaContent['url'], PHP_URL_PATH),-4) ==='.mp4'){
											$feedDef['video_type'] = 'video/mp4';
										}else{
											$feedDef['video_type'] = 'application/octet-stream';
										}
								}
								unset($mediaContent);
							break;
						
						case "media:thumbnail":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
									$feedDef['icon_uri'] = urldecode($element->attributes->item($i)->value);
							}
							break;

							/////////////////////////////////////////////////////////////
						case "media:group":  
							$mp4 = false;
						 foreach ($element->childNodes as $mediaGroup) {
							 switch ($mediaGroup->nodeName) {
								case "media:content":
									 $length = $mediaGroup->attributes->length;
								
										for ($i = 0; $i < $length; ++$i) {
											if ($mediaGroup->attributes->item($i)->name == 'url')
											{
												if(substr(parse_url($mediaGroup->attributes->item($i)->value, PHP_URL_PATH),-4) ==='.mp4')
												{
													$feedDef['uri'] = $mediaGroup->attributes->item($i)->value;
													$feedDef['video_type'] = 'video/mp4';
													$mp4 = true;
												}
											}
													 
										}
										if($mp4) break 2;
                                      //break 2; 
									  }
									}
								break;
							/////////////////////////////////////////////////////////////

						case "pubDate":
							$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
							break;
						
					}
				}

				$videos[] = $feedDef;
				
			}
		}

		return $videos;
	}

	/*
	 * 
	 */
	function refreshContent($category_id='') { 
		if($category_id == '') return false;
		global $CI;
		//refresh the top level gallery entries first
		$videos = $this->getVideoList(); 
		if(!is_array($videos) || sizeof($videos) == 0) {
			log_message('error', 'ERROR WITH video RESULTS:'.$videos);
			return false;
		}
		
		
		// setting priority 0 for Publication.before inserting new videos with priority .
		$this->feed_order_zero($category_id);
		foreach($videos as $key => $video) {
		
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$video['title']);
			$CI->db->where('uri',$video['uri']);
			$result = $CI->db->get('content');
			$videoEntry = $result->row_array();
		
			if(sizeof($videoEntry) == 0) {
				$video['category_id'] = $category_id;
				$result = $CI->db->insert('content', $video);
				$video['id'] = $CI->db->insert_id();
				log_message('debug', 'video Doesnt Exist, Adding....:'.$video['id']);
			} else {
				$video['id'] = $videoEntry['id'];
				log_message('debug','video Exist, Attempting to Upate....:'.$video['id']);
				$CI->db->where('id',$video['id']);
				$CI->db->update('content', $video);

				}
			}
		}

	/*
* feed_order_zero will update priority field value to zero(0).
* this is currently applicable to all (specificaly for missourian 08/12/2014) publication.
* param :  category id
*/ 
function feed_order_zero($cid){
        global $CI;
		$sql=("UPDATE content SET priority=0 where category_id=$cid AND priority > 0 ");
		$CI->db->query($sql);
	}
}