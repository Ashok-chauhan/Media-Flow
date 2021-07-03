<?php
class Whiz_customHandler {
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

}

}
/**
* added by ashok 2014-08-18 for wsi weather & can be used for any purpose.
* whiz custom parser can be used for any kind of atype .
* there are nothing to parse Just Spoofing refreshpub / refreshcat api call.
*/
