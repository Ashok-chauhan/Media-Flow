<?php
class Mcategory extends CI_Model {
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
            $this->objectName == 'category';
           
    
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