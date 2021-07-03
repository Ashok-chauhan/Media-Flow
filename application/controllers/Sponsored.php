<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sponsored extends CI_Controller { 
	
var $objectName = 'news';
	function __construct() {

		parent::__construct();
		//$this->load->scaffolding('dev');
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('menu');
	
	}

	function index() { 
		
		$user_type = $this->session->userdata('user');
		if(isset($_POST['id']))
		{
			$publication_id = $_POST['id'];
		}else{
			//$publication_id = $get_pubid;
			$publication_id = $this->uri->segment(3);
		}

		$this->db->where('id',$publication_id);
		$result = $this->db->get('publication');
		$publication = $result->row_array();
		// bof categories to show list of cats.
		
		$this->db->where('publication_id', $publication_id);
		$this->db->where('status','active');
		$cq = $this->db->get('category');
		$categories = $cq->result_array();
		
        // eof categoires query
        /*
		$this->template->setView('sponsored/index');
		$this->template->assign('pub_id',$user_type['pub_id']);
		$this->template->assign('type',$user_type['type']);
		$this->template->assign('page_name','sponsored');
		$this->template->assign('publication', $publication);
		$this->template->assign('categories', $categories);
        $this->template->render('main_body');
        */
        $data['main']                    = 'sponsored/index';
		$data['pub_id']                  = $user_type['pub_id'];
		$data['type']                    = $user_type['type'];
		$data['page_name']               = 'sponsored';
		$data['publication']             = $publication;
		$data['categories']              = $categories;
		$this->load->view('template', $data');

		
		
	}

	function add(){
		$publication_id = $_REQUEST['publication_id'];
		if($this->input->post('title')){
			if($this->input->post('id')){
				$data = array(
				'title'			=> $this->input->post('title'),
				'category_id'	=> $this->input->post('category_id'),
				'publication_id'=> $this->input->post('publication_id'),
				'video_url'		=> $this->input->post('video_url'),
				'video_thumbnail'=> $this->input->post('video_thumbnail'),
				'format'		=> $this->input->post('format'),
				'video_group'	=> $this->input->post('video_group'),
				'slot'			=> $this->input->post('slot')
				);
				$this->db->where('id', $this->input->post('id'));
				$this->db->update('sponsored_ad', $data);
				redirect('sponsored/index/'.$publication_id);
			}else{
			$data = array(
				'title'			=> $this->input->post('title'),
				'category_id'	=> $this->input->post('category_id'),
				'publication_id'=> $this->input->post('publication_id'),
				'video_url'		=> $this->input->post('video_url'),
				'video_thumbnail'=> $this->input->post('video_thumbnail'),
				'format'		=> $this->input->post('format'),
				'video_group'	=> $this->input->post('video_group'),
				'slot'			=> $this->input->post('slot')
				);
			$this->db->insert('sponsored_ad', $data);
			redirect('sponsored/index/'.$publication_id);
			}
		}else{
		

		$user_type = $this->session->userdata('user');
		
		$this->db->where('category_id', $_REQUEST['cid']);
		$q = $this->db->get('sponsored_ad');
		$sponsoredVideo = $q->row_array();

		$this->template->setView('sponsored/add');
		$this->template->assign('menu', getmenu($user_type['id']));
		$this->template->assign('pub_id',$user_type['pub_id']);
		$this->template->assign('type',$user_type['type']);
		$this->template->assign('cid', $_REQUEST['cid']);
		$this->template->assign('publication_id', $publication_id);
		$this->template->assign('sponsoredVideo', $sponsoredVideo);
		$this->template->assign('slots', range(0,14));
		$this->template->assign('formats', array('mp4'=>'mp4', 'hls'=>'hls'));
		$this->template->assign('video_groups', array('sponsored'=>'Sponsored','live'=>'Live Stream', 'vod'=>'Vod'));
		$this->template->render('main_body');
		}
	}

	function delete(){
	if($this->input->post('deleteid')){
		$this->db->where('id', $this->input->post('deleteid'));
		$this->db->delete('sponsored_ad');

	}
	redirect('sponsored/index/'.$this->input->post('publication_id'));
	}

}
