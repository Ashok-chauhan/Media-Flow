<?php

class PressofacgalleryHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_base_uri = '';
	var $photo_base_url = '';
	var $photo_end_url = '';
	var $gallery_list_uri = '';
	

	var $galleryDef = array(
		'title'=>'',
		'type'=>'gallery',
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
		
			$this->feed_base_uri = 'http://pressofac.mycapture.com/mycapture/rss.asp?categoryID=0&affID=POAC&albums=1&eventID=&ListSubAlbums=0';
		

		$this->gallery_list_uri = $this->feed_base_uri;
		
		//$this->photo_base_url = $CI->config->item('pictopia_photo_base_url');
		//$this->photo_end_url = $CI->config->item('pictopia_photo_end_url');
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
//echo $feedContents;
		return $feedContents;
	}

	/*
	 * This will take a provider_id and fetch the list of galleries
	 */
	function getGalleryList() {
//echo $this->gallery_list_uri."<br/>";

		$this->galleryListFeedContents = $this->getFeedContents($this->gallery_list_uri);
//--$this->galleryListFeedContents = $this->getFeedContents("http://localhost/test/cafile/cagallery.xml");
		
		if($this->galleryListFeedContents=='')  return false;

		$xml = new XMLReader();
		$xml->xml($this->galleryListFeedContents);
		$xml->read();
		$rssRoot = $xml->expand();
		
		//$rootnode = $rssRoot->documentElement;
		//$children = $rssRoot->childNodes;
	
		#$channel = $rssRoot->firstChild;
		$children = $rssRoot->childNodes;
		

		
		
	
		$galleries = array();

		foreach($children as $childs) { 
			
			foreach($childs->childNodes as $child){
			//echo '<li>'.$ch->nodeValue;
			//}
			//if ($child->nodeType == XML_TEXT_NODE) {
			//echo '<li>'.$child->nodeName;
			//}
			$galleryDef = $this->getGalleryDef();
			if($child->nodeName == 'item') { 
				$itemElements = $child->childNodes;
				##$galleryDef = $this->getGalleryDef();

				foreach($itemElements as $element) {
					switch($element->nodeName) {
						case "title":
							$galleryDef['title'] = $element->nodeValue;
							break;
						case "description":
							$galleryDef['description'] = strip_tags($element->nodeValue,'<br>');
							$galleryDef['content'] = strip_tags($element->nodeValue,'<br>');
							break;
						case "link":
							#$linkParts = preg_split('/\//',$element->nodeValue);
							$linkParts = preg_split('/=/',$element->nodeValue);
							$album = explode('&',$linkParts[1]);
							$albumid = $album[0];
							$galleryDef['uri'] = 'http://pressofac.mycapture.com/mycapture/rss.asp?affID=POAC&images=1&eventID='.$albumid.'&image_width=600';
							
							break;


							case "enclosure":
								$length = $element->attributes->length;
							for ($i = 0; $i < $length; ++$i) {
								if($element->attributes->item($i)->name == 'url')
								
									$galleryDef['icon_uri'] = $element->attributes->item($i)->value;
									
								}
							break;

						case "photo:thumbnail":
							$length = $element->attributes->length;
						
							for ($i = 0; $i < $length; ++$i) { 
								if($element->attributes->item($i)->name == 'url')
								// proto are used to add http: by ASHOK	
								$proto = substr($element->attributes->item($i)->value , 0,5);

								if($proto =='http:')
								{
									$galleryDef['icon_uri'] = $element->attributes->item($i)->value;
								}
								else
								{
									$galleryDef['icon_uri'] = 'http:'.$element->attributes->item($i)->value;
								}
							}
							break;
						case "pubDate":
							$galleryDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->nodeValue));
							break;
					}
					
				}

				$galleries[] = $galleryDef;
			}

		##############
		
		}
		##############
		
		}
		
		//echo "<pre>"; print_r($galleries['uri']); echo "</pre>";
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
	
		#$channel = $rssRoot->firstChild;
		#$children = $channel->childNodes;

		$children = $rssRoot->childNodes;
	
		$photos = array();
		foreach($children as $childs) {
			foreach($childs->childNodes as $child){
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
							$linkParts = preg_split('/\//',$element->nodeValue);
							$index = sizeof($linkParts) - 1;
							if($linkParts[$index] == '') $index = $index - 1;
							$photo_id = $linkParts[$index];
//							$index = $index - 2;
//							$gallery_id = $linkParts[$index];
							$photoDef['uri'] = $this->photo_base_url.$this->provider_id.':'.$photo_id.$this->photo_end_url;
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
			#########
		}
			#########
		}
		//echo "<pre>"; print_r($photos); echo "</pre>";
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
		foreach($galleries as $gallery) {
			// bof adde by ASHOK , check to confirm that photo uri has feed
			//--$check =$this->getPhotoList($gallery['uri']);
				
			#if(count($check) > 0 ) {
				// eof checking of blank gallery(feed) 
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
			#}
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
		}
	}
}
