<?php
class VmixvideoHandler {
	var $feed_base_uri = '';
	var $video_list_uri = '';
	var $keep_order = '';
	
	var $vmixDef = array(
		'title'=>'',
		'type'=>'video',
		'priority'=> 0,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'pub_date'=>'',
	);
	var $videoDef = array(
		'thumbnail_url'=>'',
		'play_url'=>'',
		
	);

	function __construct($category) { 
		
			$this->feed_base_uri = $category['source_uri'];
			$this->video_list_uri = $this->feed_base_uri;
			$this->keep_order = $category['orderflag'];
	}

	function __destruct() {
		//nothing yet
	}

	function getVimixVideoDef() {
		return $this->videoDef;
	}

	function getVideoDef() {
		return $this->vmixDef;
	}


	function getUri() {

	}

	function importData() {

	}

	function getFeedContents($feed_uri) {
		
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
	 * This will take a feed base uri and fetch the list of videos.
	 */
	function getVideoList($category_id='') {
		global $CI;
		$this->videoListFeedContents = $this->getFeedContents($this->video_list_uri);
		
		if($this->videoListFeedContents=='')  return false;
		$vmixvideos = array();
		$count = 0;
		$elements = json_decode($this->videoListFeedContents);
		$vmixDef = $this->getVideoDef();
		
			foreach($elements as $element){
				/* 
				* Chacking video in databse 
				* if last modified data and title matched with 
				* client feed getVideo method should not be called 
				* to save traffic on client's server.
				*/
				if($this->isvideo_notexist($category_id,$element->last_modified,$element->title)){
	
				$vmixDef['title'] = $element->title;

						// keep order of stories comming in rss feed.
						if($this->keep_order){
						$count =$count+1;
						$vmixDef['priority'] = $count;
						}
						///////////////////

				$vmixDef['description'] = $element->description;
				$vmixDef['content'] = $element->description;
				$vmixDef['pub_date'] = $element->last_modified;
				$vmixDef['author'] = $element->author;
				$formatexist658 = FALSE;
				foreach($element->formats as $formats){
					foreach($formats as $format){
					if($format->format_id == 658){
					// get mp4 file from json feed if format_id is 658. 
									
						$vid = $this->getVideo($CI->config->item('vmixplayurl').$format->token);
						$vmixDef['icon_uri'] = $vid[0]['thumbnail_url'];
						$vmixDef['uri'] = $vid[0]['play_url'];
						$formatexist658 = TRUE;
						}
					}
				}
				IF($formatexist658){
				$vmixvideos[] = $vmixDef;
				}
			  }
			}
		
		
		return $vmixvideos;
	}


	/*
	 * This method  will call only when 
	 * isvideo_notexist method return to true and
	 * fetch a mp4 file from feed url
	 */
	function getVideo($video_feed_uri) {
	
		$videoFeedContents = $this->getFeedContents($video_feed_uri);
		if($videoFeedContents == '') return false;
		$itemElements = json_decode($videoFeedContents);
		
				$videos = array();
				$videoDef = $this->getVimixVideoDef();

				foreach($itemElements as $element) {
					$videoDef['thumbnail_url'] = $element->thumbnail_url;
					$videoDef['play_url'] = $element->play_url;
					$videos[] = $videoDef;
				}
			
		return $videos;
	}
/*
* isvideo_notexist method check to find video in our database .
* param: category_id and publication data and title of video on clients feed.
*/

	function isvideo_notexist($category_id,$time,$title){
				global $CI;
				$CI->db->where('category_id',$category_id);
				$CI->db->where('pub_date',$time);
				$CI->db->where('title',$title);
				$result = $CI->db->get('content');
				if($result->num_rows > 0)
				{
					return FALSE;
				}else{
					return TRUE;
				}
			}

	function refreshContent($category_id='') {
		if($category_id == '') return false;

		global $CI;
		//refresh the top level video list entries first
		$videos = $this->getVideoList($category_id);

		foreach($videos as $video) {
			
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$video['title']);
			$CI->db->where('pub_date',$video['pub_date']);
			$result = $CI->db->get('content');
			$videoEntry = $result->row_array();
			if(sizeof($videoEntry) == 0) {
				$video['category_id'] = $category_id;
				$result = $CI->db->insert('content', $video);
				$video['id'] = $CI->db->insert_id();
				log_message('debug', 'Video Doesnt Exist, Adding....:'.$video['id']);
			} 
		
		}
	}


}


