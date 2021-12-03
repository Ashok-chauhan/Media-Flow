<?php
error_reporting(E_ALL & ~E_NOTICE);
class Api extends CI_Controller {

	private $_auth_user = null;
	private $_auth_key = null;
	private $_auth_token = null;
	private $_is_authenticated = false;

	var $_unaurthorized_header = "HTTP/1.0 401 Unauthorized";
	var $_no_content_header = "HTTP/1.0 204 No Content";

	function __construct() { 
        //parent::Controller('api');
        parent::__construct();
		$this->setupReq();
		$this->setAuth();
		//$this->httpauthentication();      // added on 27/02/2012 for basic authentication 
	}

	function sessionValid() {
		return false;
		if(is_null($this->_auth_token)) return false;

		$sessionUserObj = $this->session->userdata('user');
		if(isset($sessionUserObj['auth_token']) && $sessionUserObj['auth_token'] == $this->_auth_token) return true;

		return false;
	}

	function setAuth() {
		if(is_null($this->_auth_token)) {
			$this->_is_authenticated = false;
			return;
		} elseif($this->_auth_token === '') {
			$this->_is_authenticated = false;
			return;
		} else {
			//lookup user to auth
		}
	}

	function setupReq() { 
		(isset($_SERVER['HTTP_X_AUTH_USER'])) ? $this->_auth_user = $_SERVER['HTTP_X_AUTH_USER'] : $this->_auth_user = null;
		(isset($_SERVER['HTTP_X_AUTH_TOKEN'])) ? $this->_auth_token = $_SERVER['HTTP_X_AUTH_TOKEN'] : $this->_auth_token = null;

		$header_list = headers_list();
//		header_remove('Set-Cookie');
		header('Set-Cookie:');
		log_message('debug','REMOVING COOKIE in API->setupReq()');
	}

	function index() {
		$this->auth();
	}

	function auth() {
//		(isset($_SERVER['HTTP_X_AUTH_KEY'])) ? $this->_auth_key = $_SERVER['HTTP_X_AUTH_KEY'] : $this->_auth_key = null;

		//lookup user id
//		$this->db->where('last_name',$this->_auth_user);
//		$this->db->where('type','api');
//		$result = $this->db->get('user');
//		$user = $result->row_array();

//		if(!isset($user['id']) || $user['id'] =='') {
//			$this->notAuthenticated();
//		} else {
//			$this->db->where('status','active');
//			$this->db->where('key',$this->_auth_key);
//			$this->db->where('owner_id',$user['id']);
//			$result = $this->db->get('api');
//			$api = $result->row_array();
//			if(isset($api['key']) && $api['key'] !='') {
//				$auth_token = $this->session->userdata('session_id');
//				$user['auth_token'] = $auth_token;
//				$this->session->set_userdata('user', $user);
//				header($this->_no_content_header);
//				header('X-AUTH-TOKEN: '.$auth_token);
				header('X-API-URI: '.$this->getApiUri());
//			} else {
//				$this->notAuthenticated();
//			}
//		}
	}

	function notAuthenticated() {
		header($this->_unaurthorized_header);
		header('Content-Type: application/octet-stream');
		echo 'Wrong User/Key Information';
		echo "<pre>";
		print_r($_SERVER);
		echo "</pre>";
	}

	function getApiUri() {
		$apiUri = $this->config->item('api_uri');
		return $apiUri;
	}

