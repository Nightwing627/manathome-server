<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Api_admin extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        /*cache control*/
        $this
            ->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this
            ->output
            ->set_header('Pragma: no-cache');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

        header('Content-Type: application/json');
        $this
            ->load
            ->helper('url');
        $this
            ->load
            ->model("Api_model");

    }

    function setting()
    {
        $data = ["success" => true, "data" => ["app_name" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '3', 'value') , "default_delivery_fees" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '86', 'value') , "min_purchase_df" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '92', 'value') , "default_currency" => $this
            ->Api_model
            ->get_currency() , "enable_paypal" => "1", "google_maps_key" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '67', 'value') , "mobile_language" => "en", "app_version" => "1.0.0", "enable_version" => "1", "currency_right" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '83', 'value') , "default_currency_decimal_digits" => "2", "razorpay_key" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '84', 'value') , "instanceDelivery" => false], "message" => "Settings retrieved successfully"];
        echo json_encode($data);

    }
    
    
    
    
    
    
    

    /* login */
    function login($para1 = '', $para2 = '')
    {

        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);

        /*$data['device_id'] = $get_data->deviceToken;
        $this->db->where('email',$get_data->email);
        $this->db->update('user',$data);*/

        $loop = $this
            ->db
            ->get_where('admin', array(
            'email' => $get_data->email,
            'password' => sha1($get_data->password)
        ))
            ->result_array();
        if (count($loop) > 0)
        {
            foreach ($loop as $row)
            {
                if ($row['image'] != '')
                {
                    $img = base_url() . 'uploads/admin_image/' . $row['image'];
                }
                else
                {
                    $img = 'no_image';
                }

                if ($row['cover_image'] != '')
                {
                    $coverImage = base_url() . 'uploads/cover_image/' . $row['cover_image'];
                }
                else
                {
                    $coverImage = 'no_image';
                }

                $data1['token'] = sha1($row['admin_id']);

                $data = array(
                    'id' => $row['admin_id'],
                    'name' => $row['name'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'api_token' => $data1['token'],
                    'coverImage' => $coverImage,
                    'device_token' => '',
                    'phone' => $row['phone'],
                    'address' => $row['address'],
                    'auth' => true,
                    'image' => $img,
                );

            }
            $this
                ->db
                ->where('admin_id', $row['admin_id']);
            $this
                ->db
                ->update('admin', $data1);

        }
        else
        {

            $data = array(
                'id' => '',
                'name' => '',
                'phone' => '',
                'email' => '',
                'api_token' => '',
                'coverImage' => '',
                'device_token' => '',
                'phone' => '',
                'status' => '',
                'auth' => false,
                'image' => '',
            );
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'profile retrieved successfully',
        );
        echo json_encode($responce);

    }
    
    
    
    /** package Item */
    
    function package_item($para1='', $para2='', $para3=''){
        
        if($para1=='do_add'){
          
             $data['name'] = $this->input->post('name');
             $data['date'] = time();
             $data['status'] = 'success';
             $this->db->insert('packageitem', $data);
            
        } else if($para1=='list'){
         $loop =  $this->db->get('packageitem')->result_array(); 
            foreach($loop as $row){
                $data[] = array(
                    'id' => $row['packageitem_id'],
                    'itemName' => $row['name'],
                    'uploadImage' => base_url() . 'uploads/packageitem_image/' . $row['banner'],
                     );
            }
            
             $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'packageitem retrieved successfully',
        );
        }
        
        
        
        
        echo json_encode($responce);
    }
    
    /**sub category**/
    
         function subcategory($para1='', $para2=''){
        if($para1=='add'){
		$data['h_category_id']=$this->input->post('categoryId');
             $data['sub_category_name'] = $this->input->post('categoryName');
             $data['date'] = time();
             $this ->db->insert('h_sub_category', $data);
            
                $id = $this ->db ->insert_id();
            
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['image'] = 'h_sub_category_' . $id . '.' . $ext;
                $this->crud_model->file_up("image", "h_sub_category", $id, '', 'no', '.' . $ext);
                $this->db ->where('h_sub_category_id', $id);
                $this ->db ->update('h_sub_category', $databanner);
                $responce = array(
                    'success' => true,
                );
            
        } else if($para1=='list'){
               
              $this->db->order_by('h_sub_category_id', 'desc');
              $loop = $this ->db->get_where('h_sub_category')->result_array();
           
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['h_sub_category_id'],
						'categoryId' =>$row['h_category_id'],
                        'subcategoryName' => $row['sub_category_name'],
                        'uploadImage' => $row['image'] ? base_url() . 'uploads/h_sub_category_image/' . $row['image'] : 'no_image',
                    );

                }
            
             $responce = array(
                    'data' => $data,
                    'message' => 'h_sub_category retrieved successfully',
                    'success' => true
                );
        } else if($para1=='edit'){
           $id = $para2;
            $data['h_category_id']=$this->input->post('categoryId');
                $data['sub_category_name'] = $this->input->post('categoryName');
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $data['image'] = 'h_sub_category_' .time().$id . '.png';
                $this->db ->where('h_sub_category_id', $id);
                $this ->db ->update('h_sub_category', $data);
         
                $this->crud_model->file_up("image", "h_sub_category", time().$id, '', 'no', '.' . $ext);
               
                $responce = array(
                    'success' => true,
                );
      
        }
        
        
        echo json_encode($responce);
    }
    
    
    
    
    
    /** package **/
    function Package($para1='', $para2=''){
     
        if($para1=='add'){
             $data['name'] = $this->input->post('name');
             $data['date'] = time();
			 $data['status']='success';
             $this ->db->insert('packageitem', $data);
            
                $id = $this ->db ->insert_id();
            
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['image'] = 'packageitem_' . $id . '.' . $ext;
                $this->crud_model->file_up("banner", "packageitem", $id, '', 'no', '.' . $ext);
                $this->db ->where('packageitem_id', $id);
                $this ->db ->update('packageitem', $databanner);
                $responce = array(
                    'success' => true,
                );
            
        } else if($para1=='list'){
               
              $this->db->order_by('packageitem_id', 'desc');
              $loop = $this ->db->get_where('packageitem')->result_array();
           
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['packageitem_id'],
                        'name' => $row['name'],
                        'image' => $row['banner'] ? base_url() . 'uploads/packageitem_image/' . $row['banner'] :'no_image',
                    );

                }
            
             $responce = array(
                    'data' => $data,
                    'message' => 'packageitem retrieved successfully',
                    'success' => true
                );
        } else if($para1=='edit'){
               $id = $para2;
			   $data['status']='success';
                $data['name'] = $this->input->post('name');
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $data['image'] = 'packageitem_' . $id . '.png';
                $this->db ->where('packageitem_id', $id);
                $this ->db ->update('packageitem', $data);
         
                $this->crud_model->file_up("image", "packageitem", $id, '', 'no', '.' . $ext);
               
                $responce = array(
                    'success' => true,
                );
        }
        
        
        echo json_encode($responce);
    }
    
    
    
    
    
    /** booking **/
    
    function Booking($para1='', $para2=''){
               $loop = $this->db->get_where('booking')->result_array();
			    $data = [];
                foreach ($loop as $row) {
                    $data[] = array(
                        'bookingId' => $row['booking_id'],
                        'userId' => $row['user_id'],
                        'providerId' => $row['provider_id'],
                        'detail' => $row['detail'],
                        'phone' => $row['phone'],
                        'date' => $row['date'],
                        'status' => $row['status'],
                        'bookId' => $row['bookid'],
                        'statusManage' => $row['statusmanage'],
                        'totalAmount' => $row['totalamount'],
                        'commissionAmount' => $row['commission_amount'],
                        'commissionStatus' => $row['commission_status'],
                        'payment' => $row['payment'],
                        'transactionId' => $row['transaction_id'],
                        'categoryId' => $row['category_id'],
                    );
					}
                $response = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'Tis retrieved successfully',
                );
			echo json_encode($responce);
	}
    
    
    /** provider **/
	
	function Provider($para1 = '', $para2 = '')
    {
    if ($para1 == 'list') {
               $this->db->order_by('provider_id','desc');
               $loop = $this->db->get_where('provider')->result_array();
			    $data = [];
                foreach ($loop as $row) {
                    $data[] = array(
                        'username' => $row['username'],
                        'lastname' => $row['lastname'],
                        'dob' => $row['dob'],
                        'gender' => $row['gender'],
                        'email' => $row['email'],
                        'password' => $row['password'],
                        'mobile' => $row['mobile'],
                        'address1' => $row['address1'],
                        'address2' => $row['address2'],
                        'city' => $row['city'],
                        'state' => $row['state'],
                        'zipcode' => $row['zipcode'],
                        'aboutyou' => $row['aboutyou'],
                        'work_exp' => $row['work_exp'],
                        'date' => date("Y-m-d",$row['date']),
                        'status' => $row['status'],
                        'image' => $row['image'],
                        'latitude' => $row['latitude'],
                        'longitude' => $row['longitude'],
                        'device_id' => $row['device_id'],
                        'livestatus' => $row['livestatus'],

                        'id' => intval($row['provider_id'])
                    );
					}
                $response = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'Tis retrieved successfully',
                );
            }  
			
			else if ($para1 == 'edit') {
               echo $id = $para2;
	    $data['username'] = $this->input->post('username');
	    $data['dob'] = $this->input->post('dob');
		$data['gender'] = $this->input ->post('gender');
		$data['email'] = $this->input ->post('email');
		$data['password'] = $this->input->post('password');
		$data['address1'] = $this->input ->post('address1');
		$data['address2'] = $this->input ->post('address2');
		$data['city'] = $this->input ->post('city');
		$data['state'] = $this->input ->post('state');
		$data['zipcode'] = $this->input ->post('zipcode');
		$data['aboutyou'] = $this->input ->post('aboutyou');
		$data['work_exp'] = $this->input ->post('work_exp');
		$data['date'] = $this->input ->post('date');
		$data['status'] = $this->input ->post('status');
		$data['latitude'] = $this->input ->post('latitude');
		$data['longitude'] = $this->input ->post('longitude');
		$data['device_id'] = $this->input ->post('device_id');
		$data['livestatus'] = $this->input ->post('livestatus');
          $this->db ->where('provider_id', $id);
                $this ->db ->update('provider', $data);
				echo 'hi';
                $responce = array(
                    'success' => true,
                );

            } 
    
        echo json_encode($response);
    }  
        
        
        
      
    
