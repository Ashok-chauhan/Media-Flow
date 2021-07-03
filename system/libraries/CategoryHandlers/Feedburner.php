<?php
class FeedburnerHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $storyListFeedContents = '';
	var $label = '';
	var $catId = '';

	var $feedDef = array(
		'title'=>'',
		'type'=>'story',
		'paid'=> 1,
		'priority'=> 0,
		'assetid'=> 0,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'caption'=>'',
		'uri'=>'',
		'pub_date'=>'',
		'author'=>'',
		'comments_url'=>''
        
	);

	function __construct($category) {
		$this->feed_uri = $category['source_uri'];
		$this->keep_order = $category['orderflag'];
		$this->label = $category['label'];
		$this->catId = $category['id'];
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
		//echo '<li>'.$feed_uri.'</br>';
		log_message('debug','Getting feed contents with URI:'.$feed_uri);
		// create curl resource
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,0); //
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // $output contains the output string
		$feedContents = curl_exec($ch);
        /*if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        */
		// close curl resource to free up system resources
		curl_close($ch);
       	return $feedContents;
	}


////////////////////////// added by ashok 08/10/2010 //////TO CHECK AND CONVERT CHARSET TO UTF-8 //
	function convert_charSet($string){
		if ( true === mb_check_encoding ($string, "UTF-8") )
			{
				return $string;
			}
			else
			{
				$str = mb_convert_encoding($string, "UTF-8" );
				return $str;
			}
		
	}

	///////////////////////////////////////////////////////////////////////////////
	
	/*
	 * This will take a feed uri and fetch the list of stories
	 */
	function getStoryList() { 
		
		$this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->storyListFeedContents=='') return false;
		$xml = new XMLReader();
		////////////////////////////////////////////// USING METHOD CONVERT CHAR TO CONFIRM UTF-8
		$xml->xml($this->convert_charSet($this->storyListFeedContents));
		//////////////////////////////////////////////
		#######$xml->xml($this->storyListFeedContents);
		$stories = array();
		$count = 0;
		while($xml->read()) {
			if($xml->nodeType == XMLReader::ELEMENT && $xml->name == 'item'){
				 $doc = new DOMDocument('1.0', 'UTF-8');
                 $element = simplexml_import_dom($doc->importNode($xml->expand(),true));
					$namespacesMeta = $element->getNamespaces(true);
					$contentXML = $element->children($namespacesMeta['content']);
					$linkXML = $element->children($namespacesMeta['feedburner']);

					$feedDef = $this->getFeedDef();
					$feedDef['title'] = (string)$element->title;
					// keep order of stories comming in rss feed.
						if($this->keep_order){
						$count =$count+1;
						$feedDef['priority'] = $count;
						}
					///////////////////
					$feedDef['description'] = (string)$element->description;
					$feedDef['comments_url'] = (string)$element->comments;
					//$feedDef['pub_date'] = (string)$element->pubDate;
					$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime((string)$element->pubDate));
					$feedDef['assetid'] = (string)$element->guid;
					$feedDef['content'] = (string)$contentXML->{'encoded'};
					$feedDef['uri']		= (string)$linkXML->{'origLink'};
					//$feedDef['content'] = (string)$element->children('content', true)->encoded;
				
				$stories[] = $feedDef;
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
		$stories = $this->getStoryList(); 
		if(!is_array($stories) || sizeof($stories) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$stories);
			return false;
		}
		
		// setting priority 0 for Publication.
		// before inserting new stories with priority .
		$this->feed_order_zero($category_id);
		foreach($stories as $key => $story) {
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

	/*
* feed_order_zero will update priority field value to zero(0).
* this is currently applicable to all (specificaly for missourian 08/12/2014) publication.
* param :  category id
*/ 
function feed_order_zero($cid){
        global $CI;
		$sql=("UPDATE content SET priority=0 where category_id=$cid AND priority > 0 ");
		$CI->db->query($sql);
	}
}