	function publication() {
		$publication_id = $this->uri->segment(3);
		$dev = $this->uri->segment(4);
		
		if($dev !='' && $dev=='tablet'){
			//specific for any tablets.
		$query = "SELECT * FROM publication,category WHERE category.status='active' AND category.publication_id=publication.id AND publication.id='{$publication_id}' AND category.device='tablet' ORDER BY category.cat_order ASC";
		log_message('debug','********Calling publication with:'.$query);
		}elseif($dev !='' && $dev=='phone'){
			//specific for phone.
		$query = "SELECT * FROM publication,category WHERE category.status='active' AND category.publication_id=publication.id AND publication.id='{$publication_id}' AND category.device='phone' ORDER BY category.cat_order ASC";
		log_message('debug','********Calling publication with:'.$query);
		}elseif ($dev !='' && $dev=='both'){
			// for both devices tablet and phone.
		$query = "SELECT * FROM publication,category WHERE category.status='active' AND category.publication_id=publication.id AND publication.id='{$publication_id}' AND category.device='both' ORDER BY category.cat_order ASC";
		log_message('debug','********Calling publication with:'.$query);
		}else{
			//Default will abondon later.
			$query = "SELECT * FROM publication,category WHERE category.status='active' AND category.publication_id=publication.id AND publication.id='{$publication_id}' ORDER BY category.cat_order ASC";
		log_message('debug','********Calling publication with:'.$query);
		}
	

		
		
		$result = $this->db->query($query);
		$pubCatList = $result->result_array();
		////////////////// BOF GETTING EMAIL ID OF PUBLISHER  BY ASHOK ////////////////////
		
		$configobject = $result->row();
		////////////////// EOF GETTING EMAIL ID OF PUBLISHER  BY ASHOK ////////////////////
		$response = array();
		
		foreach($pubCatList as $catEntry) {
			$category = array();
			$category['id'] = $catEntry['id'];
			$category['orderflag'] = $catEntry['orderflag']; // KEEP FEED ORDER.
			$category['parent_id'] = $catEntry['parent_id'];
			$category['my_mf'] = $catEntry['my_mf'];
			$category['use_feature_story'] = $catEntry['feature_story'];
			$category['prod_code'] = $catEntry['prod_code'];
			$category['name'] = $catEntry['name'];
			$category['label'] = $catEntry['label'];
			$category['ipad_label'] = $catEntry['ipad_label'];
				
			///////// BOF ADDED BY ASHOK to fetch ad height ,width, ad invocation code for cat///
			$category['banner_ad_code'] = $catEntry['adcode'];
			$category['banner_ad_width'] = $catEntry['adwidth'];
			$category['banner_ad_height'] = $catEntry['adheight'];
			$category['atype'] = $catEntry['atype'];
			$category['parser_type'] = (empty($catEntry['parser_type']))? $catEntry['type'] : $catEntry['parser_type'];
			$category['uri'] = trim($catEntry['source_uri']);
			$category['device'] = $catEntry['device'];
			$category['ipad_catorder'] = $catEntry['ipad_catorder'];
			$category['cat_order'] = $catEntry['cat_order'];
			
			$category['category_color'] = $catEntry['catcolor'];
			$category['ipadtemplate'] = $catEntry['ipadtemplate'];
			$category['inline_ad_frequency'] = $catEntry['inline_ad_frequency'];
                        $category['extra'] = $catEntry['extra'];
                        $category['video_chat'] = $catEntry['video_chat'];
			////////  EOF ADDED BY ASHOK //////////
			
			//// bof dfp /////
			if($catEntry['cat_ipad_dfp_article_landscape']) $category['ipad_dfp_article_landscape']=$catEntry['cat_ipad_dfp_article_landscape'];
			if($catEntry['cat_ipad_dfp_article_portrait']) $category['ipad_dfp_article_portrait']=$catEntry['cat_ipad_dfp_article_portrait'];
			if($catEntry['cat_ipad_dfp_banner']) $category['ipad_dfp_banner']=$catEntry['cat_ipad_dfp_banner'];
			if($catEntry['cat_ipad_dfp_fullpage_landscape']) $category['ipad_dfp_fullpage_landscape']=$catEntry['cat_ipad_dfp_fullpage_landscape'];
			if($catEntry['cat_ipad_dfp_fullpage_portrait']) $category['ipad_dfp_fullpage_portrait']=$catEntry['cat_ipad_dfp_fullpage_portrait'];
			if($catEntry['cat_ipad_dfp_skyscraper']) $category['ipad_dfp_skyscraper']=$catEntry['cat_ipad_dfp_skyscraper'];
			if($catEntry['cat_fire_dfp_banner']) $category['fire_dfp_banner']=$catEntry['cat_fire_dfp_banner'];
			if($catEntry['cat_fire_dfp_small_banner']) $category['fire_dfp_small_banner']=$catEntry['cat_fire_dfp_small_banner'];
			if($catEntry['cat_fire_dfp_fullpage']) $category['fire_dfp_fullpage']=$catEntry['cat_fire_dfp_fullpage'];
			if($catEntry['cat_fire_dfp_fullpage_landscape']) $category['fire_dfp_fullpage_landscape']=$catEntry['cat_fire_dfp_fullpage_landscape'];
			if($catEntry['cat_phone_dfp_fullpage']) $category['phone_dfp_fullpage']=$catEntry['cat_phone_dfp_fullpage'];
			if($catEntry['cat_phone_dfp_banner']) $category['phone_dfp_banner']=$catEntry['cat_phone_dfp_banner'];
			
			if($catEntry['cat_banner_ads']) $category['banner_ads']=$catEntry['cat_banner_ads'];
			if($catEntry['cat_interstitial_ads']) $category['interstitial_ads']=$catEntry['cat_interstitial_ads'];
			if($catEntry['cat_video_ads']) $category['video_ads']=$catEntry['cat_video_ads'];
			if($catEntry['cat_native_ad']) $category['native_ad']=$catEntry['cat_native_ad'];
			//// eof dfp /////
			// category preroll  ads ///
			if($catEntry['cat_preroll_url_fire']) $category['preroll_url_fire']=$catEntry['cat_preroll_url_fire'];
			if($catEntry['cat_preroll_url_phone']) $category['preroll_url_phone']=$catEntry['cat_preroll_url_phone'];
			if($catEntry['cat_preroll_url_ipad']) $category['preroll_url_ipad']=$catEntry['cat_preroll_url_ipad'];
			//// category preroll ads eof ///

			
			//$category['uri'] = $this->config->item('base_url').'/api/publication/'.$publication_id.'/category/'.$catEntry['id'];

			/***
			$preproto = substr($catEntry['icon_filename'],0,7);
			
			if($preproto !='http://'){
				$category['icon_uri'] = $this->config->item('icon_base_url').'/'.$catEntry['icon_filename'];
				
			}else{
				$category['icon_uri'] = $catEntry['icon_filename'];
			}
			*/

			//Displying category icon.
			if($catEntry['icon_filename']){
				$category['icon_uri'] = $this->config->item('icon_base_url').'/'.$catEntry['icon_filename'];
			}else{
				$category['icon_uri'] = '';
				}

			$response['response']['categories'][] = $category;

			
			$story_limit = $this->getById($catEntry['id'],'category');
			if(isset($_REQUEST['limit']) && $_REQUEST['limit'] != '' ){
			$limit = $_REQUEST['limit'];
			}elseif($story_limit['story_limit'] > 0){
			$limit = $story_limit['story_limit'];
			}else{ $limit = $this->config->item('default_list_limit');}

		#########################################
		# CHECKING WHEATHER CLIENT'S FEED ORDER #
		#SHOULD MAINTAINED OR NOT WITH THE HELP #
		# OF keep_order METHOD 30/08/2011       #
		#########################################
	if($category['orderflag']){
			// RESPONSE EXACT ORDER AS IN CLIENT'S FEED
			$query = 'SELECT * FROM content WHERE category_id='.$category['id'].' AND parent_id IS NULL ORDER BY priority LIMIT 0,'.$limit;
			log_message('debug','********Getting category '.$category['id'].' with query:'.$query);
			$catResult = $this->db->query($query);
		
	}else{
			
			// RESPONSE ACCORDING TO PUB_DATE DESC.
			$query = 'SELECT * FROM content WHERE category_id='.$category['id'].' AND parent_id IS NULL ORDER BY pub_date DESC LIMIT 0,'.$limit;
			log_message('debug','********Getting category '.$category['id'].' with query:'.$query);
			$catResult = $this->db->query($query);
	}
			// Multimedia handling in story & galleries.
	$contents = $catResult->result_array();
            foreach ($contents as &$story){
                if($story['type'] == 'story' || $story['type'] == 'gallery'){
                    $this->db->where('content_id', $story['id']);
                    $this->db->select('thumbnail,media,caption,type');
                    $Q = $this->db->get('content_media');
                    $media = $Q->result();
                    $story['media'] = $media;

                }
            }

			//getting sponsored ad.
			
				$sponsored = $this->sponsoredAd($category['id']);
				if($sponsored){
					array_splice($contents, $sponsored[0]['slot'], 0, $sponsored);
				}
			
		//eof soponsored.
            $response['response']['content'][$category['name']] = $contents; 

	//$response['response']['content'][$category['name']] = $catResult->result_array();
	
	// bof ott autoplay added on 04/10/2016//
	$ottcat = array();
	if($catEntry['autoplay']){
		$ott['type'] = 'category';
		$ott['id'] = $catEntry['id'];
		$catautoplay[]= $ott;
	}
	$ottcontent= array();
	$ottquery = 'SELECT assetid from content WHERE category_id='.$category['id'].' AND sticky=1';
	$ottq = $this->db->query($ottquery);
	$ottresult = $ottq->result_array();
		foreach($ottresult as $ottvideo){
			$ottcontent['type'] = 'video';
			$ottcontent['id']	= $ottvideo['assetid'];
			$catautoplay[] = $ottcontent;
		}
	// eof ott autoplay //
			
}

		//// bof dfp ////
		if($configobject->ipad_dfp_article_landscape) $response['response']['config']['ipad_dfp_article_landscape'] = $configobject->ipad_dfp_article_landscape;
		if($configobject->ipad_dfp_article_portrait) $response['response']['config']['ipad_dfp_article_portrait'] = $configobject->ipad_dfp_article_portrait;
		if($configobject->ipad_dfp_banner) $response['response']['config']['ipad_dfp_banner'] = $configobject->ipad_dfp_banner;
		if($configobject->ipad_dfp_fullpage_landscape) $response['response']['config']['ipad_dfp_fullpage_landscape'] = $configobject->ipad_dfp_fullpage_landscape;
		if($configobject->ipad_dfp_fullpage_portrait) $response['response']['config']['ipad_dfp_fullpage_portrait'] = $configobject->ipad_dfp_fullpage_portrait;
		if($configobject->ipad_dfp_skyscraper) $response['response']['config']['ipad_dfp_skyscraper'] = $configobject->ipad_dfp_skyscraper;
		if($configobject->fire_dfp_banner) $response['response']['config']['fire_dfp_banner'] = $configobject->fire_dfp_banner;
		if($configobject->fire_dfp_small_banner) $response['response']['config']['fire_dfp_small_banner'] = $configobject->fire_dfp_small_banner;
		if($configobject->fire_dfp_fullpage) $response['response']['config']['fire_dfp_fullpage'] = $configobject->fire_dfp_fullpage;
		if($configobject->fire_dfp_fullpage_landscape) $response['response']['config']['fire_dfp_fullpage_landscape'] = $configobject->fire_dfp_fullpage_landscape;
		if($configobject->phone_dfp_fullpage) $response['response']['config']['phone_dfp_fullpage'] = $configobject->phone_dfp_fullpage;
		if($configobject->phone_dfp_banner) $response['response']['config']['phone_dfp_banner'] = $configobject->phone_dfp_banner;
		$response['response']['config']['dfp_ads'] = $configobject->dfp_ads;
		$response['response']['config']['article_ads'] = $configobject->article_ads;
		if($configobject->offline_mode_enabled) $response['response']['config']['offline_mode_enabled'] = $configobject->offline_mode_enabled;

		//// eof dfp ////
		
		$response['response']['config']['photos_per_ad'] = $configobject->photo_per_ad;
		$response['response']['config']['videos_per_ad'] = $configobject->video_per_ad;
		$response['response']['config']['fullpage_width'] = $configobject->full_page_ad_width;
		$response['response']['config']['fullpage_height'] = $configobject->full_page_ad_height;
		$response['response']['config']['fullpage_adcode'] = $configobject->full_page_ad_code;

		// BOF IPAD CONFIGURATION.
		$response['response']['config']['ipad_banner_adcode'] = $configobject->ipad_banner_adcode;
		$response['response']['config']['ipad_banner_width'] = $configobject->ipad_banner_width;
		$response['response']['config']['ipad_banner_height'] = $configobject->ipad_banner_height;

		$response['response']['config']['ipad_skyscraper_adcode'] = $configobject->ipad_skyscraper_adcode;
		$response['response']['config']['ipad_skyscraper_width'] = $configobject->ipad_skyscraper_width;
		$response['response']['config']['ipad_skyscraper_height'] = $configobject->ipad_skyscraper_height;

		$response['response']['config']['ipad_fullpage_landscape_adcode'] = $configobject->ipad_fullpage_landscape_adcode;
		$response['response']['config']['ipad_fullpage_landscape_width'] = $configobject->ipad_fullpage_landscape_width;
		$response['response']['config']['ipad_fullpage_landscape_height'] = $configobject->ipad_fullpage_landscape_height;

		$response['response']['config']['ipad_fullpage_portrait_adcode'] = $configobject->ipad_fullpage_portrait_adcode;
		$response['response']['config']['ipad_fullpage_portrait_width'] = $configobject->ipad_fullpage_portrait_width;
		$response['response']['config']['ipad_fullpage_portrait_height'] = $configobject->ipad_fullpage_portrait_height;
		// EOF IPAD CONFIGURATION.

		// BOF KINDLE FIRE CONFIGURATION.
		//$response['response']['config']['fire_banner_ad'] = $configobject->fire_banner_ad;
		$response['response']['config']['fire_banner_ad_width'] = $configobject->fire_banner_ad_width;
		$response['response']['config']['fire_banner_ad_height'] = $configobject->fire_banner_ad_height;

		$response['response']['config']['fire_fullpage_ad'] = $configobject->fire_fullpage_ad;
		$response['response']['config']['fire_fullpage_ad_width'] = $configobject->fire_fullpage_ad_width;
		$response['response']['config']['fire_fullpage_ad_height'] = $configobject->fire_fullpage_ad_height;

		$response['response']['config']['fire_fullpage_landscape_ad'] = $configobject->fire_fullpage_landscape_ad;
		$response['response']['config']['fire_fullpage_landscape_ad_width'] = $configobject->fire_fullpage_landscape_ad_width;
		$response['response']['config']['fire_fullpage_landscape_ad_height'] = $configobject->fire_fullpage_landscape_ad_height;
		// EOF KINDLE FIRE CONFIGURATION.

		if($configobject->type == 'webkit'){
		$response['response']['config']['webkit_uri'] = $configobject->source_uri;
		}
		$response['response']['config']['isay_email'] = $configobject->email;
		$response['response']['config']['video_ad_key'] = $configobject->video_ad_key;
		$response['response']['config']['banner_bg_color'] = $configobject->banner_color;
		$response['response']['config']['banner_font_color'] = $configobject->banner_font_color;
		$response['response']['config']['list_view_bg_color'] = $configobject->list_v_color;
		$response['response']['config']['list_view_font_color'] = $configobject->list_view_font_color;
		$response['response']['config']['list_view_detail_color'] = $configobject->list_view_detail_color;
		$response['response']['config']['title_bg_color'] = $configobject->title_color;
		$response['response']['config']['title_font_color'] = $configobject->title_font_color;
		$response['response']['config']['featured_story_height'] = $configobject->featured_height;
		$response['response']['config']['publication_wsm_name'] = $configobject->publication_wsm_name;
		$response['response']['config']['flurry_key_android'] = $configobject->flurrykey;
		$response['response']['config']['flurry_key_fire'] = $configobject->flurrykey_fire;
		$response['response']['config']['flurry_key_iphone'] = $configobject->flurrykey_iphone;
		$response['response']['config']['flurry_key_ipad'] = $configobject->flurrykey_ipad;
		$response['response']['config']['facebook_app_id'] = $configobject->facebook_app_id;
		$response['response']['config']['facebook_secret_code'] = $configobject->facebook_secret_code;
		$response['response']['config']['twitter_consumer_key'] = $configobject->twitterkey;
		$response['response']['config']['twitter_secret_code'] = $configobject->twitter_secret_code;
		$response['response']['config']['twitter_callback_url'] = $configobject->twitter_callback_url;
		$response['response']['config']['twitter_handle'] = $configobject->twitter_handle;
		$response['response']['config']['common_cat_banner_adcode'] = $configobject->cat_common_adcode;
		$response['response']['config']['common_cat_banner_ad_width'] = $configobject->cat_common_ad_width;
		$response['response']['config']['common_cat_banner_ad_height'] = $configobject->cat_common_ad_height;
		$response['response']['config']['zipcode'] = $configobject->zipcode;
		$response['response']['config']['feedback_email'] = $configobject->feedback_email;
		$response['response']['config']['terms_conditions_url'] = $configobject->terms_conditions;
		$response['response']['config']['privacy_policy_url'] = $configobject->privacy_policy;
		$response['response']['config']['login_stub_code'] = $configobject->login_stub_code;
		$response['response']['config']['auth_stub_code'] = $configobject->auth_stub_code;
		$response['response']['config']['weather_url'] = $configobject->weather_url;
		
		// login url (paywall) on /off switch.
		
		if($configobject->paywall){
		$response['response']['config']['login_url'] = $configobject->login_url;
		$response['response']['config']['login'] = $configobject->login;
			}else{
			$response['response']['config']['login_url'] ='';
			$response['response']['config']['login'] ='';
					
		}
		//$response['response']['config']['refresh_token_url'] = $configobject->refresh_token_url;
		$response['response']['config']['publisher_website'] = $configobject->publisher_website;


		$response['response']['config']['copyright'] = $configobject->copyright;
		$response['response']['config']['google_analytics_key_android'] = $configobject->google_analytics_key_android;
		$response['response']['config']['google_analytics_key_fire'] = $configobject->google_analytics_key_fire;
		$response['response']['config']['google_analytics_key_iphone'] = $configobject->google_analytics_key_iphone;
		$response['response']['config']['google_analytics_key_ipad'] = $configobject->google_analytics_key_ipad;
		
		$response['response']['config']['iphone_omniture_account_name'] = $configobject->iphone_omniture_account_name;
		$response['response']['config']['ipad_omniture_account_name'] = $configobject->ipad_omniture_account_name;
		
		//$response['response']['config']['android_omniture_account_name'] = //$configobject->android_omniture_account_name;

		$response['response']['config']['fire_omniture_account_name'] = $configobject->fire_omniture_account_name;
		$response['response']['config']['iphone_omniture_tracking_server'] = $configobject->iphone_omniture_tracking_server;
		$response['response']['config']['ipad_omniture_tracking_server'] = $configobject->ipad_omniture_tracking_server;

		//$response['response']['config']['android_omniture_tracking_server'] = //$configobject->android_omniture_tracking_server;
		//$response['response']['config']['fire_omniture_tracking_server'] = //$configobject->fire_omniture_tracking_server;
		$response['response']['config']['banner_ads'] = $configobject->banner_ads;
		$response['response']['config']['interstitial_ads'] = $configobject->interstitial_ads;
		$response['response']['config']['video_ads'] = $configobject->video_ads;
		$response['response']['config']['push_notification'] = $configobject->push_notification;

		
		$response['response']['config']['login_stub_code_android'] = $configobject->login_stub_code_android;
		$response['response']['config']['auth_stub_code_android'] = $configobject->auth_stub_code_android;
		$response['response']['config']['android_inapp_parsing'] = $configobject->android_inapp_parsing;
		$response['response']['config']['paper_name'] = $configobject->paper_name;
		$response['response']['config']['webtrends_android'] = $configobject->webtrends_android;
		$response['response']['config']['webtrends_fire'] = $configobject->webtrends_fire;
		$response['response']['config']['webtrends_iphone'] = $configobject->webtrends_iphone;
		$response['response']['config']['webtrends_ipad'] = $configobject->webtrends_ipad;
		$response['response']['config']['breakingnews_url'] = $configobject->breakingnews_url;
		$response['response']['config']['breakingnews_product_code'] = $configobject->breakingnews_product_code;
		if($configobject->faq_url !=' ') $response['response']['config']['faq_url'] = $configobject->faq_url;
		if($configobject->login_help_url !=' ') $response['response']['config']['login_help_url'] = $configobject->login_help_url;
		if($configobject->preroll_url_fire) $response['response']['config']['preroll_url_fire'] = $configobject->preroll_url_fire;
		if($configobject->preroll_url_phone) $response['response']['config']['preroll_url_phone'] = $configobject->preroll_url_phone;
		if($configobject->preroll_url_ipad) $response['response']['config']['preroll_url_ipad'] = $configobject->preroll_url_ipad;
		if($configobject->tabbed_categories) $response['response']['config']['tabbed_categories'] = $configobject->tabbed_categories;
		if($configobject->search_url) $response['response']['config']['search_url'] = $configobject->search_url;
		if($configobject->search_product_code) $response['response']['config']['search_product_code'] = $configobject->search_product_code;
		if($configobject->paymeter_config_ios) $response['response']['config']['paymeter_config_ios'] = $configobject->paymeter_config_ios;
		if($configobject->inapp_params) $response['response']['config']['inapp_params'] = $configobject->inapp_params;
		if($configobject->paymeter_config_android) $response['response']['config']['paymeter_config_android'] = $configobject->paymeter_config_android;
		if($configobject->native_ads) $response['response']['config']['native_ads'] = $configobject->native_ads;
		if($configobject->ios_colorscheme) $response['response']['config']['ios_colorscheme'] = $configobject->ios_colorscheme;
		if($configobject->timeline) $response['response']['config']['timeline'] = $configobject->timeline;
		//if($configobject->live_video_url) $response['response']['config']['live_video_url'] = $configobject->live_video_url;
		$response['response']['config']['live_video_url'] = $configobject->live_video_url;
		$response['response']['config']['live_video_preroll_ad'] = $configobject->live_video_preroll_ad;
		if($configobject->forgot_password_url !=' ') $response['response']['config']['forgot_password_url'] = $configobject->forgot_password_url;
		// Ott Live Frame advertise added on 11/4/2017.
		$response['response']['config']['live_frame_ads'] = $configobject->liveframeads;
		if($configobject->dfp_foreground) $response['response']['config']['dfp_foreground'] = $configobject->dfp_foreground;
		$response['response']['config']['foreground_time_limit'] = $configobject->foreground_time_limit;
		if($configobject->read_story_url) $response['response']['config']['read_story_url'] = $configobject->read_story_url;
		if($configobject->alerts_table_url) $response['response']['config']['alerts_table_url'] = $configobject->alerts_table_url;
		if($configobject->weather_json) $response['response']['config']['weather_native_url'] = $configobject->weather_json;
		$response['response']['config']['share_story_email'] = $configobject->share_story_email;
        if($configobject->image_resizer_url) $response['response']['config']['image_resizer_url'] = $configobject->image_resizer_url;
        if($configobject->extra) $response['response']['config']['extra'] = $configobject->extra;
        if($configobject->user_consent_screen) $response['response']['config']['user_consent_screen'] = $configobject->user_consent_screen;
		if($configobject->myhome) $response['response']['config']['myhome'] = $configobject->myhome;
		
		$response['response']['config']['ccpa_email'] = $configobject->ccpa_email;
		$response['response']['config']['ccpa_display'] = $configobject->ccpa_display;				
		$response['response']['config']['about_text'] = $configobject->about;
		//server timezone in UTC format - added on July 10 2012
        $response['response']['config']['timezone'] = date('O'); 
		
		///////// EOF ADDED BY ASHOK///////////////////
		//$response['response']['config']['video_ad_key'] = 'Video*Preroll*Ads';

		// ott video autoplay added on 04/10/2016//
		if(isset($catautoplay)){
			foreach($catautoplay as $key =>$autoplay){
			$response['response']['autoplay'][]=$autoplay;
			$response['response']['config']['autoplay'][]=$autoplay;
			}
		}
		
		
		//$json_response = json_encode($response);
		//echo $json_response;
		if(isset($_REQUEST['format']) && $_REQUEST['format'] =='xml'){
            $xml_response = $this->xmlOut($response['response']['categories'], $response['response']['config']);
			$this->_sendResponse(200, 'application/xml', $xml_response, $publication_id);  
        } else {
            $json_response = json_encode($response);
			$this->_sendResponse(200, 'application/json', $json_response, $publication_id);  
            //echo $json_response;
        }

	}

