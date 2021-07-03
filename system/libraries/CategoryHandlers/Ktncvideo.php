<?php

class KtncvideoHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_base_uri = '';
	var $keep_order = '';
	var $feed_uri = '';
	var $videoListFeedContents = '';
	var $provider_id = '';

	var $videoDef = array(
		'title'=>'',
		'type'=>'video',
		'priority'=> 0,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'assetid'=> 0,
		'pub_date'=>'',
	);

	function __construct($category) {
		$this->feed_uri = $category['source_uri'];
		$this->keep_order = $category['orderflag'];
	}

	function __destruct() {
		//nothing yet
	}

	function getVideoDef() {
		return $this->videoDef;
	}

	function setCategoryVars($category) {
		$category['source_uri'] = $this->feed_uri;
		return $category;
	}

	function getUri() {

	}


	function getFeedContents($feed_uri) {
		log_message('debug', "Getting Video feedContents with uri:".$feed_uri);

		// create curl resource
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// $output contains the output string
		$feedContents = curl_exec($ch);
		// close curl resource to free up system resources
		curl_close($ch);

		return $feedContents;
	}

	/*
	 * This will take a feed uri and fetch the list of stories
	 */
	function getVideoList() {
		$this->videoListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->videoListFeedContents=='') return false;

		$xml = new XMLReader();
		$xml->xml($this->videoListFeedContents);
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
				$videoDef = $this->getVideoDef();

				foreach($itemElements as $element) {
					
					switch($element->nodeName) {
						case "title":
							$videoDef['title'] = $element->nodeValue;
						///////////////////
						// keep order of stories comming in rss feed.
						if($this->keep_order){
						$count =$count+1;
						$videoDef['priority'] = $count;
						}
						///////////////////
							break;
						case "description":
							
							$videoDef['description'] = strip_tags($element->nodeValue);
							
							break;
						case "content":
							$videoDef['content'] = $element->nodeValue;
							break;
						case "guid":
							$videoDef['assetid'] = $element->nodeValue;
							break;
												
						case "media:content":
							$mediaItem = $element->childNodes;
						
						foreach($mediaItem as $media){
							
							if($media->nodeName == 'media:thumbnail'){
								$length = $media->attributes->length;
								for ($i = 0; $i < $length; ++$i) {
								if($media->attributes->item($i)->name == 'url')
											
											$videoDef['icon_uri'] = $media->attributes->item($i)->value;
											
									}
								}
							}
							break;

							case "meta":
								$videoDef['icon_uri'] = $element->nodeValue;
								break;

							case "enclosure":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								
								if($element->attributes->item($i)->name == 'url')
									
									$videoDef['uri'] = $element->attributes->item($i)->value;
							}
							break;
						case "pubDate":
							
							$videoDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
							
							break;
					}
				}
				$videos[] = $videoDef;
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

		foreach($videos as $video) {
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$video['title']);
			$CI->db->where('uri',$video['uri']);
			$result = $CI->db->get('content');
			$videoEntry = $result->row_array();
			if(sizeof($videoEntry) == 0) {
				$video['category_id'] = $category_id;
				$result = $CI->db->insert('content', $video);
				$video['id'] = $CI->db->insert_id();
				log_message('debug', 'Video Doesnt Exist, Adding....:'.$video['id']);
			} else {
				$video['id'] = $videoEntry['id'];
				log_message('debug', 'Video Exist, Attempting to Upate....:'.$video['id']);
				$CI->db->where('id',$video['id']);
				$CI->db->update('content', $video);
			}
		}
	}
}