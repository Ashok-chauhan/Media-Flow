<?php
class Whole_hog_sports_audioHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $storyListFeedContents = '';
	var $label = '';
	var $catId = '';
	const UserAgent = 'Dalvik/2.1.0 (Linux; U; Android 5.0.2; MotoE2 Build/LXC22.99-13.3)';

	var $feedDef = array(
		'title'=>'',
		'type'=>'audio',
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
		'media_type'=>'',
		'thumbnail'=>'',
		'media'=>'',
		'media_caption'=>'',
		'comments_url'=>''
        
	);

	function __construct($category) {
		$this->feed_uri = $category['source_uri'];
		$this->keep_order = $category['orderflag'];
		$this->label = $category['label'];
		$this->catId = $category['id'];
		
		//echo $this->keep_order;
		//exit;
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
		curl_setopt($ch, CURLOPT_USERAGENT, Whole_hog_sports_audioHandler::UserAgent);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        // $output contains the output string
		$feedContents = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        
		// close curl resource to free up system resources
		curl_close($ch);
        
		return $feedContents;
	}


////////////////////////// added by ashok 08/10/2010 //////////////////////////////
	//TO CHECK AND CONVERT CHARSET TO UTF-8 //
	function convert_charSet($string){
		if ( true === mb_check_encoding ($string, "UTF-8") )
			{
				echo '<li>valid (' . $encoding . ') encoded byte stream!!!!<br />';
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
	 * This will take a feed uri and fetch the list of audios
	 */
	function getStoryList() { 
		
		$this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
		if($this->storyListFeedContents=='') return false;

		$xml = new XMLReader();

		////////////////////////////////////////////// USING METHOD CONVERT CHAR TO CONFIRM UTF-8
		//$xml->xml($this->convert_charSet($this->storyListFeedContents));
		//////////////////////////////////////////////

		$xml->xml($this->storyListFeedContents);
		$xml->read();
		$rssRoot = $xml->expand();
		$rssChildren = $rssRoot->childNodes;
		$channel = null;
		foreach($rssChildren as $child) {
			if(!is_null($channel)) continue;
			if($child->nodeName == 'channel') {
				$channel = $child;
			}
		}
		$children = $channel->childNodes;
		$stories = array();
		$count = 0;
		foreach($children as $child) {
			if($child->nodeName == 'item') {
				$itemElements = $child->childNodes;
				$feedDef = $this->getFeedDef();
				foreach($itemElements as $element) {
					switch($element->nodeName) {
						case "title":
							$feedDef[$element->nodeName] = $element->nodeValue;
						///////////////////
						// keep order of stories comming in rss feed.
						if($this->keep_order){
						$count =$count+1;
						$feedDef['priority'] = $count;
						}
						///////////////////
							break;

						case "description":
							$feedDef[$element->nodeName] = preg_replace('/<.*?>/'," ",substr($element->nodeValue,0,300));
							if (!isset($feedDef['content']) || $feedDef['content']=='') {
								$feedDef['content'] = $element->nodeValue;
							}
							break;

						//case "link":
						//	$feedDef['uri'] = $element->nodeValue;
						//	break;
						case "itunes:duration":
							$feedDef['duration'] = $element->nodeValue;
							break;
						case "guid":
							$feedDef['assetid'] = $element->nodeValue;
							break;
						case "paid":
							$feedDef['paid'] = $element->nodeValue;
						break;

						case "caption":
								$feedDef['caption'] = $element->nodeValue;
							break;

						case "content":
							// if Node content is avialable all the above should be  voerride.
							$feedDef['content'] = $element->nodeValue;
							break;

						case "enclosure":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url'){
									$feedDef['media'][] = trim($element->attributes->item($i)->value);
									$feedDef['uri'] = trim($element->attributes->item($i)->value);
								}
								if($element->attributes->item($i)->name == 'type'){
									$feedDef['media_type'][]= $element->attributes->item($i)->value;
								}
							}
							
							break;
						
						case "itunes:image":
							$len = $element->attributes->length;
							for($i = 0; $i< $len; ++$i){
								if($element->attributes->item($i)->name == 'href'){
									 $feedDef['thumbnail'][] = trim($element->attributes->item($i)->value);
									 $feedDef['icon_uri'] = trim($element->attributes->item($i)->value);
								}
							}
							break;

						case "pubDate":
							$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
							break;

						case "itunes:author":
							$feedDef['author'] = $element->nodeValue;
							break;
						case "comments":
                            $feedDef['comments_url'] = $element->nodeValue;
                        break;
					}
				}
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
		//refresh the top level gallery entries first
		
		$stories = $this->getStoryList(); 

		if(!is_array($stories) || sizeof($stories) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$stories);
			return false;
		}
		//STORY MEDIA MANIPULATION.
		foreach($stories as &$content){
			$type[] = $content['media_type'];
			$media[] = $content['media'];
			$thumbnail[]= $content['thumbnail'];
			$caption[] = $content['media_caption'];
			$duration[] = $content['duration'];
			unset ($content['media_type']);
			unset ($content['media']);
			unset ($content['thumbnail']);
			unset ($content['media_caption']);
			unset ($content['duration']);
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
	///////////////////////////bof image(MEDIA) ingetion //////////////////////////////
			////////////////////////////////////////
				
			$CI->db->where('content_id', $story['id']);
			$CI->db->where('category_id', $category_id);
			$mResult = $CI->db->get('content_media');
			$mediaResult = $mResult->row_array();
			if(sizeof($mediaResult) == 0){ 
			///////////////////////////////////////
            if($media[$key]){ 
                foreach ($media[$key] as $k=>  $med) { 
					
                    $pictureData = array(
                        'content_id' => $story['id'],
						'category_id' => $category_id,
						'media_order' => $k,
                        'type' => $type[$key][$k],
                        'thumbnail' => $thumbnail[$key][$k],
                        'media' => $med,
                        'caption' => $caption[$key][$k],
						'duration' => $duration[$key],
                    );
                    $result = $CI->db->insert('content_media',$pictureData);
                    $pictureInsertId = $CI->db->insert_id();

                }
            }
			}else{
				if($media[$key]){ 
                foreach ($media[$key] as $k=>  $med) { 
                    $pictureData = array(
                        'content_id' => $story['id'],
						'category_id' => $category_id,
						'media_order' => $k,
                        'type' => $type[$key][$k],
                        'thumbnail' => $thumbnail[$key][$k],
                        'media' => $med,
                        'caption' => $caption[$key][$k],
						'duration' => $duration[$key],
                    );
                    $CI->db->where('id', $mediaResult['id']); 
                    $CI->db->update('content_media',$pictureData);
                }
            }
			}
			///////////////////eof image(MEDIA) ingetion //////////////////
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