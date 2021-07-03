<?php

class TowngalleryHandler {
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

	
/*	function __construct($category) {
		global $CI;
		if($CI->config->item('towngallery_feed_base_uri') =='')
			$this->feed_base_uri = 'http://www.zen-dev.bloxcms.com/search/?q=&t=article&l=10&d=&d1=1+year+ago&d2=&s=&sd=desc&f=atom&altf=iphone&t=collection&gallery=true';

		else
			$this->feed_base_uri = $CI->config->item('towngallery_feed_base_uri');

		
		$this->gallery_list_uri = $this->feed_base_uri;
		##$this->gallery_list_uri = $this->feed_base_uri.'/'.$category['data_store'].'/';
		#$this->provider_id = $category['data_store'];
		$this->photo_base_url = $CI->config->item('town_photo_base_url');
		
		#$this->photo_end_url = $CI->config->item('pictopia_photo_end_url');
	}

*/
function __construct($category) {
		
		$this->feed_base_uri = $category['source_uri'];
		$this->gallery_list_uri = $this->feed_base_uri;
		$this->photo_base_url = $this->feed_base_uri.'&uuid=';
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

		$xml = simplexml_load_string($this->galleryListFeedContents);
		
		$galleries = array();
		$count = 0;
		 foreach ($xml->entry as $element) {
			
				$galleryDef = $this->getGalleryDef();

				
							$galleryDef['title'] =(string)$element->title;

							
							// keep order of stories comming in rss feed.
							if($this->keep_order){
							$count =$count+1;
							$galleryDef['priority'] = $count;
							}
							///////////////////
							
							$galleryDef['description'] = (string)$element->content->div;
							$galleryDef['content'] =(string)$element->content->div;
							$galleryDef['author'] = (string) $element->author->name;

							/*foreach($element->link[0]->attributes() as $k => $url) {
								if($k =='href'){
									$galleryDef['uri'] = (string)$url;
								}
							}
							*/

							// geting uuid of individual gallery

							$linkParts = preg_split('/:/',(string)$element->id);
							$galleryDef['uri'] =$this->photo_base_url.$linkParts[2];


							foreach($element->link[1]->attributes() as $key => $icon) {
									if($key =='href'){
									$galleryDef['icon_uri'] = (string)$icon;
								}
							}
	
/*foreach($element->id[0]->attributes() as $a => $b) {
    echo '<li>'.$a,'="',$b,"<br/>";
}*/

							$galleryDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->updated));
				
				$galleries[] = $galleryDef;
			
		}
		//##echo "<pre>";print_r($galleries); echo "</pre>";echo'<li>teting';
		return $galleries;
	}

	/*
	 * This will fetch a given picture gallery feed for given provider and gallery id
	 */
	function getPhotoList($photo_feed_uri) {
		//echo '<li>'.$photo_feed_uri;
		$photoFeedContents = $this->getFeedContents($photo_feed_uri);
		if($photoFeedContents == '') return false;

		$xml = simplexml_load_string($photoFeedContents);
	
		$photos = array();
		
				foreach($xml->entry as $element) {
					$photoDef = $this->getPhotoDef();

					$photoDef['title'] =(string)$element->title;
							
							$photoDef['description'] = (string)$element->content->div;
							$photoDef['content'] =(string)$element->content->div;
							$photoDef['author'] = (string) $element->author->name;


						

							/*foreach($element->link[0]->attributes() as $k => $url) {
								if($k =='href'){
									$galleryDef['uri'] = (string)$url;
								}
							}
							*/

							// geting uuid of individual gallery
//this is feed uri 
							//$linkParts = preg_split('/:/',(string)$element->id);
							//$photoDef['uri'] =$this->photo_base_url.$linkParts[2];




							foreach($element->link[1]->attributes() as $key => $icon) {
									if($key =='href'){
									$photoDef['icon_uri'] = (string)$icon;
									//using same image for detail image
									$photoDef['uri'] = (string)$icon;
								}
							}
							
							$photoDef['pub_date'] = date('Y-m-d H:i:s',strtotime($element->updated));
				$photos[] = $photoDef;
				
				}
		
		return $photos;
	}

	/*
	 * 
	 */
	function refreshContent($category_id='') {
		if($category_id == '') return false;
		$this->refreshGalleries($category_id);
		$this->getGalleryList();
	}

	function refreshGalleries($category_id) {
		global $CI;
		//refresh the top level gallery entries first
		$galleries = $this->getGalleryList();
		foreach($galleries as $gallery) {
			// bof adde by ASHOK , check to confirm that photo uri has feed
			$check =$this->getPhotoList($gallery['uri']);
				
			if(count($check) > 0 ) {
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

			$CI->db->where('parent_id',$parent_id);

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
