<?php

class MycaptureHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_base_uri = '';
	var $keep_order = '';
	var $photo_base_url = '';
	var $photo_end_url = '';
	var $gallery_list_uri = '';
	var $provider = '';
	var $delflag = '';
	var $galleryLimit = '';
	

	var $galleryDef = array(
		'title'=>'',
		'type'=>'gallery',
		'description'=>'',
		'paid'=> 1,
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
		$this->provider = $category['data_store'];
		$this->keep_order = $category['orderflag'];
		$this->delflag = $category['delflag'];
		$this->galleryLimit = $category['story_limit'];
		$this->photo_base_url = parse_url($this->feed_base_uri);
		$this->gallery_list_uri = $this->feed_base_uri;
			
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
	 * This will take a provider_id and fetch the list of galleries
	 */
	function getGalleryList() {

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
							if($this->keep_order){
								$count =$count+1;
								$galleryDef['priority'] = $count;
								}
							break;
						case "description":
							
							$galleryDef['description'] = $element->nodeValue;
							break;
						case "link":
							
							$linkParts = preg_split('/=/',$element->nodeValue);
							$album = explode('&',$linkParts[1]);
							$albumid = $album[0];
							$galleryDef['uri'] = $this->photo_base_url['scheme'].'://'.$this->photo_base_url['host'].$this->photo_base_url['path'].'?affID='.$this->provider.'&images=1&eventID='.$albumid.'&image_width=600';
							//print '<li>'.$galleryDef['uri'];
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
					}
					
				}

				$galleries[] = $galleryDef;
			}

		
		}
		
		array_splice($galleries, $this->galleryLimit);
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
					}
					
				}
				$photos[] = $photoDef;
			
			}
		}
		//print '<pre>';print_r($photos);print '</pre>';
		return $photos;
	}

	/*
	 * 
	 */
	function refreshContent($category_id='') {
		if($category_id == '') return false;
		$this->refreshGalleries($category_id);
	}

	function refreshGalleries($category_id) {
		global $CI;
		//refresh the top level gallery entries first
		$galleries = $this->getGalleryList();
		//if delete falg true no need to check existing record to avoiding db overhead.
		if($this->delflag){ 
			foreach($galleries as $gallery){
				$gallery['category_id'] = $category_id;
					$result = $CI->db->insert('content', $gallery);
					$gallery['id'] = $CI->db->insert_id();
					log_message('debug','Gallery Doesnt Exist, Adding....:'.$gallery['id']);
					$this->refreshPhotos($gallery['uri'],$category_id,$gallery['id']);
			}
		}else{

		foreach($galleries as $gallery) {
			
				$CI->db->where('category_id',$category_id);
				$CI->db->where('title',$gallery['title']);
				//$CI->db->where('uri',$gallery['uri']);
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
	}

	function refreshPhotos($photo_feed_uri, $category_id,$parent_id) {
		global $CI;
		//refresh the photos for the given gallery
		$photos = $this->getPhotoList($photo_feed_uri);
		//if delflage is true just insert record without checking/updating existing one.
		if($this->delflag){
			foreach($photos as $photo){
				$photo['parent_id'] = $parent_id;
				$photo['category_id'] = $category_id;
				$result = $CI->db->insert('content', $photo);
				$object_id = $CI->db->insert_id();
				log_message('debug','Photo Doesnt Exist, Adding....:'.$object_id.' with parent ID:'.$photo['parent_id']);
				$this->multiMedia($photo, $parent_id, $category_id);
			}

		}else{

		foreach($photos as $photo) {
			$CI->db->where('category_id',$category_id);
			$CI->db->where('title',$photo['title']);
			//$CI->db->where('uri',$photo['uri']);
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
