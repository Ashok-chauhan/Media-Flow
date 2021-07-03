<?php

class VideoHandler {
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
}

}