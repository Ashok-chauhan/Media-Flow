<?php

error_reporting(0);
class Category extends CI_Controller {
	var $objectName = 'category';

	public function __construct(){
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
		
		$data['menu'] 	= getmenu($user_type['id']);
		$data['pub_id']	= $user_type['pub_id'];
		$data['type']	= $user_type['type'];

		
	}

	function add() {
		$user_type = $this->session->userdata('user');
		
	/*	$ur = explode('=',$_SERVER['REQUEST_URI']);
		$ur2 =explode('&',$ur[1]);
		$pubid = $ur2[0];
		$ctype = $ur[2];
		if(!isset($pubid) || $pubid == ''){
			die('Fatal Error, should clean up and have clean redirect to standard error page/view');
			return;
		}elseif(!isset($ctype) || $ctype == ''){
			die('Fatal Error, should clean up and have clean redirect to standard error page/view');
			return;
		}
	
	*/
	/// FOLOWING IS COMMENTED BY ASHOK///
	
		if(!isset($_REQUEST['publication_id']) || $_REQUEST['publication_id'] == '') {
			die('Fatal Error, should clean up and have clean redirect to standard error page/view');
			return;
		} elseif(!isset($_REQUEST['type']) || $_REQUEST['type'] == '') {
			die('Fatal Error, should clean up and have clean redirect to standard error page/view');
			return;
		}

		$publication_id = $_REQUEST['publication_id'];
		$type = $_REQUEST['type'];
		
		
		
	//	$publication_id = $pubid;
	//	$type = $ctype;

		$this->template->setView('category/'.$type.'.edit.tpl');
		$this->template->assign('type',$user_type['type']);
		$this->template->assign('publication_id',$publication_id);
		$this->template->assign('page_name','categorypanel');
		$this->template->assign('action_string','Add');
		$this->template->render('main_body');
	}
	
	function add_cat() {
		$user_type = $this->session->userdata('user');
	
	
	
		if(!isset($_REQUEST['publication_id']) || $_REQUEST['publication_id'] == '') {
			die('Fatal Error, should clean up and have clean redirect to standard error page/view');
			return;
		} 

		$publication_id = $_REQUEST['publication_id'];
		$category['type'] = $_GET['type'];
		//$this->template->assign('category',$category);
		$data['category'] = $category;
		$this->db->where('publication_id', $_GET['publication_id']);
		$allcat = $this->db->get('category');
		$allcategory = $allcat->result_array();
		$current_icon = 'image_not_available.png';
		//$this->template->assign('allcategory', $allcategory);
		$data['allcategory'] 	= $allcategory;
		$data['main'] 			= 'category/add_edit_cat';
		$data['type']			= $user_type['type'];
		$data['publication_id']	= $publication_id;
		$data['page_name']		= 'categorypanel';
		$data['action_string']	= 'Add';
		$data['current_icon']	= $current_icon;
		$this->load->view('template', $data);

		
	}
	
	function edit_cat() {
		$user_type = $this->session->userdata('user');//print_r($user_type['type']);
		$category_id = $this->uri->segment(3);
		if(!isset($category_id) || $category_id == '') {
			die('Fatal Error, should clean up and have clean redirect to standard error page/view');
			return;
		}
		$this->db->where('id',$category_id);
		$result = $this->db->get('category');
		$category = $result->row_array();

		if($category['parent_id'] >0){
			$this->db->where('id',$category['parent_id']);
			$parentCategoryResult = $this->db->get('category');
			$parentCategory = $parentCategoryResult->row_array();
			//$this->template->assign('parentCategory',$parentCategory);
			$data['parentCategory'] = $parentCategory;
		}

		$this->db->where('publication_id', $category['publication_id']);
		$allcat = $this->db->get('category');
		$allcategory = $allcat->result_array();
		if(isset($_GET['type']))
		{
			$category['type'] = $_GET['type'];
		}
		$data['main']			= 'category/add_edit_cat';
		$data['type']			= $user_type['type'];
		$data['category']		= $category;
		$data['publication_id']	= $category['publication_id'];
		$data['allcategory']	= $allcategory;
		$data['page_name']		= 'categorypanel';
		$data['action_string']	= 'Edit';
		$icon_src = $category['icon_filename'];
		$filepath = 'var/www/html/ci3/images/icons/'.$icon_src;
		if(file_exists($filepath) && $icon_src!='')
		{
			$current_icon = $icon_src;
		}else
		{
			$current_icon = 'image_not_available.png';
		}
		$data['current_icon']	= $current_icon;
		$this->load->view('template', $data);

		
	}
	
