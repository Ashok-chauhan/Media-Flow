<?php

class Vimeo_salesfuelHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $base_feed_uri = '';
	var $feed_uri = '';
	var $videoListFeedContents = '';

	//var $base_feed_uri = 'https://api.vimeo.com/me/videos?filter=playable&filter_embeddable=true';

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

				$videoDef = $this->getvideoDef();
				foreach($itemElements->data as $element) {
					if($element->privacy->view != 'anybody')
					{
						continue;
					}
					//print_r($element->privacy->view);exit;
					
					$videoDef['title'] = $element->name;
					if(isset($element->isFeatured) && $element->isFeatured ==='true'){
						$sticky = $sticky +1;
						$videoDef['sticky'] = $sticky;
					}else{
						$videoDef['sticky'] = 0;
					}
						//////////////keep order of videos comming in rss feed.
						$count =$count+1;
						$videoDef['priority'] = $count;
						///////////////////
					if($element->description==null){
						$videoDef['description'] = "";
					}else{
						$videoDef['description'] = $element->description;
					}
					$videoDef['content'] = $videoDef['description'];
					//$videoDef['uri'] = $element->files[2]->link; //requested for 'hls' video instead of SD mp4(files[0]->link).
					foreach($element->files as $key => $hls){
						if($hls->quality == 'hls'){
							$videoDef['uri'] = $element->files[$key]->link_secure; //requested for 'hls' video instead of SD mp4(files[0]->link).
							$videoDef['video_type']= 'application/octet-stream';
							break;
						}elseif($hls->quality =='hd'){
							$videoDef['uri'] = $element->files[$key]->link_secure;
							$videoDef['video_type'] = 'video/mp4';
							break;
						}elseif($hls->quality =='source'){
							$videoDef['uri'] = $element->files[$key]->link_secure;
							$videoDef['video_type'] = 'video/mp4';
							break;
						}
					}
					$videoDef['icon_uri'] = $element->pictures->sizes[3]->link;
                    //$videoDef['icon_uri'] = preg_replace("/^https:/i", "http:", $videoDef['icon_uri']);

					$videoDef['assetid'] = 'Vimeo'.$element->uri;
					$videoDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->created_time));
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
			//$CI->db->where('title',$video['title']);
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