<?php
error_reporting(E_ERROR | E_PARSE);
class Worldnow_rss_storiesHandler {
	//var $raw_feed_content = '';
	//var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $storyListFeedContents = '';
	var $label = '';
	var $catId = '';

	var $feedDef = array(
		'title'=>'',
		'type'=>'story',
		'priority'=> 0,
		'assetid'=> 0,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'pub_date'=>'',
		'author'=>''
		        
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
		log_message('debug','Getting feed contents with URI:'.$feed_uri);
		// create curl resource
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, trim($feed_uri));
		curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,0); //
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
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


////////////////////////// added by ashok 08/10/2010 //////////////////////////////
	//TO CHECK AND CONVERT CHARSET TO UTF-8 //
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
						case "link":
							$feedDef['uri'] = $element->nodeValue;
							break;
						case "guid":
							$feedDef['assetid'] = $element->nodeValue;
							break;
						case "content":
							// if Node content is avialable all the above should be  voerride.
							$feedDef['content'] = $element->nodeValue;
							break;
						/*
						case "enclosure":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
									$feedDef['icon_uri'] = $element->attributes->item($i)->value;
							}
							break;
							*/

						case "media:content":
							$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
									$feedDef['icon_uri'] = urldecode($element->attributes->item($i)->value);
							}
							break;

						case "pubDate":
							$feedDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
							break;
						case "author":
							$feedDef['author'] = $element->nodeValue;
							break;

							/////////////////////////// wn: ---- bof added on 16/1/2017 fro KATC //////////////////////////////////////////////
						case "wn:surfaceable":
							///////////////////////////////////
						$videothumb = array();
						foreach($element->childNodes as $medianode){
							 foreach($medianode->childNodes as $mediaitems){
								 foreach($mediaitems->childNodes as $itemelements){
									if($itemelements->nodeName =='media:thumbnail'){
										if(!$videothumb[0]){
											$videothumb []= $itemelements->attributes->item(1)->value;
										}
									}
								 }
							 }
						}
						
						///////////////////////////////////
							foreach($element->childNodes as $medianode){
							 foreach($medianode->childNodes as $mediaitems){
								 foreach($mediaitems->childNodes as $itemelements){
									 foreach($itemelements->childNodes as $itemelement){
									 switch($itemelement->nodeName){
										 case "media:content":
											 $len = $itemelement->attributes->length;
												for($i=0; $i < $len; ++$i){
													if($itemelement->attributes->item($i)->name == 'url'){
														if(substr(parse_url($itemelement->attributes->item($i)->value, PHP_URL_PATH),-4) ==='.mp4'){
															//$feedDef['uri'] = $itemelement->attributes->item($i)->value;
															if(!$feedDef['media']){
															$feedDef['media_type'][]= 'video';
															//$feedDef['thumbnail'][] = $videothumb[0];
															$feedDef['video_thumb'][] = $videothumb[0];
															$feedDef['media'][] = $itemelement->attributes->item($i)->value;
															
															}
															
															
														}
													}

												}
												break; //case break

											}

									 }
								 }
							 }
						}

						break;
						case "wn:bodyimages":
							
							foreach($element->childNodes as $bodyimages){
							  foreach($bodyimages->childNodes as $bodyimage){
								 switch($bodyimage->nodeName){
									  case "wn:filename":
										 // $feedDef['icon_uri'] = $bodyimage->nodeValue;
									  $feedDef['media_type'][]= 'photo';
									  $feedDef['thumbnail'][] = trim($bodyimage->nodeValue);
									   //$feedDef['media'][] = trim($bodyimage->nodeValue);
									  break;
									  case "wn:caption":
										  $feedDef['media_caption'][]= trim($bodyimage->nodeValue);
									  break;
								  }
							  }
						}
						break;
						//////////////////////////// wn: --- eof ////////////////////////////////////////////////////////
						
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
		//refresh the top level story entries first
		$stories = $this->getStoryList(); 
		if(!is_array($stories) || sizeof($stories) == 0) {
			log_message('error', 'ERROR WITH STORY RESULTS:'.$stories);
			return false;
		}

		//STORY MEDIA MANIPULATION.
		foreach($stories as &$content){
			
			$type[] = $content['media_type'];
			$media[] =  $content['media'];
			$thumbnail[]= $content['thumbnail'];
			$video_thumb [] = $content['video_thumb'];
			
			($content['media_caption'] ? $caption[]=$content['media_caption'] : $caption[] = "");
			unset ($content['media_type']);
			unset ($content['media']);
			unset ($content['thumbnail']);
			unset ($content['media_caption']);
			unset ($content['video_thumb']);

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
			
				
			$CI->db->where('content_id', $story['id']);
			$CI->db->where('category_id', $category_id);
			$mResult = $CI->db->get('content_media');
			$mediaResult = $mResult->row_array();
			if(sizeof($mediaResult) == 0){ 
			///////////////////////////////////////
					if($media[$key]){ 
						foreach ($media[$key] as $k=>  $med) { 
							$thumbCount = count($thumbnail[$key]);
							$pictureData = array(
								'content_id' => $story['id'],
								'category_id' => $category_id,
								'media_order' => $k,
								//'type' => $type[$key][$k],
								'type' => 'video',
								//'thumbnail' => $thumbnail[$key][$thumbCount-1],
								'thumbnail' => $video_thumb[$key][$k],
								'media' => $med,
								'caption' => $caption[$key][$k],
							);
							$result = $CI->db->insert('content_media',$pictureData);
							$pictureInsertId = $CI->db->insert_id();

						}
		
					}
		
		
					

				/////////////////////////////////////////
							if($thumbnail[$key]){ 
							foreach ($thumbnail[$key] as $k=>  $med) { 
								
								$pictureData = array(
									'content_id' => $story['id'],
									'category_id' => $category_id,
									'media_order' => $k,
									//'type' => $type[$key][$k],
								'type' => 'photo',
									//'thumbnail' => $thumbnail[$key][$k],
									'thumbnail' => $thumbnail[$key][$k],
									'media' => $med,
									'caption' => $caption[$key][$k],
								);
								$result = $CI->db->insert('content_media',$pictureData);
								$pictureInsertId = $CI->db->insert_id();

							}
						}
				//////////////////////////////////////////
			}else{
				/*
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
                    );
                    $CI->db->where('id', $mediaResult['id']); 
                    $CI->db->update('content_media',$pictureData);
                }
            }
			*/
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