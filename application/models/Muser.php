<?php
class MUser extends CI_Model {
    public function __construct()
    {
            $this->load->database();
    }

public function verifypub() {
$email = $_POST['email'];
$this->db->where('email',$email);
$q = $this->db->get('user');

	if($q->num_rows() > 0){
		
		return true;
	}
	
	return false;
}

function addUser(){
	$data = array ('first_name' =>$_POST['first_name'],
		'last_name'=>$_POST['last_name'],
		'email' =>$_POST['email'],
		'password' => md5($_POST['password']),
		'type' => $_POST['type'],
		'status' =>  $_POST['status'],
		'pub_id' => $_POST['pub_id'],
		'analytics' => $_POST['analytics'],
		'cms' => $_POST['cms'],
		'isay' => $_POST['isay'],
		'apn' => $_POST['apn'],
		'subscription' => $_POST['subscription'],
);
	$this->db->insert('user',$data);
	$this->session->set_flashdata('error', 'Registration Completed succesfully.');
}

}
