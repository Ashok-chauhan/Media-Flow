<?php

error_reporting(E_ALL);

class DbHandler {


    function __construct($category) {
        global $CI;
        $this->feed_uri = $category['source_uri'];
        $this->keep_order = $category['orderflag'];
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

    
 function refreshContent($category_id = '') {


    global $CI;
        //$catq("Select publication_id from category where id={$category_id}");
        $CI->db->where('publication_id', 180);
        $CI->db->where('status', 'active');
        $result = $CI->db->get('category');
        $storyEntry = $result->result_array();

        foreach ($storyEntry as $key => $value) {
            foreach ($variable as $key => &$value) {
                 if($key=='publication_id'){
                

                $value[$key]= '5104';
             }else{ 
             $value[$key]= $value[$key]; 
         }
                # code...
            }
            

        }

print '<pre>';
print_r($value);
 //            $CI->db->where('')
 // $result = $CI->db->insert('content', $story);
        ##$sql=("UPDATE content SET priority=0 where category_id={$storyEntry['id']}  ORDER BY pub_date DESC LIMIT 100 ");
        // $sql = ("UPDATE content SET priority=0 where category_id={$storyEntry['id']} AND priority > 0");
        // $CI->db->query($sql);
    }
}