	function edit() {
		$user_type = $this->session->userdata('user');
		$category_id = $this->uri->segment(3);
		if(!isset($category_id) || $category_id == '') {
			die('Fatal Error, should clean up and have clean redirect to standard error page/view');
			return;
		}
		$this->db->where('id',$category_id);
		$result = $this->db->get('category');
		$category = $result->row_array();

		if($category['parent_id'] >0){
			$this->db->where('id',$category['parent_id']);
			$parentCategoryResult = $this->db->get('category');
			$parentCategory = $parentCategoryResult->row_array();
			$this->template->assign('parentCategory',$parentCategory);
		}

		$this->db->where('publication_id', $category['publication_id']);
		$allcat = $this->db->get('category');
		$allcategory = $allcat->result_array();

		$this->template->setView('category/'.$category['type'].'.edit.tpl');
		$this->template->assign('type',$user_type['type']);
		$this->template->assign('category',$category);
		$this->template->assign('publication_id',$category['publication_id']);

		$this->template->assign('allcategory', $allcategory);
		$this->template->assign('page_name','categorypanel');
		$this->template->assign('action_string','Edit');
		$icon_src = $category['icon_filename'];
		
		$filepath = 'images/icons/'.$icon_src;
		if(file_exists($filepath) && $icon_src!='')
		{
			$current_icon = $icon_src;
		}else
		{
			$current_icon = 'image_not_available.png';
		}
		$this->template->assign('current_icon',$current_icon);
		$this->template->render('main_body');
	}

