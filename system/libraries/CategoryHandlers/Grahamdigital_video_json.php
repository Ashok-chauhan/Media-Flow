<?php

class Grahamdigital_video_jsonHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $base_feed_uri = '';
	var $feed_uri = '';
	var $videoListFeedContents = '';

	
	//https://dist.grahamdigital.com/KSAT/video/live/ott/all.json

	var $videoDef = array(
		'title'=>'',
		'type'=>'video',
		'description'=>'',
		'paid'=> 1,
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'assetid' => '',
		'priority'=>0,
		'pub_date'=>'',
		'video_type'=>'',
		'video_group'=>'vod',
	);

	function __construct($category) { 
		$this->base_feed_uri = $category['source_uri'];
		$this->feed_uri = $category['data_store'];
	}

	function __destruct() {
		//nothing yet
	}

	function getvideoDef() {
		return $this->videoDef;
	}

	function setCategoryVars($category) {
		$category['data_store'] = $this->feed_uri;
		$category['source_uri'] = $this->base_feed_uri;
		return $category;
	}

	function getUri() {

	}

	function getFeedContents($feed_uri) {
		
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		$access_token = $this->feed_uri;
		$headers = array('Authorization: Bearer ' . $access_token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// $output contains the output string
		//echo 
		$feedContents = curl_exec($ch);
		// close curl resource to free up system resources
		curl_close($ch);
		
		return $feedContents;
	}



	/*
	 * This will take a feed uri and fetch the list of videos
	 */
	function getVideoList() {

		$this->videoListFeedContents = $this->getFeedContents($this->base_feed_uri);
		if($this->videoListFeedContents=='') return false;

		$itemElements = json_decode($this->videoListFeedContents);
		
		$videos = array();
		$count = 1;
		$sticky = 0;
	
		$daiAssetKey = $itemElements->assets[0]->daiAssetKey;
	
				$videoDef = $this->getvideoDef();
				foreach($itemElements->assets as $element) {
	
					$videoDef['daiAssetKey'] = $daiAssetKey;
					$videoDef['title'] = $element->title;
					$videoDef['sticky'] = 0;
					$count =$count+1;
					$videoDef['priority'] = $count;
					
					if($element->abstract==null){
						$videoDef['description'] = "";
					}else{
						$videoDef['description'] = $element->abstract;
					}

					$videoDef['content'] = $videoDef['description'];
					$videoDef['uri'] = $element->url;
					$videoDef['icon_uri'] = $element->image;
					$ext = substr($element->url,0, -3);
					if($ext ==='mp4'){
						$videoDef['video_type'] = 'video/mp4';
					}else{
						$videoDef['video_type']= 'application/octet-stream';
					}
					
					

					$videoDef['assetid'] = $element->id;
					$videoDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->updated));
				
					$videos[] = $videoDef;
				}

		//echo '<li>'. $_SERVER['HTTP_ACCEPT_CHARSET'];

		return $videos;
	}




	/*
	 *
	 */
	function refreshContent($category_id='') {

		if($category_id == '') return false;

		global $CI;

		$videos = $this->getVideoList();

		if(!is_array($videos) || sizeof($videos) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$videos);
			return false;
		}


		foreach($videos as $video) {
			$CI->db->where('category_id',$category_id);
			
			$CI->db->where('assetid',$video['assetid']);
			
			$result = $CI->db->get('content');
			$videoEntry = $result->row_array();


			if(sizeof($videoEntry) == 0) {
				$video['category_id'] = $category_id;
				$result = $CI->db->insert('content', $video);
				$video['id'] = $CI->db->insert_id();
				//echo '<br>Story Doesnt Exist, Adding....:'.$video['id'];
				log_message('debug', 'Story Doesnt Exist, Adding....:'.$video['id']);
			} else {
				$video['id'] = $videoEntry['id'];
				//echo '<br>Story Exist, Attempting to Upate....:'.$video['id'];
				log_message('debug','Story Exist, Attempting to Upate....:'.$video['id']);
				$CI->db->where('id',$video['id']);
				$CI->db->update('content', $video);
			}

		}

	}

}