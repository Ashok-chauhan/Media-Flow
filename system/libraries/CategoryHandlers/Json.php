<?php

class JsonHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $storyListFeedContents = '';

	var $feedDef = array(
		'title'=>'',
		'type'=>'story',
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
	function getStoryList() { 
		$this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->storyListFeedContents=='') return false;
		// getting hometop flag for specified fcategory.
		$gethometop = $this->get_hometop($this->feed_uri);

		$itemElements = json_decode($this->storyListFeedContents);	
		if(count($itemElements)==0)	return false;
		
		$stories = array();
		$ishome = 0;
		$isfeatured = 0;
		$count = 2;
				$feedDef = $this->getFeedDef();

				foreach($itemElements as $element) {
					$feedDef['title'] = $element->articleMetadata->title;
					
					if(isset($gethometop)){
					if($element->articleMetadata->isHomeTop ==='true'){
						$ishome = $ishome +1;
						$feedDef['ishometop'] = $ishome;
					}else{
						$feedDef['ishometop'] = 0;
					}
					}

					if($element->articleMetadata->isFeatured ==='true'){
						$isfeatured = $isfeatured +1;
						$feedDef['isfeatured'] = $isfeatured;
					}else{
						$feedDef['isfeatured'] = 0;
					}
					
						//////////////keep order of stories comming in rss feed.
						$count =$count+1;
						$feedDef['priority'] = $count;
						///////////////////

					$feedDef['description'] = $element->articleMetadata->excerpt;
					$feedDef['content'] = $element->articleMetadata->articleData;
					$feedDef['uri'] = $element->articleMetadata->articleURL;
					$feedDef['assetid'] = $element->articleMetadata->articleURL;
					$feedDef['icon_uri'] = $element->articleMetadata->imageThumbnail;
					$feedDef['caption'] = $element->articleMetadata->featureImageCaption;
					$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->articleMetadata->publishDate));
					//$feedDef['pub_date'] = date('Y-m-d H:i:s P',strtotime($element->articleMetadata->publishDate));
					$feedDef['author'] = $element->articleMetadata->author;
					$stories[] = $feedDef;
				}
					
		//echo '<li>'. $_SERVER['HTTP_ACCEPT_CHARSET'];
		
		// BOF featured stories //////
		foreach($stories as $key => &$value){
			if(isset($gethometop)){
				if($value['ishometop']==1){
					$value['priority']=1;
						}
					}
				if($value['isfeatured']==1){
						$value['priority']=2;
					}
			
			unset($value['ishometop']);
			unset($value['isfeatured']);
		}
		////// Eof of featured story. ///////////

		return $stories;
	}

	/*
	 * 
	 */
	function refreshContent($category_id='') {
		if($category_id == '') return false;

		global $CI;
		//refresh the top level gallery entries first
		
		$stories = $this->getStoryList();

		if(!is_array($stories) || sizeof($stories) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$stories);
			return false;
		}
		

		foreach($stories as $story) {
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$story['title']);
			$result = $CI->db->get('content');
			$storyEntry = $result->row_array();
			
			
			if(sizeof($storyEntry) == 0) {
				$story['category_id'] = $category_id;
				$result = $CI->db->insert('content', $story);
				$story['id'] = $CI->db->insert_id();
				log_message('debug', 'Story Doesnt Exist, Adding....:'.$story['id']);
			} else {
				$story['id'] = $storyEntry['id'];
				log_message('debug','Story Exist, Attempting to Upate....:'.$story['id']);
				$CI->db->where('id',$story['id']);
				$CI->db->update('content', $story);
			}
			
		}

		
	}





// excluding unwanted characters from title CURRENTLY NOT IN USE MAY BE USE IN FUTURE.
	function unhtmlentities($string){
    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
    // replace literal entities
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

	/**
	* getting home top and parsing.
	* if hometop is not setted will ignore hometop
	* for all the categories.
	*/
	function get_hometop($url){
		$homevalue = explode('?',$url);
		if(isset($homevalue[1])){
		$homevar = explode('=', $homevalue[1]);
			if($homevar[0]=='hometop' && $homevar[1]=='true'){
				$gethometop = true;
				return $gethometop;
			}
		}
	}


}