	function category() {
		
		$response = array();
			$category_id = $this->uri->segment(3);
			// BOF LIMIT STUFF //		
			if(!isset($_REQUEST['parent_id'])){
				$story_limit = $this->getById($category_id,'category');
			}else{
				$photo = $this->getById($_REQUEST['parent_id'],'content');
				$photo_limit = $this->getById($photo['category_id'],'category');
			}
			if(isset($_REQUEST['limit']) && $_REQUEST['limit'] != '' ){
			$limit = $_REQUEST['limit'];
			}elseif(isset($story_limit['story_limit']) && $story_limit['story_limit'] > 0){ 
			$limit = $story_limit['story_limit'];
			}elseif(isset($photo_limit['photo_limit']) && $photo_limit['photo_limit'] > 0){
				$limit = $photo_limit['photo_limit'];
			}else{ $limit = $this->config->item('default_list_limit');}
			// EOF LIMIT STUFF //

		if($category_id !='') {
			$this->db->where('category_id',$category_id);
			$this->db->where('parent_id',NULL);
			//CHECKING WHEATHER CLIENT'S FEED ORDER SHOULD maintened OR NOT BY keep_order 30/08/2011.
			if($this->keep_order($category_id)){
				$this->db->order_by('priority');
				//$this->db->where('priority >0'); //commented on 19/8/2016 due to 0rth recored not comprises in category api from content table.
			}else{
				$this->db->order_by('pub_date DESC');
			}

		
		}else{
			
			$parent_id = $_REQUEST['parent_id'];
			$this->db->where('parent_id',$parent_id);
			$this->db->order_by('pub_date DESC');
		}
	$this->db->limit($limit);
	$catResult = $this->db->get('content');
		
////////////// BOF STORIY MEDIA ////////////////////////////////////////////////////////

$contents = $catResult->result_array();
        foreach ($contents as &$story){

            if($story['type'] == 'story' 
				|| $story['type'] == 'gallery'
				|| $story['type'] == 'audio/mpeg'
				|| $story['type'] == 'audio'){
                $this->db->where('content_id', $story['id']);
                $this->db->select('thumbnail,media,caption,type, duration');
				if(isset($story_limit['photo_limit']) && $story_limit['photo_limit'] > 0 ){
					$limit = $story_limit['photo_limit'];
				}else{
					$limit = $this->config->item('default_list_limit');
				}
				$this->db->limit($limit);
				$this->db->order_by('media_order');
                $Q = $this->db->get('content_media');
                $media = $Q->result();
               
				//to add audio icon for kusports app(if icon absent).
				$media = $this->kuSportsAudioThumbnail($media, $story_limit['publication_id']);
                $story['media'] = $media;

            }
        }

///////////// EOF STORY MEDIA///////////////////////////////////////////////////////////
	
		##$response['response']['content'] = $catResult->result_array();
		//getting sponsored ad.
		$sponsored = $this->sponsoredAd($category_id);
		if($sponsored){
			array_splice($contents, $sponsored[0]['slot'], 0, $sponsored);
		}
		
		$response['response']['content'] = $contents;
		

		header('Content-type: application/json; charset=UTF-8');
		header('Content-Language: en');
		echo json_encode($response);
		
		
}

