<?php
error_reporting(E_ALL);
class Whiz_rss_syndicasterHandler
{
    var $raw_feed_content = '';
    var $xml_parser = null;
    var $feed_uri = '';
    var $keep_order = '';
    var $storyListFeedContents = '';
	var $share_uri = '';
	var $sharing = false;
	var $sharingTV = false;
	var $catId = '';

	private $articles;
	
    var $feedDef = array(
        'title'=>'',
        'type'=>'story',
        'paid'=> 1,
        'priority'=> 0,
        'assetid'=> 0,
        'description'=>'',
        'content'=>'',
        'icon_uri'=>'',
        'caption'=>'',
		'cc_type'=>'',
        'uri'=>'',
        'pub_date'=>'',
        'author'=>'',
        'comments_url'=>'',
		'share_url'=>'',
		'video_group'=>'vod'
    );

    function __construct($category)
    {
		global $CI;
        $this->feed_uri = $category['source_uri'];
        $this->keep_order = $category['orderflag'];
		$this->catId = $category['id'];
	}

    function __destruct()
    {
        //nothing yet
    }

    function getFeedDef()
    {
        return $this->feedDef;
    }

    function setCategoryVars($category)
    {
        $category['source_uri'] = $this->feed_uri;
        return $category;
    }

    function getUri()
    {

    }

    function getFeedContents($feed_uri)
    {
		
        log_message('debug', 'Getting feed contents with URI:' . $feed_uri);
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $feed_uri);
		curl_setopt($ch, CURLOPT_USERAGENT, "Whiz Technologies/102010 (http://whizti.com)");
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0); //
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // $output contains the output string
        $feedContents = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        // close curl resource to free up system resources
        curl_close($ch);
//print $feedContents;
        return $feedContents;
    }


    function getStoryList()
    { 
        $this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
        if ($this->storyListFeedContents == '') return false;

       $dom = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        if (!($dom->loadXML($this->storyListFeedContents))) {
            throw new Exception("Feed has error!.");
        }

        $items = $dom->getElementsByTagName('item');
		$index = 0;
		
		foreach ($items as $item)
			{
			//echo '<li>****** >> <strong> '.$item->nodeName."</strong> --->> <li>".$item->nodeValue."<br>";
            $title = $uri = $image =null;
	if ($xmlVideo = $item->getElementsByTagNameNS('http://search.yahoo.com/mrss/', 'content')) {
			if($xmlVideo){
						$video = $xmlVideo->item(0);
						$uri = $video->getAttribute('url');
						$type = $video->getAttribute('medium');
						$video_type = $video->getAttribute('type');
						
						// geting CC url
						$cc = $xmlVideo->item(1);
                        if ($cc != null) {
                            $ccUrl = $cc->getAttribute('url');
                            $ccType = $cc->getAttribute('type');
                        }
					}
				}

	if ($xmlThumbnail = $item->getElementsByTagNameNS('http://search.yahoo.com/mrss/', 'thumbnail')) {
				if($xmlThumbnail){			
				
						$image = $xmlThumbnail->item(0);
                        if ($image != null) {
                            $icon_uri = $image->getAttribute('url');
                        } else {
                            $icon_uri = '';
                        }
						//print '<pre>';
						//print_r($image->getAttribute('url'));
						//exit;

					}
				}

            
			if ($xmlTitle = $item->getElementsByTagName("title")) {
				
				if($xmlTitle->length > 0)
                $title = $xmlTitle->item(0)->nodeValue;

            }
			if ($xmlGuid = $item->getElementsByTagName("guid")) {
				if($xmlGuid->length > 0)
                $guid = $xmlGuid->item(0)->nodeValue;

            }
			if ($xmlPubdate = $item->getElementsByTagName("pubDate")) {
				if($xmlPubdate->length > 0)
                $pub_date = date('Y-m-d H:i:s', strtotime($xmlPubdate->item(0)->nodeValue));
				
            }

			if($xmlDescription = $item->getElementsByTagName("description")){
				if($xmlDescription->length > 0)
					$description = $xmlDescription->item(0)->nodeValue;
				
			}

		
            //$media = $this->parseMedia($item);

            if ($title != null and $uri != null) {
				$feedDef = $this->getFeedDef();
                				$feedDef['priority'] = $index;
								$feedDef['title'] = $title; 
								$feedDef['type'] = $type;
								$feedDef['video_type'] = $video_type;
								$feedDef['uri'] = $uri;
								$feedDef['icon_uri'] = $icon_uri;
								$feedDef['assetid']	= $guid;
								$feedDef['description'] = $description;
								$feedDef['pub_date']	= $pub_date;
								$feedDef['caption'] = $ccUrl;
								$feedDef['cc_type'] = $ccType;
									
								//);

               // $this->articles[$index++] = $data;
			    $this->articles[$index++] = $feedDef;
            }
        }
       // return $stories;
	   return $this->articles;
    }

	

    function refreshContent($category_id = '')
    {

        if ($category_id == '') return false;

        global $CI;
        $stories = $this->getStoryList();
        if (!is_array($stories) || sizeof($stories) == 0) {
            log_message('error', 'ERROR WITH STORY RESULTS:' . $stories);
            return false;
        }

     		

        foreach ($stories as $key => $story) {
			 
            $CI->db->where('category_id', $category_id);
            $CI->db->where('title', $story['title']);
            $CI->db->where('uri', $story['uri']);
			$CI->db->where('pub_date', $story['pub_date']); 
            $result = $CI->db->get('content');
            $storyEntry = $result->row_array();

            if (sizeof($storyEntry) == 0) {


                $story['category_id'] = $category_id;
				
                $result = $CI->db->insert('content', $story);
                $story['id'] = $CI->db->insert_id();
				
                log_message('debug', 'Story Doesnt Exist, Adding....:' . $story['id']);
            } else {
			
                $story['id'] = $storyEntry['id'];
                log_message('debug', 'Story Exist, Attempting to Upate....:' . $story['id']);
                $CI->db->where('id', $story['id']);
                $CI->db->update('content', $story);
            }
           
        }
    }


  

	


}