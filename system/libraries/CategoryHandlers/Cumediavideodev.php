<?php
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);
class CumediavideodevHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $videoListFeedContents = '';
	var $videoFeed = '';
	var $feedDef = array(
		'title'=>'',
		'type'=>'video',
		'paid'=> 0,
		'priority'=> 0,
		'assetid'=> 0,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'caption'=>'',
		'uri'=>'',
		'pub_date'=>'',
		'author'=>'',
		
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
		log_message('debug','Getting feed contents with URI:'.$feed_uri);
		// create curl resource
		$ch = curl_init();
		// set url
		
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,0); //
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
$feedContents = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        
		// close curl resource to free up system resources
		curl_close($ch);
     // print_r($feedContents);
		return $feedContents;
	}



	/*
	 * This will take a feed uri and fetch the list of videos
	 */
	function getVideos() { 
		$this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->storyListFeedContents=='') return false;
		
		$itemElements = json_decode($this->storyListFeedContents);
		
		$feedDef = $this->getFeedDef();
foreach($itemElements->data->videoSections as $videoSections){
	
	foreach($videoSections->item->section->page->latest_video->items as $item){
		$count =$count+1;
		$feedDef['priority'] = $count;
		$feedDef['title'] = $item->title;
		$feedDef['cc_type'] = $item->caption_href;
		$feedDef['pub_date'] = $item->updated;
		$feedDef['assetid'] = microtime();
		$feedDef['uri'] = $item->mediaInfo->videos[0]->uri;
		$feedDef['icon_uri'] = $item->keyframeImages[0]->content->fields->landscape320->href_full;
		$feedDef['video_type'] = $item->mediaInfo->videos[0]->mimeType;
		
		$videos[] = $feedDef;

		}
	}

//print '<pre>';
//print_r($videos);
return $videos;
	}

	/*
	 * 
	 */

	 function refreshContent($category_id='') { 
		if($category_id == '') return false;
		global $CI;
		$videos = $this->getVideos(); 
		if(!is_array($videos) || sizeof($videos) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$videos);
			return false;
		}
	

		foreach($videos as $key => $video) {
			
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$video['title']);
			//$CI->db->where('uri',$video['uri']);
			$result = $CI->db->get('content');
			$videoEntry = $result->row_array();
			
			if(sizeof($videoEntry) == 0) {
				$video['category_id'] = $category_id;
				$result = $CI->db->insert('content', $video);
				$video['id'] = $CI->db->insert_id();
			
				log_message('debug', 'Story Doesnt Exist, Adding....:'.$video['id']);
				
				
			} else {
				
				$video['id'] = $videoEntry['id'];
				log_message('debug','Story Exist, Attempting to Upate....:'.$video['id']);
				$CI->db->where('id',$video['id']);
				$CI->db->update('content', $video);
				
			}


		}
	}

	



// excluding unwanted characters from title CURRENTLY NOT IN USE MAY BE USE IN FUTURE.
// not in use.
	function unhtmlentities($string){
    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
    // replace literal entities
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
	}

}
// Video parser for Cuemedia videos created on 17/6/2019.