<?php

class PictopiaHandler {
	var $raw_feed_content = '';
	var $xml_parser = null;
	var $feed_base_uri = '';
	var $keep_order = '';
	var $photo_base_url = '';
	var $photo_end_url = '';
	var $gallery_list_uri = '';
	var $provider_id = '';

	var $galleryDef = array(
		'title'=>'',
		'type'=>'gallery',
		'priority'=> 0,
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
		if($CI->config->item('pictopia_feed_base_uri') =='')
			$this->feed_base_uri = 'http://gallery.pictopia.com/feeds';
		else
			$this->feed_base_uri = $CI->config->item('pictopia_feed_base_uri');

		$this->gallery_list_uri = $this->feed_base_uri.'/'.$category['data_store'].'/';
		$this->provider_id = $category['data_store'];
		$this->photo_base_url = $CI->config->item('pictopia_photo_base_url');
		$this->photo_end_url = $CI->config->item('pictopia_photo_end_url');
		$this->keep_order = $category['orderflag'];
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
//echo $this->gallery_list_uri."<br/>";

		$this->galleryListFeedContents = $this->getFeedContents($this->gallery_list_uri);
		if($this->galleryListFeedContents=='') return false;

		$xml = new XMLReader();
		$xml->xml($this->galleryListFeedContents);
		$xml->read();
		$rssRoot = $xml->expand();
		$channel = $rssRoot->firstChild;
		$lastBuildDateList = $channel->getElementsByTagName('lastBuildDate');
		if($lastBuildDateList->length>1) {
			$lastBuildDate = $lastBuildDateList->item(1);
			// We could add field in category db and check if the date has changed
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
								///////////////////
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
							$linkParts = preg_split('/\//',$element->nodeValue);
							$index = sizeof($linkParts) - 1;
							if($linkParts[$index] == '') $index = $index - 1;
							$gallery_id = $linkParts[$index];
							$galleryDef['uri'] = 'http://gallery.pictopia.com/feeds/'.$this->provider_id.'/gallery/'.$linkParts[$index];
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
		
		}
		return $galleries;
	}

	/*
	 * This will fetch a given picture gallery feed for given provider and gallery id
	 */
	function getPhotoList($photo_feed_uri) {
		$photoFeedContents = $this->getFeedContents($photo_feed_uri.'/');
		if($photoFeedContents == '') return false;

		$xml = new XMLReader();
		$xml->xml($photoFeedContents);
		$xml->read();
		$rssRoot = $xml->expand();
	
		$channel = $rssRoot->firstChild;
		$lastBuildDateList = $channel->getElementsByTagName('lastBuildDate');
		if($lastBuildDateList->length>1) {
			$lastBuildDate = $lastBuildDateList->item(1);
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
			// bof adde by ASHOK , check to confirm that photo gallery['uri'] uri has feed
			
				
			if($gallery['uri']) {
				$refreshPhoto = true;
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
				else if($gallery['pub_date']!=$galleryEntry['pub_date'])
				{
					$gallery['id'] = $galleryEntry['id'];
					log_message('debug','Gallery Exist, Attempting to Upate....:'.$gallery['id']);
					$CI->db->where('id',$gallery['id']);
					$CI->db->update('content', $gallery);
				}
				else
				{
					$gallery['id'] = $galleryEntry['id'];
					$refreshPhoto = false;
					log_message('debug','Gallery Exist and pubDate not changed, hence dont update....:'.$gallery['id'].'  pubDate '.$gallery['pub_date']);
				}
				if($refreshPhoto)
				{
					$this->refreshPhotos($gallery['uri'],$category_id,$gallery['id']);
				}
			}
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
			} else if($photo['pub_date']!=$photoEntry['pub_date']) {
				$photo['id'] = $photoEntry['id'];
				log_message('debug','Photo Exist, Attempting to Upate....:'.$photo['id'].' with parent ID:'.$photo['parent_id']);
				$CI->db->where('id',$photo['id']);
				$CI->db->update('content', $photo);
			} else {
				$photo['id'] = $photoEntry['id'];
				log_message('debug','Photo Exist, and pubDate not changed, hence dont update....:'.$photo['id'].' with parent ID:'.$photo['parent_id'].'  pubDate '.$photo['pub_date']);
			}
		}
	}
}
