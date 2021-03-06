<?php

class ViddlerHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_base_uri = '';
	var $feed_uri = '';
	var $keep_order = '';
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
		'pub_date'=>'',
	);

	function __construct($category) {
		global $CI;
		if($CI->config->item('viddler_feed_base_uri') =='')
			$this->feed_base_uri = 'http://www.viddler.com/explore';
		else
			$this->feed_base_uri = $CI->config->item('viddler_feed_base_uri');

		$this->feed_uri = $this->feed_base_uri.'/'.$category['data_store'].$CI->config->item('viddler_end_url');
		$this->provider_id = $category['data_store'];
		$this->keep_order = $category['orderflag'];
	}

	function __destruct() {
		//nothing yet
	}

	function setCategoryVars($category) {
		$category['source_uri'] = $this->feed_uri;
		return $category;
	}

	function getVideoDef() {
		return $this->videoDef;
	}

	function getUri() {

	}

	function getFeedContents($feed_uri) {
		log_message('debug', "Getting Viddler feedContents with uri:".$feed_uri);

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

							// keep order of stories comming in rss feed.
							if($this->keep_order){
							$count =$count+1;
							$videoDef['priority'] = $count;
							}
							///////////////////
							break;
						case "description":
							$videoDef['description'] = $element->nodeValue;
							break;
						case "link":
							$baseLink = substr($element->nodeValue,0,-1);
							$videoDef['uri'] = $baseLink.'.mp4';
							break;
						case "media:content":
							$mediaChildren = $element->childNodes;
							foreach($mediaChildren as $mediaChild) {
								if($mediaChild->nodeName == 'media:thumbnail') {
									$length = $mediaChild->attributes->length;
									for ($i = 0; $i < $length; ++$i) {
										if($mediaChild->attributes->item($i)->name == 'url')
											$videoDef['icon_uri'] = $mediaChild->attributes->item($i)->value;
									}
								}
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