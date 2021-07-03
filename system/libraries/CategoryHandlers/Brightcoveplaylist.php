<?php
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);
class BrightcovePlaylistHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $videoListFeedContents = '';
	var $base_feed_uri = 'https://edge.api.brightcove.com/playback/v1/';
	
	//Play back api implemented on 20/3/2017, url creation : baseurl/accounts/{account id}/playlists/{playlists id}
	// Policy key should be sent in Header(BCOV-Policy:).
	// Source uril contains: Policy_key##accounts_id##playlists_id (separated by ## sign).
	//e.g: 'https://edge.api.brightcove.com/playback/v1/accounts/1105443290001/playlists/1111546818001';
	//Plicy_key:  //BCpkADawqM3i4ukeng17unalq7V2bQus763SO0KcJfXvx_4VxSQcbaRhkbMPPahzUHdVoNGsaBHydMcWF33fvutc8T65q5fJncXKIsjdluVljnpBuBH4aykSID21uieOM3FYZIH-Y7mwJYq5

	
	/* Legacy api call (Depricated)
	var $base_feed_uri = 'http://api.brightcove.com/services/library?command=find_playlist_by_id&media_delivery=http&video_fields=id,name,shortDescription,videoStillURL,publishedDate,thumbnailURL,FLVURL&token=';
	*/

	/*example url of Legacy api call.

	https://api.brightcove.com/services/library?command=find_playlist_by_id&media_delivery=http&video_fields=id,name,shortDescription,videoStillURL,publishedDate,thumbnailURL,FLVURL&token=hLGCV_uw2wWjyVxq6wgMMPHhLf3RjQbjeBWFnRgfxBFGsCaSAPYepg..&playlist_id=1111546818001
	*/

	var $videoDef = array(
		'title'=>'',
		'type'=>'video',
		'description'=>'',
		'content'=>'',
		'paid'=> 1,
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

	function getFeedContents($feed_uri, $policy_key) {
		log_message('debug','Getting feed contents with URI:'.$feed_uri);
		// create curl resource
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('BCOV-Policy:'.$policy_key));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// $output contains the output string
		$feedContents = curl_exec($ch);
		if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
			$er = 'Curl error: ' . curl_error($ch);
			print $er;
	
        }
		// close curl resource to free up system resources
		curl_close($ch);
//print $feedContents;
		return $feedContents;
	}



	/*
	 * This will take a feed uri and fetch the list of videos
	 */
	function getVideoList() { 
		$api_info_arr = explode('##',$this->feed_uri);
		$policy_key = $api_info_arr[0];
		$account_id = $api_info_arr[1];
		$playlist_id = $api_info_arr[2];
		$this->feed_uri =  $this->base_feed_uri.'accounts/'.$account_id.'/playlists/'.$playlist_id;
		$this->videoListFeedContents = $this->getFeedContents($this->feed_uri, $policy_key);
		if($this->videoListFeedContents=='') return false;

		$itemElements = json_decode($this->videoListFeedContents);	
		$videos = array();
		$count = 1;
		
				$videoDef = $this->getvideoDef();
				foreach($itemElements->videos as $element) {
					$videoDef['title'] = $element->name;
						$count =$count+1;
						$videoDef['priority'] = $count;
					if($element->description == 'null' || $element->description == null){$element->description = '';}
					$videoDef['description'] = $element->description;
					$videoDef['content'] = $element->description;

					foreach($element->poster_sources as $poster){
						if(parse_url($poster->src, PHP_URL_SCHEME) ==='https'){
							$videoDef['icon_uri'] = $poster->src;
						}
					}
					$heighBitRate = 0;
					foreach($element->sources as $k => $source){
						
						if(parse_url($source->src, PHP_URL_SCHEME)==='https'){
							if($heighBitRate < $source->avg_bitrate){
								$heighBitRate = $source->avg_bitrate;
								$videoDef['uri'] = $source->src;
							}
						}
					}

					$videoDef['assetid'] = 'Brightcove'.$element->id;
					$videoDef['pub_date'] = date('Y-m-d H:i:a', strtotime($element->published_at));
					$videos[] = $videoDef;




				}
				
				
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