function VendorMembership($para1 = '', $para2 = '')
    {
	if($para1=='add'){
	        $data['plan_name'] = $this->input->post('planname');
	    $data['price'] = $this->input->post('price');
		$data['commision'] = $this->input ->post('commission');
		$data['product_upload_limit'] = $this->input ->post('productlimit');
		$data['validity'] = $this->input->post('validity');
       
		$data['focus_id'] = $this->input ->post('shoptype');
          $this->db ->insert('vendor_membership', $data);
                $id = $this->db->insert_id();
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['image'] = 'vendor_membership_' . $id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("image", "vendor_membership", $id, '', 'no', '.' . $ext);
                $this
                    ->db
                    ->where('vendor_membership_id', $id);
                $this
                    ->db
                    ->update('vendor_membership', $databanner);
                $responce = array(
                    'success' => true,
                );

            }
		else if($para1=='list'){
		
		$this->db->order_by('vendor_membership_id', 'desc');
              $loop = $this ->db->get_where('vendor_membership')->result_array();
           
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['vendor_membership_id'],
                        'planname' => $row['plan_name'],
						'price' => $row['price'],
						'commission' => $row['commision'],
						'productlimit' => $row['product_upload_limit'],
						'validity' => $row['validity'],
						'shoptype' => $row['focus_id'],
                        'typeName'  => $this->db->get_where('shop_focus',array('shop_focus_id' => $row['focus_id']))->row()->title,
                        'uploadImage' => $row['image'] ? base_url() . 'uploads/vendor_membership_image/' . $row['image'] : 'no_image',
                    );

                }
            
             $responce = array(
                    'data' => $data,
                    'message' => 'Vendor Membership Plan List retrieved successfully',
                    'success' => true
                );
        }
		
		if($para1=='edit'){
		$id = $para2;
              $time = time();
	        $data['plan_name'] = $this->input->post('planname');
	    $data['price'] = $this->input->post('price');
		$data['commision'] = $this->input ->post('commission');
		$data['product_upload_limit'] = $this->input ->post('productlimit');
		$data['validity'] = $this->input->post('validity');
		$data['focus_id'] = $this->input ->post('shoptype');
		$data['image'] = 'vendor_membership_' .$time.$id . '.png';
          $this->db ->where('vendor_membership_id', $id);
                $this ->db ->update('vendor_membership', $data);
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
              
                $databanner['image'] = 'vendor_membership_' . $time.$id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("image", "vendor_membership",  $time.$id, '', 'no', '.' . $ext);

                $responce = array(
                    'success' => true,
                );

            }
		
        echo json_encode($responce);
	
    }  
    
    
    
    /** super category **/
    function SuperCategory($para1='', $para2=''){
     
        if($para1=='add'){
             $data['name'] = $this->input->post('categoryName');
			 $data['shop_type'] =0;
			 $data['short_by'] = $this->input->post('sortBy');
             $data['date'] =time();
			 $data['image']=0;
             $this ->db->insert('supercategory', $data);
                $id = $this ->db ->insert_id();
                $responce = array(
                    'success' => true,
                );  
        } 
	
	else if($para1=='list'){
              $this->db->order_by('supercategory_id', 'desc');
              $loop = $this ->db->get_where('supercategory')->result_array();
           
                foreach ($loop as $row)
                {
                    
                    
				if($row['status']==0 || $row['status']==''){
					$bool=false;
				}
				else{
					$bool=true;
				}
                        
                    $data[] = array(
                        'id' => $row['supercategory_id'],
                        'categoryName' => $row['name'],
                        'sortBy' => $row['short_by'],
                        'shopType' => $row['shop_type'],
                        'status' => $bool,
                        'uploadImage' => $row['image'],
                    );

                }
            
             $responce = array(
                    'data' => $data,
                    'message' => 'packageitem retrieved successfully',
                    'success' => true
                );
        }
		
		if($para1=='edit'){
		$id = $para2;
             $data['name'] = $this->input->post('categoryName');
			 $data['shop_type'] =0;
			 $data['short_by'] = $this->input->post('sortBy');
             $data['date'] =time();
			 $data['image']=0;
             $this->db ->where('supercategory_id', $id);
                $this ->db ->update('supercategory', $data);
                $responce = array(
                    'success' => true,
                );  
        } 
        echo json_encode($responce);
    }
    
    
    /** tax **/
	
	function Tax($para1='',$para2=''){
		
		if($para1=='add'){
		 $data['tax'] = $this->input->post('tax');
             $data['date'] =time();
             $this ->db->insert('tax', $data);
                $responce = array(
                    'success' => true,
                );  
			
		}
		else if($para1=='list'){
		
		$this->db->order_by('tax_id', 'desc');
              $loop = $this ->db->get_where('tax')->result_array();
           
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['tax_id'],
                        'tax' => $row['tax'],
                        'date' =>date("Y-m-d",$row['date']),
                    );
                }
            
             $responce = array(
                    'data' => $data,
                    'message' => 'packageitem retrieved successfully',
                    'success' => true
                );
			
		}
		
		else if($para1=='edit'){
		$id = $para2;
             $data['tax'] = $this->input->post('tax');
             $this->db ->where('tax_id', $id);
                $this ->db ->update('tax', $data);
                $responce = array(
                    'success' => true,
                );  	
		}
		
		echo json_encode($responce);
	}    
        
	
	function Policy($para1='', $para2=''){
     if($para1=='list'){
               
              $this->db->order_by('policy_id');
              $loop = $this ->db->get_where('policy')->result_array();
           
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['policy_id'],
                        'policy' => $row['policy'],
                        'value' => $row['value'],
                    );

                }
            
             $responce = array(
                    'data' => $data,
                    'message' => 'policy retrieved successfully',
                    'success' => true
                );
        } else if($para1=='edit'){
             
                $data['value'] = $this->input->post('value');
                $this->db->where('policy_id', $id);
                $this ->db->update('policy', $data);
                $responce = array(
                    'success' => true,
                );
        }
        
        
        echo json_encode($responce);
    } 
    
    
    /** payment **/

    function payment($para1 = '', $para2 = '', $para3 = '')
    {

        if ($para1 == 'paymenylist')
        {
            $this
                ->db
                ->order_by('vendor_invoice_id', 'desc');
            $loop = $this
                ->db
                ->get_where('vendor_invoice')
                ->result_array();
            $i = 0;
            foreach ($loop as $row)
            {
                $i++;
                $data[] = array(
                    'id' => $i,
                    'invoiceID' => $row['invoice_id'],
                    'itemAmount' => $row['order_amount'],
                    'settlementAmount' => $row['settlement_value'],
                    'driverComission' => $row['driver_comission'],
                    'driverSettlement' => $row['driver_fees'] - $row['driver_comission'],
                    'driverfees' => $row['driver_fees'],
                    'commission' => $row['commission'],
                    'paymentMethod' => $row['method'],
                    'status' => $row['status'],
                    'date' => date("F j, Y", $row['order_date']) ,
                    'transactionID' => '1',
                );

            }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'payment details retrieved successfully',
            );
        }
        else if ($para1 == 'summaryReport')
        {

            $data = array(
                'totalRevenue' => round($this
                    ->Api_model
                    ->sumof_sammaryReportadmin('vendor_invoice', 'settlement_value') , 2) ,
                'thisMonthRevenue' => round($this
                    ->Api_model
                    ->count_monthreportadmin(date('m') , 'vendor_invoice', 'sum', 'settlement_value') , 2) ,
                'totalPaidCommission' => round($this
                    ->Api_model
                    ->sumof_wc('vendor_invoice', 'status', 'paid', 'commission') , 2) ,
                'totalDueCommission' => round($this
                    ->Api_model
                    ->sumof_wc('vendor_invoice', 'method !=', 'cancelled', 'commission') , 2) ,
                'totalOrders' => $this
                    ->Api_model
                    ->count_wcopt('vendor_invoice', 'method !=', 'cancelled') ,
                'thisMonthOrders' => $this
                    ->Api_model
                    ->count_monthreportadmin(date('m') , 'vendor_invoice', 'count', '') ,
                'totalPaidOrders' => $this
                    ->Api_model
                    ->count_2wcopt('vendor_invoice', 'method !=', 'cancelled', 'status', 'paid') ,
                'totalDueOrders' => $this
                    ->Api_model
                    ->count_2wcopt('vendor_invoice', 'method !=', 'cancelled', 'status', 'due') ,
            );

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'summary report retrieved successfully',
            );

        }
        echo json_encode($responce);
    }
    
    
    
    /** orderAssign **/
     function orderAssign($para1 = '', $para2 = '', $para3 = '', $para4 = '', $para5='')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para4, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'autoAssign' && $para2 == 1)
            {
                $shoplatitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id',$para5, 'latitude');
                $shoplongitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $para5, 'longitude');
                $result = $this
                    ->Api_model
                    ->nearestDriver($shoplatitude, $shoplongitude);
                foreach ($result as $row)
                {

                    $data['delivery_assigned'] = $row['driver_id'];
                    $data['deliver_assignedtime'] = time();
                    $data['status'] = 'Shipped';
                    $this
                        ->db
                        ->where('sale_code', $para3);
                    $this
                        ->db
                        ->update('sale', $data);
                    
                    $data1['driver_id'] =   $row['driver_id'];
                    $data1['deliver_assignedtime'] = $data['deliver_assignedtime'];
                    $this->db->where('invoice_id', $para3);
                    $this->db->update('vendor_invoice', $data1);
                    
                    
                    
                    $data1 = array(
                        'driverId' => $row['driver_id'],
                        'driverName' => $row['name'],
                        'phone' => $row['phone'],
                        'latitude' => $row['latitude'],
                        'longitude' => $row['longitude']
                    );
                }

            }
            else if ($para1 == 'autoAssign' && $para2 == 0)
            {

                $shoplatitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $para5, 'latitude');
                $shoplongitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $para5, 'longitude');
                $result = $this
                    ->Api_model
                    ->nearestDriverwithVendor($shoplatitude, $shoplongitude, $para5);
                foreach ($result as $row)
                {

                    $data['delivery_assigned'] = $row['driver_id'];
                    $data['deliver_assignedtime'] = time();
                    $data['status'] = 'Shipped';
                    $this
                        ->db
                        ->where('sale_code', $para3);
                    $this
                        ->db
                        ->update('sale', $data);
                    
                      
                    $data1['driver_id'] =   $row['driver_id'];
                    $data1['deliver_assignedtime'] = $data['deliver_assignedtime'];
                    $this->db->where('invoice_id', $para3);
                    $this->db->update('vendor_invoice', $data1);
                    
                    
                    $data1 = array(
                        'driverId' => $row['driver_id'],
                        'driverName' => $row['name'],
                        'phone' => $row['phone'],
                        'latitude' => $row['latitude'],
                        'longitude' => $row['longitude']
                    );
                }

            }
            else if ($para1 == 'manual' && $para2 == 1)
            {

                $shoplatitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $para4, 'latitude');
                $shoplongitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $para4, 'longitude');
                $result = $this
                    ->Api_model
                    ->nearestDriver($shoplatitude, $shoplongitude);
                foreach ($result as $row)
                {
                    $data1[] = array(
                        'id' => $row['driver_id'],
                        'name' => $row['name'],
                    );
                }

            }
            else if ($para1 == 'manual' && $para2 == 0)
            {

                $shoplatitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $para4, 'latitude');
                $shoplongitude = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $para4, 'longitude');

                $result = $this
                    ->Api_model
                    ->nearestDriverwithVendor($shoplatitude, $shoplongitude, $para4);

                foreach ($result as $row)
                {
                    $data1[] = array(
                        'id' => $row['driver_id'],
                        'name' => $row['name'],
                    );
                }

            }
            else if ($para1 == 'update')
            {

                $data['delivery_assigned'] = $para2;
                $data['deliver_assignedtime'] = time();
                $data['status'] = 'Shipped';
                $this
                    ->db
                    ->where('sale_code', $para3);
                $this
                    ->db
                    ->update('sale', $data);
                
                  
                    $data1['driver_id'] =   $row['driver_id'];
                    $data1['deliver_assignedtime'] = $data['deliver_assignedtime'];
                    $this->db->where('invoice_id', $para3);
                    $this->db->update('vendor_invoice', $data1);
                
                
                $data1 = array(
                    'driverId' => $para2,
                    'driverName' => $this
                        ->Api_model
                        ->singleselectbox('driver', 'driver_id', $para2, 'name') ,
                    'phone' => $this
                        ->Api_model
                        ->singleselectbox('driver', 'driver_id', $para2, 'phone') ,
                    'latitude' => $this
                        ->Api_model
                        ->singleselectbox('driver', 'driver_id', $para2, 'latitude') ,
                    'longitude' => $this
                        ->Api_model
                        ->singleselectbox('driver', 'driver_id', $para2, 'longitude') ,
                );

            }

            $responce = array(
                'success' => true,
                'data' => $data1,
                'message' => 'profile retrieved successfully',
            );

        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();

        }
        echo json_encode($responce);
    }

    
    
    /** orders **/
     function Order($para1 = '', $para2 = '', $para3 = '', $para4 = '')
    {
       

        if ($this ->Api_model->singleselectbox('admin', 'admin_id', $para4, 'token') == $this ->input ->get('api_token', true))
        {
            if ($para1 == 'list')
            {
              

                $this
                    ->db
                    ->select('sale_code, status, grand_total, buyer, payment_type, product_details, vendor,sale_datetime,order_type,delivery_slot,focus_id,p_image ');
                $this ->db->order_by('sale_id', 'desc');
                $loop = $this ->db->get_where('sale', array(
                    'status' => $para3
                ))->result_array();
                foreach ($loop as $row)
                {
                    $loop1 = json_decode($row['product_details'], true);
                    $i = 1;
                    foreach ($loop1 as $row1)
                    {
                        if ($i == 1)
                        {
                            $image = $row1['image'];
                            $name = $row1['product_name'];
                        }
                        $i++;
                    }
                    date_default_timezone_set('Asia/Kolkata');
                    $date = date('M, d Y, h:i A', $row['sale_datetime']);
                    $data[] = array(
                        'orderId' => $row['sale_code'],
                        'status' => $row['status'],
                        'price' => $row['grand_total'],
                        'contact' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->phone,
                        'username' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->username,
                        'userId' => $row['buyer'],
                        'paymentType' => $row['payment_type'],
                        'details' => '1',
                        'itemImage' => $image,
                        'date' => $date,
                        'shopId' => $row['vendor'],
                        'itemTotal' => count($loop1) ,
                        'orderType' => $row['order_type'],
                        'deliverSlot' => $row['delivery_slot'],
                        'shopTypeId' => $this
                            ->db
                            ->get_where('shop_focus', array(
                            'shop_focus_id' => $row['focus_id']
                        ))->row()->shop_type,
                        'pImage' => $row['p_image'] ? base_url() . 'uploads/sales_image/' . $row['p_image'] : 'no_image',
                    );
                }
                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'coupons retrieved successfully',
                );

            }
            else if ($para1 == 'invoicefull')
            {

                $loop = $this
                    ->db
                    ->get_where('sale', array(
                    'sale_code' => $para2
                ))->result_array();
                foreach ($loop as $row)
                {

                    $vendor_detail = array(
                        'username' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->username,
                        'phone' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->phone,
                        'addressSelect' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->address1,

                    );

                    $data = array(
                        'orderId' => $row['sale_code'],
                        'cart' => json_decode($row['product_details'], true) ,
                        'userDetails' => json_decode($row['shipping_address'], true) ,
                        'grand_total' => $row['grand_total'],
                        'username' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->username,
                        'userId' => $row['buyer'],
                        'paymentType' => $row['payment_type'],
                        'paymentType' => $row['payment_type'],
                        'vendorDetails' => $vendor_detail,
                        'date' => date('M, d Y, h:i A', $row['sale_datetime']) ,
                        'orderType' => $row2['order_type']
                    );
                }
                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'invoice retrieved successfully',
                );

            }
            else if ($para1 == 'summaryReport')
            {

                $data = array(
                    'totalRevenue' => $this
                        ->Api_model
                        ->sumof_sammaryReport('vendor_invoice', 'vendor_id', $para4, 'settlement_value') ,
                    'thisMonthRevenue' => $this
                        ->Api_model
                        ->count_monthreport(date('m') , 'vendor_invoice', 'vendor_id', $para4, 'sum', 'settlement_value') ,
                    'totalPaid' => $this
                        ->Api_model
                        ->sumof_sammaryReportwithc('vendor_invoice', 'vendor_id', $para4, 'status', 'paid', 'settlement_value') ,
                    'totalDue' => $this
                        ->Api_model
                        ->sumof_sammaryReportwithc('vendor_invoice', 'vendor_id', $para4, 'status', 'due', 'settlement_value') ,
                    'totalOrders' => $this
                        ->Api_model
                        ->count_2wcopt('vendor_invoice', 'vendor_id', $para4, 'method !=', 'cancelled') ,
                    'thisMonthOrders' => $this
                        ->Api_model
                        ->count_monthreport(date('m') , 'vendor_invoice', 'vendor_id', $para4, 'count', '') ,
                    'totalPaidOrders' => $this
                        ->Api_model
                        ->count_3wcopt('vendor_invoice', 'vendor_id', $para4, 'method !=', 'cancelled', 'status', 'paid') ,
                    'totalDueOrders' => $this
                        ->Api_model
                        ->count_3wcopt('vendor_invoice', 'vendor_id', $para4, 'method !=', 'cancelled', 'status', 'due') ,
                );

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'summary report retrieved successfully',
                );

            }
            else if ($para1 == 'invoiceList')
            {
                $this
                    ->db
                    ->order_by('vendor_invoice_id', 'desc');
                $loop = $this
                    ->db
                    ->get_where('vendor_invoice', array(
                    'vendor_id' => $para4
                ))->result_array();
                $i = 0;
                foreach ($loop as $row)
                {

                    $i++;
                    $data[] = array(
                        'id' => $i,
                        'invoiceID' => '#' . $row['invoice_id'],
                        'itemAmount' => $row['order_amount'],
                        'settlementAmount' => $row['settlement_value'],
                        'commission' => $row['commission'],
                        'paymentMethod' => $row['method'],
                        'status' => $row['status'],
                        'date' => date("F j, Y", $row['order_date']) ,
                        'transactionID' => $row['transaction_id'] == '' ? 'waiting' : $row['transaction_id'],
                    );

                }

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'payment details retrieved successfully',
                );

            }
            else if ($para1 == 'invoiceView')
            {
                $loop = $this
                    ->db
                    ->get_where('sale', array(
                    'sale_code' => $para2
                ))->result_array();
                //'payment' => json_decode($row['payment_details']),
                //'orderDate' =>  date("F j, Y",$row['sale_datetime']),
                //'userId' =>   '12',
                //		'status' =>  $row['status'],
                //	'saleCode' => $row['sale_code'],
                //	'instanceDelivery' => false,
                foreach ($loop as $row)
                {
                    $addressShop = array(
                        'addressSelect' => $this
                            ->Api_model
                            ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'address1') ,
                        'username' => $this
                            ->Api_model
                            ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'display_name') ,
                        'phone' => $this
                            ->Api_model
                            ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'phone') ,
                    );

                    $data = array(
                        'id' => $row['sale_id'],
                        'productDetails' => json_decode($row['product_details']) ,
                        'addressUser' => json_decode($row['shipping_address']) ,
                        'addressShop' => $addressShop,
                        'payment' => json_decode($row['payment_details']) ,
                        'orderDate' => date("F j, Y", $row['sale_datetime']) ,
                        'status' => $row['status'],
                        'userId' => '12',
                        'saleCode' => $row['sale_code'],
                        'instanceDelivery' => false,
                    );

                }
                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'invoice details retrieved successfully',
                );
            }
            else if ($para1 == 'statusUpdate')
            {

                $data['status'] = $para3;

                $this
                    ->db
                    ->where('sale_code', $para2);
                $this
                    ->db
                    ->update('sale', $data);

                $responce = array(
                    'success' => true,
                );
            }
            else if ($para1 == 'search')
            {

                $this
                    ->db
                    ->like('sale_code', $para3);
                $this
                    ->db
                    ->select('sale_code, status, grand_total, buyer, payment_type, product_details, sale_datetime,order_type,delivery_slot');
                $this
                    ->db
                    ->order_by('sale_id', 'desc');
                $loop = $this
                    ->db
                    ->get_where('sale', array(
                    'vendor' => $para4
                ))->result_array();
                foreach ($loop as $row)
                {
                    $loop1 = json_decode($row['product_details'], true);
                    $i = 1;
                    foreach ($loop1 as $row1)
                    {
                        if ($i == 1)
                        {
                            $image = $row1['image'];
                            $name = $row1['product_name'];
                        }
                        $i++;
                    }
                    $date = date('M, d Y, h:i A', $row['sale_datetime']);
                    $data[] = array(
                        'orderId' => $row['sale_code'],
                        'status' => $row['status'],
                        'price' => $row['grand_total'],
                        'contact' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->phone,
                        'username' => $this
                            ->db
                            ->get_where('user', array(
                            'user_id' => $row['buyer']
                        ))->row()->username,
                        'userId' => $row['buyer'],
                        'paymentType' => $row['payment_type'],
                        'details' => '1',
                        'itemImage' => $image,
                        'date' => $date,
                        'itemTotal' => count($loop1) ,
                        'orderType' => $row['order_type'],
                        'deliverSlot' => $row['delivery_slot']
                    );
                }
                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'coupons retrieved successfully',
                );
            }
            else if ($para1 == 'amountUpdate')
            {

                $data['grand_total'] = $para2;
                $this
                    ->db
                    ->where('sale_code', $para3);
                $this
                    ->db
                    ->update('sale', $data);
                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'coupons retrieved successfully',
                );

            }
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();

        }

        echo json_encode($responce);

    }
    
    
    
    /** Driver Tips */

    function deliveryTips($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'do_add')
            {
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                $data['amount'] = $get_data->tips;
                $data['name'] = $get_data->name;
                $data['date'] = time();
                $this
                    ->db
                    ->insert('tips', $data);
                $response = array(
                    'success' => true
                );
            }
            elseif ($para1 == 'list')
            {
                $loop = $this
                    ->db
                    ->get_where('tips')
                    ->result_array();
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'name' => $row['name'],
                        'tips' => $row['amount'],
                        'id' => intval($row['tips_id'])
                    );

                }
                $response = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'Tis retrieved successfully',
                );

            }
            elseif ($para1 == "delete")
            {
                $this
                    ->db
                    ->where('tips_id', $para2);
                $this
                    ->db
                    ->delete('tips');
                $response = array(
                    'success' => true
                );
            }
            elseif ($para1 == "update")
            {
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                $data['amount'] = $get_data->tips;
                $data['name'] = $get_data->name;
                $this
                    ->db
                    ->where('tips_id', $para2);
                $this
                    ->db
                    ->update('tips', $data);
                $response = array(
                    'success' => true
                );

            }

        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }
        echo json_encode($response);

    }

    /** Vendor **/

    function vendor($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this ->Api_model  ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this ->input  ->get('api_token', true))
        {
            if ($para1 == 'list')
            {
                $this
                    ->db
                    ->order_by('shop_focus_id', 'asc');
                $this
                    ->db
                    ->select('shop_focus_id, title');
                $loop1 = $this
                    ->db
                    ->get_where('shop_focus')
                    ->result_array();

                foreach ($loop1 as $row1)
                {

                    $this
                        ->db
                        ->select('rating,logo,cover_image, general_detail, status, display_name, vendor_id,profile_complete,plan');
                    $this
                        ->db
                        ->order_by('vendor_id', 'desc');
                    $loop = $this
                        ->db
                        ->get_where('vendor', array(
                        'focus_id' => $row1['shop_focus_id']
                    ))->result_array();
                    $data = [];
                    foreach ($loop as $row)
                    {

                        if ($row['logo'] != '')
                        {
                            $img = base_url() . 'uploads/vendor_image/' . $row['logo'];
                        }
                        else
                        {
                            $img = 'no_image';
                        }

                        if ($row['cover_image'] != '')
                        {
                            $coverImage = base_url() . 'uploads/cover_image/' . $row['cover_image'];
                        }
                        else
                        {
                            $coverImage = 'no_image';
                        }
                        if ($row['status'] == 'unapproved')
                        {
                            $status = true;
                        }
                        else
                        {
                            $status = false;
                        }
                        $planName = '';
                        if($row['profile_complete']=='5'){
                         $planName =   $this->Api_model->singleselectbox('vendor_membership', 'vendor_membership_id',$row['plan'],'plan_name');
                        } else{
                             $planName = 'no';
                        }
                        
                        $data[] = array(
                            'shopId' => intval($row['vendor_id']) ,
                            'rating' => floatval($row['rating']) ,
                            'coverImage' => $coverImage,
                            'image' => $img,
                            'general' => json_decode($row['general_detail'], true) ,
                            'status' => $status,
                            'shopName' => $row['display_name'],
                            'planName' =>  $planName,
                            'planId' =>  $row['plan'],
                            'profileComplete' => $row['profile_complete'],
                            'profileStatus' => $row['status']
                         
                        );

                    }
                    $responce[] = array(
                        'id' => $row1['shop_focus_id'],
                        'shopType' => $row1['title'],
                        'vendor' => $data,
                    );

                }
                $responce = array(
                    'success' => true,
                    'data' => $responce,
                    'message' => 'vendors retrieved successfully',
                );

            }
            else if ($para1 == 'profile_view')
            {

                $this
                    ->db
                    ->select('general_detail, kyc_seller, rating');
                $loop = $this
                    ->db
                    ->get_where('vendor', array(
                    'vendor_id' => $para2
                ))->result_array();
                foreach ($loop as $row)
                {
                    $data = array(
                        'general' => json_decode($row['general_detail'], true) ,
                        'bankDetails' => json_decode($row['kyc_seller'], true) ,
                        'rating' => floatval($row['rating']) ,
                        'totalOrders' => $this
                            ->Api_model
                            ->count_2wcopt('vendor_invoice', 'vendor_id', $para2, 'method !=', 'cancelled') ,
                        'totalProducts' => $this
                            ->Api_model
                            ->count_wcopt('product', 'added_by', json_encode(array(
                            'type' => 'vendor',
                            'id' => $para2
                        ))) ,
                    );
                    $responce = array(
                        'success' => true,
                        'data' => $data,
                        'message' => 'profile retrieved successfully',
                    );
                }
            }
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }
        echo json_encode($responce);
    }

    /** pushnotification **/

    function pushNotification($para1 = '', $para2 = '')
    {

        if ($para1 == 'do_add')
        {
            if ($this
                ->Api_model
                ->singleselectbox('admin', 'admin_id', $para2, 'token') == $this
                ->input
                ->get('api_token', true))
            {
                $this
                    ->crud_model
                    ->file_up("image", "pushnotification", 1, '', 'no', '.png');
                $responce = array(
                    'success' => true,
                );
            }
        }

        echo json_encode($responce);
    }

    /** global dropdown */

    function globaldropdown($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            $this->db  ->order_by($para1 . '_id', 'asc');
            $loop = $this  ->db  ->get_where($para1)->result_array();
            if (count($loop) > 0)
            {
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row[$para1 . '_id'],
                        'name' => $row[$para2],
                    );

                }
                $responce = array(
                    'success' => true,
                    'data' => $data,
                );
            }

        }
        else
        {

            $responce = $this
                ->Api_model
                ->tokenfailed();
        }
        echo json_encode($responce);
    }

    /** global dropdown with single condition*/

    function globaldropdownsc($para1 = '', $para2 = '', $para3 = '', $para4 = '', $para5 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para5, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            $this
                ->db
                ->order_by($para1 . '_id', 'asc');

            $loop = $this
                ->db
                ->get_where($para1, array(
                $para3 => $para4
            ))->result_array();
            if (count($loop) > 0)
            {
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row[$para1 . '_id'],
                        'name' => $row[$para2],
                    );

                }
                $responce = array(
                    'success' => true,
                    'data' => $data,
                );

            }

        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();

        }
        echo json_encode($responce);

    }
    

    /** global Delete */
    
    function globalDelete($table='',$para1=''){
        $this->db->where($table.'_id',$para1);
        $this->db->delete($table);
         $responce = array(
                    'data' => 'success',
                    'success' => true,
                );
        
         echo json_encode($responce);

    }
    

    /** paymentupdate**/

    function paymentstatusupdate($para1 = '', $para2 = '', $para3 = '', $para4 = '')
    {

        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'Vendor')
            {
                $data['transaction_id'] = $para4;
                $this
                    ->db
                    ->where('invoice_id', $para2);
                $this
                    ->db
                    ->update('vendor_invoice', $data);
                $responce = array(
                    'success' => true,
                );
            }
            else if ($para1 == 'Driver')
            {

                $data['drivertransaction_id'] = $para4;
                $this
                    ->db
                    ->where('invoice_id', $para2);
                $this
                    ->db
                    ->update('vendor_invoice', $data);
                $responce = array(
                    'success' => true,
                );
            }
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();

        }
        echo json_encode($responce);
    }

    /** currency */
    function currency($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'do_add')
            {
                $data['currency_symbol'] = $this
                    ->input
                    ->post('currencySymbol');
                $data['currency_name'] = $this
                    ->input
                    ->post('currencyName');
                $data['country'] = $this
                    ->input
                    ->post('country');
                $data['date'] = time();
                $this
                    ->db
                    ->insert('currency_method', $data);
                $id = $this
                    ->db
                    ->insert_id();
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['image'] = 'currency_' . $id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("image", "currency", $id, '', 'no', '.' . $ext);
                $this
                    ->db
                    ->where('currency_method_id', $id);
                $this
                    ->db
                    ->update('currency_method', $databanner);
                $responce = array(
                    'success' => true,
                );

            }
            else if ($para1 == 'list')
            {

                $loop = $this
                    ->db
                    ->get_where('currency_method')
                    ->result_array();
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['currency_method_id'],
                        'currencyName' => $row['currency_symbol'],
                        'currencySymbol' => $row['currency_name'],
                        'country' => $row['country'],
                        'uploadImage' => base_url() . 'uploads/currency_image/' . $row['image'],
                    );
                }

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'currency method retrieved successfully',
                );

            }
            else if ($para1 == 'update')
            {

                $data['currency_symbol'] = $this
                    ->input
                    ->post('currencySymbol');
                $data['currency_name'] = $this
                    ->input
                    ->post('currencyName');
                $data['country'] = $this
                    ->input
                    ->post('country');
                $id = $para2;
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $data['image'] = 'currency_' . $id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("image", "currency", $id, '', 'no', '.' . $ext);
                $this
                    ->db
                    ->where('currency_method_id', $id);
                $this
                    ->db
                    ->update('currency_method', $databanner);
                $responce = array(
                    'success' => true,
                );

            }
            else if ($para1 == 'delete')
            {
                $this
                    ->db
                    ->where('currency_method_id', $para2);
                $this
                    ->db
                    ->delete('currency_method');
                $response = array(
                    'success' => true
                );
            }
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }

        echo json_encode($responce);
    }

    /** profileupdate */

    function profileupdate($para1 = '', $para2 = '', $para3 = '')
    {

        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            if ($para1 == 'general')
            {
                $this
                    ->db
                    ->where('type', "system_name");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->systemName
                ));
                $this
                    ->db
                    ->where('type', "system_email");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->email
                ));
                $this
                    ->db
                    ->where('type', "system_title");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->systemTitle
                ));

                $this
                    ->db
                    ->where('type', "address");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->address
                ));
                $this
                    ->db
                    ->where('type', "phone");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->phone
                ));
                $this
                    ->db
                    ->where('type', "short_description");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->description
                ));

                $this
                    ->db
                    ->where('type', "google_api_key");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->api
                ));

                $responce = array(
                    'success' => true,
                    'data' => 'success'
                );

            }
            else if ($para1 == 'payment')
            {

                $this
                    ->db
                    ->where('type', "rayzorpay");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->rayzopay
                ));
                $this
                    ->db
                    ->where('type', "strip");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->strip
                ));
                $this
                    ->db
                    ->where('type', "currency");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->currency
                ));

                $this
                    ->db
                    ->where('type', "right_postition");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->currencyPosition
                ));

                $this
                    ->db
                    ->where('type', "minimum_purchase");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->minimumPurchase
                ));

                $this
                    ->db
                    ->where('type', "cancel_timer");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->cancelTimer
                ));

                $this
                    ->db
                    ->where('type', "driver_commission");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->driverCommission
                ));

                $responce = array(
                    'success' => true,
                    'data' => 'success'
                );

            }
            else if ($para1 == 'smtp')
            {

                $this
                    ->db
                    ->where('type', "smtp_host");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->host
                ));
                $this
                    ->db
                    ->where('type', "smtp_port");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->port
                ));
                $this
                    ->db
                    ->where('type', "smtp_user");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->user
                ));

                $this
                    ->db
                    ->where('type', "smtp_pass");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->password
                ));
                $responce = array(
                    'success' => true,
                    'data' => 'success'
                );

            }
            else if ($para1 == 'delivery')
            {

                $this
                    ->db
                    ->where('type', "autoassing");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->autoassing
                ));
                $this
                    ->db
                    ->where('type', "delivery_tips");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->deliveryTips
                ));
                $this
                    ->db
                    ->where('type', "radius");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->radius
                ));

                $this
                    ->db
                    ->where('type', "instant_delivery");
                $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->instantDelivery
                ));
                 $this
                    ->db
                    ->where('type', "driver_radius");
                 $this
                    ->db
                    ->update('general_settings', array(
                    'value' => $get_data->driverRadius
                ));
                $responce = array(
                    'success' => true,
                    'data' => 'success'
                );

            }

            else if ($para1 == 'profileview')
            {

                $general = array(
                    'systemName' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '1', 'value') ,
                    'systemTitle' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '3', 'value') ,
                    'email' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '2', 'value') ,
                    'address' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '4', 'value') ,
                    'description' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '80', 'value') ,
                    'phone' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '5', 'value') ,
                    'api' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '67', 'value') ,
                );

                $payment = array(
                    'rayzopay' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '84', 'value') ,
                    'currencyPosition' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '83', 'value') ? true : false,
                    'currency' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '82', 'value') ,
                    'strip' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '85', 'value') ,
                    'driverCommission' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '95', 'value') ,
                    'cancelTimer' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '96', 'value') ,
                    'minimumPurchase' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '86', 'value') ,
                );

                $delivery = array(
                    'radius' => floatval($this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '93', 'value')) ,
                    'driverRadius' => floatval($this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '97', 'value')) ,
                    'autoassing' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '91', 'value') ? true : false,
                    'deliveryTips' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '92', 'value') ? true : false,
                    'instantDelivery' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '94', 'value') ? true : false,
                );

                $smtp = array(
                    'host' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '72', 'value') ,
                    'port' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '73', 'value') ,
                    'user' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '74', 'value') ,
                    'password' => $this
                        ->Api_model
                        ->get_type_name_by_id('general_settings', '75', 'value') ,
                );

                $data = array(
                    'general' => $general,
                    'payment' => $payment,
                    'delivery' => $delivery,
                    'smtp' => $smtp
                );

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'profile retrieved successfully',
                );

            }
        }
        else
        {

            $responce = $this
                ->Api_model
                ->tokenfailed();
        }

        echo json_encode($responce);
    }

    
    
