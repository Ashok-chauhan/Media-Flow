<?php

class AtomHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $storyListFeedContents = '';

	var $feedDef = array(
		'title'=>'',
		'type'=>'story',
		'priority'=> 0,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'pub_date'=>'',
		'author'=>'',
	);

	function __construct($category) {
				
		$this->feed_uri = $category['source_uri'];
		$this->keep_order = $category['orderflag'];
		
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

		$xml = simplexml_load_string($this->storyListFeedContents);

		$stories = array();
		$count = 0;
  foreach ($xml->entry as $entries) { 
	 $feedDef = $this->getFeedDef();
	 if(isset($entries->title)){
		 $feedDef['title'] = (string)$entries->title;
		 ///////////////////
						// keep order of stories comming in rss feed.
						if($this->keep_order){
						$count =$count+1;
						$feedDef['priority'] = $count;
						}
		///////////////////
	 }
	 if(isset($entries->updated)){
		 $feedDef['pub_date'] = (string)$entries->updated;
	 }
	 if(isset($entries->author->name)){
			 $string = (string)$entries->author->name;
			 $pattern = array();
			 $pattern[0] = '/By/';
			 $pattern[1] = '/by/';
			 $replacements = array();
			 $replacements[1] = '';
			 $replacements[0] = '';
			 $writter = preg_replace ($pattern, $replacements, $string);
			$feedDef['author'] = $writter;
		 
	 }elseif(isset($entries->author)){
			$string = (string)$entries->author;
			 $pattern = array();
			 $pattern[0] = '/By/';
			 $pattern[1] = '/by/';
			 $replacements = array();
			 $replacements[1] = '';
			 $replacements[0] = '';
			 $writter = preg_replace ($pattern, $replacements, $string);
			 $feedDef['author'] = $writter;
	 }

////////////////GETING ATTRIBUTES FOR URL AND IMAGE URL/////////////////////////////

foreach ($entries->children() as $subchild)
	{		
			if($subchild->getName() =='link')
			{
		
				foreach ($subchild->attributes() as $k => $sattr)
				{
					if($sattr->getName() == 'href'  ){ 
						if(substr((string)$sattr, -4) == '.jpg'){
						$feedDef['icon_uri'] = (string)$sattr;
						}else{
						$feedDef['uri']= (string)$sattr;
						}
					}
								
				}
			
			}

		}

////////////////////////////////////////////////////////////////////////////////////////
				 
		 $content_data = $entries->content->asXML();
		 $tags = array('<content type="xhtml">',"</content>");
		 $notags = array("","");
		 $feedDef['content'] = str_replace($tags, $notags, $content_data);
		 $feedDef['description'] = substr(strip_tags((string)$entries->content->asXML()),0,120);
		 
		  $stories[]= $feedDef;
      
  }
		
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
			$CI->db->where('uri',$story['uri']);
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
}