	/*
	 * this function will update all the contents for the given category
	 */
	function refreshcat($catId='') { 
		if($catId == '')
			$category_id = $this->uri->segment(3);
		else
			$category_id = $catId;

		$category = $this->getById($category_id,'category');

        // Tom 3/25/2019
        // put a transaction/commit around wipe and update content
        // fixes issue when api/category request comes after wipe and before refresh update

        $start_time = microtime(true);

        if (strcmp('cumedia_inforum_json', $category['type']) != 0
            && strcmp('webkit', $category['type']) != 0) {
            $this->db->query('START TRANSACTION;');

            //$this->db->query('SET autocommit=0;');
            //$this->db->query('LOCK TABLES content WRITE, content_media WRITE;');
        }

        // END
        if (strcmp('cumedia_inforum_json', $category['type']) != 0
            && strcmp('webkit', $category['type']) != 0) {
            $this->wipecontent($category_id); // wiping old data if delflag 1 of category
        }
		
        //error_log('Category: ' . $category['id'] . ' ' . $category['type']);

		$categoryName = ucfirst($category['type']);

		require_once(BASEPATH.'libraries/CategoryHandlers/'.$categoryName.'.php');
		$handlerClass = $categoryName.'Handler';
		
		$catHandler = new $handlerClass($category);
		//log_message('debug','rereshCat on category_id:'.$category_id.'-- type:'.$handlerClass.'-- label:'.$category['label'].' publication :'.$category['publication_id']);
		//log_message('debug','handler libraries/CategoryHandlers/'.$categoryName.'.php');
		log_message('debug','********Calling rereshCat on category_id:'.$category_id);
		
		$catHandler->refreshContent($category_id);
        // Tom 3/25/2019

        $delta_time = microtime(true) - $start_time;

        //error_log('Publication: ' . $category['publication_id']);
        //error_log('Category:    ' . $category_id);
        error_log('Locked: ' . $category['type'] . ' ' . $category['publication_id'] . ' ' . $category_id . ' ' . number_format($delta_time, 3, '.', ','));

        if (strcmp('cumedia_inforum_json', $category['type']) != 0
            && strcmp('webkit', $category['type']) != 0) {
            $this->db->query('COMMIT;');

            //$this->db->query('COMMIT;');
            //$this->db->query('UNLOCK TABLES;');
        }
        // END
       // $this->session->set_flashdata('catupdate','Category ' .$category['label'].' is Refreshed succsessfuly...');
	//	redirect('/publication/manage/'.$category['publication_id']);
	}

