
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Email extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
		$this->load->model("email_model");
    }
	
	function emailtest($para1='', $para2=''){
		
		 $this->email_model->demoemail();	
			
		}
		
    function thankyouregister($para1='', $para2=''){
		 $username  = 'balaji';
		 $useremail = 'balaji30nsit@gmail.com';
		 $this->email_model->thankyouregister($username,$useremail);	
			
		}

	function resetPassword($para1='', $para2=''){
			$email =  $this->input->post('email');
			$otp  =  $this->input->post('otp');
			$type  =  $this->input->post('type');
			if($type=='vendor'){
			$username = $this->db->get_where('vendor', array('email' => $email))->row()->display_name;
			} else if($type == 'admin'){
			 $username = $this->db->get_where('admin', array('email' => $email))->row()->name; 
			} else if($type=='driver'){
			 $username = $this->db->get_where('driver', array('email' => $email))->row()->name;     
			} else if($type=='user'){
			   
			 $username = $this->db->get_where('user', array('email' => $email))->row()->username;     
			}else if($type=='provider'){
			   
			 $username = $this->db->get_where('provider', array('email' => $email))->row()->username;     
			}
		   
			if($username!=''){
			$this->email_model->otp_page($email,$otp,$username);
			}
			   
		   }
		   
		
   function notification($para1='', $para2='', $para3=''){
	   header("Access-Control-Allow-Origin: *");

	     $data2['sale_code'] = $para2;
		 $data2['status'] = $para3;
	     $this->load->view('pushnotification',$data2);
		 
   }
}

?>
 
