<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * CI_Loader
	 *
	 * @var	CI_Loader
	 */
	public $load;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

	// BOF WHIZ CUSTOMIZATION

	public  function saveSetting($fields=array(),$redirect=true, $redirect_uri='') {
	//	public function save2( $redirect_uri) {
		//public function save() {
		if(sizeof($fields) <=0) {
			foreach($_POST as $field => $value) {
				if(isset($this->fields[$field]))
					$fields[$field] = $value;
			}
		}
        if($this->objectName == 'publication')
        {
        	/* copy preroll to all fields*/
			//$fields['preroll_url_fire'] = $fields['preroll_url_phone'];
			//$fields['preroll_url_ipad'] = $fields['preroll_url_phone'];

			/* copy dfp ad tag to all fields*/
			/*$fields['ipad_dfp_article_landscape'] = $fields['phone_dfp_banner'];
			$fields['ipad_dfp_article_portrait'] = $fields['phone_dfp_banner'];
			$fields['ipad_dfp_banner'] = $fields['phone_dfp_banner'];
			$fields['ipad_dfp_fullpage_landscape'] = $fields['phone_dfp_banner'];
			$fields['ipad_dfp_fullpage_portrait'] = $fields['phone_dfp_banner'];
			$fields['ipad_dfp_skyscraper'] = $fields['phone_dfp_banner'];
			$fields['fire_dfp_banner'] = $fields['phone_dfp_banner'];
			$fields['fire_dfp_small_banner'] = $fields['phone_dfp_banner'];
			$fields['fire_dfp_fullpage'] = $fields['phone_dfp_banner'];
			$fields['fire_dfp_fullpage_landscape'] = $fields['phone_dfp_banner'];
			$fields['phone_dfp_fullpage'] = $fields['phone_dfp_banner'];
			*/

			/* copy preroll to all fields*/
			$fields['preroll_url_fire'] = ($fields['preroll_url_phone'] ?$fields['preroll_url_phone'] :'');
			$fields['preroll_url_ipad'] = ($fields['preroll_url_phone'] ?$fields['preroll_url_phone'] :'');

			/* copy dfp ad tag to all fields*/
			$fields['ipad_dfp_article_landscape'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');
			$fields['ipad_dfp_article_portrait'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');
			$fields['ipad_dfp_banner'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] : '');
			$fields['ipad_dfp_fullpage_landscape'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');
			$fields['ipad_dfp_fullpage_portrait'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');
			$fields['ipad_dfp_skyscraper'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');
			$fields['fire_dfp_banner'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');
			$fields['fire_dfp_small_banner'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] : '');
			$fields['fire_dfp_fullpage'] = ($fields['phone_dfp_banner']? $fields['phone_dfp_banner'] : '');
			$fields['fire_dfp_fullpage_landscape'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');
			$fields['phone_dfp_fullpage'] = ($fields['phone_dfp_banner'] ?$fields['phone_dfp_banner'] :'');

        }

		if(!isset($fields['id']) || $fields['id'] == '') {
			$this->setFieldsForSave($fields,'insert');
			
			$result = $this->db->insert($this->objectName, $fields);
			$object_id = $this->db->insert_id();
		} else {
			
			$this->setFieldsForSave($fields,'update');
			$this->db->where('id',$fields['id']);
			$this->db->update($this->objectName, $fields);
			$object_id = $fields['id'];
			
		}
		if($redirect == true) {
			if($redirect_uri =='') $redirect_uri =$this->objectName.'/manage/'.$object_id; 
			$this->session->set_flashdata('flashmsg','Operation completed successfully.'); //<!--kiran  aug 2-->
			redirect($redirect_uri);
		} else $object_id;
	}

	function setFieldsForSave(&$fields,$saveType) {
		$dateTime = date('Y-m-d H:i:s');
		$fields['date_modified'] = $dateTime;
		$fields['modified_by'] = 1;
		if($saveType == 'insert') {
			$fields['created_by'] = 1;
			$fields['date_created'] = $dateTime;
			unset($fields['id']);
		} elseif($saveType == 'update') {
			
		}
	}

	function sessionValid() {
		$sessionUserObj = $this->session->userdata('user');
		if(isset($sessionUserObj['email']) && isset($sessionUserObj['password'])) return true;

		return false;
	}

	function apiSessionValid() {
		$sessionUserObj = $this->session->userdata('user');
		$session_token = '';
		(isset($_SERVER['HTTP_X_AUTH_TOKEN'])) ? $session_token = $_SERVER['HTTP_X_AUTH_TOKEN'] : $session_token = null;
		if(isset($sessionUserObj['auth_token']) && $sessionUserObj['auth_token'] != null && $sessionUserObj['auth_token'] == $session_token) return true;

		return false;
	}

	function destroySession() {
		$this->session->set_userdata('user',null);
	}

	function getById($id,$objectName='') {
		if($id == '') return false;
		$this->db->where('id',$id);
		($objectName == '') ? $objectName = $this->objectName : null;
		$result = $this->db->get($objectName);
		return $result->row_array();
	}
	function viaAPI() {
		if($this->uri->segment(1) == 'api' || $this->uri->segment(1) == 'apiv2') return true;
		else return false;
	}
	// EOF WHIZ CUSTOMIZATION


}