	/*
	 * this function will update all the contents for the given category
	 */
	function refreshpub() {
		$publication_id = $this->uri->segment(3);
		$this->db->where('publication_id',$publication_id);
		$this->db->where('status','active');
		$result = $this->db->get('category');
		$categories = $result->result_array();

		foreach($categories as $category) {
			//$this->wipecontent($category['id']); // wiping old data if delflag 1 of category //stopped on 4/11/2016
			$this->refreshcat($category['id']);
		}
	}

	///////////////////////// BOF ADDED BY ASHOK FOR ISAY//////

	function isay() {
		if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
			
			//$input = file_get_contents("php://input");
		
			$this->MIsay->addstory();
		
			$restresponse['response'] = 'Successfully Submitted';
			echo json_encode($restresponse);
			
		}else{
			$restresponse['response'] = 'Error Not Submitted';
		echo json_encode($restresponse);
			
		}
	}
	
	function clearcatcontent(){ 
		if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
			$input = file_get_contents("php://input");
			
			if(filter_var($input, FILTER_VALIDATE_INT)){
						///// delete all associated data for category_id ////
					$this->db->where('category_id', $input);
					$q = $this->db->get('content');
					
					if($q->num_rows() > 0){
					$this->db->where('category_id', $input);
					$this->db->delete('content');
					
					$restresponse['response'] = 'Successfully Deleted';
					echo json_encode($restresponse);
					}else{
					$restresponse['response'] = 'There is no any Records are associated with this Category';
					echo json_encode($restresponse);
					}
						/////////////////////
								
			}else{
				$restresponse['response'] = 'Error Not Deleted';
				echo json_encode($restresponse);
				
			}
		}

	}

	///////////// ADED BY ASHOK TO GET JSON LIST OF PUBLICATIONS 17-09-2010 //////////

	function publist(){
		
		$group_id = $this->uri->segment(3);
		if ($group_id >0){
			//Commented on 18/3/2020 since SaltWire need paper_name instead name.
			//$query = "select id,name,pub_city,pub_state,latitude,longitude,splash_screen,logo,company_icon from publication where group_id = '{$group_id}'";
			$query = "select id,paper_name AS name ,pub_city,pub_state,latitude,longitude,splash_screen,logo,company_icon from publication where group_id = '{$group_id}'order by name asc";
			
			log_message('debug','********Calling publist with:'.$query);
			$result = $this->db->query($query);
			//$pubs = $result->row_array();
			$pubs = $result->result_array();
			$response = array();
			$response['response']['publications'] = $pubs;

				$json_response = json_encode($response);
				echo $json_response;
		}else{
				//Commented on 18/3/2020 since SaltWire need paper_name instead name.
				//$query = "select id,name,pub_city,pub_state,latitude,longitude,splash_screen,logo,company_icon from publication";
				$query = "select id,paper_name AS name ,pub_city,pub_state,latitude,longitude,splash_screen,logo,company_icon from publication";
				log_message('debug','******Calling publist with :'.$query);
				$result = $this->db->query($query);
				$pubs = $result->result_array();
				$response = array();
				$response['response']['publications'] = $pubs;
				
				$json_response = json_encode($response);
				echo $json_response;
			}
		}


	/*
	* added by Ashok to delete records before adding new 03/11/2010
	*
	*/
	function wipecontent($category_id=''){
		if($category_id == '') return false;
		$this->db->where('id', $category_id);
		$result = $this->db->get('category');
		$cat = $result->row_array();

		if($cat['delflag'] == 1){
			$this->db->where('category_id', $category_id);
			$this->db->delete('content');
			//// deleting stories media associated with this category.
			$this->wipecontentMedia($category_id);
		}

	}

	/*
	*
	* EOF deleting records before adding new 
	*/


	/*
	* to delete records from content_media when wipecontent is true.
	*
	*/
	function wipecontentMedia($category_id=''){
		$this->db->where('category_id', $category_id);
		$this->db->delete('content_media');
	}