	function save() { 
		
		// echo "<pre>";
		// 	print_r($_REQUEST);
		// 	echo "</pre>";
		// 	exit;
		if(!isset($_REQUEST['publication_id'])) {
			echo "<pre>";
			print_r($_REQUEST);
			echo "</pre>";
			echo 'ERROR: No Publication ID provided<br/><a href="javascript:history.go(-1);">&lt;&lt Go Back</a>';
			exit;
		}

		
		if(isset($_POST['id']) && $_POST['id']!='')
		{
			$query_parent = "SELECT parent_id FROM category WHERE id = ".$_POST['id']."";
			$result_parent = $this->db->query($query_parent);
			$parentidArr = $result_parent->row_array();
			
			if($parentidArr['parent_id'] == 0)
			{
				$parentidArr['parent_id'] = 'NULL';
			}
		}else
		{
			$parentidArr['parent_id'] = NULL;
		}
		
		if(($_REQUEST['parentcatid'] == NULL || $_REQUEST['parentcatid'] == 'NULL') && $_POST['id']=='')
		{//echo 'hi';exit;
			$query_y = "SELECT max(cat_order) as max_order, id FROM category WHERE publication_id=".$_REQUEST['publication_id']."";
			$temp_null = $this->db->query($query_y);
			$result_row_null = $temp_null->row_array();
			$max_max_cat_order = $result_row_null['max_order']+1;
			
		}
		else if($_REQUEST['parentcatid'] != $parentidArr['parent_id'])
		{
			$query = "SELECT * FROM category WHERE publication_id=".$_REQUEST['publication_id']." order by cat_order";
			$temp_query = $this->db->query($query);
			$i=0;
			foreach($temp_query->result_array() as $key => $value)
			{
				//echo $_REQUEST['parentcatid'];echo '   ' ; echo $value['id'];echo '<br>';
				if($value['id'] == $_REQUEST['parentcatid'])
				{
					$temp_where = '';
					if(isset($_POST['id']) && $_POST['id']!='')
					{
						$temp_where = "id != ".$_POST['id']." and";
					}
					
					$query_x = "SELECT max(cat_order) as max_order, id FROM category WHERE ".$temp_where." parent_id=".$value['id'];
					$temp_result = $this->db->query($query_x);
					$result_row = $temp_result->row_array();
					
					if(!isset($result_row['max_order']))
					{
						$query_x = "SELECT cat_order as max_order, id FROM category WHERE id=".$value['id'];
						$temp_result = $this->db->query($query_x);
						$result_row = $temp_result->row_array();
					}
					
					$query_y = "SELECT* FROM category WHERE  cat_order = ".$result_row['max_order']." and publication_id=".$_REQUEST['publication_id'];
					$temp_nextcat_result = $this->db->query($query_y);
					$nextcat_result_row = $temp_nextcat_result->row_array();
					
					/*$temp_count = $result_row['max_order']+1;
					echo  $query_y = "SELECT parent_id, id FROM category WHERE  cat_order = ".$temp_count." and publication_id=".$_REQUEST['publication_id'];
					$temp_nextcat_result = $this->db->query($query_y);
					$nextcat_result_row = $temp_nextcat_result->row_array();
					if($nextcat_result_row['parent_id'] == 0)
					{
						$updatetd_cat =  $result_row['max_order'];
					}else
					{
						$updatetd_cat =  $result_row['max_order']+1;
					}*/
					 $updatetd_cat_id = $nextcat_result_row['id'];
					//$catArray[$updatetd_cat] = $_POST['id'];
					
				}
				/*if($updatetd_cat == $i)
				{
					$i++;
				}*/
				
				if($value['id']!=$_POST['id'])
				{
					$catArray[$i] = $value['id'];
					if($updatetd_cat_id == $value['id'] )
					{
						$i++;
						$catArray[$i] = $_POST['id'];
					}
				}
				$i++;
			}
			//print_r($catArray);exit;
			for($i=0; $i < count($catArray)+10; $i++) {
				if(isset($catArray[$i]) && $catArray[$i]!='')
				{
					$catID = $catArray[$i];
					$query = "UPDATE category SET cat_order=".$i." WHERE publication_id=".$_REQUEST['publication_id']." AND id=".$catID;
					$this->db->query($query);
				}
			}
		}
		
		//print_r($_REQUEST);exit;
		$publication_id = $_REQUEST['publication_id'];
		$query = "SELECT count(*) as count FROM category WHERE publication_id=".$publication_id;
		$result = $this->db->query($query);
		$numCats = $result->row_array(); 
		
		$this->db->where('id',$_POST['id']);
		$result = $this->db->get('category');
		$category = $result->row_array();
		if(($_REQUEST['parentcatid'] == NULL || $_REQUEST['parentcatid'] == 'NULL') && $_POST['id']=='')
		{
			$category['cat_order'] = $max_max_cat_order;
		}
		$category['id'] = $_POST['id'];
		if($_REQUEST['name'] =='Watch now'){
			$category['name'] = $_REQUEST['name'];
		}else{
			$category['name'] = $_REQUEST['label'].'_'.time(); //$_REQUEST['name'];
		}
		$category['label'] = $_REQUEST['label'];
		$category['type'] = $_REQUEST['type'];
		$category['ipadtemplate'] = $_REQUEST['ipadtemplate'];
		$category['catcolor'] = trim($_REQUEST['catcolor']);
		
		#### MANIPULATION OF ATYPE #############
		if($_REQUEST['edit_atype']){
			$category['atype'] = $_REQUEST['edit_atype'];
		}else{
			$category['atype'] = $_REQUEST['atype'];
		}
		
		if($_REQUEST['type'] == 'webkit'){
			$category['atype'] = 'webkit';
			$_REQUEST['parser_type'] = 'full';
		}else
		{
			if($_REQUEST['parser_type'] == 'full')
			{
				$_REQUEST['parser_type'] = '';
			}
			if($category['atype'] == 'webkit')
			{
				$category['atype'] = 'list';
			}
		}
		
		if($_POST['id'] == '')
		{
			if($_REQUEST['type'] == 'webkit'){
				$category['atype'] = 'webkit';
			}else if($_REQUEST['type'] == 'isay')
			{
				$category['atype'] = 'isay';
			}
		}
		
		
		#### EOF ATYPE MANIPULATION ############
		
		$category['adwidth'] = $_REQUEST['adwidth'];
		$category['adheight'] = $_REQUEST['adheight'];
		$category['adcode'] = $_REQUEST['adcode'];
		$category['parent_id'] = $_REQUEST['parentcatid'];
		
		if(!isset($_REQUEST['delflag']))$_REQUEST['delflag']=0;
		$category['delflag'] = $_REQUEST['delflag'];
		if(!isset($_REQUEST['orderflag']))$_REQUEST['orderflag']=0;
		$category['orderflag'] = $_REQUEST['orderflag'];
		$category['cache'] = $_REQUEST['cache'];
		$category['device'] = $_REQUEST['device'];
		$category['ipad_catorder'] = $_REQUEST['ipad_catorder'];
		$category['story_limit'] = $_REQUEST['story_limit'];
		$category['photo_limit'] = $_REQUEST['photo_limit'];
		if($category['photo_limit']==''){$category['photo_limit']=0;}
		$category['ipad_label'] = $_REQUEST['ipad_label'];
		if(!isset($_REQUEST['my_mf']))$_REQUEST['my_mf']=0;
		$category['my_mf'] = $_REQUEST['my_mf'];
		if(!isset($_REQUEST['feature_story']))$_REQUEST['feature_story']=0;
		$category['feature_story'] = $_REQUEST['feature_story'];
		$category['inline_ad_frequency'] = $_REQUEST['inline_ad_frequency'];
	

		if($_REQUEST['prod_code']){
			$category['prod_code'] = $_REQUEST['prod_code'];
		}else{
			$category['prod_code'] = 'NULL';
		}
		(isset($_REQUEST['parser_type'])) ? $category['parser_type'] = $_REQUEST['parser_type'] : $category['parser_type'] = '';
		//// bof dfp ///
		(isset($_REQUEST['cat_ipad_dfp_article_landscape'])) ? $category['cat_ipad_dfp_article_landscape'] = $_REQUEST['cat_ipad_dfp_article_landscape'] : $category['cat_ipad_dfp_article_landscape'] = '';
		(isset($_REQUEST['cat_ipad_dfp_article_portrait'])) ? $category['cat_ipad_dfp_article_portrait'] = $_REQUEST['cat_ipad_dfp_article_portrait'] : $category['cat_ipad_dfp_article_portrait'] = '';
		(isset($_REQUEST['cat_ipad_dfp_banner'])) ? $category['cat_ipad_dfp_banner'] = $_REQUEST['cat_ipad_dfp_banner'] : $category['cat_ipad_dfp_banner'] = '';
		(isset($_REQUEST['cat_ipad_dfp_fullpage_landscape'])) ? $category['cat_ipad_dfp_fullpage_landscape'] = $_REQUEST['cat_ipad_dfp_fullpage_landscape'] : $category['cat_ipad_dfp_fullpage_landscape'] = '';
		(isset($_REQUEST['cat_ipad_dfp_fullpage_portrait'])) ? $category['cat_ipad_dfp_fullpage_portrait'] = $_REQUEST['cat_ipad_dfp_fullpage_portrait'] : $category['cat_ipad_dfp_fullpage_portrait'] = '';
		(isset($_REQUEST['cat_ipad_dfp_skyscraper'])) ? $category['cat_ipad_dfp_skyscraper'] = $_REQUEST['cat_ipad_dfp_skyscraper'] : $category['cat_ipad_dfp_skyscraper'] = '';
		(isset($_REQUEST['cat_fire_dfp_banner'])) ? $category['cat_fire_dfp_banner'] = $_REQUEST['cat_fire_dfp_banner'] : $category['cat_fire_dfp_banner'] = '';
		(isset($_REQUEST['cat_fire_dfp_small_banner'])) ? $category['cat_fire_dfp_small_banner'] = $_REQUEST['cat_fire_dfp_small_banner'] : $category['cat_fire_dfp_small_banner'] = '';
		(isset($_REQUEST['cat_fire_dfp_fullpage'])) ? $category['cat_fire_dfp_fullpage'] = $_REQUEST['cat_fire_dfp_fullpage'] : $category['cat_fire_dfp_fullpage'] = '';
		(isset($_REQUEST['cat_fire_dfp_fullpage_landscape'])) ? $category['cat_fire_dfp_fullpage_landscape'] = $_REQUEST['cat_fire_dfp_fullpage_landscape'] : $category['cat_fire_dfp_fullpage_landscape'] = '';
		(isset($_REQUEST['cat_phone_dfp_fullpage'])) ? $category['cat_phone_dfp_fullpage'] = $_REQUEST['cat_phone_dfp_fullpage'] : $category['cat_phone_dfp_fullpage'] = '';
		(isset($_REQUEST['cat_phone_dfp_banner'])) ? $category['cat_phone_dfp_banner'] = $_REQUEST['cat_phone_dfp_banner'] : $category['cat_phone_dfp_banner'] = '';

		(isset($_REQUEST['cat_banner_ads'])) ? $category['cat_banner_ads'] = $_REQUEST['cat_banner_ads'] : $category['cat_banner_ads'] = '';
		(isset($_REQUEST['cat_interstitial_ads'])) ? $category['cat_interstitial_ads'] = $_REQUEST['cat_interstitial_ads'] : $category['cat_interstitial_ads'] = '';
		(isset($_REQUEST['cat_video_ads'])) ? $category['cat_video_ads'] = $_REQUEST['cat_video_ads'] : $category['cat_video_ads'] = '';

		(isset($_REQUEST['cat_native_ad'])) ? $category['cat_native_ad'] = $_REQUEST['cat_native_ad'] : $category['cat_native_ad'] = '';

		//// eof dfp ///
		/// bof category preroll ads ////
		(isset($_REQUEST['cat_preroll_url_fire'])) ? $category['cat_preroll_url_fire'] = $_REQUEST['cat_preroll_url_fire'] : $category['cat_preroll_url_fire'] = '';
		(isset($_REQUEST['cat_preroll_url_phone'])) ? $category['cat_preroll_url_phone'] = $_REQUEST['cat_preroll_url_phone'] : $category['cat_preroll_url_phone'] = '';
		(isset($_REQUEST['cat_preroll_url_ipad'])) ? $category['cat_preroll_url_ipad'] = $_REQUEST['cat_preroll_url_ipad'] : $category['cat_preroll_url_ipad'] = '';


		/// eof category preroll ads ////

		/* copy preroll to all fields*/
		$category['cat_preroll_url_fire'] = $category['cat_preroll_url_phone'];
		$category['cat_preroll_url_ipad'] = $category['cat_preroll_url_phone'];

		/* copy dfp ad tag to all fields*/
		$category['cat_ipad_dfp_article_landscape'] = $category['cat_phone_dfp_banner'];
		$category['cat_ipad_dfp_article_portrait'] = $category['cat_phone_dfp_banner'];
		$category['cat_ipad_dfp_banner'] = $category['cat_phone_dfp_banner'];
		$category['cat_ipad_dfp_fullpage_landscape'] = $category['cat_phone_dfp_banner'];
		$category['cat_ipad_dfp_fullpage_portrait'] = $category['cat_phone_dfp_banner'];
		$category['cat_ipad_dfp_skyscraper'] = $category['cat_phone_dfp_banner'];
		$category['cat_fire_dfp_banner'] = $category['cat_phone_dfp_banner'];
		$category['cat_fire_dfp_small_banner'] = $category['cat_phone_dfp_banner'];
		$category['cat_fire_dfp_fullpage'] = $category['cat_phone_dfp_banner'];
		$category['cat_fire_dfp_fullpage_landscape'] = $category['cat_phone_dfp_banner'];
		$category['cat_phone_dfp_fullpage'] = $category['cat_phone_dfp_banner'];
		
		/////////// EOF ADDED BY ASHOK //////////
		(isset($_REQUEST['autoplay'])) ? $category['autoplay'] = $_REQUEST['autoplay'] : $category['autoplay'] = 0; //autoplay for ott videos.
		(isset($_REQUEST['source_uri'])) ? $category['source_uri'] = $_REQUEST['source_uri'] : $category['source_uri'] = '';
		(isset($_REQUEST['data_store'])) ? $category['data_store'] = $_REQUEST['data_store'] : $category['data_store'] = '';
		if(!isset($category['id']) || $category['id'] =='') $category['cat_order'] = $numCats['count'] + 1;
		$category['publication_id'] = $publication_id;
		//copy icon file into place
		if(isset($_FILES['icon_file']['name']) && $_FILES['icon_file']['name'] !='') {
			$current_iconfilename = '';
			if(isset($category['icon_filename']) && $category['icon_filename'] !='') $current_iconfilename = $category['icon_filename'];
			$uploadedIcon = $_FILES['icon_file'];
			$iconFilename = rand(0,250).'-'.$uploadedIcon['name'];
			$category['icon_filename'] = $iconFilename;
			$savePath = $this->config->item('icon_save_location').'/'.$iconFilename;

			print '<pre>';
			print_r($_FILES);
			print '<li>iconFilename'.$iconFilename;
			print '<li> icon_filename'.$category['icon_filename'];
			print '<li>savePath'.$savePath;
			print_r($uploadedIcon);
			print '<li> current_iconfilename '.$current_iconfilename;
			//exit;
			if(move_uploaded_file($uploadedIcon['tmp_name'], $savePath) && $current_iconfilename !='')
				unlink($this->config->item('icon_save_location').'/'.$current_iconfilename);
		}
		$categoryName = ucfirst($category['type']);
		require(BASEPATH.'libraries/CategoryHandlers/'.$categoryName.'.php');
		$handlerClass = $categoryName.'Handler';
		
		$catHandler = new $handlerClass($category);

		if(method_exists($catHandler, 'setCategoryVars')) {
			$category = $catHandler->setCategoryVars($category);
		
		}
		
		//$this->saveSetting($category,true,'publication/manage/'.$publication_id);
		$this->Mcategory->save($category,true,'publication/manage/'.$publication_id);
		
	}

