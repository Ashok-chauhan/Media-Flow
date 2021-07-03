<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Ott extends CI_Controller {
	var $objectName = 'category';

	public function __construct() {
        parent::__construct();
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		//$this->load->scaffolding('dev');
		$this->load->helper('url');
		$this->load->helper('form');
		
		
		$this->load->helper('menu');
		$user_type = $this->session->userdata('user');
		// $this->template->assign('menu', getmenu($user_type['id']));
		// $this->template->assign('pub_id',$user_type['pub_id']);
        // $this->template->assign('type',$user_type['type']);
        $this->menu   = getmenu($user_type['id']);
        $this->pub_id = $user_type['pub_id'];
        $this->type   = $user_type['type'];
	}


	function catStatus(){
	if(isset($_POST['catid'])){
			if( $_POST['status']=='active') {
				$this->db->where('id', $_POST['catid']);
				$this->db->update('category', array('status'=>'inactive'));
			echo 'Section is disabled.';
			redirect('ott/category/'.$this->input->post('pub_id'));

			}elseif($_POST['status'] == 'inactive'){
			
				$this->db->where('id', $_POST['catid']);
				$this->db->update('category', array('status'=>'active'));
			echo  'Section is Enabled.';
			redirect('ott/category/'.$this->input->post('pub_id'));
			}
		}
	}

	function catlabel(){
		if(isset($_POST['catid'])){
			$this->db->where('id', $_POST['catid']);
			$this->db->update('category', array('label'=> $_POST['label']));
			redirect('ott/category/'.$this->input->post('pub_id'));
		}
	}



	function category(){
		
		if(isset($_POST['item'])){
			$i = 0;
			foreach($_POST['item'] as $value){
			// Execute statement:
			// UPDATE [Table] SET [Position] = $i WHERE [EntityId] = $value
			$data = array(
				'priority'	=> $i,
				);
			$this->db->where('id', $value);
			$this->db->update('content', $data);
			$i++;
			}
			

		}
		
		$user_type = $this->session->userdata('user');
			$this->db->where('name','Watch now');
			if($this->uri->segment(3)){
				$this->db->where('publication_id', $this->uri->segment(3));
				$pubid = $this->uri->segment(3);
			}else{
				//$this->db->where('publication_id', $user_type['pub_id']);
				$this->db->where('publication_id', $this->input->post('pub_id'));
				$pubid = $this->input->post('pub_id');
			}
			
			$query = $this->db->get('category');
			$row = $query->row_array();
			if(isset($row['id'])){
				$catid = $row['id'];
				$catstatus = $row['status'];
				$label		= $row['label'];
				$pubid		= $row['publication_id'];

			}
			
			
		if(isset($_POST['video_title'])){
			if($query->num_rows() == 1){
				
				$updateData = array(
					'date_modified'	=> date('Y-m-d H:i:s'),
					'modified_by'	=> $user_type['id'],
					);
				$this->db->where('id', $catid);
				$this->db->update('category', $updateData);

			}else{
			
			$date = date('Y-m-d H:i:s');
			$data = array(
				'date_created' => $date,
				'parent_id'		=> 0,
				'date_modified' => $date,
				'created_by'	=> $user_type['id'],
				'name'			=> 'Watch now',
				'label'			=> 'Watch now',
				'type'			=> 'whiz_rss_stories',
				'atype'			=>	'list',
				'cat_order'		=> 0,
				'publication_id'=> $pubid,
				'status'		=> 'active',
				'device'		=> 'both',
				'orderflag'		=> 1,
			);
			$this->db->insert('category', $data);
			$catid = $this->db->insert_id();
			}

	if(isset($_POST['video_title'])){
		if($_POST['format'] =='mp4'){
				$video_type = "video/mp4";
			}else{
				$video_type = "application/octet-stream";
			}
	$contArray = array(
		'category_id'	=> $catid,
		'assetid'		=> microtime(),
		'pub_date'		=> date('Y-m-d H:i:s'),
		'author'		=> $user_type['first_name'].$user_type['last_name'],
		'type'			=> 'video',
		'title'			=> $_POST['video_title'],
		'icon_uri'		=> $_POST['video_thumb'],
		'uri'			=> $_POST['video_url'],
		'video_type'	=> $video_type,
		'video_group'	=> $this->input->post('video_group'),
		'liveframe'		=> $this->input->post('liveframe'),
		'priority'		=> $_POST['priority'],
		'daiAssetKey'	=> $this->input->post('daiAssetKey'),
		'daiApiKey'		=> $this->input->post('daiApiKey'),
		'authUrl'		=> $this->input->post('authUrl'),
		'sticky'		=> (isset($_POST['sticky'])) ? $_POST['sticky'] : $_POST['sticky'] = 0,
		);
		$this->db->insert('content', $contArray);
		}
	
	}
		if(isset($catid)){
		$this->db->where('category_id',$catid);
		$Q = $this->db->get('content');
		$videos = $Q->result_array();
		$priority = array();
		foreach($videos as $key => $row){
			$priority[$key] = $row['priority'];
		}
		array_multisort($priority, SORT_ASC, $videos);
		}
	
		
		// $this->template->setView('ott/category');
		// $this->template->assign('type',$user_type['type']);
		// $this->template->assign('menu', getmenu($user_type['id']));
		
		// if(isset($pubid)) $this->template->assign('pub_id', $pubid);
		// $this->template->assign('page_name','category');
		// if(isset($videos)) $this->template->assign('videos', $videos);
		// if(isset($priority)) $this->template->assign('priority', count($priority));
		// if(isset($catstatus)) $this->template->assign('status', $catstatus);
		// if(isset($catid)) $this->template->assign('catid', $catid);
		// if(isset($label)) $this->template->assign('label', $label);
        // $this->template->render('main_body');
        
        $data['main']       = 'ott/category';
        $data['type']       = $this->type;
        $data['menu']       = $this->menu;
        if(isset($pubid)) $data['pub_id']   = $pubid;
        $data['page_name']  = 'category';
        // if(isset($videos)) $data['videos']  = $videos;
        // if(isset($priority)) $data['priority']  = count($priority);
        // if(isset($catstatus)) $data['status']   = $catstatus;
        // if(isset($catid))     $data['catid']    = $catid;
		// if(isset($label))     $data['label']    = $label;
		
		$data['videos']  = $videos;
        $data['priority']  = count($priority);
        $data['status']   = $catstatus;
        $data['catid']    = $catid;
		$data['label']    = $label;
		
		
        $this->load->view('template', $data);
		
		
}

public function edit(){
		$user_type = $this->session->userdata('user');
		$id = $this->uri->segment(3);
		$pub_id = $this->uri->segment(4);
		$this->db->where('id', $id);
		$Q = $this->db->get('content');
		$video = $Q->row_array();
		if(isset($_POST['video_title'])){
			if($_POST['format'] =='mp4'){
				$video_type = "video/mp4";
			}else{
				$video_type = "application/octet-stream";
			}
			$contEdit = array(
				'pub_date'		=> date('Y-m-d H:i:s'),
				'title'			=> $_POST['video_title'],
				'icon_uri'		=> $_POST['video_thumb'],
				'uri'			=> $_POST['video_url'],
				'video_type'	=> $video_type,
				'video_group'	=> $this->input->post('video_group'),
				'liveframe'		=> $this->input->post('liveframe'),
				'sticky'		=> $this->input->post('sticky'),
				'daiAssetKey'	=> $this->input->post('daiAssetKey'),
				'daiApiKey'		=> $this->input->post('daiApiKey'),
				'authUrl'		=> $this->input->post('authUrl'),
				);
			$this->db->where('id',$_POST['id']);
			$this->db->update('content', $contEdit);
			redirect('ott/category/'.$this->input->post('pub_id'));
		}
		$vtype = explode('/', $video['video_type']);
		
		$user_type = $this->session->userdata('user');
		
        //$this->template->assign('formatOption', array('mp4' =>'mp4', 'hls' => 'hls'));
        $data['formatOption'] = array('mp4' => 'mp4', 'hls' => 'hls');

		if($video['video_type'] =='video/mp4'){
            //$this->template->assign('formatSelect', 'mp4');
            $data['formatSelect'] = 'mp4';
		}else{
            $data['formatSelect'] = 'hls';
			//$this->template->assign('formatSelect', 'hls');
		}

		//$this->template->assign('typeOption', array('sponsored' =>'Sponsored', 'vod' => 'VOD', 'live' => 'Live Stream'));
        $data['typeOption'] = array('sponsored' =>'Sponsored', 'vod' => 'VOD', 'live' => 'Live Stream');
        //$this->template->assign('typeSelect', $video['video_group']);
        $data['typeSelect'] = $video['video_group'];

        //$this->template->assign('liveframeOption', array('0' =>'Off [0]', '1' => 'On [1]', '2' => 'Others [2]'));
        $data['liveframeOption'] = array('0' =>'Off [0]', '1' => 'On [1]', '2' => 'Others [2]');
        //$this->template->assign('liveframeSelect', $video['liveframe']);
        $data['lveframeSelect'] = $video['liveframe'];

        //$this->template->assign('menu', getmenu($user_type['id']));
        $data['menu'] = getmenu($user_type['id']);
		if($user_type['pub_id']){
            //$this->template->assign('pub_id',$user_type['pub_id']);
            $data['pub_id'] = $user_type['pub_id'];
		}else{
            //$this->template->assign('pub_id',$pub_id);
            $data['pub_id'] = $pub_id;
		}
		// $this->template->assign('type',$user_type['type']);
		// $this->template->assign('video',$video);
		// $this->template->setView('ott/edit');
		// $this->template->assign('page_name','edit');
        // $this->template->render('main_body');

        $data['type']   = $user_type['type'];
        $data['video']  = $video;
        $data['page_name'] = 'edit';
        $data['main'] = 'ott/edit';
        $this->load->view('template', $data);

	
}

public function delete(){
		$this->db->where('id', $this->uri->segment(3));
		$this->db->delete('content');
		redirect('ott/category/'.$this->uri->segment(4));
		
}



/* fucntion to generate xml for ott wathch now
* category and put it in folder after publication id
* and same file link should be stored in category table
* to ingest xml content into content table.
*/
  function ottxml(){
		//header('Content-type: text/xml; charset=UTF-8');
		$writer = new XMLWriter;
		$uri = 'test.xml';
		touch($uri);
        $uri = realpath($uri);
		//print '<li>'.$uri;
		$writer->openURI($uri);
		//$writer->openURI('test.xml');
		#$writer->openMemory();
		$writer->startDocument('1.0', 'UTF-8');
		$writer->startElement('rss');
		$writer->writeAttribute('xmlns:media','http://search.yahoo.com/mrss/');
		$writer->writeAttribute('version','2.0');
		$writer->startElement('channel');
	
				$writer->writeElement('title','local news');
				$writer->writeElement('link','http://krdo.com');
				$writer->writeElement('description','');
				$writer->writeElement('generation','Whiz technologies');
				$writer->writeElement('lastBuildDate','');
				$writer->writeElement('category','');
				$writer->writeElement('webMaster','support@whizti.com (Whiz team)');
				for ($i = 1; $i <= 10; $i++){
					$writer->startElement('item');
						$writer->writeElement('link','http://krdo.com');
						$writer->writeElement('title','local news');
							$writer->startElement('guid');
								$writer->writeAttribute('isPermaLink','false');
								$writer->text('7508b5483c292328c47f139d8c0133f872c84a68');
							$writer->endElement();
							$writer->writeElement('description','');
							$writer->writeElement('category','news');
							$writer->writeElement('pubDate','');
							$writer->startElement('media:content');
								$writer->writeAttribute('url','http://api.new.livestreaem');
								$writer->writeAttribute('height','720');
								$writer->writeAttribute('duration','161');
								$writer->writeAttribute('bitrate','2000');
								$writer->writeAttribute('medium','video');
								$writer->writeAttribute('type','application/octet-stream');
								$writer->writeAttribute('expression','full');
							$writer->endElement();
							$writer->startElement('media:thumbnail');
								$writer->writeAttribute('url','http://prodman.whizti.com/live/kris-live.jpg');
							$writer->endElement();

				$writer->endElement(); //item
						
				} //end of for loop of items 

		$writer->endElement();
		$writer->endElement();
		$writer->endDocument();
		//print $writer->outputMemory(TRUE);
		//return $writer->outputMemory(TRUE);
		$writer->flush();
		unset($writer);
	}

	

	

}
