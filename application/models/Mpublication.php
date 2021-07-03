<?php
class Mpublication extends CI_Model {
    public function __construct()
    {
            $this->load->database();
    }


    public  function save($fields=array(),$redirect=true, $redirect_uri='') {
        
            if(sizeof($fields) <=0) {
                foreach($_POST as $field => $value) {
                    if(isset($this->fields[$field]))
                        $fields[$field] = $value;
                }
            }
            if($this->objectName == 'publication')
            {
                                 
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
                $this->session->set_flashdata('flashmsg','Operation completed successfully.'); 
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
    

}