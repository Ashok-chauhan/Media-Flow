<?php
class Megaphone_audioHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_uri = '';
	var $keep_order = '';
	var $storyListFeedContents = '';
	var $label = '';
	var $catId = '';

	var $feedDef = array(
		'title'=>'',
		'type'=>'audio',
		'subtype'=>'',
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
		'media_type'=>array(),
		'thumbnail'=>array(),
		'media'=>array(),
		'media_caption'=>array(),
		
		'comments_url'=>'',
		'duration' => array()
        
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


	
	/*
	 * This will take a feed uri and fetch the list of stories
	 */
	function getAudioList() { 
		
		//$this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
		//if($this->storyListFeedContents=='') return false;

		$podcasts = new simpleXMLElement($this->feed_uri, null, true);

		////////////////////////////////////////////// USING METHOD CONVERT CHAR TO CONFIRM UTF-8
		//$xml->xml($this->convert_charSet($this->storyListFeedContents));
		//////////////////////////////////////////////
		$stories = array();
		$count = 0;
		
		$ns = $podcasts->getNamespaces(true);
		//$ns_itunes = $podcasts->getNamespaces(true);
		$ns_itunes = $podcasts->getNamespaces('"http://www.itunes.com/dtds/podcast-1.0.dtd');
		foreach ($podcasts->channel->item as $key => $item) {
			//$ns = $item->getNamespaces(true);
			
			
			$feedDef = $this->getFeedDef();

			$feedDef['title']		= (string)$item->title;
			$feedDef['content']		= (string)$item->children($ns['content']);
			$feedDef['assetid'] 	= (string)$item->guid;
			$feedDef['description']	= (string)$item->description;
			$feedDef['pub_date']	= (string) date('Y-m-d H:i:s',strtotime($item->pubDate));
			$feedDef['uri']			= (string)$item->enclosure['url'];
			//$feedDef['icon_uri']	= (string)$img->image->href;
			//$feedDef['author']		= $item->children($ns_itunes['author']);
			$feedDef['caption']		= '';
			$feedDef['comments_url'] = '';
			// keep order of stories comming in rss feed.
			
				$count =$count+1;
				$feedDef['priority'] = $count;
	
		

			$feedDef['media_type'][]	= 'audio';
			$feedDef['icon_uri'] 		= '';
            $feedDef['media'][] 		= (string)trim($item->enclosure['url']);
			$feedDef['media_caption'][]	= '';
			$feedDef['duration'][]		= ''; 
			$feedDef['thumbnail'][]		= '';
			

			$stories[] = $feedDef;

		}
//$stories[] = $feedDef;

//print_r($stories);

		
		return $stories;
	}

	/*
	 * 
	 */
	function refreshContent($category_id='') { 
		if($category_id == '') return false;

		global $CI;
		//refresh the top level gallery entries first
		
		$stories = $this->getAudioList(); 

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
			unset ($content['duration']);
			unset ($content['media_type']);
			unset ($content['media']);
			unset ($content['thumbnail']);
			unset ($content['media_caption']);
		}
		// setting priority 0 for Publication.
		// before inserting new stories with priority .
		$this->feed_order_zero($category_id);
		//storyvalidator($category_id, $stories); //story validator helper. commented on 30/3/2018
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
				log_message('debug', 'Story Doesnt Exist, Adding....:'.$story['id'].'--'.$this->label . '-- catid '.$this->catId);
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
						'duration' => $duration[$key][$k],
                        'caption' => $caption[$key][$k],
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
						'duration' => $duration[$key][$k],
                        'caption' => $caption[$key][$k],
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