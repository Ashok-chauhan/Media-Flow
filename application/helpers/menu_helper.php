<?php  
####################################################################################
## Developed by Ashok kumar singh chauhan 06/09/2010, whizti					  ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~##
## THIS HELPER ARE WRITTEN  TO GENERATE HEADER NAVIGATION						  ##	
## DYANMICALY ACCORDING TO PUBLISHER'S ACCESS RIGHT OF DIFRENT MODULE			  ##	
## THIS MENU HELPER ARE NOT APPLICATBLE TO WHIZ ADMIN AREA BECAUSE THERE          ##
## NO ACCESS RULE FOR WHIZ ADMINISTRATOR										  ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~##
####################################################################################

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (! function_exists('getmenu'))
{
	function getmenu($id='')
	{
		$ci=& get_instance();
        $ci->load->database(); 

$sql = "select * from user where id= $id ";
$query = $ci->db->query($sql);
$row = $query->row_array();
	//******* BOF HEADER NAVIGATION ************//
	// access right generating menu
	$menu = array();
	$menu[] = $row['analytics'];
	$menu[] = $row['isay'];
	$menu[] = $row['cms'];
	$menu[] = $row['apn'];
	$menu[] = $row['subscription'];
	$menu[] = $row['android'];
	$menu[] = $row['iphone'];

	
	// filtering empty value
	foreach ($menu as $key => $value){
		if(empty($value)){
			unset($menu[$key]);
		}
	}

	//reordering menu key
	$newmenu = array();
	$i=1;
	foreach($menu as $key => $value){
		if(!empty($value)){
			$newmenu[$i] = $value;
			$i++;
		}
	}
	
	return $newmenu;
	//********* EOF HEADER NAVIGATION ***********//
	}
}


############################################################################
# Developed by Ashok kumar singh chauhan 29/11/2010, whizti				####
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~####
# THIS HELPER ARE WRITEN TO GET INFO ABOUT PUBLICATION , WHEATHER THY   ####
# THEY HAVE ANY CATEGORY (AT LEAST 1) ASSOCIATED WITH THEM , IF NOT		####
# PUBLISHER SHOULD BE REDIRECTED TO DESIRED LOCATION E.G. CATEGORY		####
# CREATION CONTROL PANNEL.												####
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~####
############################################################################

if (! function_exists('catcheck'))
{
	function catcheck($pubid='')
	{
		$ci=& get_instance();
        $ci->load->database(); 
// *** Bof to check available category of publication  ********//
$sql = "select * from category where publication_id= $pubid ";
$query = $ci->db->query($sql);
//$row = $query->row_array();

if($query->num_rows() >0){	
		return TRUE;
	}else{
		return FALSE;
	}
	//********* EOF category check ***********//
	}
}