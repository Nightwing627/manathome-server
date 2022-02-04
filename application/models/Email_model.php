<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');



class Email_model extends CI_Model
{
 
    
    function __construct()
    {
        parent::__construct();
    }
    
    

   
    
	function otp_page($to='',$otp='', $username=''){
	
        $from = 'info@canaryhow.com';
        $to         =  $to;
        $subject    = 'Verification code to reset password';
		$page_data['username'] = $username;
		$page_data['optdata'] = $otp;
        $msg        =  $this->load->view('email/otp_page',$page_data,true);
		$this->do_email($from, $to, $subject, $msg);
	}

    function do_email($from = '', $to = '', $sub ='', $msg ='')
    {   
        $host =  $this->db->get_where('general_settings', array('type' => 'smtp_host'))->row()->value;
        $port =  $this->db->get_where('general_settings', array('type' => 'smtp_port'))->row()->value;
        $user =  $this->db->get_where('general_settings', array('type' => 'smtp_user'))->row()->value;
        $pass =  $this->db->get_where('general_settings', array('type' => 'smtp_pass'))->row()->value;

        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://'. $host,
            'smtp_port' => $port,
            'smtp_user' => $user, // change it to yours
            'smtp_pass' => $pass, // change it to yours
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
        );
          
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from($from); // change it to yours
        $this->email->to($to);// change it to yours
        $this->email->subject($sub);
        $this->email->message($msg);

        if($this->email->send()){
            echo 'Email sent.';
        }
        else {
            show_error($this->email->print_debugger());
        }
    }
}
