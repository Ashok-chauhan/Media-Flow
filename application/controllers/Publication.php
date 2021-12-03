<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//error_reporting(0);

class Publication extends CI_Controller {

	var $objectName = 'publication';
	public function __construct(){
		parent::__construct();
		
		if(!$this->session->userdata('user')) redirect($this->config->item('base_url').'user/login');
		$this->load->helper('menu');
		$this->load->helper('form');
		
	}
	
	function save(){
		$this->Mpublication->save($_REQUEST);
	}

	function index() {
		
		$user_type = $this->session->userdata('user');
		
		if($user_type['type']=='admin' && $user_type['status'] =='active') {
			$this->db->order_by("id", "asc");
			$query = $this->db->get('publication');
			$publications = $query->result_array();
			// bof group 
			$gq = $this->db->get('group');
			$group = $gq->result_array();
			// eof group query
			$data['main'] = 'publication/index';
			$data['type'] = $user_type['type'];
			$data['publications'] = $publications;
			$data['group'] = $group;
			$data['menu'] = getmenu($user_type['id']);
			$data['page_name'] = 'adminhome';
			$this->load->view('template', $data);


		}else{ 
			$this->load->helper('menu');
			
			/*
			$this->template->setView('pubdashboard/index');
				///flag for unpublished 
			$this->template->assign('flag','Not Published');
			$this->template->assign('first_name',$user_type['first_name']);
			$this->template->assign('last_name',$user_type['last_name']);
			$this->template->render('main_body');
		*/
			$menu = getmenu($user_type['id']);
			
			if($user_type['pub_id'] > 0 && $user_type['status']=='active'){
				
				redirect($this->config->item('base_url').'/publication/dashboard');
							switch($menu[1]){
								case "analytics":
									redirect($this->config->item('base_url').'/analytics/?1');
									break;
								case "isay":
									redirect($this->config->item('base_url').'/pubdashboard/unpublished/?1');
									break;
								case "cms":
									if(catcheck($user_type['pub_id'])){
									redirect($this->config->item('base_url').'/cms/?1');
								}else{
									redirect($this->config->item('base_url').'/publication/view/'.$user_type['pub_id']);
								}
									break;

									case "android":
									if(catcheck($user_type['pub_id'])){
									redirect($this->config->item('base_url').'/android/?1');
								}else{
									redirect($this->config->item('base_url').'/publication/view/'.$user_type['pub_id']);
								}
									break;
									case "iphone":
									if(catcheck($user_type['pub_id'])){
									redirect($this->config->item('base_url').'/iphone/?1');
								}else{
									redirect($this->config->item('base_url').'/publication/view/'.$user_type['pub_id']);
								}
									break;
								case "apn":
									redirect($this->config->item('base_url').'/push/?1');
									break;
								default:
									echo '<div style="margin-top:100px;border:solid red 1px;">You have not any active Category, Contatct to Whizti Administration.</div>';
								$this->session->sess_destroy();
								exit;
							}


						}else{
							
						echo '<div style="margin-top:100px;border:solid red 1px;">You have not any active Category, Contatct to Whizti Administration.</div>';
						$this->session->sess_destroy();
						exit;
						}

		}
	}

	
	
	
	function settings($get_pubid=NULL) {
		$user_type = $this->session->userdata('user');
		//echo $get_pubid;
		if(isset($_POST['id']))
		{
			$publication_id = $_POST['id'];
		}else{
			$publication_id = $get_pubid;
		}
		$this->db->where('id',$publication_id);
		$result = $this->db->get('publication');
		$publication = $result->row_array();
		// bof group to get groups name from  group table
		$gq = $this->db->get('group');
			$group = $gq->result_array();
		// eof group query
		$data['main']			= 'publication/settings';
		$data['type']			= $user_type['type'];
		$data['menu']			= getmenu($user_type['id']);
		$data['pub_id']			= $user_type['pub_id'];
		$data['page_name']		= 'settings';
		$data['publication']	= $publication;
		$data['group']			= $group;
		$this->load->view('template', $data);
	}