	function validateFeed($url = NULL,$type = NULL)
	{
		 ini_set('display_errors',1);error_reporting(0);
		if($url == ''){$url = $_POST['url'];} 
		if($type == ''){$type = $_POST['type'];}
		$categoryName = ucfirst($type);
		$category['source_uri'] = $url;
		require_once(BASEPATH.'libraries/CategoryHandlers/'.$categoryName.'.php');
		$handlerClass = $categoryName.'Handler';
		
		$catHandler = new $handlerClass($category);		
		$all_stories = $catHandler->validateStoryList($url);
		//print_r($all_stories);
		$return_msg = '';
		foreach($all_stories as $key => $value)
		{
			if($value == 0){$return_msg .= $key.' is not present in feed<br>';}
		}
		echo $return_msg;
		exit;
	}
	
	function reorder() { 
		
// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";
// exit;
		
		if(!isset($_POST['publication_id']) || $_POST['publication_id'] == '') {
			echo 'ERROR: No Publication ID provided<br/><a href="javascript:history.go(-1);">&lt;&lt Go Back</a>';
			exit;
		}

		$numCats = $_POST['num_cats'];
		$publication_id = $_POST['publication_id'];

		for($i=0; $i < $numCats; $i++) {
			$inputID = 'cat_'.$i;
			$splitArray = preg_split('/__/',$_POST[$inputID]);
//echo "<pre>";
//print_r($splitArray);
//echo "</pre>";
			$catID = $splitArray[0];
			$catStatus = $splitArray[1];

			$query = "UPDATE category SET cat_order=".$i.",status='".$catStatus."' WHERE publication_id=".$publication_id." AND id=".$catID;
			$this->db->query($query);
//		echo $query."<br/>";
		}
		redirect('publication/view/'.$publication_id);
	}
	
