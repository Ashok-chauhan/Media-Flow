<?php
class Xmlvalidator{
	 	function __construct(){
		libxml_use_internal_errors(true);

	}

function validate($feeduri){
	
$doc = simplexml_load_string(file_get_contents($feeduri));
//$doc = simplexml_load_file('http://util.buffalo.com/whiz/getRSS.php?feed=topStories');
$xml = explode("\n", file_get_contents($feeduri));
if (!$doc) {
    $errors = libxml_get_errors();

    foreach ($errors as $error) {
		$error->file = $feeduri; //getting uril becuse "simplexml_load_file" is not working.
		$er ='';
        $er .= $this->display_xml_error($error, $xml);
    }
	//mail('ashok@whizti.com','Error in feed', $er);
//email bof

/*$Name = "Ashok kumar"; //senders name 
$email = "ashok@whizti.com"; //senders e-mail adress 
$recipient = "ashok975@gmail.com"; //recipient 
$mail_body = $er;  //mail body 
$subject = "Error in feed"; //subject 
$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields 

mail($recipient, $subject, $mail_body, $header); //mail command :) 
*/
//global $CI;
$CI =& get_instance();
     $CI->load->library('email'); // load library 
$config =array();
$CI->config['charset'] = 'iso-8859-1';
$CI->config['wordwrap'] = TRUE;

$CI->config['protocol'] = 'smtp';
$CI->config['smtp_host'] = 'smtpout.secureserver.net';
$CI->config['smtp_user'] = 'ashok@whizti.com';
$CI->config['smtp_pass'] = 'ashok74';
$CI->email->initialize($config);

$CI->email->from('ashok@whizti.com', 'Ashok k');
$CI->email->to('ashok@whizti.com');
//$this->email->cc('another@another-example.com');
//$this->email->bcc('them@their-example.com');

$CI->email->subject('Email Test');
$CI->email->message('Testing the email class.');

$CI->email->send();

echo $CI->email->print_debugger();





//email eof 

//print $er; // er prepared to send email.
    libxml_clear_errors();
}

}

function display_xml_error($error, $xml)
{
    $return ='';
	$return .= str_repeat('-', 9) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Line: $error->line" .
               "\n  Column: $error->column \n ---".$xml[$error->line - 1] . "---";

    if ($error->file) {
		  $return .= "\n  URL/File: $error->file";
    }

    return "$return\n\n------------------------------------------------------------------------------------------------------------\n\n";
}

}