<?php

class JewishgalleryHandler
{
    var $raw_feed_content = '';
    var $xml_parser = null;
    var $feed_base_uri = '';
    var $photo_base_url = '';
    var $photo_end_url = '';
    var $gallery_list_uri = '';


    var $galleryDef = array(
        'title' => '',
        'type' => 'gallery',
        'description' => '',
        'content' => '',
        'icon_uri' => '',
        'uri' => '',
        'pub_date' => '',
    );
    var $photoDef = array(
        'title' => '',
        'type' => 'photo',
        'description' => '',
        'content' => '',
        'icon_uri' => '',
        'uri' => '',
        'pub_date' => '',
    );

    function __construct($category)
    {
        global $CI;

        $this->feed_base_uri = $category['source_uri'];
        $this->gallery_list_uri = $this->feed_base_uri;

    }

    function __destruct()
    {
        //nothing yet
    }

    function getPhotoDef()
    {
        return $this->photoDef;
    }

    function getGalleryDef()
    {
        return $this->galleryDef;
    }

    function setCategoryVars($category)
    {
        $category['source_uri'] = $this->gallery_list_uri;
        return $category;
    }

    function getUri()
    {

    }

    function importData()
    {

    }

    function getFeedContents($feed_uri)
    {

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
    function getGalleryList()
    {
        //echo $this->gallery_list_uri."<br/>";

        $this->galleryListFeedContents = $this->getFeedContents($this->gallery_list_uri);

        if ($this->galleryListFeedContents == '') return false;

        $itemElements = json_decode($this->galleryListFeedContents);
        $galleries = array();
        $count = 1;
        $sticky = 0;
        $galleryDef['gallery'] = $this->getGalleryDef();
        foreach ($itemElements as $element) {
            //////////////keep order of photos comming in rss feed.
            $count = $count + 1;
            $galleryDef['gallery']['priority'] = $count;
            ///////////////////

            $galleryDef['gallery']['title'] = $element->articleMetadata->title;
            $galleryDef['gallery']['description'] = $element->articleMetadata->excerpt;
            $galleryDef['gallery']['content'] = $element->articleMetadata->excerpt;
            $galleryDef['gallery']['uri'] = $element->articleMetadata->galleryURL;
            $galleryDef['gallery']['icon_uri'] = $element->articleMetadata->imageThumbnail;
            $galleryDef['gallery']['pub_date'] = date('Y-m-d H:i:s', strtotime($element->articleMetadata->publishDate));
            $galleryDef['gallery']['author'] = $element->articleMetadata->author;

            $galleryDef['photos'] = $this->getPhotoList($element->articleMetadata->photos);

            $galleries[] = $galleryDef;
            
        }


        return $galleries;
    }

    /*
      * This will fetch a given picture gallery feed for given provider and gallery id
      */
    function getPhotoList($itemElements)
    {
        //if ($photoFeedContents == '') return false;


        $photos = array();
        $count = 1;
        $sticky = 0;
        $photoDef = $this->getPhotoDef();
        foreach ($itemElements as $element) {

            //////////////keep order of videos comming in rss feed.
            $count = $count + 1;
            $photoDef['priority'] = $count;
            ///////////////////

            $photoDef['description'] = $element->photoCaption;
            $photoDef['uri'] = $element->photoPhoto;
            $photoDef['icon_uri'] = $element->photoPhoto;
            //$photoDef['pub_date'] = date('Y-m-d H:i:s', strtotime($element->articleMetadata->publishDate));
            $photos[] = $photoDef;
        }

        //echo '<li>'. $_SERVER['HTTP_ACCEPT_CHARSET'];


        ////// Eof of featured video. ///////////
        return $photos;
    }

    /*
      *
      */
    function refreshContent($category_id = '')
    {
        if ($category_id == '') return false;
        $this->refreshGalleries($category_id);
    }

    function refreshGalleries($category_id)
    {
        global $CI;
        //refresh the top level gallery entries first
        $galleries = $this->getGalleryList();

        foreach ($galleries as $gallery) {
            $CI->db->where('category_id', $category_id);
            $CI->db->where('title', $gallery['gallery']['title']);
            $CI->db->where('uri', $gallery['gallery']['uri']);
            $result = $CI->db->get('content');
            $galleryEntry = $result->row_array();
            if (sizeof($galleryEntry) == 0) {
                $gallery['gallery']['category_id'] = $category_id;
                $result = $CI->db->insert('content', $gallery['gallery']);
                $gallery['gallery']['id'] = $CI->db->insert_id();
                log_message('debug', 'Gallery Doesnt Exist, Adding....:' . $gallery['gallery']['id']);
            }
            else
            {
                $gallery['gallery']['id'] = $galleryEntry['id'];
                log_message('debug', 'Gallery Exist, Attempting to Upate....:' . $gallery['gallery']['id']);
                $CI->db->where('id', $gallery['gallery']['id']);
                $CI->db->update('content', $gallery['gallery']);
            }
            $this->refreshPhotos($gallery['photos'], $category_id, $gallery['gallery']['id']);

        }
    }

    function refreshPhotos($photos, $category_id, $parent_id)
    {
        //echo 'from each gallery to photo insert';

        global $CI;
        //refresh the photos for the given gallery
        foreach ($photos as $photo) {
            $CI->db->where('category_id', $category_id);
            $CI->db->where('title', $photo['title']);
            $CI->db->where('uri', $photo['uri']);
            $result = $CI->db->get('content');
            $photoEntry = $result->row_array();
            $photo['parent_id'] = $parent_id;
            if (sizeof($photoEntry) == 0) {
                $photo['category_id'] = $category_id;
                $result = $CI->db->insert('content', $photo);
                $object_id = $CI->db->insert_id();
                log_message('debug', 'Photo Doesnt Exist, Adding....:' . $object_id . ' with parent ID:' . $photo['parent_id']);
            } else {
                $photo['id'] = $photoEntry['id'];
                log_message('debug', 'Photo Exist, Attempting to Upate....:' . $photo['id'] . ' with parent ID:' . $photo['parent_id']);
                $CI->db->where('id', $photo['id']);
                $CI->db->update('content', $photo);
            }
        }
    }
}