	/*
	*abandoned
	function view() {
		$user_type = $this->session->userdata('user');
		$this->db->where('id', $this->uri->segment(3));
		$result = $this->db->get('publication');
		$publication = $result->row_array();

		$this->db->where('publication_id', $publication['id']);
		$this->db->orderby('cat_order','ASC');
//		$this->db->groupby('status');
		$result = $this->db->get('category');
		$categories = $result->result_array();
		$avail_categories = array();
		$active_categories = array();
		foreach($categories as $category) {
			if($category['status'] == 'inactive') $avail_categories[] = $category;
			else $active_categories[] = $category;
		}

		$data['main'] 			= 'publication/view';
		$data['menu']			= getmenu($user_type['id']);
		$data['pub_id']			= $user_type['pub_id'];
		$data['type']			= $user_type['type'];
		$data['icon_base_url']	= $this->config->item('icon_base_url');
		$data['publication']	= $publication;
		$data['publication_id']	= $publication['id'];
		$data['avail_categories'] = $avail_categories;
		$data['active_categories'] = $active_categories;
		$data['num_cats']		= count($categories);

		
	}

	*/
	
	function GenerateNavArray($arr, $parent = 0)
	{
		$pages = Array();
		foreach($arr as $page)
		{
			if($page['parent_id'] == $parent)
			{
				$page['sub'] = isset($page['sub']) ? $page['sub'] : $this->GenerateNavArray($arr, $page['id']);
				$pages[] = $page;
			}
		}
		return $pages;
	}

	function manage() {
		$user_type = $this->session->userdata('user');
		$this->db->where('id', $this->uri->segment(3));
		$result = $this->db->get('publication');
		$publication = $result->row_array();
		if($this->input->post('Search'))
		{
			if($this->input->post('type')!='all')
			{
				$this->db->where('status', $this->input->post('type'));
			}
			if($this->input->post('search_txt')!='Search')
			{
				$this->db->like('label', $this->input->post('search_txt')); 
			}
			$this->template->assign('searchform','yes');
		}
		$this->db->where('publication_id', $publication['id']);
		$this->db->order_by('cat_order','ASC');
//		$this->db->groupby('status');
		$result = $this->db->get('category');
		$categories = $result->result_array();
		$avail_categories = array();
		$active_categories = array();
		//print_r($categories);
		$all_categories = $this->GenerateNavArray($categories);
		//print_r($all_categories);
		$flashmsg = $this->session->flashdata('flashmsg'); //<!--kiran  aug 2-->
		//get superhome categories.
		$this->db->select('category_id');
		$this->db->where('publication_id', $publication['id']);
		$homeData = $this->db->get('superhome');
		$super_home = $homeData->result_array();
		$superHome = [];
		if(count($super_home) >0){
			foreach($super_home as $home ){
				$superHome[] = $home['category_id'];
			}
		}
		//

		
		// php template bof
		$data['main'] 			= 'publication/manage';
		$data['flashmsg'] 		= $flashmsg;
		$data['menu'] 			= getmenu($user_type['id']);
		if($user_type['pub_id']){
			$data['pub_id'] 	= $user_type['pub_id'];
			}else{
			$data['pub_id'] 	= $this->uri->segment(3);
		}
		$data['type']			= $user_type['type'];
		$data['icon_base_url'] 	= $this->config->item('icon_base_url');
		$data['publication'] 	= $publication;
		$data['publication_id'] = $publication['id'];
		$data['page_name']		= 'categorypanel';
		$data['searched_type'] 	= $this->input->post('type');
		$data['all_categories'] = $all_categories;
		$data['num_cats']		= count($categories);
		$data['superHome']		= $superHome;
		$this->load->view('template', $data);
		// php template eof
	}


