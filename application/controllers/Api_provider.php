<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Api_provider extends CI_Controller
{
    
    function __construct()
    {
        parent::__construct();
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        header('Content-Type: application/json');
        $this->load->helper('url');
			$this->load->model("Home_model");
            $this
            ->load
            ->model("Api_model");


    }
    
    /* index of the admin. Default: Dashboard; On No Login Session: Back to login page. */
    public function index()
    {
		
		
      
    }
    
	function profile_image($para1='', $para2=''){
	 	
		     $row  =          json_decode($_POST['name'],true);
		$data['username'] = $row['firstname'];
		$data['lastname'] = $row['lastname'];
		$data['dob'] = $row['dob'];
		$data['gender'] = $row['gender'];
        $data['email'] = $row['email'];
		$data['password'] = sha1($row['password']);
		$data['mobile'] = $row['mobile'];
		$data['address1'] = $row['address1'];
		$data['address2'] = $row['address2'];
		$data['city'] = $row['city'];
		$data['state'] = $row['state'];
		$data['zipcode'] = $row['zipcode'];
		$data['aboutyou'] = $row['aboutyou'];
		$data['work_exp'] = $row['workexp'];
		$data['latitude'] = $row['latitude'];
		$data['longitude'] = $row['longtitude'];
		$data['date'] = time();
		$data['status']   = 'success';
		$data['token']   = '1';
		$data['device_id']   = '1';
		$data['livestatus'] =  'true';
		$this->db->insert('provider',$data);
		$id = $this->db->insert_id();
            
            $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $data_banner['image']       = 'provider_'.$id.'.'.$ext;
            $this->crud_model->file_up("image", "provider", $id, '', 'no', '.'.$ext);
            $this->db->where('provider_id', $id);
            $this->db->update('provider', $data_banner);
			foreach($row['category'] as $row1){
				 $data1['categoryName'] = $row1['categoryName'];
				 $data1['categoryId']   = $row1['categoryId'];
				 $data1['subcategoryName'] = $row1['subcategoryName'];
				 $data1['subcategoryId'] = $row1['subcategoryId'];
				 $data1['experience'] = $row1['experience'];
				 $data1['chargePreHrs'] = $row1['chargePreHrs'];
				  $data1['quickPitch'] = $row1['quickPitch'];
				 $data1['userid'] = $id;
				 $this->db->insert('provider_databook',$data1);
			}
			
	}
	
    
       /** dashboard **/
    function dashboard($para1 = '', $para2 = '', $para3 = '')
    {
        if ($para1 == 'topbar')
            {
                $today = date("Y-m-d");
               
                $start = $this
                    ->Api_model
                    ->date_timestamp($today, 'start');
                $end = $this
                    ->Api_model
                    ->date_timestamp($today, 'end');
                
                
                $data = array(
                    'todayOrders' => $this ->Api_model ->count_4wcopt('vendor_invoice', 'driver_id', $para2, 'method !=', 'cancelled', 'deliver_assignedtime >=', $start, 'deliver_assignedtime <=', $end) ,
                    'totalOrders' => $this->Api_model ->count_2wcopt('vendor_invoice', 'driver_id', $para2, 'method !=', 'cancelled') ,
                    'todayEarn' => floatval($this ->Api_model->sumof_sammaryReportdatewise('vendor_invoice', 'driver_id', $para2, 'deliver_assignedtime >=', $start, 'deliver_assignedtime <=', $end, 'driver_fees') + $this->Api_model
                        ->sumof_sammaryReportdatewise('vendor_invoice', 'driver_id', $para2, 'deliver_assignedtime >=', $start, 'deliver_assignedtime <=', $end, 'driver_tips')) ,
                    'totalEarn' => floatval($this
                        ->Api_model
                        ->sumof_sammaryReport('vendor_invoice', 'driver_id', $para2, 'driver_fees') + $this
                        ->Api_model
                        ->sumof_sammaryReport('vendor_invoice', 'driver_id', $para2, 'driver_tips')) ,
                );

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'topbar retrieved successfully',
                );
            }
        
     

        echo json_encode($responce);

    }
	
	function statusUpdate($para1='',$para2=''){
		if($para1=='update'){
			
			
		  $content_data      = file_get_contents("php://input");
          $get_data          = json_decode($content_data);
		  $prestatus  = json_decode($this->Home_model->singleselectbox('booking','bookid',$get_data->bookId,'statusmanage'),true);
		  
		
		 array_push($prestatus, $get_data);
		  $data['statusmanage'] = json_encode($prestatus);
		  $data['status']       = $get_data->status;
		  $this->db->where('bookid',$get_data->bookId);
		  $this->db->update('booking',$data); 
		   $responce = array(
	                     'success' => true,
						 'data' =>  count($prestatus),
						 'message' => 'booked  successfully',
						);
		}
		 echo json_encode($responce);	
	}
	
	
	function serviceImage_upload($para1='', $para2=''){
		if($para1=='before'){
		    $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $this->crud_model->file_up("image", "beforeservice",$para2, '', 'no', '.'.$ext);
		}else{
			$path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $this->crud_model->file_up("image", "afterservice", $para2, '', 'no', '.'.$ext);
		}
	}
    
     /** settings **/
    function settings()
    {
        $data = ["success" => true, 
                 "data" => [
            "app_name" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '3', 'value') , 
            "enable_stripe" => "1", "phone" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '88', 'value') , 
            "default_currency" => $this->Api_model->get_currency() , 
                     "enable_paypal" => "1", 
            "address" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '89', 'value') ,
            "google_maps_key" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '67', 'value') , 
            "mobile_language" => "en", 
            "app_version" => "2.0.0",
            "enable_version" => "1", 
            "commission" => '0.0',
                     "defaultTax" => '0.0',
            "currency_right" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '83', 'value') , 
            "default_currency_decimal_digits" => "2", 
            "enable_razorpay" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '84', 'value') ], "message" => "Settings retrieved successfully"];
        echo json_encode($data);

    }



    function services($para1='', $para2='', $para3='', $para4=''){
	   if($para1=='list'){	
		   $loop = $this->db->get_where('provider_databook',array('userid' => $para2))->result_array();
		   foreach($loop as $row){
		        $data[] = array(
				
				    'id' => $row['provider_databook_id'],
				    'categoryName' => $row['categoryName'],
				    'categoryId' => $row['categoryId'],
				    'subcategoryName' => $row['subcategoryName'],
				    'subcategoryId' => $row['subcategoryId'],
				    'experience' => intval($row['experience']), 
					'chargePreHrs' => intval($row['chargePreHrs']), 
					'quickPitch' => intval($row['quickPitch']), 
				     );
		   } 
	   }else if($para1=='delete'){
		 
		    $multipleWhere = array('categoryId' => $para2, 'subcategoryId' => $para3, 'userid' => $para4 );
		    $this->db->where($multipleWhere);
			$this->db->delete('provider_databook');
			$data = 'success';
			
	   }else if($para1 == 'do_add'){
		     $content_data = file_get_contents("php://input");
             $get_data     = json_decode($content_data);
		      $data['categoryName'] = $get_data->categoryName;
			  $data['categoryId'] = $get_data->categoryId;
			  $data['subcategoryName'] = $get_data->subcategoryName;
			  $data['subcategoryId'] = $get_data->subcategoryId;
			  $data['experience'] = $get_data->experience;
			  $data['chargePreHrs'] = $get_data->chargePreHrs;
			  $data['quickPitch'] = $get_data->quickPitch;
			  $data['userid'] = $para2;
			  $this->db->insert('provider_databook',$data);
			  $data = 'success';
	   }
	   
	    $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'services state successfully',
						);
		   
		    echo json_encode($responce);
	}
    

	
	function bookingstatus($para1='', $para2=''){
	      $content_data = file_get_contents("php://input");
          $get_data     = json_decode($content_data);
		  $general[] = $get_data;
		  $data['payment']           = json_encode($general);
		  $data['commission_amount'] = $get_data->commissionAmount;
		  $data['commission_status'] = 'due';
		  $data['totalamount'] = $get_data->grandTotal;
		  $this->db->where('bookid',$get_data->bookingId);
		  $this->db->update('booking',$data);
			  $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'update date successfully',
						);
		   
		    echo json_encode($responce);
		  
	}
	
	
   function categories($para1='', $para2=''){    
    
	$this->db->order_by('category_name','asc');
    $loop = $this->db->get('h_category')->result_array();
	foreach($loop as $row){
	   $data[] = array(
	            'name' => $row['category_name'],
				'id' => $row['h_category_id'], 
	            );
	}
	
	 $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'Categories retrieved successfully',
						);
		   
		    echo json_encode($responce);
   }
   
    
	function subcategories($para1='', $para2=''){

  
	$this->db->order_by('h_sub_category_id','asc');
    $loop = $this->db->get_where('h_sub_category',array('h_category_id'=>$para1))->result_array();
	foreach($loop as $row){
	   $data[] = array(
	            'name' => $row['sub_category_name'],
				'id' => $row['h_sub_category_id'], 
	            );
	}
	
	 $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'Categories retrieved successfully',
						);
		   
		    echo json_encode($responce);
	}
    
	
	function rating($para1='', $para2=''){
	  if($para1=='do_add'){	
		   $content_data = file_get_contents("php://input");
           $get_data     = json_decode($content_data);
		   
		   $data['bookId'] = $get_data->bookId;
		   $data['rate'] = $get_data->rate;
		   $data['userId'] = $get_data->userId;
		   $data['providerId'] = $get_data->providerId;
		   $data['type'] = $get_data->type;
		   $data['review'] = $get_data->review;
		   $this->db->insert('comments',$data);
	  }
	}


	function profile($para1='', $para2='',$para3=''){

    if($para1=='status'){
    
	 $data['livestatus'] = $para3;
	 $this->db->where('provider_id',$para2);  
	 $this->db->update('provider',$data);  
	 $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'Categories retrieved successfully',
						);
	}
		   
		    echo json_encode($responce);
	}
    


	





	

 
 function login($para1='',$para2=''){
	    $content_data = file_get_contents("php://input");
        $get_data     = json_decode($content_data);

		 $loop = $this->db->get_where('provider',array('email'=>$get_data->email,'password'=>sha1($get_data->password),'status'=>'success'))->result_array();
		 if($loop>0){
		 foreach($loop as $row){
			 $data1['token'] = sha1($row['provider_id']);
             $data =  array(
			        'id' => $row['provider_id'],
					'name' => $row['username'],
					'email' => $row['email'],
					'api_token' => $data1['token'],
					'device_token' => $row['device_id'],
					'phone' => $row['mobile'],
					'status'  => $row['status'],
					'about'  => $row['aboutyou'],	
					'auth'       => true,
					'address'   => '1',
					'liveStatus'   => $row['livestatus']? true: false,
					'image'      => base_url().'uploads/provider_image/'.$row['image'],
			 );
			 
			 $this
                ->db
                ->where('provider_id', $row['provider_id']);
            $this
                ->db
                ->update('provider', $data1);
		 }
		 }else{
			 
		 }
		 
		   $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'profile retrieved successfully',
						);
	   echo json_encode($responce);	
		 
 }
  
 
 
  
  
  



  

  

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */