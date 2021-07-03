<?php

class BrightcoveHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $videoListFeedContents = '';
	var $base_feed_uri = 'http://api.brightcove.com/services/library?command=find_all_videos&sort_by=PUBLISH_DATE&page_size=20&video_fields=id,name,shortDescription,videoStillURL,publishedDate,thumbnailURL,FLVURL&media_delivery=http&token=';

	var $videoDef = array(
		'title'=>'',
		'type'=>'video',
		'paid'=> 1,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'assetid' => '',
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
print '<li>'.$feed_uri;
		print $feedContents;
		return $feedContents;
	}



	/*
	 * This will take a feed uri and fetch the list of videos
	 */
	function getVideoList() { 

		if (strstr($this->feed_uri, '##') !== false) { 
		
			$temparr = explode('##',$this->feed_uri);
			$temp_feed = $temparr[0].'&player_id='.$temparr[1];
			$this->feed_uri =  $this->base_feed_uri.$temp_feed;
			
		}else
		{
			$this->feed_uri =  $this->base_feed_uri.$this->feed_uri;
		}
		
		$this->videoListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->videoListFeedContents=='') return false;

		$itemElements = json_decode($this->videoListFeedContents);	
		$videos = array();
		$count = 1;
		$sticky = 0;
		
				$videoDef = $this->getvideoDef();
				foreach($itemElements->items as $element) {
					
					$videoDef['title'] = $element->name;
					if(@$element->isFeatured ==='true'){
						$sticky = $sticky +1;
						$videoDef['sticky'] = $sticky;
					}else{
						$videoDef['sticky'] = 0;
					}
						//////////////keep order of videos comming in rss feed.
						$count =$count+1;
						$videoDef['priority'] = $count;
						///////////////////

					$videoDef['description'] = $element->shortDescription;
					$videoDef['content'] = $element->shortDescription;
					$videoDef['uri'] = $element->FLVURL;
					$videoDef['icon_uri'] = $element->videoStillURL;
					$videoDef['assetid'] = 'Brightcove'.$element->id;
					$videoDef['pub_date'] = date('Y-m-d H:i:s',substr($element->publishedDate,0,-3));
				//	$videoDef['author'] = $element->articleMetadata->author;
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