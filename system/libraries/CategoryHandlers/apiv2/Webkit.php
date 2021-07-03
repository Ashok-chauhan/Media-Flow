<?php

class WebkitHandler {
	var $feed_uri = '';

	function __construct($category) {
		$this->feed_uri = $category['source_uri'];
	}

	function __destruct() {
		//nothing yet
	}

	function setCategoryVars($category) {
		$category['source_uri'] = $this->feed_uri;
		return $category;
	}

	function getUri() {

	}

	
////////////////////////////////////////
function refreshContent($category_id='') {
		if($category_id == '') return false;

		global $CI;
		//refresh the top level gallery entries first
		
			$CI->db->where('category_id',$category_id);
			
			$result = $CI->db->get('content');
			$storyEntry = $result->row_array();
			if(sizeof($storyEntry) == 0) { 
				$story['category_id'] = $category_id;
				
				$data = array(
					'uri'=> $this->feed_uri,
					'category_id'=>$story['category_id'] ,
					'type'=>'webkit'
					);
				$result = $CI->db->insert('content', $data);
				
				$story['id'] = $CI->db->insert_id();

				log_message('debug', 'Story Doesnt Exist, Adding....:'.$story['id']);
			} else {
				$story = array(
					'uri'=> $this->feed_uri,
					'type'=>'webkit'
					);
				$story['id'] = $storyEntry['id'];
				log_message('debug','Story Exist, Attempting to Upate....:'.$story['id']);
				$CI->db->where('id',$story['id']);
				$CI->db->update('content',$story);
			}
		
	}
/////////////////////////////////////////

	
}