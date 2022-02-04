
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stripe extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
          $this
            ->load
            ->model("Api_model");
	
    }
	
	function index($para1='', $para2=''){
       
       if ($this->Api_model->singleselectbox('user', 'user_id', $para1, 'token') == $this->input ->get('api_token', true))
        {
           
        $data['grandtotal'] = intval($para2);
        $this->load->view('payment_gateway/stripe',$data);
        }else{
          echo 'invalid token';
       }
    }
    
     function success(){
       echo 'success';
    }
}

?>
 
