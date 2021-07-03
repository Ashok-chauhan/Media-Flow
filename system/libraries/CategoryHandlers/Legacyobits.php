<?php

class LegacyObitsHandler
{
    var $raw_feed_content = '';
    var $xml_parser = null;
    var $feed_uri = '';
    var $storyListFeedContents = '';
    var $keep_order = '';

    var $feedDef = array(
        'title' => '',
        'type' => 'story',
        'paid' => 1,
        'priority' => 0,
        'assetid' => 0,
        'description' => '',
        'content' => '',
        'icon_uri' => '',
        'caption' => '',
        'uri' => '',
        'pub_date' => '',
        'author' => '',
    );

    function __construct($category)
    {
        $this->feed_uri = $category['source_uri'];
        $this->keep_order = $category['orderflag'];
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
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // $output contains the output string
        $feedContents = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // close curl resource to free up system resources
        curl_close($ch);

        return $feedContents;
    }


    //TO CHECK AND CONVERT CHARSET TO UTF-8 //
    function convert_charSet($string)
    {
        if (true === mb_check_encoding($string, "UTF-8")) {
            //echo '<li>valid (' . $encoding . ') encoded byte stream!!!!<br />';
            return $string;
        }
        else {
            $str = mb_convert_encoding($string, "UTF-8");
            return $str;
        }

    }

    ///////////////////////////////////////////////////////////////////////////////

    /*
      * This will take a feed uri and fetch the list of stories
      */
    function getStoryList()
    {
        $this->storyListFeedContents = $this->getFeedContents($this->feed_uri);
        if ($this->storyListFeedContents == '') return false;

        $xml = new XMLReader();

        ////////////////////////////////////////////// USING METHOD CONVERT CHAR TO CONFIRM UTF-8
        #####$xml->xml($this->convert_charSet($this->storyListFeedContents));
        //////////////////////////////////////////////

        $xml->xml($this->storyListFeedContents);
        $xml->read();

        $rssRoot = $xml->expand();
        $rssChildren = $rssRoot->childNodes;
        $channel = null;
        foreach ($rssChildren as $child) {

            if (!is_null($channel)) continue;
            if ($child->nodeName == 'sql:query') {
                $channel = $child;
            }
        }

        $children = $channel->childNodes;
        $stories = array();
        $count = 0;
        foreach ($children as $child) {

            if ($child->nodeName == 'Notice') {
                $itemElements = $child->childNodes;

                $feedDef = $this->getFeedDef();
                foreach ($itemElements as $element) {

                    switch ($element->nodeName) {
                        case "FirstName":
                            $feedDef['title'] .= $element->nodeValue;
                            if($this->keep_order){
                                $count =$count+1;
                                $feedDef['priority'] = $count;
                            }
                            break;
                        case "MiddleName":
                            $feedDef['title'] .= ' ' . $element->nodeValue;
                            break;
                        case "LastName":
                            $feedDef['title'] .= ' ' . $element->nodeValue;
                            break;
                        case "City":
                            if(trim($element->nodeValue)!=''){
                                $feedDef['title'] .= ', ' . $element->nodeValue;
                            }
                            break;
                        case "NoticeText":
                            $feedDef['content'] = strip_tags($element->nodeValue,'<p>,<div>,<span>,<b>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<strong>,<i>,<em>,<a>,<ul>,<li>,<ol>,<br>,<u>,<table>,<tr>,<td>'); 
                            $feedDef['description'] = $this->snippet(strip_tags($element->nodeValue));
                            break;
                        case "DisplayURL":
                            $feedDef['uri'] = $element->nodeValue;
                            $feedDef['assetid'] = $element->nodeValue;
                            break;
                        case "ImageUrl":
                            $feedDef['icon_uri'] = $element->nodeValue;
                            break;
                        case "paid":
                            $feedDef['paid'] = $element->nodeValue;
                        break;    
                        case "PersonId":
                            //$feedDef['assetid'] = $element->nodeValue;
                            break;
                        case "DateCreated":
                            $feedDef['pub_date'] = $element->nodeValue;
                            break;
                        case "PublishedBy":
                            $feedDef['author'] = $element->nodeValue;
                            break;


                    }


                }

                $stories[] = $feedDef;

            }
        }
//print '<pre>';
//print_r($stories);
//print '</pre>';
        return $stories;
    }


    function refreshContent($category_id = '')
    {
        if ($category_id == '') return false;

        global $CI;
        //refresh the top level gallery entries first

        $stories = $this->getStoryList();

        if (!is_array($stories) || sizeof($stories) == 0) {
            log_message('error', 'ERROR WITH STORY RESULTS:' . $stories);
            return false;
        }

        foreach ($stories as $key => $story) {

            $CI->db->where('category_id', $category_id);
            $CI->db->where('title', $story['title']);
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


    /*
     * feed_order will keep the order of incomming rss feed .
     * count will stored in priority field currently this is
     * applicabel to Commercial Appeal publication.
     * param count
     */

    function feed_order($count)
    {
        global $CI;
        $path = explode('/', $_SERVER['PATH_INFO']);
        $number = 0;
        if ($path[2] == 'refreshpub') {
            if ($path[3] == 5) {
                $number = $count;
            }
        } else {
            $catid = $path[3];
            $CI->db->where('id', $catid);
            $result = $CI->db->get('category');
            $rset = $result->row_array();
            if ($rset['publication_id'] == 5) {
                $number = $count;
            }
        }
        return $number;
    }

    /*
    * feed_order_zero will update priority field value to zero(0).
    * latest 30 stories will updated to 0.
    * this is currently applicable to Commercial Appeal publication.
    * param :  category id
    */

    function feed_order_zero($cid)

    {
        global $CI;
        //$catq("Select publication_id from category where id={$category_id}");
        $CI->db->where('id', $cid);
        $result = $CI->db->get('category');
        $storyEntry = $result->row_array();

        ##$sql=("UPDATE content SET priority=0 where category_id={$storyEntry['id']}  ORDER BY pub_date DESC LIMIT 100 ");

        $sql = ("UPDATE content SET priority=0 where category_id={$storyEntry['id']} AND priority > 0");

        $CI->db->query($sql);
        //return $story;
    }


    function snippet($text, $length = 100, $tail = "...")
    {
        $text = trim($text);
        $txtl = strlen($text);
        if ($txtl > $length) {
            for ($i = 1; $text[$length - $i] != " "; $i++) {
                if ($i == $length) {
                    return substr($text, 0, $length) . $tail;
                }
            }
            $text = substr($text, 0, $length - $i + 1) . $tail;
        }
        return $text;
    }


}