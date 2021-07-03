<?php

class Whiz_rss_photosHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_base_uri = '';
	var $keep_order = '';
	var $photo_base_url = '';
	var $photo_end_url = '';
	var $gallery_list_uri = '';
	var $gallery_limit = '';
	

	var $galleryDef = array(
		'title'=>'',
		'type'=>'gallery',
		'priority'=> 0,
		'paid'=> 1,
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'pub_date'=>'',
	);
	var $photoDef = array(
		'title'=>'',
		'type'=>'photo',
		'description'=>'',
		'content'=>'',
		'icon_uri'=>'',
		'uri'=>'',
		'pub_date'=>'',
	);

	function __construct($category) {
		global $CI;
		
		$this->feed_base_uri = $category['source_uri'];
		$this->gallery_list_uri = $this->feed_base_uri;
		$this->keep_order = $category['orderflag'];
		$this->gallery_limit = $category['story_limit'];
		$this->catId = $category['id']; 
		
	}

	function __destruct() {
		//nothing yet
	}

	function getPhotoDef() {
		return $this->photoDef;
	}

	function getGalleryDef() {
		return $this->galleryDef;
	}

	function setCategoryVars($category) {
		$category['source_uri'] = $this->gallery_list_uri;
		return $category;
	}

	function getUri() {

	}

	function importData() {

	}

	function getFeedContents($feed_uri) {
		$feed_uri = trim($feed_uri);
		// create curl resource
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		// $output contains the output string
		$feedContents = curl_exec($ch);
		// close curl resource to free up system resources
		#$response = curl_getinfo( $ch );
		//$response = curl_getinfo( $ch );print_r($response);print curl_error($ch);
		curl_close($ch);
		return $feedContents;
	}

	/*
	 * This will take a provider_id and fetch the list of galleries
	 */
	function getGalleryList() {
//echo $this->gallery_list_uri."<br/>";

		$this->galleryListFeedContents = $this->getFeedContents($this->gallery_list_uri);
		
		if($this->galleryListFeedContents=='')  return false;


		$xml = new XMLReader();
		$xml->xml($this->galleryListFeedContents);
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
		$galleries = array();
		$count = 0;
		foreach($children as $child) {
			
			if($child->nodeName == 'item') { 
				$itemElements = $child->childNodes;
				
				$galleryDef = $this->getGalleryDef();
				foreach($itemElements as $element) {
					
					
					switch($element->nodeName) { 
						case "title":
														
							$galleryDef['title'] = $element->nodeValue;
							
						// keep order of stories comming in rss feed.
						if($this->keep_order){
						$count =$count+1;
						$galleryDef['priority'] = $count;
						}
						///////////////////
							break;
						case "description":
							
							$galleryDef['description'] = $element->nodeValue;
							break;
						case "link":
							$galleryDef['uri'] = $element->nodeValue;
							break;
						case "paid":
							$feedDef['paid'] = $element->nodeValue;
						break;
						case "enclosure":
								$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
								
									$galleryDef['icon_uri'] = $element->attributes->item($i)->value;
									
								}
							break;
						case "pubDate":
							$galleryDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
							break;
						case "author":
							$galleryDef['author'] = $element->nodeValue;
							break;
					}
					
				}

				$galleries[] = $galleryDef;
			}

		
		}
		
		array_splice($galleries, $this->gallery_limit);
		return $galleries;
	}

	/*
	 * This will fetch a given picture gallery feed for given provider and gallery id
	 */
	function getPhotoList($photo_feed_uri) {
	
		$photoFeedContents = $this->getFeedContents($photo_feed_uri);
		
		if($photoFeedContents == '') return false;

		$xml = new XMLReader();
		$xml->xml($photoFeedContents);
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
		$photos = array();
		foreach($children as $child) {
			
			if($child->nodeName == 'item') { 
				$itemElements = $child->childNodes;
				
				$photoDef = $this->getPhotoDef();
				foreach($itemElements as $element) {

				
					switch($element->nodeName) {
						case "description":
							$photoDef['description'] = strip_tags($element->nodeValue,'<br>');
							$photoDef['content'] = strip_tags($element->nodeValue,'<br>');
							
							break;
						case "title":
							
							$photoDef[strtolower($element->nodeName)] = $element->nodeValue;
							break;
						case "guid":
							
							break;

							case "enclosure":
								$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
								
									$photoDef['icon_uri'] = $element->attributes->item($i)->value;
									$photoDef['uri'] = $element->attributes->item($i)->value;
								}
							break;
							case "caption":
								$photoDef[strtolower($element->nodeName)] = $element->nodeValue;
							break;

						case "photo:thumbnail":
								$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
									//BOF PROTO TO CHECK http: prefix added BY ASHOK
								$proto = substr($element->attributes->item($i)->value , 0,5);
								
								if($proto =='http:')
								{
									$photoDef['icon_uri'] = $element->attributes->item($i)->value;
								}
								else
								{
									$photoDef['icon_uri'] = 'http:'.$element->attributes->item($i)->value;
								}
							}
							break;
						case "pubDate":
							$photoDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
							break;
						case "author":
							$potoDef['author'] = $element->nodeValue;
							break;
					}
					
				}
				$photos[] = $photoDef;
			
			}
		}
		
		
		return $photos;
	}

	/*
	 * 
	 */
	function refreshContent($category_id='') {
		global $CI;
		if($category_id == '') return false;
		//Deleting all the photos/media for currnt category from content_media to avoide rdundency 26/12/2014.
		$CI->db->where('category_id', $category_id);
		$CI->db->delete('content_media');

		$this->refreshGalleries($category_id);
	}

	function refreshGalleries($category_id) {
		global $CI;
		//refresh the top level gallery entries first
		$galleries = $this->getGalleryList();
		foreach($galleries as $gallery) {
				$CI->db->where('category_id',$category_id);
				$CI->db->where('title',$gallery['title']);
				$CI->db->where('uri',$gallery['uri']);
				$result = $CI->db->get('content');
				$galleryEntry = $result->row_array();
				if(sizeof($galleryEntry) == 0)
				{
					$gallery['category_id'] = $category_id;
					$result = $CI->db->insert('content', $gallery);
					$gallery['id'] = $CI->db->insert_id();
					log_message('debug','Gallery Doesnt Exist, Adding....:'.$gallery['id']);
				}
				else
				{
					$gallery['id'] = $galleryEntry['id'];
					log_message('debug','Gallery Exist, Attempting to Upate....:'.$gallery['id']);
					$CI->db->where('id',$gallery['id']);
					$CI->db->update('content', $gallery);
				}
				$this->refreshPhotos($gallery['uri'],$category_id,$gallery['id']);
			
		}
	}

	function refreshPhotos($photo_feed_uri, $category_id,$parent_id) {
		global $CI;
		
		//refresh the photos for the given gallery
		$photos = $this->getPhotoList($photo_feed_uri);
		
		foreach($photos as $photo) {
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$photo['title']);
			$CI->db->where('uri',$photo['uri']);
			$result = $CI->db->get('content');
			$photoEntry = $result->row_array();
			$photo['parent_id'] = $parent_id;
			if(sizeof($photoEntry) == 0) {
				$photo['category_id'] = $category_id;
				$result = $CI->db->insert('content', $photo);
				$object_id = $CI->db->insert_id();
				log_message('debug','Photo Doesnt Exist, Adding....:'.$object_id.' with parent ID:'.$photo['parent_id']);
			} else {
				$photo['id'] = $photoEntry['id'];
				log_message('debug','Photo Exist, Attempting to Upate....:'.$photo['id'].' with parent ID:'.$photo['parent_id']);
				$CI->db->where('id',$photo['id']);
				$CI->db->update('content', $photo);
			}
			/// inserting all the associated photos of gallery into content_media table
			/// to respond in JSON media object for photo gallery.
			$this->multiMedia($photo, $parent_id, $category_id);
		}
	}


	/*
* this mathod will insert all the photos of associated gallery.
* gallery id of content table should be content_id of content_media.
*/
function multiMedia($data, $content_id, $category_id){
			global $CI;
			$mediaData = array(
				'content_id' => $data['parent_id'],
				'category_id'=> $category_id,
				'type		'=> $data['type'],
				'caption	'=> $data['description'],
				'thumbnail	'=> $data['icon_uri'],
				'media		'=> $data['uri'],
				);
            	
                $result = $CI->db->insert('content_media', $mediaData);
                $object_id = $CI->db->insert_id();
        	
		}
}