	function reorder_ajax() { 
		
 
		
		if(!isset($_POST['publication_id']) || $_POST['publication_id'] == '') {
			echo 'ERROR: No Publication ID provided<br/><a href="javascript:history.go(-1);">&lt;&lt Go Back</a>';
			exit;
		}
		
		//$numCats = $_POST['num_cats'];
		$numCats = count($_POST['cat']);
		$publication_id = $_POST['publication_id'];
		$catArray = $_POST['cat'];
		//print_r($_POST);print_r($catArray); //exit;
		for($i=0; $i < $numCats; $i++) {
		//	$inputID = 'cat_'.$i;
		//	$splitArray = preg_split('/__/',$_POST[$inputID]);
			$catID = $catArray[$i];

			$query = "UPDATE category SET date_modified= now(), ipad_catorder=".($i*10).",  cat_order=".$i." WHERE publication_id=".$publication_id." AND id=".$catID;
			$this->db->query($query);
//		echo $query."<br/>";
			if($i == $numCats-1)
			{
				echo 'success';exit;
			}
		}
		echo 'success';exit;
	}

	function reorder_ajax_superhome() { 
		
 
		
		//die('got');
		
		//$numCats = $_POST['num_cats'];
		$numCats = count($_POST['cat']);
		$publication_id = $_POST['publication_id'];
		$catArray = $_POST['cat'];
		//print_r($_POST);print_r($catArray); //exit;


						// creating log file for testing.
						// $fp = fopen('/var/www/html/ci3/images/reorder.txt', 'a+');
						// fwrite($fp,$publication_id."\n");
						// fclose($fp);

//die('got');
		for($i=0; $i < $numCats; $i++) {
		
			$catID = $catArray[$i];

			$query = "UPDATE superhome SET date_created= now(), json_order=".$i." WHERE publication_id=".$publication_id." AND id=".$catID;
			$this->db->query($query);
//		echo $query."<br/>";
			if($i == $numCats-1)
			{
				echo 'success';exit;
			}
		}
		echo 'success';exit;
	}

