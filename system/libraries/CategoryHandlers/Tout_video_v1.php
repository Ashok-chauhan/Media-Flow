<?php
class Tout_video_v1Handler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $videoListFeedContents = '';
	var $provider_id = '';
	var $keep_order = '';

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
		'video_group' =>'vod',
	);
	
	function __construct($category) {
		$this->feed_uri = trim($category['source_uri']);
		$this->keep_order = $category['orderflag'];
		//https://api.tout.com/api/v1/feeds/aede4d/touts?access_token=142586ced038fa6f1cf00e6d6ec10c8a2a1a735ce1edfda0beab1ab113f4e3ab
		}

	function __destruct() {
		//nothing yet
	}
	function setCategoryVars($category) {
		$category['source_uri'] = $this->feed_uri;
		return $category;
	}

	function getvideoDef() {
		return $this->videoDef;
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
		curl_setopt($ch, CURLOPT_ENCODING," ");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// $output contains the output string
		$feedContents = curl_exec($ch);
		if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
		// close curl resource to free up system resources
		curl_close($ch);

		//var_dump($feedContents);
		return $feedContents;
	}




	/*
	 * This will take a feed uri and fetch the list of videos
	 */
	function getVideoList() { 
		
		$this->videoListFeedContents = $this->getFeedContents($this->feed_uri);
		//print $this->videoListFeedContents ;
		if($this->videoListFeedContents=='') return false;

		$itemElements = json_decode(utf8_encode($this->videoListFeedContents));	
		//$itemElements = json_decode($this->videoListFeedContents);	
		if($itemElements== NULL)$this->jsonError();
		
		$videos = array();
		$count = 1;
		
				$videoDef = $this->getvideoDef();
				foreach($itemElements->touts as $key =>$element) {
						$videoDef['title'] = $element->tout->text;
					
						//////////////keep order of videos comming in rss feed.
						$count =$count+1;
						$videoDef['priority'] = $count;
						///////////////////

					$videoDef['description'] = $element->tout->text;
					$videoDef['content'] = $element->tout->text;
					$videoDef['uri'] = $element->tout->video->mp4->https_url;
					if($element->tout->video->mp4){
						$videoDef['video_type']= 'video/mp4';
					}
					$videoDef['assetid'] = $element->tout->uid;
					$videoDef['icon_uri'] = $element->tout->image->poster->https_url;
					$videoDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->tout->published_at));
					
					$videoDef['author'] = $element->tout->user->username;
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

function jsonError(){
	switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }
}


	





	

}