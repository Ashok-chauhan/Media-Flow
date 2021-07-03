<?php

class JewishvideoHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $videoListFeedContents = '';

	var $videoDef = array(
		'title'=>'',
		'type'=>'video',
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'assetid'=> 0,
		'uri'=>'',
		'priority'=>0,
		'pub_date'=>'',
	);

	function __construct($category) {
		$this->feed_uri = $category['source_uri'];
	}

	function __destruct() {
		//nothing yet
	}

	function getvideoDef() {
		return $this->videoDef;
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
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// $output contains the output string
		$feedContents = curl_exec($ch);
		// close curl resource to free up system resources
		curl_close($ch);

		
		return $feedContents;
	}



	/*
	 * This will take a feed uri and fetch the list of videos
	 */
	function getVideoList() { 
		$this->videoListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->videoListFeedContents=='') return false;

		$itemElements = json_decode($this->videoListFeedContents);	
		$videos = array();
		$count = 1;
		$sticky = 0;
				$videoDef = $this->getvideoDef();
				foreach($itemElements as $element) {
					
					$videoDef['title'] = $element->articleMetadata->title;
					if($element->articleMetadata->isFeatured ==='true'){
						$sticky = $sticky +1;
						$videoDef['sticky'] = $sticky;
					}else{
						$videoDef['sticky'] = 0;
					}
						//////////////keep order of videos comming in rss feed.
						$count =$count+1;
						$videoDef['priority'] = $count;
						///////////////////

					$videoDef['description'] = $element->articleMetadata->excerpt;
					$videoDef['content'] = $element->articleMetadata->excerpt;
					$videoDef['uri'] = $element->articleMetadata->videoURL;
					$videoDef['assetid'] = $element->articleMetadata->videoURL;
					$videoDef['icon_uri'] = $element->articleMetadata->imageThumbnail;
					$videoDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->articleMetadata->publishDate));
					$videoDef['author'] = $element->articleMetadata->author;
					$videos[] = $videoDef;
				}
					
		//echo '<li>'. $_SERVER['HTTP_ACCEPT_CHARSET'];
		
		///////Setting featured video.////////
		foreach($videos as $key => &$value){
			if($value['sticky']==1){
				$value['priority']=1;
			}
		}
		////// Eof of featured video. ///////////
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
			$CI->db->where('title',$video['title']);
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



	





	

}