/** payment gateway */
    
    function paymentGateway($para1='', $para2=''){
        if($para1=='gateway'){
             $data = array(
                    'rayzorPaySK' => $this ->Api_model ->get_type_name_by_id('general_settings', '84', 'value') ,
                    'upiId' => $this ->Api_model ->get_type_name_by_id('general_settings', '108', 'value') ,
                    'paypalClientID' => $this ->Api_model ->get_type_name_by_id('general_settings', '98', 'value') ,
                    'fwpbfPublickey' => $this ->Api_model ->get_type_name_by_id('general_settings', '99', 'value') ,
                    'stripePK' => $this ->Api_model ->get_type_name_by_id('general_settings', '100', 'value') ,
                    'stripeSK' => $this ->Api_model ->get_type_name_by_id('general_settings', '101', 'value') ,
                    'rayzorPay' => $this ->Api_model ->get_type_name_by_id('general_settings', '102', 'value')? true : false,
                    'upiID' => $this ->Api_model ->get_type_name_by_id('general_settings', '103', 'value')? true : false,
                    'paypal' => $this ->Api_model ->get_type_name_by_id('general_settings', '104', 'value')? true : false,
                    'flutterWay' => $this ->Api_model ->get_type_name_by_id('general_settings', '106', 'value')? true : false,
                    'stripe' => $this ->Api_model ->get_type_name_by_id('general_settings', '107', 'value')? true : false,
                   
                 
                );
          $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'payment retrieved successfully',
                );
        } else if($para1=='gatewayupdate'){
             
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                $this ->db->where('type', "razorpay");
                $this->db->update('general_settings', array( 'value' => $get_data->rayzorPaySK  ));
            
            
                $this ->db->where('type', "UpiIdk");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->upiId  ));
            
                $this ->db->where('type', "paypalClientID");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->paypalClientID  ));
            
                $this ->db->where('type', "fwpbfPublickey");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->fwpbfPublickey  ));
            
                $this ->db->where('type', "stripe_p_key");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->stripePK  ));
            
                $this ->db->where('type', "stripe_s_key");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->stripeSK  ));
            
              $this ->db->where('type', "rayzorPay");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->rayzorPay  ));
            
              $this ->db->where('type', "upiID");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->upiID  ));
            
              $this ->db->where('type', "paypal");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->paypal  ));
            
              $this ->db->where('type', "flutterwave");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->flutterWay  ));
            
             $this ->db->where('type', "stripe");
                $this  ->db ->update('general_settings', array(   'value' => $get_data->stripe  ));
            
            
            
              $responce = array(
                    'success' => true,
                    'message' => 'payment update successfully',
                );
        }
          echo json_encode($responce);
    }
    
    function deliveryFees($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'do_add')
            {
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                echo 1;
                $data['from_range'] = $get_data->fromRange;
                $data['to_range'] = $get_data->toRange;
                $data['amount'] = $get_data->fees;
                $data['name'] = $get_data->name;
                $this
                    ->db
                    ->insert('logistics_pricing', $data);
                $response = array(
                    'success' => true
                );
            }

            elseif ($para1 == 'list')
            {

                $loop = $this
                    ->db
                    ->get_where('logistics_pricing')
                    ->result_array();
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'fromRange' => $row['from_range'],
                        'toRange' => $row['to_range'],
                        'name' => $row['name'],
                        'fees' => $row['amount'],
                        'id' => intval($row['logistics_pricing_id'])
                    );

                }

                $response = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'deliveryFees retrieved successfully',
                );

            }
            else if ($para1 == 'delete')
            {

                $this
                    ->db
                    ->where('logistics_pricing_id', $para2);
                $this
                    ->db
                    ->delete('logistics_pricing');
                $response = array(
                    'success' => true
                );

            }
            else if ($para1 == 'update')
            {
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                $data['from_range'] = $get_data->fromRange;
                $data['to_range'] = $get_data->toRange;
                $data['amount'] = $get_data->fees;
                $data['name'] = $get_data->name;
                $this
                    ->db
                    ->where('logistics_pricing_id', $para2);
                $this
                    ->db
                    ->update('logistics_pricing', $data);
                $response = array(
                    'success' => true
                );
            }

        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }

        echo json_encode($response);

    }

    /** user **/

    function user($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'list')
            {
                $this
                    ->db
                    ->select('username,image,email, date, user_id, phone,selected_address,image');
                $this
                    ->db
                    ->order_by('user_id', 'desc');
                $loop = $this
                    ->db
                    ->get_where('user')
                    ->result_array();

                foreach ($loop as $row)
                {

                    $data[] = array(
                        'userName' => $row['username'],
                        'email' => $row['email'],
                        'image' => $row['image'] != 'noimg' ? base_url() . 'uploads/user_image/' . $row['image'] : 'no_image',
                        'date' => date('d  M,Y h:i A', $row['date']) ,
                        'totalSale' => $this
                            ->Api_model
                            ->sumof_w2c('sale', 'buyer', $row['user_id'], 'status !=', 'cancelled', 'grand_total') ,
                        'id' => intval($row['user_id']) ,
                        'phone' => $row['phone'],
                        'address' => $row['selected_address'],
                        'status' => $row['status'],
                    );
                    $responce = array(
                        'success' => true,
                        'data' => $data,
                        'message' => 'user retrieved successfully',
                    );

                }

            }
            else if ($para1 == 'profile_view')
            {

                $this
                    ->db
                    ->select('general_detail, bank_details, rating');
                $loop = $this
                    ->db
                    ->get_where('vendor', array(
                    'vendor_id' => $para2
                ))->result_array();
                foreach ($loop as $row)
                {
                    $data = array(
                        'general' => json_decode($row['general_detail'], true) ,
                        'bankDetails' => json_decode($row['	kyc_seller'], true) ,
                        'rating' => floatval($row['rating']) ,
                        'totalOrders' => $this
                            ->Api_model
                            ->count_2wcopt('vendor_invoice', 'vendor_id', $para2, 'method !=', 'cancelled') ,
                        'totalProducts' => $this
                            ->Api_model
                            ->count_wcopt('product', 'added_by', json_encode(array(
                            'type' => 'vendor',
                            'id' => $para2
                        ))) ,
                    );
                    $responce = array(
                        'success' => true,
                        'data' => $data,
                        'message' => 'profile retrieved successfully',
                    );
                }
            }
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }
        echo json_encode($responce);
    }
    
    
    /** admin **/
    function driverDetails($para1='', $para2='', $para3=''){
     
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {
          if ($para1 == 'do_add')
            {
              
               $this->db->select('deliver_assignedtime,status,delivery_assigned');
               $loop = $this->db->get_where('sale',array('sale_code'=>$para2))->result_array();
            foreach ($loop as $row) {
               
              $data = array(
                            'driverName' =>$this->Api_model ->singleselectbox('driver', 'driver_id', $row['delivery_assigned'], 'name'),
                            'phone' => $this->Api_model ->singleselectbox('driver', 'driver_id', $row['delivery_assigned'], 'phone'),
                            'status' => $row['status'],
                            'orderDate' => date('d  M,Y h:i A', $row['deliver_assignedtime']),
                            'id' => $row['delivery_assigned'],
                        );
            }
              
              
              
               $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'driver details retrieved successfully',
            ); 
              
          }
        }  else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();

        }
        echo json_encode($responce);
    }
    



    /** password update */
    function passwordManagement($para1 = '', $para2 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'update')
            {
                if ($this
                    ->Api_model
                    ->singleselectbox('admin', 'admin_id', $para2, 'password') == sha1($this
                    ->input
                    ->post('prePassword')))
                {
                    $data['password'] = sha1($this
                        ->input
                        ->post('password'));
                    $this
                        ->db
                        ->where('admin_id', $para2);
                    $this
                        ->db
                        ->update('admin', $data);
                    $responce = array(
                        'success' => true,
                    );
                }
                else
                {
                    $responce = array(
                        'success' => false,
                    );
                }
            }
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }

        echo json_encode($responce);
    }
    
 
    

    /** driver **/

    function Driver($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'list')
            {
                $this
                    ->db
                    ->select('driver_id,name,email,gender, age, address, phone, status,drivingMode,last_name,image,store_id,livestatus,date,block');
                $this
                    ->db
                    ->order_by('driver_id', 'desc');
                $loop = $this
                    ->db
                    ->get_where('driver')
                    ->result_array();

                foreach ($loop as $row)
                {

                    $data[] = array(
                        'userName' => $row['name'],
                        'email' => $row['email'],
                        'gender' => $row['gender'],
                        'age' => intval($row['age']) ,
                        'address' => $row['address'],
                        'phone' => $row['phone'],
                        'status' => $row['status'],
                        'image' => $row['image'] != '' ? $img = base_url() . 'uploads/driver_image/' . $row['image'] : 'no_image',
                        'type' => '1',
                        'id' => $row['driver_id'] ,
                        'mode' => $row['drivingMode'],
                        'storeId' => $row['store_id'],
                        'date' => date("F j, Y", $row['date']),
                        'liveStatus' => $row['livestatus'],
                        'last_name' => $row['last_name'],
                        'block' => $row['block']=='true'?true:false,

                    );
                    $responce = array(
                        'success' => true,
                        'data' => $data,
                        'message' => 'driver retrieved successfully',
                    );

                }

            }
            else if ($para1 == 'profile_view')
            {

                $this
                    ->db
                    ->select('general_detail, bank_details, rating');
                $loop = $this
                    ->db
                    ->get_where('vendor', array(
                    'vendor_id' => $para2
                ))->result_array();
                foreach ($loop as $row)
                {
                    $data = array(
                        'general' => json_decode($row['general_detail'], true) ,
                        'bankDetails' => json_decode($row['bank_details'], true) ,
                        'rating' => floatval($row['rating']) ,
                        'totalOrders' => $this
                            ->Api_model
                            ->count_2wcopt('vendor_invoice', 'vendor_id', $para2, 'method !=', 'cancelled') ,
                        'totalProducts' => $this
                            ->Api_model
                            ->count_wcopt('product', 'added_by', json_encode(array(
                            'type' => 'vendor',
                            'id' => $para2
                        ))) ,
                    );
                    $responce = array(
                        'success' => true,
                        'data' => $data,
                        'message' => 'profile retrieved successfully',
                    );
                }
            }
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }
        echo json_encode($responce);
    }

    /** dashboard */

    function dashboard($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            if ($para1 == 'topbar')
            {

                $neworders = $this
                    ->Api_model
                    ->count_wcopt('sale', 'status', 'Placed');
                $processing = $this
                    ->Api_model
                    ->count_wcopt('sale', 'status', 'Accepted');
                $outForDelivery = $this
                    ->Api_model
                    ->count_wcopt('sale', 'status', 'Shipped');
                $completed = $this
                    ->Api_model
                    ->count_wcopt('sale', 'status', 'Completed');

                $codearn = $this
                    ->Api_model
                    ->sumof_w2c('vendor_invoice', 'method', 'cash on delivery', 'method !=', 'cancelled', 'settlement_value');
                $onlineearn = $this
                    ->Api_model
                    ->sumof_w2c('vendor_invoice', 'method', 'online', 'method !=', 'cancelled', 'settlement_value');
                $date = date('d-m-Y');
                $fromtime = DateTime::createFromFormat('d-m-Y H:i:s', $date . ' 00:00:00', new DateTimeZone('UTC'));
                $totime = DateTime::createFromFormat('d-m-Y H:i:s', $date . ' 11:59:59', new DateTimeZone('UTC'));

                $totalorders = $neworders + $processing + $outForDelivery + $completed;
                $newOrdersPercent = intval(($neworders * 100) / $totalorders);
                $processingPercent = intval(($processing * 100) / $totalorders);
                $outForDeliveryPercent = intval(($outForDelivery * 100) / $totalorders);
                $completePercent = intval(($completed * 100) / $totalorders);

                $data = array(

                    'newOrders' => $neworders,
                    'processing' => $processing,
                    'outForDelivery' => $outForDelivery,
                    'completed' => $completed,
                    'newOrdersPercent' => $newOrdersPercent,
                    'processingPercent' => $processingPercent,
                    'outForDeliveryPercent' => $outForDeliveryPercent,
                    'completePercent' => $completePercent,
                    'totalEarnCod' => round($codearn, 2) ,
                    'totalEarnOnline' => round($onlineearn, 2) ,
                    'totalEarn' => round($codearn + $onlineearn, 2) ,
                    'vendorNew' => $this
                        ->Api_model
                        ->count_2wcopt('vendor', 'create_timestamp >=', $fromtime->getTimestamp() , 'create_timestamp <=', $totime->getTimestamp()) ,
                    'userNew' => $this
                        ->Api_model
                        ->count_2wcopt('user', 'date >=', $fromtime->getTimestamp() , 'date <=', $totime->getTimestamp()) ,
                    'driverNew' => $this
                        ->Api_model
                        ->count_2wcopt('driver', 'date >=', $fromtime->getTimestamp() , 'date <=', $totime->getTimestamp()) ,
                    'thisMonthEarn' => round($this
                        ->Api_model
                        ->count_monthreportadmin(date('m') , 'vendor_invoice', 'sum', 'settlement_value') , 2)
                );
                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'dashboard retrieved successfully',
                );
            }
            else if ($para1 == 'bargraph1')
            {

                for ($i = 1;$i <= 12;$i++)
                {
                    $data[] = array(
                        'month' => $i,
                        'orders' => $this
                            ->Api_model
                            ->count_totalsalesreport($i, 'vendor_invoice', 'commission') ,
                    );

                }

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'dashboard bargraph1 retrieved successfully',
                );
            }
            else if ($para1 == 'topVendors')
            {

                $this
                    ->db
                    ->order_by('rating', 'desc');
                $this
                    ->db
                    ->limit(10);
                $this
                    ->db
                    ->select('display_name, vendor_id, cover_image,logo');
                $loop = $this
                    ->db
                    ->get_where('vendor')
                    ->result_array();
                if (count($loop) > 0)
                {
                    foreach ($loop as $row)
                    {

                        if ($row['cover_image'] != '')
                        {
                            $coverImage = base_url() . 'uploads/cover_image/' . $row['cover_image'];
                        }
                        else
                        {
                            $coverImage = 'no_image';
                        }

                        if ($row['logo'] != '')
                        {
                            $img = base_url() . 'uploads/vendor_image/' . $row['logo'];
                        }
                        else
                        {
                            $img = 'no_image';
                        }
                        $data[] = array(
                            'name' => $row['display_name'],
                            'image' => $coverImage,
                            'logo' => $img,
                            'id' => $row['vendor_id'],
                        );

                    }
                    $responce = array(
                        'success' => true,
                        'data' => $data,
                    );
                }

            }
            else if ($para1 == 'bargraph2')
            {

                $responce = array(
                    'success' => true,
                    'data' => $this
                        ->Api_model
                        ->count_2wcopt('sale', 'vendor', $para2, 'status', 'Placed') ,
                    'message' => 'order count successfully',
                );
            }
            else if ($para1 == 'dashboardcounter')
            {
                $data[] = array(
                    'imagePath' => 'assets/img/grocery.png',
                    'titleTxt' => 'Sales',
                    'startColor' => '#FA7D82',
                    'endColor' => '#FFB295',
                    'subtext' => 'Total Sales',
                    'total' => $this
                        ->Api_model
                        ->count_wcopt('sale', 'status !=', 'cancelled')
                );

                $data[] = array(
                    'imagePath' => 'assets/img/food.png',
                    'titleTxt' => 'User',
                    'startColor' => '#738AE6',
                    'endColor' => '#5C5EDD',
                    'subtext' => 'Total Users',
                    'total' => $this
                        ->Api_model
                        ->count_wcopt('user', 'status', 'success')
                );
                $data[] = array(
                    'imagePath' => 'assets/img/vendor.png',
                    'titleTxt' => 'Shops',
                    'startColor' => '#FE95B6',
                    'endColor' => '#FF5287',
                    'subtext' => 'Total Shops',
                    'total' => $this
                        ->Api_model
                        ->count_wcopt('vendor', 'status', 'approved')
                );

                $data[] = array(
                    'imagePath' => 'assets/img/driverboy.png',
                    'titleTxt' => 'Driver',
                    'startColor' => '#6F72CA',
                    'endColor' => '#1E1466',
                    'subtext' => 'Total Drivers',
                    'total' => $this
                        ->Api_model
                        ->count_wcopt('driver', 'status', 'success')
                );
                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'dashborad count successfully',
                );

            }

        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }
        echo json_encode($responce);
    }
   /** banner **/
    function banner($para1 = '', $para2 = '', $para3 = '', $para4 = '', $para5 = '')
    {

        if ($para1 == 'do_add')
        {
            if ($this
                ->Api_model
                ->singleselectbox('admin', 'admin_id', $para4, 'token') == $this
                ->input
                ->get('api_token', true))
            {
                $data['title'] = $this->input ->post('title');
                $data['para'] = $this ->input ->post('para');
                $data['superCategoryId'] = '1';
                
                $data['type'] = $this  ->input->post('type');
                 $data['redirect_type'] = $this->input->post('redirect_type');
                $data['admin'] = $para2;
                $data['date'] = time();
                $this
                    ->db
                    ->insert('banner_master', $data);
                $id = $this
                    ->db
                    ->insert_id();

                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['image'] = 'banner_master_' . $id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("image", "banner_master", $id, '', 'no', '.' . $ext);
                $this
                    ->db
                    ->where('banner_master_id', $id);
                $this
                    ->db
                    ->update('banner_master', $databanner);
                $responce = array(
                    'success' => true,
                );

            }
            else
            {
                $responce = $this
                    ->Api_model
                    ->tokenfailed();
            }

        }
        else if ($para1 == 'list')
        {

            if ($this
                ->Api_model
                ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
                ->input
                ->get('api_token', true))
            {
                
               
                $loop = $this->db ->get_where('banner_master', array('type' => $para2  ))->result_array();
                if (count($loop) > 0)
                {
                    foreach ($loop as $row)
                    {
                        $data[] = array(
                            'type' => $row['type'],
                            'uploadImage' => base_url() . 'uploads/banner_master_image/' . $row['image'],
                            'title' => $row['title'],
                            'para' => $row['para'],
                            'id' => $row['banner_master_id'],
                        );

                    }
                    $responce = array(
                        'success' => true,
                        'data' => $data,
                    );
                }
            }
            else
            {
                $responce = $this
                    ->Api_model
                    ->tokenfailed();
            }

        }
        else if ($para1 == 'delete')
        {

            $this ->db->where('banner_master_id', $para2);
            $this  ->db
                ->delete('banner_master');
            $responce = array(
                'success' => true,
            );
        }
        else if ($para1 == 'update')
        {
            $data['title'] = $this  ->input ->post('title');
            $data['para'] = $this->input ->post('para');
            $data['type'] = $this  ->input->post('type');
             $data['redirect_type'] = $this->input->post('redirect_type');
             $path = $_FILES['image']['name'];
               $ext = pathinfo($path, PATHINFO_EXTENSION);
            $time =time();
            $data['image'] = 'banner_master_' .$time.$id . '.' . $ext;
             if ($ext != '')
            {
			
	               $this
                    ->crud_model
                    ->file_up("image", "banner_master", $time.$id, '', 'no', '.' . $ext);
            }

            $id = $para3;
            $this
                ->db
                ->where('banner_master_id', $id);
            $this
                ->db
                ->update('banner_master', $data);
           
         
           
            $responce = array(
                'success' => true,
            );
        }

        echo json_encode($responce);
    }
    
       
    /** singlestatus **/
    
    function singleStatus($table='',$id='',$select='', $value=''){
            $data[$select] = '';
        $data[$select] = $value;
        $this->db->where($table.'_id', $id);
        $response = $this ->db->update($table, $data);
        if($response){
            echo "true";
        } else {
            echo "false";
        }

    }
    
    
 /** vendor member ship **/
  function statusMemberUpdate($vendorid='',$id=''){
    
           
        $days = $this->Api_model->singleselectbox('vendor_membership', 'vendor_membership_id', $id, 'validity');
         $new_expire = time() + ($days * 24 * 60 * 60);
        $data['member_expire_timestamp'] = $new_expire;
        $data['status'] =  'approved';
        $data['profile_complete'] =  '5';
        $this->db->where('vendor_id', $vendorid);
        $this ->db->update('vendor', $data);
    }
    
    
    
    /** export action **/

    function export_action($para1 = '', $para2 = '', $para3 = '')
    {
        $this
            ->load
            ->model("IEport_model");
        $date = date("Y-m-d");

        if ($para1 == 'vendor')
        {
            $filename = $date . '_vendor.csv';
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv; ");
            $usersData = $this
                ->IEport_model
                ->fetch_vendor('export');
        }

        $file = fopen('php://output', 'w');

        $header = array(
            "",
            "",
            ""
        );
        fputcsv($file, $header);
        foreach ($usersData as $key => $line)
        {
            fputcsv($file, $line);
        }

        fputcsv($file, '1');
        fclose($file);
        exit;
    }
    /** packageItem **/
    function packageItem($para1 = '', $para2 = '', $para3 = '')
    {
        if ($para1 == 'add')
        {

            $data['name'] = $this
                ->input
                ->post('name');
            $this
                ->db
                ->insert('packageitem', $data);
            $id = $this
                ->db
                ->insert_id();
            $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $databanner['banner'] = 'packageitem_' . $id . '.' . $ext;
            $this
                ->crud_model
                ->file_up("image", "packageitem", $id, '', 'no', '.' . $ext);
            $this
                ->db
                ->where('packageitem_id', $id);
            $this
                ->db
                ->update('packageitem', $databanner);
            $responce = array(
                'success' => true,
            );

        }
        else if ($para1 == 'list')
        {
            $this
                ->db
                ->order_by('packageitem_id', 'desc');
            $loop = $this
                ->db
                ->get_where('packageitem')
                ->result_array();
            if (count($loop) > 0)
            {
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'name' => $row['name'],
                        'image' => base_url() . 'uploads/packageitem_image/' . $row['banner'],
                        'id' => $row['packageitem_id'],
                    );

                }
                $responce = array(
                    'success' => true,
                    'data' => $data,
                );
            }

        }
        else if ($para1 == 'delete')
        {

            $this
                ->db
                ->where('packageitem_id', $para2);
            $this
                ->db
                ->delete('packageitem');
            $responce = array(
                'success' => true,
            );
        }
        else if ($para1 == 'edit')
        {

            $data['name'] = $this
                ->input
                ->post('name');
            $this
                ->db
                ->where('packageitem_id', $para2);
            $this
                ->db
                ->update('packageitem', $data);
            $id = $para2;
            $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ($ext != NULL)
            {
                $databanner['banner'] = 'packageitem_' . $id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("image", "packageitem", $id, '', 'no', '.' . $ext);
                $this
                    ->db
                    ->where('packageitem_id', $id);
                $this
                    ->db
                    ->update('packageitem', $databanner);
            }
            $responce = array(
                'success' => true,
            );
        }

        echo json_encode($responce);
    }

    /** shopType **/

    function shopFocusType($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            if ($para1 == 'list')
            {

                $loop = $this
                    ->db
                    ->get_where('shop_focus')
                    ->result_array();
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => intval($row['shop_focus_id']) ,
                        'focusTypeName' => $row['title'],
                        'colorCode' => $row['color_code'],
                        'previewImage' => $row['preview_image'] ? base_url() . 'uploads/preview_image/' . $row['preview_image'] : 'no_image',
                        'coverImage' => $row['cover_image'] ? base_url() . 'uploads/shoptypecover_image/' . $row['cover_image'] : 'no_image',
                        'commission' => $row['commission'],
                        'shopTypeId' => $row['shop_type'],
                        'superCategory' => $row['supercatgeory_id'],
                        'date' => $row['date'],

                    );

                }

                $response = array(
                    'data' => $data,
                    'message' => 'shop Type retrieved successfully',
                    'success' => true
                );

            }
            else if ($para1 == 'do_add')
            {

                $data['title'] = $this  ->input  ->post('title');
                $data['color_code'] = $this ->input ->post('color_code');
                $data['commission'] = '0';
                $data['shop_type'] = $this ->input->post('shop_type');
                $data['supercatgeory_id'] = $this ->input->post('superCategory');
                $data['date'] = time();
                $data['status'] = 'success';
                $this
                    ->db
                    ->insert('shop_focus', $data);
                $id = $this
                    ->db
                    ->insert_id();

                $path = $_FILES['previewImage']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['preview_image'] = 'preview_' . $id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("previewImage", "preview", $id, '', 'no', '.' . $ext);

                $path = $_FILES['coverImage']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['cover_image'] = 'shoptypecover_' . $id . '.' . $ext;
                $this
                    ->crud_model
                    ->file_up("coverImage", "shoptypecover", $id, '', 'no', '.' . $ext);

                $this
                    ->db
                    ->where('shop_focus_id', $id);
                $this
                    ->db
                    ->update('shop_focus', $databanner);

                $response = array(

                    'success' => true
                );

            }
            else if ($para1 == 'update')
            {

                $data['title'] = $this
                    ->input
                    ->post('title');
                $data['color_code'] = $this
                    ->input
                    ->post('color_code');
                $data['commission'] = '0';
                $data['shop_type'] = $this
                    ->input
                    ->post('shop_type');
               $data['supercatgeory_id'] = $this ->input->post('superCategory');
                $id = $para2;

                $path = $_FILES['previewImage']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $time = time();
                if ($ext != '')
                {
                    $data['preview_image'] = 'preview_' .$time. $id . '.' . $ext;
                    $this
                        ->crud_model
                        ->file_up("previewImage", "preview", $time.$id, '', 'no', '.' . $ext);
                }

                $path = $_FILES['coverImage']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if ($ext != '')
                {
                    $data['cover_image'] = 'shoptypecover_' .$time. $id . '.' . $ext;
                    $this
                        ->crud_model
                        ->file_up("coverImage", "shoptypecover", $time.$id, '', 'no', '.' . $ext);
                }

                $this
                    ->db
                    ->where('shop_focus_id', $id);
                $this
                    ->db
                    ->update('shop_focus', $data);

                $response = array(

                    'success' => true
                );

            }
            else if ($para1 == 'delete')
            {
                $this
                    ->db
                    ->where('shop_focus_id', $para2);
                $this
                    ->db
                    ->delete('shop_focus');
                $responce = array(
                    'success' => true,
                    'data' => true,
                    'message' => 'shop focus  successfully',
                );
            }

        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }
        echo json_encode($response);

    }

    /** profile update**/
    function profileUpdateadmin($para1 = '', $para2 = '', $para3 = '')
    {

        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            $content_data = file_get_contents("php://input");
            $get_data = json_decode($content_data);

            if ($para1 == 'general')
            {

                $data['name'] = $get_data->name;
                $data['phone'] = $get_data->phone;
                $data['address'] = $get_data->address;
                $this
                    ->db
                    ->where('admin_id', $para2);
                $this
                    ->db
                    ->update('admin', $data);
                $responce = array(
                    'success' => true,
                    'data' => 'success'
                );

            }
        }
        echo json_encode($responce);
    }
   /** profileimage **/
    function profileimage($para1 = '', $para2 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para1, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            $id = $para1;
            $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $data_banner['image'] = 'vendor_' . $id . '.' . $ext;
            $this
                ->crud_model
                ->file_up("image", "vendor", $id, '', 'no', '.png');
            $this
                ->db
                ->where('admin_id', $id);
            $this
                ->db
                ->update('admin', $data_banner);
            $responce = array(
                'success' => true,
            );
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }

        echo json_encode($responce);
    }

    function coverimage($para1 = '', $para2 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('admin', 'admin_id', $para1, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            $id = $para1;
            $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $data_banner['cover_image'] = 'cover_' . $id . '.jpg';
            $this
                ->crud_model
                ->file_up("image", "cover", $id, '', 'no', '.jpg');
            $this
                ->db
                ->where('admin_id', $id);
            $this
                ->db
                ->update('admin', $data_banner);
            $responce = array(
                'success' => true,
            );
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();
        }

        echo json_encode($responce);
    }
    
    /** H category */
    
    
    function h_category($para1='', $para2=''){
     
        if($para1=='add'){
             $data['category_name'] = $this->input->post('categoryName');
             $data['date'] = time();
             $this ->db->insert('h_category', $data);
            
                $id = $this ->db ->insert_id();
            
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $databanner['image'] = 'h_category_' . $id . '.' . $ext;
                $this->crud_model->file_up("image", "h_category", $id, '', 'no', '.' . $ext);
                $this->db ->where('h_category_id', $id);
                $this ->db ->update('h_category', $databanner);
                $responce = array(
                    'success' => true,
                );
            
        } else if($para1=='list'){
               
              $this->db->order_by('h_category_id', 'desc');
              $loop = $this ->db->get_where('h_category')->result_array();
           
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['h_category_id'],
                        'categoryName' => $row['category_name'],
                        'uploadImage' => $row['image'] ? base_url() . 'uploads/h_category_image/' . $row['image'] : 'no_image',
                    );

                }
            
             $responce = array(
                    'data' => $data,
                    'message' => 'H_category retrieved successfully',
                    'success' => true
                );
        } else if($para1=='edit'){
                $id = $para2;
            
                $data['category_name'] = $this->input->post('categoryName');
                $path = $_FILES['image']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $data['image'] = 'h_category_' .time().$id . '.png';
                $this->db ->where('h_category_id', $id);
                $this ->db ->update('h_category', $data);
         
                $this->crud_model->file_up("image", "h_category", time().$id, '', 'no', '.' . $ext);
               
                $responce = array(
                    'success' => true,
                );
        }
        
        
        echo json_encode($responce);
    }
    
    
    
    
     //sub category//
    
    function h_sub_category($para1 = '', $para2 = '')
    {
       
      
		if ($para1 == 'do_add') {
            $data['sub_category_name']          = $this->input->post('nameglobaldropdownsc');
            $data['category_id']                = $this->input->post('category_id');
            $data['date']                        = time();
		    
            $this->db->insert('h_sub_category', $data); 
			
            $id = $this->db->insert_id();
            $path = $_FILES['img']['name'];
            $exe = pathinfo($path,PATHINFO_EXTENSION);
            $this->crud_model->file_up("img","sub_category",$id,'','no','.'.$exe);
            $data_banner['image'] = 'sub_category_'.$id.'.'.$exe;
            $this->db->where('sub_category_id',$id);
            $this->db->update('sub_category',$data_banner);
			
			
        }elseif ($para1 == 'update')
		{
			$data['sub_category_name'] = $this->input->post('name');
            $data['category_id']   = $this->input->post('category_id');
            $path = $_FILES['img']['name'];
            if($path!=''){
            $exe = pathinfo($path,PATHINFO_EXTENSION);
            $this->crud_model->file_up("img","sub_category",time().$para2,'','no','.'.$exe);
            $data['image'] = 'sub_category_'.time().$para2.'.'.$exe;

            }


            $this->db->where('sub_category_id',$para2);
            $this->db->update('sub_category', $data);
      
      
			
            }
           
		elseif ($para1 == 'delete') {
			
            $this->db->where('sub_category_id', $para2);
            $this->db->delete('sub_category');
			
        }
		
    }
	


    function drecommendation($para1='',$para2=''){

if($para1=='add'){
             $data['super_id '] = $this->input->post('superID');
			 if( $this->input->post('superID')=='7'){
			      $data['shoptype_id'] = $this->input->post('shopTypeId');
			 	 $data['focustype_id '] = '0';
			 }
			 else{
			 	$data['shoptype_id'] = $this->Api_model->singleselectbox('shop_focus', 'shop_focus_id',$this->input->post('shopTypeId'), 'shop_type');
             $data['focustype_id '] = $this->input->post('shopTypeId');
			 }
             $data['sortby'] = 0;
             $this ->db->insert('d_recommendation', $data);
                $responce = array(
                    'success' => true,
                );
	
}
else if($para1=='list'){
             $this->db->order_by('d_recommendation_id','asc');
              $loop = $this ->db->get_where('d_recommendation')->result_array();
     
          
                foreach ($loop as $row)
                {
                      $supername = '';
                      $shopType = '';
                    if($row['super_id']==7){
                        $supername = 'Handy Service';
                          $shopType =     $this->Api_model->singleselectbox('h_category','h_category_id',$row['shoptype_id'],'category_name');
                    } else{
                        $shopType =     $this->Api_model->singleselectbox('shop_focus', 'shop_focus_id',$row['focustype_id'],'title');
                         $supername = 'Shop Type';
                    }
                    
                    $data[] = array(
					'id'=>$row['d_recommendation_id'],
                        'superId' => $supername,
                        'shopTypeId' =>  $shopType,
                        'focusTypeId' => $row['focustype_id'],
                        'sortBy' => $row['sortby'],
                    );

                }
				$responce = array(
                    'data' => $data,
                    'message' => 'H_category retrieved successfully',
                    'success' => true
                );
			
	
}

else if($para1=='edit'){
$id = $para2;
            
                $data['super_id '] = $this->input->post('superID');
			 if( $this->input->post('superID')=='7'){
			      $data['shoptype_id'] = $this->input->post('shopTypeId');
			 	 $data['focustype_id '] = '0';
			 }
			 else{
			 	$data['shoptype_id'] = $this->Api_model->singleselectbox('shop_focus', 'shop_focus_id',$this->input->post('shopTypeId'), 'shop_type');
             $data['focustype_id '] = $this->input->post('shopTypeId');
			 }
             $data['sortby'] = 0;
                $this->db ->where('h_category_id', $id);
                $this ->db ->update('h_category', $data);
               
                $responce = array(
                    'success' => true,
                );
           
			
	
}

echo json_encode($responce);
	
}