/*
* This api call returns multiplae categories contents of specified No. of categories.
* categories is separeted by (undersocre _ ).
* paream category id separated by underscore (_) .
* grouped with category id in content object in JSON feed .
*/

function categories() {
		$response = array();
		
		
		if(!isset($_REQUEST['parent_id'])){
		$story_limit = $this->getById($this->uri->segment(3),'category');
		}
		if(isset($_REQUEST['limit']) && $_REQUEST['limit'] != '' ){
		$limit = $_REQUEST['limit'];
		}elseif(isset($story_limit['story_limit']) && $story_limit['story_limit'] > 0){ 
		$limit = $story_limit['story_limit'];
		}else{ $limit = $this->config->item('default_list_limit');}


		if($this->uri->segment(3)){
		$category_id = $this->uri->segment(3);
		/////////////////////////////////////
		$ids = explode('_',$category_id);
		
		$response = array();
		foreach($ids as $category_id){
			$category['id'] = $category_id;

		

				if(isset($_REQUEST['parent_id'])){ 
			$this->db->limit($limit);
			$parent_id = $_REQUEST['parent_id'];
			$this->db->where('parent_id',$parent_id);
			$this->db->order_by('pub_date DESC');
			$catResult = $this->db->get('content');
			$response['response']['content'][$category['id']] = $catResult->result_array();


			}else{ 

			$this->db->where('category_id',$category_id);
			$this->db->where('parent_id',NULL);
			$this->db->limit($limit);
		#########################################
		# CHECKING WHEATHER CLIENT'S FEED ORDER #
		# IS SHOUD MAINTEN OR NOT WITH THE HELP #
		# OF keep_order METHOD   30/08/2011     #
		#########################################
			if($category_id !=''){
				if($this->keep_order($category_id)){
					$this->db->order_by('priority');
				}else{
					$this->db->order_by('pub_date DESC');
				}	
			}
			$catResult = $this->db->get('content');
			$response['response']['content'][$category['id']] = $catResult->result_array();


				}


			

		}
	}
		$json_response = json_encode($response);
		echo $json_response;

}

/*
* geting publication id from category id.
* this is for internaal use only.
*/
function get_publication($cid)
	{
		$sql=("SELECT publication_id from category where id={$cid}");
		$result = $this->db->query($sql);
		if($result->num_rows()>0){
		$storyEntry = $result->row_array();
		return $storyEntry['publication_id'];
		}else{
			return false;
		}
	}

/*
* this api call is used to remotly authenticate Publisher.
* @param: email, password.
* response : OK , publication not found.
*/
function getPublication(){
	if(isset($_REQUEST['email']) && $_REQUEST['email'] !='' && isset($_REQUEST['password']) && $_REQUEST['password'] !=''){
			$email = $_REQUEST['email'];
			$password = md5($_REQUEST['password']);

			$this->db->where('email',$email);
			$this->db->where('password',$password);
			$this->db->where('type','publisher');
			$this->db->where('status','active');
			$this->db->limit(1);

			$result = $this->db->get('user');
			$row = $result->row_array();
			if($result->num_rows() > 0){
			
			$this->_sendResponse(200, 
                print($row['pub_id']) );
				
			}else{
				$this->_sendResponse(401, 
                print('Unauthorized'));
				
			}
	}else{
		 $this->_sendResponse(400, 
			 print('Error: Parameter is missing') );
		//print 'Erorr';
	}

}

/**
* getting featured flag which is configured publication wise.
* param category id .
*/

function get_featured($id){
	$publicationid = $this->get_publication($id);
	if(!$publicationid) return false;
	$sql = ("SELECT featured from publication WHERE id ={$publicationid} AND featured = 1");
	$result = $this->db->query($sql);
	
	if($result->num_rows() >0){
	$featured = $result->row_array();
	return $featured['featured'];
	}else{
		return false;
	}
	
}


/*
* GETTING CONTENT FORM CAONTENT TABLE BASED ON CATEGORY ID .
* PAREM: CATEGORY ID.
* OPTIONAL PARAM type i.e gallery , photo, video to exclude form query.
*/

function get_content_data($cid)
	{
				
		$sql=("SELECT type from content where category_id={$cid}");
		$result = $this->db->query($sql);
		$storyEntry = $result->row_array();
		
		if(isset($storyEntry['type'])){
			return $storyEntry['type'];
		}
	}

/*
* eof of Commercial Appeal
*/

function iphonetoken(){
	//added on 31-03-2011
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$tok = file_get_contents("php://input");

				$tokens = explode(';',$tok);

				$data = array(
					'iphone_token' => $tokens[0],
					'pub_id' => $tokens[1],
					'app_id' => $tokens[2]
					);

				if(!empty($tok)){
					$this->db->where('iphone_token',$tokens[0]);
					$this->db->where('pub_id',$tokens[1]);
					$q = $this->db->get('devices');
					$id = $q->row_array();
					
						if($q->num_rows()){

							$this->db->where('id',$id['id']);
							$this->db->update('devices',$data);
							// execute  and return the results
							
						/*	header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
							header('Content-Type: text/plain');
							print json_encode($data);

							*/
			//////////////// log ......./////
		$msg ='iphoneToken ' .$tokens[0]. ' app_id ' .$tokens[2]. ' pub_id ' .$tokens[1]. ' TIME--- ' .date('Y:m:d:H:i:s');
						// creating log file for testing.
						//$fp = fopen('/var/www/prodman.whizti.com/images/publishers/device.txt', 'a+');
						//fwrite($fp,$msg."\n");
						//fclose($fp);
		/////////// eof log ///////////////////////////

							
							
						}else{
							$this->db->insert('devices',$data);
							// execute  and return the results
							if($this->db->affected_rows()){
							
						/*	header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
							header('Content-Type: text/plain');
							print json_encode($data);

							*/

		//////////////// log ......./////
		$msg ='iphoneToken ' .$tokens[0].' app_id ' . $tokens[2]. ' pub_id ' .$tokens[1].' TIME--- ' .date('Y:m:d:H:i:s');
						// creating log file for testing.
						//$fp = fopen('/var/www/prodman.whizti.com/images/publishers/device.txt', 'a+');
						//fwrite($fp,$msg."\n");
						//fclose($fp);
		/////////// eof log ///////////////////////////

							}else{
								echo mysql_errno();
							}

						}
				}
			}


	}
/*
* app Absolsensce
* added on 05/04/2011
*/

