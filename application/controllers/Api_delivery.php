<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Api_delivery extends CI_Controller
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
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: application/json');
        $this
            ->load
            ->helper('url');
        $this
            ->load
            ->model("Api_model");
    }

    /* index of the admin. Default: Dashboard; On No Login Session: Back to login page. */
    public function index()
    {

    }

    function profile_image($para1 = '', $para2 = '')
    {
        $image = $_FILES['image']['name'];
        $imagePath = 'uploads/profile_image/' . $image;
        $tmp_name = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp_name, $imagePath);
        $row = json_decode($_POST['name'], true);
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
        $data['status'] = 'success';
        $data['token'] = '1';
        $data['device_id'] = '1';
        $data['livestatus'] = 'true';
        $this
            ->db
            ->insert('provider', $data);
        $id = $this
            ->db
            ->insert_id();

        $path = $_FILES['image']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $data_banner['image'] = 'provider_' . $id . '.' . $ext;
        $this
            ->crud_model
            ->file_up("image", "provider", $id, '', 'no', '.' . $ext);
        $this
            ->db
            ->where('provider_id', $id);
        $this
            ->db
            ->update('provider', $data_banner);
        foreach ($row['category'] as $row1)
        {
            $data1['categoryName'] = $row1['categoryName'];
            $data1['categoryId'] = $row1['categoryId'];
            $data1['subcategoryName'] = $row1['subcategoryName'];
            $data1['subcategoryId'] = $row1['subcategoryId'];
            $data1['experience'] = $row1['experience'];
            $data1['chargePreHrs'] = $row1['chargePreHrs'];
            $data1['quickPitch'] = $row1['quickPitch'];
            $data1['userid'] = $id;
            $this
                ->db
                ->insert('provider_databook', $data1);
        }

    }
    
    
    
    
    
    
    
    /** settings **/
    function settings()
    {
        $data = ["success" => true, "data" => [
            "app_name" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '3', 'value') , 
             "enable_stripe" => "1", "phone" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '88', 'value') , 
            "default_currency" => $this
            ->Api_model
            ->get_currency() , "enable_paypal" => "1",
            "address" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '89', 'value') ,
            "google_maps_key" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '67', 'value') , 
            "mobile_language" => "en",
            "app_version" => "2.0.0", "enable_version" => "1", "currency_right" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '83', 'value') , "default_currency_decimal_digits" => "2", "enable_razorpay" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '84', 'value')
            ], "message" => "Settings retrieved successfully"];
        echo json_encode($data);

    }
    /** orders **/
    function order($para1 = '', $para2 = '', $para3 = '')
    {

        if ($para1 == 'list')
        {

            if ($this
                ->Api_model
                ->singleselectbox('driver', 'driver_id', $para2, 'token') == $this
                ->input
                ->get('api_token', true))
            {

                $this
                    ->db
                    ->order_by('sale_id', 'desc');
                $loop = $this
                    ->db
                    ->get_where('sale', array(
                    'delivery_assigned' => $para2
                ))->result_array();
                // count($loop);
                foreach ($loop as $row2)
                {
                    $shippingaddress[] = json_decode($row2['shipping_address'], true);
                    $data[] = array(
                        'userid' => $row2['buyer'],
                        'sale_code' => $row2['sale_code'],
                        'product_details' => json_decode($row2['product_details'], true) ,
                        'address' => $shippingaddress,
                        'shipping' => $row2['shipping'],
                        'payment_type' => $row2['payment_type'],
                        'payment_status' => $row2['payment_status'],
                        'payment_timestamp' => $row2['payment_timestamp'],
                        'grand_total' => $row2['grand_total'],
                        'sale_datetime' => $row2['sale_datetime'],
                        'delivary_datetime' => $row2['delivary_datetime'],
                        'deliver_assignedtime' => $row2['deliver_assignedtime'],
                        'delivery_state' => $row2['delivery_state'],
                    );
                }

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'order retrieved successfully',
                );
            }
            else
            {
                $responce = $this
                    ->Api_model
                    ->tokenfailed();
            }
        }
        else if ($para1 == 'orderhistory')
        {

            if ($this
                ->Api_model
                ->singleselectbox('driver', 'driver_id', $para2, 'token') == $this
                ->input
                ->get('api_token', true))
            {

                $this
                    ->db
                    ->order_by('sale_id', 'desc');
                $loop = $this
                    ->db
                    ->get_where('sale', array(
                    'delivery_assigned' => $para2
                ))->result_array();

                foreach ($loop as $row2)
                {
                    $shippingaddress[] = json_decode($row2['shipping_address'], true);
                    $data[] = array(
                        'userid' => $row2['buyer'],
                        'sale_code' => $row2['sale_code'],
                        'product_details' => json_decode($row2['product_details'], true) ,
                        'address' => $shippingaddress,
                        'shipping' => $row2['shipping'],
                        'payment_type' => $row2['payment_type'],
                        'payment_status' => $row2['payment_status'],
                        'payment_timestamp' => $row2['payment_timestamp'],
                        'grand_total' => $row2['grand_total'],
                        'sale_datetime' => $row2['sale_datetime'],
                        'delivary_datetime' => $row2['delivary_datetime'],
                        'deliver_assignedtime' => $row2['deliver_assignedtime'],
                        'delivery_state' => $row2['delivery_state'],
                    );
                }

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'order retrieved successfully',
                );
            }
            else
            {
                $responce = $this
                    ->Api_model
                    ->tokenfailed();
            }

        }
        else if ($para1 == 'orderDetails')
        {

            if ($this
                ->Api_model
                ->singleselectbox('driver', 'driver_id', $para2, 'token') == $this
                ->input
                ->get('api_token', true))
            {

                $this
                    ->db
                    ->order_by('sale_id', 'desc');
                $loop = $this
                    ->db
                    ->get_where('sale', array(
                    'delivery_assigned' => $para2,
                    'sale_code' => $para3
                ))->result_array();

                foreach ($loop as $row2)
                {
                    $shippingaddress = json_decode($row2['shipping_address'], true);
                    $this
                        ->db
                        ->select('address1, display_name, phone, latitude, longitude');
                    $loop1 = $this
                        ->db
                        ->get_where('vendor', array(
                        'vendor_id' => $row2['vendor']
                    ))->result_array();
                    foreach ($loop1 as $row1)
                    {
                        $shipped = array(
                            'addressSelect' => $row1['address1'],
                            'username' => $row1['display_name'],
                            'phone' => $row1['phone'],
                            'userId' => $row2['vendor'],
                            'latitude' => floatval($row1['latitude']) ,
                            'longitude' => floatval($row1['longitude']) ,
                            'isDefault' => 'true',
                            'id' => ''
                        );
                    }
                    $data = array(
                        'userid' => $row2['buyer'],
                        'sale_code' => $row2['sale_code'],
                        'status' => $row2['status'],
                        'product_details' => json_decode($row2['product_details'], true) ,
                        'address' => $shippingaddress,
                        'shop' => $shipped,
                        'payment_type' => $row2['payment_type'],
                        'payment_status' => $row2['payment_status'],
                        'payment_timestamp' => $row2['payment_timestamp'],
                        'grand_total' => $row2['grand_total'],
                        'sale_datetime' => $row2['sale_datetime'],
                        'delivary_datetime' => $row2['delivary_datetime'],
                        'deliver_assignedtime' => $row2['deliver_assignedtime'],
                        'delivery_state' => $row2['delivery_state'],
                    );
                }

                $responce = array(
                    'success' => true,
                    'data' => $data,
                    'message' => 'orderdetails retrieved successfully',
                );
            }
            else
            {
                $responce = $this
                    ->Api_model
                    ->tokenfailed();
            }

        }
        echo json_encode($responce);
    }
    /** deliverStatus **/
    function deliverStatus($para1 = '', $para2 = '', $para3 = '', $para4 = '')
    {
        if ($para1 == 'update')
        {
            if ($para2 != 'delivered')
            {

                $data['delivery_state'] = $para2;
                $this
                    ->db
                    ->where('sale_code', $para3);
                $this
                    ->db
                    ->update('sale', $data);
                $responce = array(
                    'success' => true,
                    'data' => true,
                    'message' => 'order retrieved successfully',
                );
            }
            else
            {

                $this
                    ->db
                    ->select('otp');
                $optloop = $this
                    ->db
                    ->get_where('sale', array(
                    'sale_code' => $para3
                ))->result_array();
                foreach ($optloop as $row)
                {
                    $otp = $row['otp'];
                }
                if ($otp == $para4)
                {
                    $delivery_status = json_decode($this
                        ->db
                        ->get_where('sale', array(
                        'sale_code' => $para3
                    ))->row()->delivery_status, true);
                    $delivery_status['deliverstatus'] = true;
                    $delivery_status['delivered'] = time();
                    $data['delivery_state'] = $para2;
                    $data['delivery_status'] = json_encode($delivery_status);
                    $data['status'] = 'Delivered';
                    $this
                        ->db
                        ->where('sale_code', $para3);
                    $this
                        ->db
                        ->update('sale', $data);
                    $responce = array(
                        'success' => true,
                        'data' => true,
                        'message' => 'order retrieved successfully',
                    );
                }
                else
                {
                    $responce = array(
                        'success' => true,
                        'data' => false,
                        'message' => 'order retrieved successfully',
                    );

                }

            }
            echo json_encode($responce);
        }
    }
    /** login **/
    function login($para1 = '', $para2 = '')
    {
        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);

        $loop = $this
            ->db
            ->get_where('driver', array(
            'email' => $get_data->email,
            'password' => sha1($get_data->password) ,
            'block' => 'false'
        ))
            ->result_array();
        if ($loop > 0)
        {
            foreach ($loop as $row)
            {
                $data1['token'] = sha1($row['user_id']);
                $data = array(
                    'id' => $row['driver_id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'api_token' => $data1['token'],
                    'password' => 'hidden',
                    'device_token' => $row['device_id'],
                    'gender' => $row['gender'],
                    'phone' => $row['phone'],
                    'status' => $row['status'],
                    'auth' => true,
                    'liveStatus' => $row['livestatus'] == 'true' ? true : false,
                    'address' => '1',
                    'image' => base_url() . 'uploads/driver_image/' . $row['image'],
					'latitude' => $row['latitude'],
					'longitude' => $row['longitude'],
                );
            }

            $this
                ->db
                ->where('driver_id', $row['driver_id']);
            $this
                ->db
                ->update('driver', $data1);
        }
        else
        {

        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'profile retrieved successfully',
        );
        echo json_encode($responce);

    }
    /** statusUpdate **/
    function statusUpdate($para1 = '', $para2 = '', $para3 = '')
    {

        if ($this
            ->Api_model
            ->singleselectbox('driver', 'driver_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            $data['livestatus'] = $para1;
            $this
                ->db
                ->where('driver_id', $para2);
            $this
                ->db
                ->update('driver', $data);
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

    /** latlongUpdate **/
    function latlongUpdate($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('driver', 'driver_id', $para3, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            $data['latitude'] = $para1;
            $data['longitude'] = $para2;
            $this
                ->db
                ->where('driver_id', $para3);
            $this
                ->db
                ->update('driver', $data);
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
    
    
    
     /** wallet **/
    function wallet($para1='', $para2='', $para3=''){
		if($para1=='balance'){
			$loop = $this->db->get_where('secondary_wallet',array('id'=>$para2,'user_type'=>$para3))->result_array();
              if(count($loop)>0){
			foreach($loop as $row){
              
		   $data[] = array(
		               'user_id' => $para2,
					   'balance' => $row['balance'],
					   'wallet_id' => $row['wallet_id']
					   );   
			}  }else{
                 $data[] = array(
		               'user_id' => $para2,
					   'balance' => '0',
					   'wallet_id' => '0'
					   );   
            }
			
			 $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'wallet retrive successfully',
						);
		}else if($para1=='list'){
			if($para3=='recent'){
			   $this->db->limit(6);
			}
			$this->db->order_by('wallet_transactions_id','desc');
			$loop = $this->db->get_where('wallet_transactions',array('user_id'=>$para2))->result_array();
			foreach($loop as $row){
			 $data[] = array(
		               'user_id' => $para2,
					   'transactions_id' => $row['wallet_transactions_id'],
					   'type' => $row['type'],
					   'amount' => $row['amount'],
					   'balance' => $row['balance'],
					   'status' => $row['status'],
					   'date' => $row['date'],
					   'access_vendor' => $row['access_vendor'],
					   'product_id' => $row['product_id']
					   );   			
			}
			 $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'wallet transaction retrive successfully',
						);
		}
		
		 echo json_encode($responce);
		
	}
	
	

    /** dashboard **/
    function dashboard($para1 = '', $para2 = '', $para3 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('driver', 'driver_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
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
        }
        else
        {
            $responce = $this
                ->Api_model
                ->tokenfailed();

        }

        echo json_encode($responce);

    }

    /** register **/
    function register($para1 = '', $para2 = '', $para3 = '')
    {

        $row = json_decode($_POST['name'], true);
        $data['name'] = $row['firstname'];
        $data['last_name'] = $row['lastname'];
        $data['date'] = time();
        $data['status'] = 'waiting';
        $data['age'] = $row['dob'];
        $data['gender'] = $row['gender'];
        $data['email'] = $row['email'];
        $data['password'] = sha1($row['password']);
        $data['phone'] = $row['mobile'];
        $data['address'] = $row['address1'] . ',' . $row['address2'] . ',' . $row['city'] . ',' . $row['state'] . ',' . $row['zipcode'] . '.';
        $data['latitude'] = $row['latitude'];
        $data['longitude'] = $row['longtitude'];
        $data['store_id'] = $row['storeId'];
        $data['drivingMode'] = $row['drivingMode'];
        $data['token'] = '1';
        $data['device_id'] = '1';
        $data['livestatus'] = 'true';
        $data['block'] = 'false';
		

        $this
            ->db
            ->insert('driver', $data);

        $path = $_FILES['image']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $ext = 'png';
        $data_banner['image'] = 'driver_' . $id . '.' . $ext;
        $this
            ->crud_model
            ->file_up("image", "driver", $id, '', 'no', '.' . $ext);
        $this
            ->db
            ->where('driver_id', $id);
        $this
            ->db
            ->update('driver', $data_banner);
        $data = ["success" => true, ];

        echo json_encode($responce);

    }

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