function walletstatusupdate($para1 = '', $para2 = '', $para3 = '', $para4 = '')
    {

            
                $data['status'] = $para1;
                $this
                    ->db
                    ->where('wallet_vendor_transactions_id', $para2);
                $this
                    ->db
                    ->update('wallet_vendor_transactions', $data);
                $responce = array(
                    'success' => true,
                );
            
        echo json_encode($responce);
    }
    
    
    function VendorWallet($para1='',$para2=''){
if($para1=='add'){
$id=$para2;
             $data['vendor_id'] = $id;
             $data['amount'] = $this->input->post('amount');
             $data['status'] = 'Requested';
             $data['requested_date '] = time();
             $this ->db->insert('wallet_vendor_transactions', $data);
                $responce = array(
                    'success' => true,
                );
            
        }
	
	if($para1=='list'){
		$this->db->order_by('wallet_vendor_transactions_id');
              $loop = $this ->db->get_where('wallet_vendor_transactions')->result_array();
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['wallet_vendor_transactions_id'],
                        'vendorId' => $row['vendor_id'],
                        'amount' => $row['amount'],
                        'status' => $row['status'],
                        'notes' => $row['notes'],
						'reqDate'=>date("Y-m-d",$row['requested_date']),
						'processDate'=>date("Y-m-d",$row['process_date']),
                    );

                }
				$responce = array(
                    'data' => $data,
                    'message' => 'H_category retrieved successfully',
                    'success' => true
                );
	}
	echo json_encode($responce);
}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