	function changeStatus($catId,$publication_id,$catStatus,$catLabel) {
		
			if(!isset($publication_id) || $publication_id == '') {
				echo 'ERROR: No Publication ID provided<br/><a href="javascript:history.go(-1);">&lt;&lt Go Back</a>';
				exit;
			}
	
			$query = "UPDATE category SET date_modified= now(), status='".$catStatus."' WHERE publication_id=".$publication_id." AND id=".$catId;
			$this->db->query($query);
			if($catStatus=='active')
			{
				 $return_msg = $catStatus.'##<a href="#" onclick="grabword(\'refcat\',\''.base_url().'category/changeStatus/'.$catId.'/'.$publication_id.'/inactive/'.$catLabel.'\',\''.$catLabel.'\');" class="tour_6"><img src="'.base_url().'/public/img/active.png" style="width: 25px;"></a>';
				 $return_msg.= '##';
				 $return_msg.='<a href="#" onClick="grabword(\'refcat\',\''.base_url().'/api/refreshcat/'.$catId.'\',\''.$catLabel.'\');" class="tour_4"><img src="'.base_url().'public/img/refresh.png" style="width: 20px;"/></a>';
				 $return_msg.='##'.$catId;
				
			}else if($catStatus=='inactive')
			{
			$return_msg = $catStatus.'##<a href="#" onclick="grabword(\'refcat\',\''.base_url().'category/changeStatus/'.$catId.'/'.$publication_id.'/active/'.$catLabel.'\',\''.$catLabel.'\');" class="tour_6"><img src="'.base_url().'/public/img/inactive.png" style="width: 25px;"></a>';
			$return_msg.= '##';
			$return_msg.='NA';
			$return_msg.='##'.$catId;
			}
			
			//$numCats = $_POST['num_cats'];
		$numCats = count($_POST['cat']);
		$publication_id = $_POST['publication_id'];
		$catArray = $_POST['cat'];
		//print_r($_POST);print_r($catArray);exit;
		for($i=0; $i < $numCats; $i++) {
		//	$inputID = 'cat_'.$i;
		//	$splitArray = preg_split('/__/',$_POST[$inputID]);
			$catID = $catArray[$i];

			$query = "UPDATE category SET date_modified= now(), ipad_catorder=".($i*10).",  cat_order=".$i." WHERE publication_id=".$publication_id." AND id=".$catID;
			$this->db->query($query);
//		echo $query."<br/>";
			
		}
			echo $return_msg;
			exit;
			//redirect('publication/manage/'.$publication_id);
		}


		function changecategorystatus(){
			// print '<pre>';
			// print_r($this->input->Post('publicationid'));
			// exit;
			if($this->input->post('publicationid') == '') {
				echo 'ERROR: No Publication ID provided<br/><a href="javascript:history.go(-1);">&lt;&lt Go Back</a>';
				exit;
			}
	
			$query = "UPDATE category SET date_modified= now(), status='".$this->input->post('status')."' WHERE publication_id=".$this->input->post('publicationid')." AND id=".$this->input->post('categoryid');
			$this->db->query($query);
			
			if($this->input->post('status') == 'active'){
				$this->session->set_flashdata('catupdate','Category ' .$this->input->post('label').' is Activated succsessfuly...');
			}else{
				$this->session->set_flashdata('catupdate','Category ' .$this->input->post('label').' is Deactivated succsessfuly...');
			}
			redirect('/publication/manage/'.$this->input->post('publicationid'));

		}

		//Super home
	public function superhome(){
		$cid = $this->input->post('categoryid');
		$pid = $this->input->post('publicationid');
		$label = $this->input->post('label');
		
		$data = array(
			'category_id'	=>$cid,
			'publication_id'=>$pid,
			'label'			=>$label,
			'date_created'	=>date('Y-m-d')

		);
		$query = $this->db->query("SELECT category_id FROM superhome WHERE category_id=$cid");
		$catid = $query->row();
		
		if($query->num_rows() >0){
			//delete
			$catid = $query->row();
			$this->db->delete('superhome', array('category_id' =>$catid->category_id));
			//print 'removed';
						
		}else{
			//insert
			$this->db->insert('superhome', $data);
			//print 'added';
			
		}
		redirect('/publication/manage/'.$this->input->post('publicationid'));
	}

	function deletesuperhome(){
		$this->db->where('id', $this->input->post('id'));
		$this->db->delete('superhome');
		redirect('/publication/superhome/'.$this->input->post('publicationid'));
	}



	
}
