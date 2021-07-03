<?php
class FlickrHandler{
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $storyListFeedContents = '';

	var $feedDef = array(
		'title'=>'',
		'type'=>'photo',
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

		// Parsing a large document with Expand and SimpleXML
	$reader = new XMLReader();

	$reader->xml($this->storyListFeedContents);
	$count = 0;
	while ($reader->read()) {
   switch ($reader->nodeType) {
   case (XMLREADER::ELEMENT):
      if ($reader->localName == "entry") {
         
            while ($reader->read()) {
               if ($reader->nodeType == XMLREADER::ELEMENT) {
                  if ($reader->localName == "title") {
                     $reader->read();
                    // echo $reader->value;
					 $this->feedDef['title']= $reader->value;
					 /////////////// keep order of stories comming in rss feed.
						if($this->keep_order){
						$count =$count+1;
						$feedDef['priority'] = $count;
						}
					///////////////////
                    
                  }
				  if ($reader->localName == "published") {
					  $reader->read();
						$this->feedDef['pub_date']= $reader->value;
						
				  }
                  if ($reader->localName == "name") {
					  $reader->read();
					  $this->feedDef['author']= $reader->value;
                    
                  }
				  if ($reader->localName == "uri") {
					  $reader->read();
						$this->feedDef['uri']= $reader->value;
                   
				  }
				  if ($reader->localName == "content") {
					  $reader->read();
					   preg_match_all('/(<img)\s (src="([a-zA-Z0-9\.;:\/\?&=_|\r|\n]{1,})")/isxmU',$reader->value,$patterns);
			   $this->feedDef['content']= $patterns[3][0];
				
				  $stories[] = $this->feedDef;
				  }
				  
               }

           }
         
      }
   }
}

return $stories;
	}


	/*
	 * 
	 */
	function refreshContent($category_id='') {
 
		if($category_id == '') return false;

		global $CI;

		//refresh the top level  entries first
		$stories = $this->getStoryList(); 





		if(!is_array($stories) || sizeof($stories) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$stories);
			return false;
		}
		foreach($stories as $story) {

			
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$story['title']['0']);
			//$CI->db->where('uri',$story['uri']['0']);
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