	function superhome(){
		$user_type = $this->session->userdata('user');
		$this->db->select('myhome');
		$this->db->where('id', $this->uri->segment(3));
		$myhomeData = $this->db->get('publication');
		$myhome = $myhomeData->row();
		
		$this->db->where('publication_id', $this->uri->segment(3));
		$this->db->order_by('json_order');
		$homeData = $this->db->get('superhome');
		$super_home = $homeData->result_array();
		
		$data['main'] 			= 'publication/superhome';
		//$data['flashmsg'] 		= $flashmsg;
		$data['menu'] 			= getmenu($user_type['id']);
		if($user_type['pub_id']){
			$data['pub_id'] 	= $user_type['pub_id'];
			}else{
			$data['pub_id'] 	= $this->uri->segment(3);
		}
		$data['type']			= $user_type['type'];
		$data['icon_base_url'] 	= $this->config->item('icon_base_url');
		$data['page_name']		= 'categorypanel';
		$data['num_cats']		= count($super_home);
		$data['publication_id']	= $this->uri->segment(3);
		$data['superhome']		= $super_home;
		$data['myhome']			= $myhome;
		$this->load->view('template', $data);


		

	}


	public function setSuperhome(){
		
		$this->db->select('category_id');
		$this->db->where('publication_id', $this->input->post('publicationid'));
		$this->db->order_by('json_order');
		$homeData = $this->db->get('superhome');
		$super_home = $homeData->result_array();
		$superHome = [];
		if(count($super_home) >0){
			foreach($super_home as $home ){
				$superHome[] = $home['category_id'];
			}
		}else{
			$this->session->set_flashdata('superhomeMessage','No Super home category, you have to select category!');
			redirect('/publication/manage/'.$this->input->post('publicationid'));
		}
		
		$superHomeSetting = json_encode(array(
			'section_ids'=>$superHome,
			'min_items'=> $this->input->post('min_items'),
			'min_items_tablet'=> $this->input->post('min_items_tablet'),
			'hide_alerts'=> $this->input->post('hide_alerts')?'1':'0'
		));
		$this->db->set('myhome', $superHomeSetting);
		$this->db->where('id', $this->input->post('publicationid'));
		$this->db->update('publication');
		redirect('/publication/manage/'.$this->input->post('publicationid'));
		
	}
	

	//////// BOF Added by Ashok to Create Publication Group 02/11/2010 /////
	function adgroup(){
		$user_type = $this->session->userdata('user');
		if($_POST){
			
			$data = array(
				'group_name'=>$_POST['group']
			);
			$this->db->insert('group',$data);
			redirect('publication/adgroup');

		}else{
			
			$result = $this->db->get('group');
			$group = $result->result_array();
			$data['main']	 = 'publication/adgroup';
			$data['type']	 = $user_type['type'];
			$data['groups']	 = $group;
			$this->load->view('template', $data);
		}


	}
	
	/*
	*Abendoned in ci3
	function showgroup(){
		$user_type = $this->session->userdata('user');
		$result = $this->db->get('group');
		$group = $result->result_array();

		$this->template->setView('publication/viewgroup');
		$this->template->assign('type', $user_type['type']);
		$this->template->assign('group', $group);
		$this->template->render('main_body');


	}
	*/

function getFeedContents($feed_uri,$http_header = NULL) { 
		
		log_message('debug','Getting feed contents with URI:'.$feed_uri);
		// create curl resource
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $feed_uri);
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		if($http_header!='')
		{
			curl_setopt($ch,CURLOPT_HTTPHEADER,array($http_header));
		}
		
		// $output contains the output string
		$feedContents = curl_exec($ch);
		// close curl resource to free up system resources
		curl_close($ch);
		//print $feedContents;		
		return $feedContents;
	}
	////////              EOF GROUP CREATION ///////////////////////////////
	
	function ads($get_pubid=NULL) {
		$user_type = $this->session->userdata('user');
		//echo $get_pubid;
		if(isset($_POST['id']))
		{
			$publication_id = $_POST['id'];
		}else{
			$publication_id = $get_pubid;
		}
		$this->db->where('id',$publication_id);
		$result = $this->db->get('publication');
		$publication = $result->row_array();
		// bof group to get groups name from  group table
		$gq = $this->db->get('group');
			$group = $gq->result_array();
		// eof group query
		$this->template->setView('publication/ads');
		$this->template->assign('type',$user_type['type']);
		$this->template->assign('menu', getmenu($user_type['id']));
		$this->template->assign('pub_id',$user_type['pub_id']);
		$this->template->assign('page_name','adpanel');
		$this->template->assign('publication', $publication);
		$this->template->assign('group', $group);
		$this->template->render('main_body');
	}
}