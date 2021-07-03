<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	public function __construct(){
		parent::__construct();
		//Your own constructor code.
		$this->load->helper('url');
		$this->load->helper('form');
		//$this->load->helper('menu');
		$this->load->model('Muser');

	}

	public function login()
	{
		if(isset($_REQUEST['error']) && $_REQUEST['error'] !='')
			$error_string = '<div class="error">'.$_REQUEST['error'].'</div>';
		else $error_string = '';
		$data['main']= 'user/login';
		$data['error_messages'] = $error_string;
		$this->load->view('template',$data);
		
	}

	public function authenticate(){
		$error_string = '';
		if(!isset($_POST['email']) || $_POST['email'] == '')
			$error_string .= 'Email Is Required<br/>';

		if(!isset($_POST['password']) || $_POST['password'] == '')
			$error_string .= 'Password Is Required<br/>';
		
		if($error_string !='')
			redirect('user/login/?error='.$error_string);

		else {

			$email = $_POST['email'];
			$password = md5($_POST['password']);
			$this->db->where('email',$email);
			$this->db->where('password',$password);
			$result = $this->db->get('user');
			$user = $result->row_array();
			/////////// BOF ADDED BY ASHOK TO FILTER PUBLISHER ////
			if($user['type'] == 'publisher' && $user['pub_id'] >0 ){
					if(isset($user['id']) && $user['id'] !='') {
						if($user['status']=='inactive'){
						$error_string .= 'Account is not Activated<br/>';
						redirect('user/login/?error='.$error_string);
						}
						$this->session->set_userdata('user', $user);

						$user_type = $this->session->userdata('user');
						// GENERATING MENU AND PROVIDE ACCESS ACCORDING TO MODULE
						$menu = getmenu($user_type['id']);
						
						
						if($user_type['pub_id'] > 0 && $user_type['status']=='active'){
							redirect($this->config->item('base_url').'publication/manage/'.$user_type['pub_id']);
							
							switch($menu[1]){
								case "analytics":
									redirect($this->config->item('base_url').'analytics/?1');
									break;
								case "isay":
									redirect($this->config->item('base_url').'pubdashboard/unpublished/?1');
									break;
								case "cms":
									if(catcheck($user_type['pub_id'])){
									redirect($this->config->item('base_url').'cms/manage/?1');
									}else{ 
									redirect($this->config->item('base_url').'publication/view/'.$user_type['pub_id']);
									}

									break;
									case "android":
									if(catcheck($user_type['pub_id'])){ 
									redirect($this->config->item('base_url').'android/?1');
								}else{
									redirect($this->config->item('base_url').'publication/view/'.$user_type['pub_id']);
								}
									break;
									case "iphone":
									if(catcheck($user_type['pub_id'])){
									redirect($this->config->item('base_url').'iphone/?1');
								}else{
									redirect($this->config->item('base_url').'publication/view/'.$user_type['pub_id']);
								}
									break;
								case "apn":
									redirect($this->config->item('base_url').'push/?1');
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
												
						
					} else {
						$error_string .= 'The username or password you entered is incorrect.<br/>';
						redirect('user/login/?error='.$error_string);
					}
			}else{
			/////////// EOF TO FILTER PUBLISHER ///////////////////
					if(isset($user['id']) && $user['id'] !='' && $user['type']=='admin') {
						
						$this->session->set_userdata('user', $user);
						//redirect($this->config->item('base_url'));
						redirect($this->config->item('base_url').'publication');
						
					} else {
						$error_string .= 'The username or password you entered is incorrect.<br/>';
						redirect('user/login/?error='.$error_string);
					}
		
			}
		}

	}

	function logout() {
		$this->session->sess_destroy();
		redirect($this->config->item('base_url').'user/login');
	}

	function pubregistration() { 

			$user_type = $this->session->userdata('user');
				if($user_type['type']=='admin'){
					
			if($this->input->post('first_name')){
				$error_string = '';
				
				if(!isset($_POST['first_name']) || $_POST['first_name'] == '')
					$error_string .='First Name Is Required<br/>';
		
				if(!isset($_POST['last_name']) || $_POST['last_name'] == '')
					$error_string .='Last Name Is Required<br/>';
		
				if(!isset($_POST['email']) || $_POST['email'] == '')
					$error_string .='Email Is Required<br/>';
				
				if(!isset($_POST['password']) || $_POST['password'] == '')
					$error_string .='Password Is Required<br/>';
		
				if(!isset($_POST['pub_id']) || $_POST['pub_id'] == '')
					$error_string .='Publisher ID Is Required<br/>';
					
				if($error_string !='')
					redirect('user/pubregistration/?error='.$error_string);
		
					if($this->Muser->verifypub()){
					$error_string = 'email address already registered!.<br/>';
					redirect('user/pubregistration/?error='.$error_string);
				}

		
			$this->Muser->addUser();
			redirect($this->config->item('base_url').'publication');
			}else{
				
				if(isset($_REQUEST['error']) && $_REQUEST['error'] !='')
					$error_string = '<div class="error">'.$_REQUEST['error'].'</div>';
				else $error_string = '';
				$data['main']		= 'user/pubregistration';
				$data['type']		= $user_type['type'];
				$data['error_messages'] = $error_string;
				$data['page_name']	= 'regpub';
				$this->load->view('template', $data);

		
			}
		
			}else{
				echo '<div style="margin-top:100px;border:solid red 1px;">Access Denied, Contatct to Whizti Administration.</div>';
				$this->session->sess_destroy();
				exit;
				}
		
		}

function publist()
	{
	$user_type = $this->session->userdata('user');
		if($user_type['type']=='admin'){
		$this->db->where('type','publisher');
		$pubq = $this->db->get('user');
		$pub = $pubq->result_array();
		$data['main']		= 'user/publist';
		$data['type']		= $user_type['type'];
		$data['publisher']	= $pub;
		$this->load->view('template', $data);

		}else{
		echo '<div style="margin-top:100px;border:solid red 1px;">Access Denied, Contatct to Whizti Administration.</div>';
		$this->session->sess_destroy();
		exit;
		}

	}

function pubedit()
	{
	$user_type = $this->session->userdata('user');
	if($user_type['type']=='admin'){


	if($_POST){
		
		$data = array(
			'first_name'=>$_POST['first_name'],
			'last_name'=>$_POST['last_name'],
			'email'=>$_POST['email'],
			'status'=>$_POST['status'],
			'pub_id'=>$_POST['pub_id'],
			'analytics' => $_POST['analytics'],
			'cms' => $_POST['cms'],
			'isay' => $_POST['isay'],
			'apn' => $_POST['apn'],
			'subscription' => $_POST['subscription']
			);
				if($_POST['password']){
					$pass = array(
						'password'=> md5($_POST['password'])
						);
					$this->db->where('id',$_POST['id']);
					$this->db->where('type','publisher');
					$this->db->update('user',$pass);
				}
		$this->db->where('id',$_POST['id']);
		$this->db->where('type','publisher');
		$this->db->update('user',$data);
	
	////11/08/2010/ author Ashok UPLOADING PUBLISHER LOGO
	if($_FILES){
		$config['upload_path'] = 'images/publishers';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = '0';
		$this->load->library('upload',$config);
			if(!$this->upload->do_upload('pub_logo'))
					{
							echo $this->upload->display_errors();
					}
					else
					{
						$file_data = $this->upload->data();
									
						$config['image_library'] = 'GD2';
						$config['source_image'] = 'images/publishers/'.$file_data['file_name'];
						$config['new_image'] = 'images/publishers/'.$_POST['pub_id'].'.png';
						$config['maintain_ratio'] = FALSE;
						$config['width'] = 320;
						$config['height'] = 100;
						$this->load->library('image_lib', $config);
						if(!$this->image_lib->resize())
									{
										echo $this->image_lib->display_errors();
									}else{
										unlink('images/publishers/'.$file_data['file_name']);
									}
									
					}

			}
	///// EOF uploading Publishers logo
				redirect('user/publist');
	}else{
		
		$id = $this->uri->segment(3);
		
		$this->db->where('id',$id);
		$this->db->where('type','publisher');
		$result = $this->db->get('user');
		$pubedit = $result->row_array();

		$data['main']		= 'user/pubedit';
		$data['type']		= $user_type['type'];
		$data['pubedit']	= $pubedit;
		$this->load->view('template', $data);

	}

	}else{
		echo '<div style="margin-top:100px;border:solid red 1px;">Access Denied, Contatct to Whizti Administration.</div>';
		$this->session->sess_destroy();
		exit;
		}

	}

function pubdel()
	{
	$user_type = $this->session->userdata('user');
	if($user_type['type']=='admin'){

		$id = $this->uri->segment(3);
		$this->db->where('id',$id);
		$result = $this->db->get('user');
		$pdata = $result->row_array();
			$filename = 'images/publishers/'.$pdata['pub_id'].'png';
			if(file_exists($filename)){
				unlink('images/publishers/'.$pdata['pub_id'].'png');
				}
		if($id){
			$this->db->where('id',$id);
			$this->db->where('type','publisher');
			$this->db->delete('user');
			}
			redirect('user/publist');

		}else{
		echo '<div style="margin-top:100px;border:solid red 1px;">Access Denied, Contatct to Whizti Administration.</div>';
		$this->session->sess_destroy();
		exit;
		}
	}
		





}