function appok()
	{
	$device = $this->uri->segment(3);
	$appver = $this->uri->segment(4);
		
	$oldversion = array(
	'status'=>'410 Gone',
	'version'=>$appver,
	'message'=>'We have released a new and improved App. We request you to download the new app from the appstore and start using the new App. Thank you.'
);

	$ok = array('status'=>'200 OK');
	
if($device !='' && $appver !='')
	{
		$devicelist = array('html5','iphone','ipad','atab','android');
			if(in_array($device,$devicelist))
				{
				if($appver !=0.9)
				{
					echo json_encode($ok);
				}else{

					echo json_encode($oldversion);
				}

			}else{
				return false;
			}


	}else {
		return false;
	}
}
	



/**
* this is used to get priority field from category
* to get flage , ensure that preserving feed order.
* @parem category id
*/
/*private function getCategory($id){
		$query= "Select orderflag, story_limit from category WHERE id =".$id;
		$Q = $this->db->query($query);
		return $Q->row_array();
}
*/

//////////////////////// HTTP BASIC AUTHENTICATION BOF 30/05/2011////////////////////////
function httpauthentication(){

if (!$this->auth_validate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ) {
    header('WWW-Authenticate: Basic realm="api"');
    header('HTTP/1.0 401 Unauthorized');
    echo "You need to enter a valid username and password.";
    exit;
}

}

function auth_validate($user,$pass) {
    
	$this->db->where('first_name',$user);
	$this->db->where('password',md5($pass));
	$result = $this->db->get('user');
	$users = $result->row_array();

if($result->num_rows()){ 

    if ($users['first_name']===$user  && $users['password']===md5($pass)) {
		
        return true;
    } else {
		
        return false;
    }
}

}
///////////////////////// HTTP BASIC AUTHENTICATION EOF ////////////////////////

/**
* this is used to uthenticate
* Publications subscription system 
* @param: username, password,publication name,device
* response: json Product id , expiration date.
* added on 28/02/2012
*/
function userstatus(){
	$response = array();
	if(isset($_REQUEST['username']) && $_REQUEST['username'] !='' && isset($_REQUEST['password']) && $_REQUEST['password'] !='' && isset($_REQUEST['pubname']) && $_REQUEST['pubname'] !='' && isset($_REQUEST['device']) && $_REQUEST['device'] !=''){
		$user = $_REQUEST['username'];
		$pass = $_REQUEST['password'];
		$pubname = $_REQUEST['pubname'];
		$device = $_REQUEST['device'];

		// checking publication table for pubname.
		$this->db->where('name',$pubname);
		$pub = $this->db->get('publication');
		if($pub->num_rows() ==0){
			$response['result']= '0'; 
			$response['response'] = array('Publication not found');
			echo json_encode($response);
			die();
		}

		$this->db->where('username',$user);
		$this->db->where('userpassword',$pass);
		$this->db->where('publication',$pubname);
		
		$q = $this->db->get('subs_user');

		if($q->num_rows()== 1){
			$id = $q->row_array();
			// geting record from subs_product_code
			$this->db->where('subs_user_id',$id['id']);
			$result = $this->db->get('subs_product_code');
			if($result->num_rows() > 0){
				$response['result']= '1'; 
				$response['response'] = $result->result_array();

			}else{
				$response['result']= '0'; 
				$response['response'] = array('Product code not found');
			}

		}else{
				$response['result']= '0'; 
				$response['response'] = array('Incorrect username/password');
			}
			echo json_encode($response);

}else{
$response['result']= '0'; 
$response['response'] = array('Wrong parameter');
echo json_encode($response);
}
}

//////////////////////////////// SERVER RESPONSE CODES //////////////////////////////////////////
protected function _sendResponse($status = 200, $content_type = 'text/html', $body='', $pub_id=''){
    // set the status
    $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
    header($status_header);
    // and the content type
    header('Content-type: ' . $content_type.'; charset=UTF-8');
	header('Content-Language: en');
	header('Content-Length: ' . strlen($body));
	header('Last-Modified: ' . $this->_getlastModified($pub_id));
    // pages with body are easy
    if($body != '')
    {
        // send the body
        echo $body;
        exit;
    }
	
   
}


protected function _getStatusCodeMessage($status){
    
    $codes = Array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
    );
    return (isset($codes[$status])) ? $codes[$status] : '';
}

protected function _getlastModified($pub_id){
	$ctime = "SELECT MAX(unix_timestamp(date_modified)) as catTime FROM `category` where publication_id=$pub_id";
	$catTime = $this->db->query($ctime);

	$conf = "SELECT unix_timestamp(date_modified) as confTime FROM `publication` where id=$pub_id";
	$confTime = $this->db->query($conf);

	$cat = $catTime->row();
	$cnf = $confTime->row();
	
	if($cat->catTime >= $cnf->confTime){
		return $cat->catTime;
	}else{
		return $cnf->confTime;
	}
	return false;
}
///////////////////////////////////////////////////////////////////////////////////////////////////
	

	function keep_order($catid)
	{
	$sql ="SELECT orderflag FROM category WHERE id= $catid AND orderflag > 0 ";
	$rec = $this->db->query($sql);
	$result = $rec->num_rows();
		if($result >0){
			return true;
		}else{ 
			return false;
		}
	}

 /*
     * generatinon of xml response object
     * for category and config if requested by parem xml
     */

    private function xmlOut($cat, $conf) {
        header('Content-type: text/xml; charset=UTF-8');
        $oXMLWriter = new XMLWriter;
        $oXMLWriter->openMemory();
        $oXMLWriter->startDocument('1.0', 'UTF-8');
        $oXMLWriter->startElement('response');
        $oXMLWriter->startElement('categories');
        foreach ($cat as $catArr) {
			$oXMLWriter->startElement('category');
            foreach ($catArr as $key => $cats) {

                $oXMLWriter->writeElement($key, $cats);
            }
			$oXMLWriter->endElement();
        }
        $oXMLWriter->endElement();
        $oXMLWriter->startElement('config');
        foreach ($conf as $key => $value) {
            $oXMLWriter->writeElement($key, $value);
        }
        $oXMLWriter->endElement();

        $oXMLWriter->endElement();
        $oXMLWriter->endDocument();
        return $oXMLWriter->outputMemory(TRUE);
    }

