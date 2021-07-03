<?php

class CumediaHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $storyListFeedContents = '';
	var $storyFeed = '';

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
		/*'media_type'=>'',
		'thumbnail'=>'',
		'media'=>'',
		'media_caption'=>''
		*/
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
		//https://api.forum.cue.cloud/inforum/sports/baseball/appdata
		//print '<li>'. $feed_uri;
		log_message('debug','Getting feed contents with URI:'.$feed_uri);
		
		// create curl resource
		$ch = curl_init();
		// set url
		
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,0); //
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
$feedContents = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        
		// close curl resource to free up system resources
		curl_close($ch);
       //print $feedContents;

		
		//var_dump($feedContents);
		return $feedContents;
	}



	/*
	 * This will take a feed uri and fetch the list of stories
	 */
	function getStoryLink() { 
		$this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->storyListFeedContents=='') return false;
		

		$itemElements = json_decode($this->storyListFeedContents);	
		if(count($itemElements)==0)	return false;
		
		$stories = array();
		$count = 0;
				$feedDef = $this->getFeedDef();
				$storyUrl=array();
				foreach($itemElements->data->latestInSection->items as $element) {
					$links = 'https://api.forum.cue.cloud'.$element->appdataUrl;
					$storyUrl[] = $links;
				}
		
		//return $storyUrl;
foreach($storyUrl as $storyLink){
				$this->storyFeed = $this->getFeedContents($storyLink);
				$itemElements = json_decode($this->storyFeed);	
					if(count($itemElements)==0)	return false;
						$text = '';
						$count =$count+1;
						$feedDef['priority'] = $count;

					$feedDef['pub_date'] = $itemElements->data->context->updated;
					if(isset($itemElements->data->context->authors[0]->name))
					$feedDef['author'] = $itemElements->data->context->authors[0]->name;
					$feedDef['title'] = $itemElements->data->context->title;
					$feedDef['uri'] = $itemElements->data->context->href;
					$feedDef['assetid'] = $itemElements->data->context->href;
				
		foreach($itemElements->data->context->fields->body as $key => $storyValue){
			$formatType = $storyValue->type;
			  foreach($storyValue as $k=>$paragraph){
			
				if($k=='children' && $paragraph[0]->type =='text'){
			
				$text .= '<'.$formatType.'>'.$paragraph[0]->text.'</'.$formatType.'>';
				}elseif(isset($paragraph[0]->type) && $paragraph[0]->type =='b'){
					$text .= '<b>'.$paragraph[0]->children[0]->text.'</b></br>';
				}
				
			}
			
			$feedDef['content'] = $text;
			$feedDef['description'] = substr($text, 0, 250);

						
		}

		$feedDef['caption'] = $itemElements->data->context->topMedia[0]->content->fields->caption;
			$feedDef['icon_uri'] = $itemElements->data->context->topMedia[0]->content->fields->landscape390->href_full;
/*
			// bof extra media //
			
			 $feedDef['media_caption'][] = $itemElements->data->context->topMedia[0]->content->fields->caption;
			 $feedDef['media'] []= $itemElements->data->context->topMedia[0]->content->fields->landscape390->href_full;
			 $feedDef['thumbnail'][] = $itemElements->data->context->topMedia[0]->content->fields->landscape390->href_full;
			 if($itemElements->data->context->topMedia[0]->content->type =='Picture'){
				 $feedDef['media_type'][] = 'photo';
			 }else{
				 $feedDef['media_type'] []= 'video';
			 }
		
			// eof extra media //
			*/
			
			

					$stories[] = $feedDef;
					

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
		
		//$stories = $this->getStoryList();
$stories = $this->getStoryLink();
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



function storyMedia($itemElements){
	
			$media = array();
			$related = array();
			foreach($itemElements->data->context->topMedia as $value){
				//print '<pre>';
				//print 'ggg';
				if($value->content->type =='Picture')
				$media['type'] =  'photo';
				$media['media'] = $value->content->fields->landscape390->href_full; //work
				$media['thumbnail'] = $value->content->fields->landscape320->href_full; //work
				$media['caption'] = $value->caption;
				
				
			}
			foreach($itemElements->data->context->relatedMedia as $relatedMedia){
				
				if($relatedMedia->content->type =='Picture') 
					$related['type'] =  'photo';
				
				$related['media'] = $relatedMedia->content->fields->landscape390->href_full; //work
				$related['thumbnail'] = $relatedMedia->content->fields->landscape320->href_full; //work
				$related['caption'] = $relatedMedia->caption;
				
				
			}
			
			$articleMedia[] = $media;
			$articleMedia[] = $related;
			return $articleMedia;

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