// system log parsing for daily mail summary report 13/09/2012.
function dailymail() { 
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('memory_limit', '-1');
		$date = new DateTime(date('Y-m-d'));
		$date->sub(new DateInterval('P1D'));
		
		if($this->uri->segment(3) =='') return false;
		if($this->uri->segment(3) =='today'){
			$apidate = date('Y.m.d');
		//$log_file = '/var/log/httpd/daily_log.'.date('Y.m.d');
		$log_file = '/var/log/httpd/daily_log.'.$apidate;
		}elseif($this->uri->segment(3) =='yesterday'){
			$apidate = $date->format('Y.m.d');
		$log_file = '/var/log/httpd/daily_log.'.$apidate;
		}
		$pattern = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")$/';
		$fh = fopen($log_file,'r') or die($php_errormsg);
		
		$log = array();
		while (!feof($fh)) {
    // read each line and trim off leading/trailing whitespace
			if ($s = trim(fgets($fh,16384))) {
				// match the line to the pattern
				if (preg_match($pattern,$s,$matches)) { 
					
					$log[] = $matches;
					
				} else {
					// complain if the line didn't match the pattern 
					/////echo "Can't parse line $i: $s";
				}
			}
   
		}
		
		// getting api calls Uniqe
		foreach ($log as $apis){
				$logpub[]= $apis;
			}

		// publication calls
		foreach($logpub as $logp){
		$str = substr($logp[8],0,8);

			switch($str){
				case "/api/pub":
					$pub[]		= $logp[8];
					$pubdata[]	= $logp[11];
					break;
				case "/api/cat":
					$cat[]		= $logp[8];
					$catdata[]	= $logp[11];
					break;
				/*case "/api/ref": 
					$refresh[]	= $logp[8];
					$refdata[]	= $logp[11];
					break;*/
			}

		}

// publication details report
$pubunique = array_unique($pub);

foreach($pubunique as $pubs){
	foreach($pub as $key => $pb){
		if($pubs == $pb){
		$publication[$pubs]['publication'][] = $pb;
		$publication[$pubs]['datatransfer'][] = $pubdata[$key];
		}
	}
}

foreach ($publication as $key => $value){
		$pubCount[$key][] = count($value['publication']);
		$pubCount[$key][] = array_sum($value['datatransfer']);
}
$pubDetails = '';
foreach($pubCount as $key => $value){
	$psplit = explode('?', $key);
	$pubName = explode('/', $psplit[0]);
	$deviceNversion = explode('&', $psplit[1]);
	$deviceAndVersion = $deviceNversion[1].' '.$deviceNversion[2];

	$pubDetails .= '<tr>
      <td style="color:#0085cc;"> '.ucfirst($pubName[2]) .' </td><td style="color:#0085cc;"><strong> '.$pubName[3] .' </strong></td><td style="color:#0085cc;"> Calls / Data transfer</td><td style="color:#0085cc;"> <strong>' .$value[0] . ' / ' . $value[1].' </strong></td><td style="color:#0085cc;"> '.$deviceAndVersion.'</td>
    </tr>';


}

// Categories details report
$catunique = array_unique($cat);
foreach($catunique as $cats){
	foreach($cat as $key => $ct){
		if($cats == $ct){
		$categories[$cats]['category'][] = $ct;
		$categories[$cats]['datatransfer'][] = $catdata[$key];
		}
	}
}

foreach ($categories as $key => $value){
		$catCount[$key][] = count($value['category']);
		$catCount[$key][] = array_sum($value['datatransfer']);
}
$catDetails = '';
foreach($catCount as $key => $value){
	$csplit = explode('?', $key);
	$catName = explode('/', $csplit[0]);
	$deviceVersion = $csplit[1];
	
	$catDetails .= '<tr>
      <td style="color:#0085cc;"> '.ucfirst($catName[2]) .' </td>';
	  if(isset($catName[3])){
			$catDetails .= '<td style="color:#0085cc;"><strong> '.$catName[3] .' </strong></td>';
	  }else{
		  	$catDetails .= '<td style="color:#0085cc;"><strong> Photos </strong></td>';
	  }
	$catDetails .= '<td style="color:#0085cc;"> Calls / Data transfer</td><td style="color:#0085cc;"> <strong>' .$value[0] . ' / ' . $value[1].' </strong></td><td style="color:#0085cc;"> ' . $deviceVersion .' </td>
    </tr>';


}




////////////////////////// sendign daily email //////////////////////////////////////////////

// multiple recipients
$to  = 'ashok@whizti.com' . ', '; // note the comma
$to .= 'ashok975@hotmail.com';

// subject
$subject = 'Daily Report of Api calls.';

// message
// <p align="center"><strong> Today\'s ('.date('Y-m-d').') api calls </strong> </p>

$message = '
<html>
<head>
  <title> Api calls on '.date('Y-m-d').'</title>
</head>
<body>
  <p align="center"><strong> ('.$apidate.') api calls </strong> </p>
  <table border="0" align="center">
    
    <tr>
      <td bgcolor="#cccccc">Total Publication api calls</td><td bgcolor="#cccccc"><strong> '.count($pub).' </strong></td><td bgcolor="#cccccc">Total Publication calls Data transfer</td><td bgcolor="#cccccc"> <strong>' .array_sum($pubdata) . ' </strong></td><td bgcolor="#cccccc"> Device/format & version</td>
    </tr>'.$pubDetails.'<tr>
      <td bgcolor="#cccccc">Total Category api calls</td><td bgcolor="#cccccc"><strong> '.count($cat).' </strong></td><td bgcolor="#cccccc">Total Category call Data transfer</td><td bgcolor="#cccccc"><strong> ' .array_sum($catdata).' </strong></td><td bgcolor="#cccccc"> Device & version</td>
    </tr>'.$catDetails .'
	  
  </table>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
//$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
$headers .= 'From: Whizti Scripps Server <ashok@whizti.com>' . "\r\n";
//$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
//$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";

// Mail it
echo $message;

//mail($to, $subject, $message, $headers);

/////////////////////////////////////////////////////////////////////////////////////
}

/**
* getting content by id from content table.
* related media from content media table.
**/
function getContent(){
		$response = array();
		$id = $this->uri->segment(3);
		if(is_numeric($id) && $id !='') {
			$contentResult = $this->getById($id,'content');
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = file_get_contents("php://input");
			$this->db->where('uri', $id);
			$result = $this->db->get('content');
			$contentResult = $result->row_array();
		}else{

		// Errro handling.
			$status_header = 'HTTP/1.1  400 '  . $this->_getStatusCodeMessage('400');
			header($status_header);
			header('Content-type: text/html; charset=UTF-8');
			header('Content-Language: en');
			// send the body
			echo 'Bad Request';
			exit;
   		}
////////////// BOF STORIY MEDIA //////
		$this->db->where('content_id', $contentResult['id']);
		$this->db->select('thumbnail, media, caption, type');
		$this->db->order_by('media_order');
		$Q = $this->db->get('content_media');
		$contentResult['media'] = $Q->result();
		$response['response']['content'] = $contentResult;
		$data = json_encode($response);
		$status_header = 'HTTP/1.1  200 '  . $this->_getStatusCodeMessage('200');
		header($status_header);
		// and the content type
		header('Content-type: application/json; charset=UTF-8');
		header('Content-Language: en');
		header('Content-Length: ' . strlen($data));
		// pages with body are easy
		if($data != '')
		{
			// send the body
			echo $data;
			exit;
		}
}
/*
* to get sponsored ad from sponsored_ad table.
*/

private function sponsoredAd($category_id){
	
	$this->db->where('category_id', $category_id);
	$ad = $this->db->get('sponsored_ad');
	//$result = $ad->row_array();
	$result = $ad->result_array();
	if($result){
		$resultAdd['uri'] = $result[0]['video_url'];
		$resultAdd['icon_uri'] = $result[0]['video_thumbnail'];
		$resultAdd['assetid'] = $result[0]['id'];
		$resultAdd['type'] = 'video';
		$resultAdd['video_group'] = $result[0]['video_group'];
		$resultAdd['liveframe'] = $result[0]['liveframe'];
				if($result[0]['format'] =='hls'){
					$resultAdd['video_type']='application/octet-stream';
				}else{
					$resultAdd['video_type'] = 'video/mp4';
				}
		
		
		unset($result[0]['format']);
		unset($result[0]['video_url']);
		unset($result[0]['video_thumbnail']);
		$response[] = array_merge($result[0], $resultAdd);
		return $response;
	}else{
		return false;
	}
	
	}


	private function kuSportsAudioThumbnail($media, $pubid){
		
		$mediaEdited = array();
		foreach($media as $key=> $media){
	
			if($pubid == 160 && $media->thumbnail =='' && $media->type =='audio'){
				$media->thumbnail = 'https://prodman.whizti.com/images/audioicon.png';
				
			}
			$mediaEdited[] = $media;
		}
		
		return $mediaEdited;
	}

	
	






}
