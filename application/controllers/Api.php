<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Api extends CI_Controller
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

    /** settings **/
    function setting()
    {
        $data = ["success" => true, "data" => ["app_name" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '3', 'value') , "minimum_purchase" => $this
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
            ->get_type_name_by_id('general_settings', '84', 'value') , "instanceDelivery" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '94', 'value') ? true : false, "deliveryTips" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '92', 'value') ? true : false, "cancel_timer" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '96', 'value') , "coverDistance" => floatval($this
            ->Api_model
            ->get_type_name_by_id('general_settings', '93', 'value')) ,
              "noticeboard" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '80', 'value'), 
                                               
              "rayzorPay" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '102', 'value')? true : false,
              "upi" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '103', 'value')? true : false,
            "paypal" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '104', 'value')? true : false,
            "stripe" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '107', 'value')? true : false,
             "flutterWave" => $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '106', 'value')? true : false,
                                              
                                              ],
                 "message" => "Settings retrieved successfully"];
        echo json_encode($data);

    }

    /** payment **/
    function paymentDetails($para1 = '', $para2 = '')
    {
        if ($para1 == 'list')
        {
            $this
                ->db
                ->select('payment_details');
            $loop = $this
                ->db
                ->get_where('sale', array(
                'sale_code' => $para2
            ))->result_array();
            foreach ($loop as $row)
            {
                $data = json_decode($row['payment_details'], true);
            }
            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'PaymentDetails retrieved successfully',
            );

        }
        echo json_encode($responce);
    }

    /** singleproductbox **/
    function singleproductboxfilter($para1 = '', $para2 = '')
    {
        $this->searchtxt = $this
            ->input
            ->get('search', true);

        if ($para2 != 'no')
        {
            $this
                ->db
                ->where('discount <=', $para2);
        }
        $this
            ->db
            ->select('product_id, title , sale_price , purchase_price, discount, rating_num, num_of_imgs, color, unit');

        $loop = $this
            ->db
            ->get_where('product', array(
            'sub_category' => $para1
        ))->result_array();
        if (count($loop) > 0)
        {
            foreach ($loop as $row)
            {
                if ($row['color'] == 'null')
                {
                    $color = 'no';
                }
                else
                {
                    $color = 'yes';
                }

                $data[] = array(
                    'id' => $row['product_id'],
                    'product_name' => $row['title'],
                    'price' => $row['sale_price'],
                    'strike' => $row['purchase_price'],
                    'offer' => intval($row['discount']) ,
                    'rating' => intval($row['rating_total']) ,
                    'unit' => $row['unit'],
                    'image' => base_url() . 'uploads/product_image/product_' . $row['product_id'] . '_1_thumb.jpg',
                    'num_of_imgs' => intval($row['num_of_imgs']) ,
                    'color' => $color,
                );
            }
        }
        else
        {
            $data[] = array(
                'id' => 'No_data',
                'product_name' => '',
                'price' => '',
                'strike' => '',
                'offer' => 0,
                'rating' => 0,
                'image' => '',
                'num_of_imgs' => 0,
                'color' => '',

            );
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'product retrieved successfully',
        );

        echo json_encode($responce);

    }
    
    
    /** shoplist **/
    function shoplistOffer($para1 = '', $para2 = '', $para3 = '')
    {

       
       
       $this->myLat = $this->input ->get('myLat', true);
        $this->myLon = $this ->input ->get('myLon', true);

        $cover_radius = $this->Api_model ->get_type_name_by_id('general_settings', '93', 'value');
        // AND market_camping_value = $para3 
         
        if($para3=='discount_upto'){
         $sql = $this
            ->db
            ->query("SELECT  cover_image, vendor_id, latitude, livestatus, 	market_camping_value, market_camping_key, longitude,display_name, subtitle, address1, logo,type,focus_id,instant,takeaway,schedule, SQRT(
    POW(69.1 * (latitude - $this->myLat), 2) +
    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM vendor WHERE type = $para1 AND market_camping_value = $para2 AND 	market_camping_key = 'discount' HAVING distance < $cover_radius ORDER BY distance")->result_array();
        } else if ($para3=='offers_upto'){
          
            $sql = $this
            ->db
            ->query("SELECT  cover_image, vendor_id, latitude, livestatus, 	market_camping_value, market_camping_key, longitude,display_name, subtitle, address1, logo,type,focus_id,instant,takeaway,schedule,  SQRT(
    POW(69.1 * (latitude - $this->myLat), 2) +
    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM vendor WHERE type = $para1 AND market_camping_value = $para2 AND 	market_camping_key = 'offer' HAVING distance < $cover_radius ORDER BY distance")->result_array();
            
        }
       
        
             if (count($sql) > 0)
        {

            foreach ($sql as $row)
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
                $distance = number_format($this
                    ->Api_model
                    ->distance($this->myLat, $this->myLon, $row['latitude'], $row['longitude'], "k") , 2);
                $data[] = array(
                    'shopId' => $row['vendor_id'],
                    'shopName' => $row['display_name'],
                    'subtitle' => $row['subtitle'],
                    'locationMark' => $row['address1'],
                    'rate' => '2',
                    'distance' => $distance,
                    'logo' => $img,
                    'cover' => $coverImage,
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'openStatus' => $row['livestatus']=='true'?true:false,
                    'shopType' => $row['type'],
                    'focusType' => $row['focus_id'],
                    'marketKey' => $row['market_camping_key'],
                    'marketValue' => $row['market_camping_value'],
                    'instant' => $row['instant']==1?true:false,
                    'takeaway' => $row['takeaway']==1?true:false,
                    'schedule' => $row['schedule']==1?true:false,
                );

            }
        }
        else
        {

            $data[] = array(
                'shopId' => 'no_data',
                'shopName' => '',
                'subtitle' => '',
                'locationMark' => '',
                'rate' => '',
                'distance' => '',
                'logo' => '',
                'cover' => '',
                'latitude' => '',
                'longitude' => '',
                'openStatus' => '',
                'shopType' => '',
                'focusType' => '',
            );

        }
          $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'shop retrieved successfully',
        );

        echo json_encode($responce);
        
    }
    
    

    /** shoplist **/
    function shoplist($para1 = '', $para2 = '', $para3 = '')
    {

        $this->myLat = $this->input ->get('myLat', true);
        $this->myLon = $this ->input ->get('myLon', true);

        $cover_radius = $this->Api_model ->get_type_name_by_id('general_settings', '93', 'value');

        $sql = $this
            ->db
            ->query("SELECT  cover_image, vendor_id, latitude, livestatus, instant,	takeaway,schedule, market_camping_value, market_camping_key, longitude,display_name, subtitle, address1, logo,type,focus_id, SQRT(
    POW(69.1 * (latitude - $this->myLat), 2) +
    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM vendor WHERE type = $para1 AND focus_id = $para2 ORDER BY distance")->result_array();
	//	 $sql = $this
    //        ->db
   //         ->query("SELECT  cover_image, vendor_id, latitude, livestatus, instant,	takeaway,schedule, market_camping_value, market_camping_key, longitude,display_name, subtitle, address1, logo,type,focus_id, SQRT(
//    POW(69.1 * (latitude - $this->myLat), 2) +
//    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
//FROM vendor WHERE type = $para1 AND focus_id = $para2 HAVING distance < $cover_radius ORDER BY distance")->result_array();
        if (count($sql) > 0)
        {

            foreach ($sql as $row)
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
                $distance = number_format($this
                    ->Api_model
                    ->distance($this->myLat, $this->myLon, $row['latitude'], $row['longitude'], "k") , 2);
                $data[] = array(
                    'shopId' => $row['vendor_id'],
                    'shopName' => $row['display_name'],
                    'subtitle' => $row['subtitle'],
                    'locationMark' => $row['address1'],
                    'rate' => '2',
                    'distance' => $distance,
                    'logo' => $img,
                    'cover' => $coverImage,
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'openStatus' => $row['livestatus']=='true'?true:false,
                    'shopType' => $row['type'],
                    'focusType' => $row['focus_id'],
                    'marketKey' => $row['market_camping_key'],
                    'marketValue' => $row['market_camping_value'],
                    'instant' => $row['instant']==1?true:false,
                    'takeaway' => $row['takeaway']==1?true:false,
                    'schedule' => $row['schedule']==1?true:false,
                );

            }
        }
        else
        {

            $data[] = array(
                'shopId' => 'no_data',
                'shopName' => '',
                'subtitle' => '',
                'locationMark' => '',
                'rate' => '',
                'distance' => '',
                'logo' => '',
                'cover' => '',
                'latitude' => '',
                'longitude' => '',
                'openStatus' => '',
                'shopType' => '',
                'focusType' => '',
            );

        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'shoplist retrieved successfully',
        );
        echo json_encode($responce);

    }
    
    
    
    
    /** shop home setting **/
    
    function shopSetting($para1='', $para2='', $para3=''){
        if($para1=='productList'){
            	$loop = $this->db->get_where('product',array('added_by'=> json_encode(array( 'type' => 'vendor','id' => $para2) ),''))->result_array();
            
                
                  if (count($loop) > 0)
        {
            foreach ($loop as $row1)
            {

                $variant = $this  ->Api_model ->twowloop('variant_2', 'product_id', $row1['product_id'], 'type', $row1['product_type']);

                $variants = [];
                $i = 0;
                foreach ($variant as $row2)
                {
                    if ($i == 0)
                    {
                        $selected = true;

                    }
                    else
                    {
                        $selected = false;
                    }

                    if($row2['image_type'] == 'external') {
                        $image = $row2['image'];
                    } else if($row2['image_type'] == 'no_image' || $row2['image_type'] == '') {
                        $image = base_url() . 'uploads/product_image/' . $row2['image'];
                    }
                    $i++;
                    $variants[] = array(
                        'variant_id' => $row2['variant_2_id'],
                        'product_id' => $row2['product_id'],
                        'quantity' => $row2['quantity'],
                        'name' => $row2['name'],
                        'unit' => $row2['unit'],
                        'sale_price' => $row2['sale_price'],
                        'strike_price' => $row2['strike_price'],
                        'type' => $row2['type'],
                        'minPurchase' => $row2['min_purchase'],
                        'maxPurchase' => $row2['max_purchase'],
                        'tax' =>  doubleval(['tax']),
                        'discount' => $row2['discount'],
                        'selected' => $selected,
                        'image' => $image,

                    );
                }

                $data[] = array(

                    'id' => $row1['product_id'],
                    'product_name' => $row1['title'],
                    'variant' => $variants,
                    'productType' => $row1['product_type'],
                );

            }

        }
        else
        {
            $data[] = array(
                'id' => 'No_data',
                'product_name' => '',
                'price' => '',
                'strike' => '',
                'offer' => 0,
                'rating' => 0,
                'image' => '',
                'num_of_imgs' => 0,
                'color' => '',

            );
        }
                
            
            $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'product retrieved successfully',
        ); 
            
            
        } else if($para1=='banner'){
            
        $this->db ->order_by('banner_id', 'asc');
        $loop = $this ->db->get_where('banner', array( 'vendor' => $para2 ))->result_array();
        foreach ($loop as $row)
        {
            $data[] = array(
                'id' => $row['banner_id'],
                'slider_text' => $row['title'],
                'redirect_type' => $row['redirect_type'],
                'para' => $row['para'],
                'superCategoryId' => '',
                'image' => base_url() . 'uploads/banner_image/' . $row['image'],
            );
        }
        
        
           $responce = array(
            'success' => true,
            'data' => $data,
            ); 
            
        }
        
        
         echo json_encode($responce);
        
    }
    
    
    
    /** wallet **/
    function wallet($para1='', $para2='', $para3=''){
		if($para1=='balance'){
			$loop = $this->db->get_where('wallet',array('user_id'=>$para2))->result_array();
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
	
	

    /** shopSearch **/
    function shopSearch($para1 = '', $para2 = '', $para3 = '')
    {

        $this->myLat = $this
            ->input
            ->get('myLat', true);
        $this->myLon = $this
            ->input
            ->get('myLon', true);
        $this->search = $this
            ->input
            ->get('search', true);

        $cover_radius = $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '93', 'value');

        $sql = $this
            ->db
            ->query("SELECT  cover_image, vendor_id, latitude,livestatus, longitude,display_name, subtitle, address1, logo,type,focus_id,market_camping_key,market_camping_value,instant,takeaway,schedule, SQRT(
    POW(69.1 * (latitude - $this->myLat), 2) +
    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM vendor WHERE status = 'approved' AND  display_name LIKE '%$this->search%' HAVING distance < $cover_radius ORDER BY distance")->result_array();

        foreach ($sql as $row)
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
            $distance = number_format($this
                ->Api_model
                ->distance($this->myLat, $this->myLon, $row['latitude'], $row['longitude'], "k") , 2);
            $data[] = array(
                'shopId' => $row['vendor_id'],
                'shopName' => $row['display_name'],
                'subtitle' => $row['subtitle'],
                'locationMark' => $row['address1'],
                'rate' => '2',
                'distance' => $distance,
                'logo' => $img,
                'cover' => $coverImage,
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'openStatus' => $row['livestatus']=='true'?true:false,
                'shopType' => $row['type'],
                'focusType' => $row['focus_id'],
                'marketKey' => $row['market_camping_key'],
                'marketValue' => $row['market_camping_value'],
                'instant' => $row['instant']==1?true:false,
                'takeaway' => $row['takeaway']==1?true:false,
                'schedule' => $row['schedule']==1?true:false,
            );

        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'shop retrieved successfully',
        );
        echo json_encode($responce);

    }

    /** productList **/
    function productlist($sort = '', $para2 = '')
    {

        $this->searchtxt = $this
            ->input
            ->get('search', true);

        $this->subcategory = $this
            ->input
            ->get('subcategory', true);
        $this->minamount = $this
            ->input
            ->get('minamount', true);
        $this->maxamount = $this
            ->input
            ->get('maxamount', true);

        $this
            ->db
            ->select('product_id, title,product_type ');

        if ($sort == 'low')
        {
            $this
                ->db
                ->order_by('sale_price', 'asc');
        }
        else if ($sort == 'high')
        {
            $this
                ->db
                ->order_by('sale_price', 'desc');
        }
        else if ($sort == 'new')
        {
            $this
                ->db
                ->order_by('product_id', 'desc');
        }
        else if ($sort == 'popularity')
        {
            // $this->db->order_by('product_id','asc');
            
        }
        if (count(json_decode($this->subcategory, true)) > 0)
        {

            $this
                ->db
                ->where_in('sub_category', json_decode($this->subcategory, true));
        }

        if ($this->minamount != '' && $this->maxamount != '')
        {
            $this
                ->db
                ->where('sale_price >=', $this->minamount);
            $this
                ->db
                ->where('sale_price <=', $this->maxamount);
        }
        if ($this->searchtxt != '')
        {

            $searchTxt = $this->searchtxt;

            $this->db ->like('title', $searchTxt);

        }

        $loop = $this ->db->get_where('product') ->result_array();
        if (count($loop) > 0)
        {
            foreach ($loop as $row1)
            {

                $variant = $this  ->Api_model ->twowloop('variant_2', 'product_id', $row1['product_id'], 'type', $row1['product_type']);

                $variants = [];
                $i = 0;
                foreach ($variant as $row2)
                {
                    if ($i == 0)
                    {
                        $selected = true;

                    }
                    else
                    {
                        $selected = false;
                    }

                    if($row2['image_type'] == 'external') {
                        $image = $row2['image'];
                    } else if($row2['image_type'] == 'no_image' || $row2['image_type'] == '') {
                        $image = base_url() . 'uploads/product_image/' . $row2['image'];
                    }

                    $i++;
                    $variants[] = array(
                        'variant_id' => $row2['variant_2_id'],
                        'product_id' => $row2['product_id'],
                        'quantity' => $row2['quantity'],
                        'name' => $row2['name'],
                        'unit' => $row2['unit'],
                        'sale_price' => $row2['sale_price'],
                        'strike_price' => $row2['strike_price'],
                        'type' => $row2['type'],
                        'minPurchase' => $row2['min_purchase'],
                        'maxPurchase' => $row2['max_purchase'],
                        'tax' =>  doubleval(['tax']),
                        'discount' => $row2['discount'],
                        'selected' => $selected,
                        'image' => $image,

                    );
                }

                $data[] = array(

                    'id' => $row1['product_id'],
                    'product_name' => $row1['title'],
                    'variant' => $variants,
                    'productType' => $row1['product_type'],
                );

            }

        }
        else
        {
            $data[] = array(
                'id' => 'No_data',
                'product_name' => '',
                'price' => '',
                'strike' => '',
                'offer' => 0,
                'rating' => 0,
                'image' => '',
                'num_of_imgs' => 0,
                'color' => '',

            );
        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'product retrieved successfully',
        );

        echo json_encode($responce);

    }
    
    
 /** getbussinescard **/    

    function bussinessCard($para1='', $para2=''){
        $this->db->select('general_detail, social_setting');
        $loop = $this->db->get_where('vendor', array('vendor_id'=> $para1)) ->result_array();
       
        foreach($loop as $row){
            
        $data = array(
                'basicInformation' =>json_decode($row['general_detail'],true),
                'social_setting' => json_decode($row['social_setting'],true),
                'preview' => base_url() . 'uploads/vendor_image/' . $row['logo'],
                'coverImage'=> base_url() . 'uploads/cover_image/' . $row['cover_image'],
        );
        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'buissness card retrieved successfully',
        );

        echo json_encode($responce);
    }
    
    
    function shop_category($para1='', $para2=''){
        
        if($para1 == 'shopcategories'){
            $this->db->order_by('short_by','asc');
                 $loop = $this->db->get_where('supercategory', array('status'=>'1')) ->result_array();
                 $i=0;
                 foreach($loop as $row){
                     $i++;
                      $data[] = array(
                         'id' => $row['supercategory_id'],
                         'name' => $row['name'],
                         'selected' => $i==1?true:false,
                      );        
                 
                 }
             $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'shoptype retrieved successfully',
            );
            
        } else if ($para1 == 'shoptype')
        {
                 
           if($para2=='first'){
               $this->db->order_by('short_by', 'asc');
            $this->db->select('supercategory_id');
            $this->db->limit(1);
            $loopfirst = $this->db->get_where('supercategory',array('status'=>'1')) ->result_array();
            foreach($loopfirst as $rowfirst){
               $select_id = $rowfirst['supercategory_id'];
            }
           }else {
               $select_id = $para2;
           }
            
        if($para2!='7' && $para2!='2'){ 
            
            $this ->db->order_by('shop_focus_id', 'asc');
            $this ->db ->select('shop_focus_id,title,preview_image,cover_image,shop_type');
            $loop = $this->db->get_where('shop_focus',array('supercatgeory_id'=>$select_id)) ->result_array();
        
           
                
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['shop_focus_id'],
                    'title' => $row['title'],
                    'previewImage' => $row['preview_image'] != '' ? base_url() . 'uploads/preview_image/' . $row['preview_image'] : 'no_image',
                    'coverImage' => $row['cover_image'] != '' ? base_url() . 'uploads/shoptypecover_image/' . $row['cover_image'] : 'no_image',
                    'shopType' => $select_id,
                    'homeShopType'=>$row['shop_type']
                );
            }
            
        } else if($para2=='7') {
            
            
             $loop = $this->db->get_where('h_category') ->result_array();
        
           
                
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['h_category_id'],
                    'title' => $row['category_name'],
                    'previewImage' => $row['image'] != '' ? base_url() . 'uploads/h_category_image/' . $row['image'] : 'no_image',
                    'coverImage' => $row['image'] != '' ? base_url() . 'uploads/h_category_image/' . $row['image'] : 'no_image',
                    'shopType' => '7',
                );
            }
        } else if($para2=='2'){
            
             $loop = $this->db->get_where('transport&logistics') ->result_array();
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['transport&logistics_id'],
                    'title' => $row['name'],
                    'previewImage' => $row['image'] != '' ? base_url() . 'uploads/transport&logistics_image/' . $row['image'] : 'no_image',
                    'coverImage' => $row['image'] != '' ? base_url() . 'uploads/transport&logistics_image/' . $row['image'] : 'no_image',
                    'shopType' => '2',
                );
            }
            
        }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'shoptype retrieved successfully',
            );
        } 
        
          echo json_encode($responce);
        
    }
    
    
    /** explore **/
    
    function explore($para1='', $para2=''){
        
        
        
            $this ->db->order_by('short_by', 'asc');
            $this ->db ->select('supercategory_id,	name');
            $loopsuper = $this->db->get_where('supercategory',array('status'=>'1'))->result_array();
        foreach($loopsuper as $rowsuper){
            $data = [];
            
          if($rowsuper['supercategory_id']!='7' && $rowsuper['supercategory_id']!='2'){ 
            
            $this ->db->order_by('shop_focus_id', 'asc');
            $this ->db ->select('shop_focus_id,title,preview_image,cover_image,shop_type');
            $loop = $this->db->get_where('shop_focus',array('supercatgeory_id'=>$rowsuper['supercategory_id'])) ->result_array();
        
           
                
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['shop_focus_id'],
                    'title' => $row['title'],
                    'previewImage' => $row['preview_image'] != '' ? base_url() . 'uploads/preview_image/' . $row['preview_image'] : 'no_image',
                    'coverImage' => $row['cover_image'] != '' ? base_url() . 'uploads/shoptypecover_image/' . $row['cover_image'] : 'no_image',
                    'shopType' => $rowsuper['supercategory_id'],
                    'homeShopType' => $row['shop_type'],
                
                );
            }
            
        } else if($rowsuper['supercategory_id']=='7') {
            
            
             $loop = $this->db->get_where('h_category') ->result_array();
        
           
                
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['h_category_id'],
                    'title' => $row['category_name'],
                    'previewImage' => $row['image'] != '' ? base_url() . 'uploads/h_category_image/' . $row['image'] : 'no_image',
                    'coverImage' => $row['image'] != '' ? base_url() . 'uploads/h_category_image/' . $row['image'] : 'no_image',
                    'shopType' => '7',
                );
            }
        } else if($rowsuper['supercategory_id']=='2'){
            
             $loop = $this->db->get_where('transport&logistics') ->result_array();
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['transport&logistics_id'],
                    'title' => $row['name'],
                    'previewImage' => $row['image'] != '' ? base_url() . 'uploads/transport&logistics_image/' . $row['image'] : 'no_image',
                    'coverImage' => $row['image'] != '' ? base_url() . 'uploads/transport&logistics_image/' . $row['image'] : 'no_image',
                    'shopType' => '2',
                );
            }
            
        }
         

            $data1[] = array(
                      'title' => $rowsuper['name'],
                      'id' => $rowsuper['supercategory_id'],
                      'focusType' => $data
            
                      );
            
        }
            $responce = array(
                'success' => true,
                'data' => $data1,
                'message' => 'shoptype retrieved successfully',
            );
        
        
          echo json_encode($responce);

        
        
    }
    
    
    
    
    /** recommendation **/
    function my_recommendation($para1='', $para2=''){
            $this->db->order_by('sortby','asc');
            $loop1 = $this->db->get_where('d_recommendation') ->result_array();
            foreach ($loop1 as $row1)
            {
                
                
               
                
                if($row1['super_id']!='7' && $row1['super_id']!='2'){ 
           
            $this ->db->order_by('shop_focus_id', 'asc');
            $this ->db ->select('shop_focus_id,title,preview_image,cover_image,shop_type');
            $loop = $this->db->get_where('shop_focus',array('shop_focus_id'=> $row1['focustype_id'])) ->result_array();
      
                
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['shop_focus_id'],
                    'title' => $row['title'],
                    'previewImage' => $row['preview_image'] != '' ? base_url() . 'uploads/preview_image/' . $row['preview_image'] : 'no_image',
                    'coverImage' => $row['cover_image'] != '' ? base_url() . 'uploads/shoptypecover_image/' . $row['cover_image'] : 'no_image',
                    'shopType' => $row1['super_id'],
                    'homeShopType' => $row['shop_type'],
                );
            }
            
        } else if($row1['super_id']=='7') {
            
                  
            
             $loop = $this->db->get_where('h_category',array('h_category_id'=>$row1['shoptype_id'])) ->result_array();
        
           
               
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['h_category_id'],
                    'title' => $row['category_name'],
                    'previewImage' => $row['image'] != '' ? base_url() . 'uploads/h_category_image/' . $row['image'] : 'no_image',
                    'coverImage' => $row['image'] != '' ? base_url() . 'uploads/h_category_image/' . $row['image'] : 'no_image',
                    'shopType' => '7',
                );
            }
        }else if($row1['super_id']=='2'){
           
             $loop = $this->db->get_where('transport&logistics', array('transport&logistics_id'=>$row1['shoptype_id'])) ->result_array();
                    
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['transport&logistics_id'],
                    'title' => $row['name'],
                    'previewImage' => $row['image'] != '' ? base_url() . 'uploads/transport&logistics_image/' . $row['image'] : 'no_image',
                    'coverImage' => $row['image'] != '' ? base_url() . 'uploads/transport&logistics_image/' . $row['image'] : 'no_image',
                    'shopType' => '2',
                );
            }
            
        }
                
            }
        
            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'recommendation retrieved successfully',
            );
        
        echo json_encode($responce);
    }
    
    
    
    
    
    
    function getfilter($para1 = '', $para2 = '')
    {
        $this->searchtxt = $this
            ->input
            ->get('search', true);

        if ($this->searchtxt != '')
        {
            $this
                ->db
                ->like('title', $this->searchtxt);
        }

        $this
            ->db
            ->like('title', $this->searchtxt);
        $id = $this
            ->db
            ->get('product')
            ->row()->category;
        $min = $this->get_range_lvl('category', $id, 'min');
        $max = $this->get_range_lvl('category', $id, 'max');
        // $this->Api_model->color_code($id);
        $loop = $this
            ->db
            ->get_where('sub_category', array(
            'category' => $id
        ))->result_array();
        foreach ($loop as $row)
        {
            $subcategory[] = array(
                'id' => $row['sub_category_id'],
                'name' => $row['sub_category_name'],
                'image' => '',
                'select' => false
            );
        }

        $data[] = array(
            'min_amount' => intval($min) ,
            'max_amount' => intval($max) ,
            'subcategory' => $subcategory,
            'search_txt' => $this->searchtxt,
            'brand' => [],
            'colors' => [],
        );
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'Filter retrieved successfully',
        );

        echo json_encode($responce);

    }

    function get_range_lvl($by = "", $id = "", $type = "")
    {
        if ($type == "min")
        {
            $set = 'asc';
        }
        elseif ($type == "max")
        {
            $set = 'desc';
        }
        $this
            ->db
            ->limit(1);
        $this
            ->db
            ->order_by('sale_price', $set);
        if (count($a = $this
            ->db
            ->get_where('product', array(
            $by => $id
        ))->result_array()) > 0)
        {
            foreach ($a as $r)
            {
                return $r['sale_price'];
            }
        }
        else
        {
            return 0;
        }
    }

 	function coupons($para1='', $para2=''){
         
	
	   	  //$loop =   $this->db->get_where('coupon',array('added_by'=>'vendor','added_id'=>$para1))->result_array();
        
        $loop =   $this->db->get_where('coupon')->result_array();
		  foreach($loop as $row){
			    
				   
		       $data[] = array(
			        'title' => $row['title'],
					'till' => $row['till'],
					'code'  => $row['code'],
                    'status'  => $row['status'],
                    'image'  => base_url() . 'uploads/coupon_image/' . $row['image'],
					'discount_type' => $row['discountType'],
					'discount' => doubleval($row['discount']),
					'terms' =>   $row['terms'],
                    'minimumPurchasedAmount' => doubleval(['minimumPurchasedAmount']),
                    'limit' =>   $row['limitUser'],
                    'couponType' =>   $row['couponType'],
			       );
		  }
		   $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'coupons retrieved successfully',
						);
		    echo json_encode($responce);
	}
	

    /** comments **/
    function comments($para1 = '', $para2 = '', $para3 = '')
    {

        if ($para1 == 'addcomments')
        {
            $id = $this
                ->input
                ->post('courseid');
            $data['comments'] = $this
                ->input
                ->post('comments');
            $data['rating'] = $this
                ->input
                ->post('totalrating');
            $this
                ->db
                ->where('courseid', $id);
            $this
                ->db
                ->update('course', $data);
            $responce = array(
                'status' => 'success'
            );

            echo json_encode($responce);
        }
    }
    /** setDetault */
    function setdefault($para1 = '', $para2 = '')
    {

        $data['isDefault'] = false;
        $this
            ->db
            ->where('userid', $para1);
        $this
            ->db
            ->update('address', $data);

        $data['isDefault'] = 'true';
        $this
            ->db
            ->where('address_id', $para2);
        $this
            ->db
            ->update('address', $data);
        $data = ["success" => true, "data" => [], "message" => "setdefault address successfully"];

        echo json_encode($data);
    }
    /** inter_sort_view**/
    function inter_sort_view($para1 = '', $para2 = '')
    {

        $loop = $this
            ->db
            ->get_where('inter_short_view')
            ->result_array();
        foreach ($loop as $row2)
        {
            $data[] = array(
                'title' => $row2['title'],
                'sort' => $row2['short'],
                'image' => base_url() . 'uploads/inter_short_view_image/' . $row2['image'],
            );
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'inter short view  successfully',
        );
        echo json_encode($responce);
    }

    /** deliverytimeslot **/
    function deliverytimeslot($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->order_by('deliverytimeslot_id', 'asc');
        $loop = $this
            ->db
            ->get_where('deliverytimeslot', array(
            'vendor' => $para1
        ))->result_array();
        $data = [];
        $i = 0;
        foreach ($loop as $row)
        {
            if ($i == 0)
            {
                $status = true;
            }
            else
            {
                $status = false;
            }
            $data[] = array(
                'timeid' => intval($row['time_id']) ,
                'fromTime' => $row['fromTime'],
                'toTime' => $row['toTime'],
                'selected' => $status,
            );
            $i++;
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'time_slot retrieved successfully',
        );
        echo json_encode($responce);
    }
    /** orders **/
    function order($para1 = '', $para2 = '')
    {
        if ($para1 == 'do_add')
        {
            $content_data = file_get_contents("php://input");
            $get_data = json_decode($content_data);
            $general[] = $get_data;
            $data['sale_code'] = $get_data->userid;
            $data['provider_id'] = $get_data->providerid;
            $data['phone'] = $get_data->providermobile;
            $data['detail'] = json_encode($general);
            $data['date'] = time();
            $data['status'] = 'pending';
            $this
                ->db
                ->insert('booking', $data);
            $responce = array(
                'success' => true,
                'data' => 'success',
                'message' => 'booked  successfully',
            );

        }
        else if ($para1 == 'list')
        {

            if ($this
                ->Api_model
                ->singleselectbox('user', 'user_id', $para2, 'token') == $this
                ->input
                ->get('api_token', true))
            {

                $loop = $this
                    ->db
                    ->get_where('sale', array(
                    'buyer' => $para2
                ))->result_array();

                foreach ($loop as $row2)
                {
                    $data[] = array(
                        'userid' => $row2['buyer'],
                        'sale_code' => $row2['sale_code'],
                        'product_details' => json_decode($row2['product_details'], true) ,
                        'shipping_address' => json_decode($row2['shipping_address'], true) ,
                        'shipping' => $row2['shipping'],
                        'payment_type' => $row2['payment_type'],
                        'payment_status' => $row2['payment_status'],
                        'payment_timestamp' => $row2['payment_timestamp'],
                        'grand_total' => $row2['grand_total'],
                        'sale_datetime' => $row2['sale_datetime'],
                        'delivary_datetime' => $row2['delivary_datetime'],
                        'orderType' => $row2['order_type'],
                        'shopTypeId' => $this
                            ->Api_model
                            ->singleselectbox('shop_focus', 'shop_focus_id', $row2['focus_id'], 'shop_type')

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
        else if ($para1 == 'test')
        {
            if ($this
                ->Api_model
                ->singleselectbox('user', 'user_id', $para2, 'token') == $this
                ->input
                ->get('api_token', true))
            {

                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);

                $deliveray_status = array(
                    'admin' => '',
                    'placedstatus' => true,
                    'placedtime' => time() ,
                    'acceptstatus' => false,
                    'accepttime' => 0,
                    'packedstatus' => false,
                    'packedtime' => 0,
                    'shippedstatus' => false,
                    'shippedtime' => 0,
                    'deliverstatus' => false,
                    'delivered' => 0,
                );

                if ($get_data
                    ->payment->method == 'online')
                {
                    $paymentstatus = 'Paid';
                }
                else
                {
                    $paymentstatus = 'UnPaid';
                }

                $data['product_details'] = json_encode($get_data->cart);
                $data['shipping_address'] = json_encode($get_data->address);
                $data['sale_code'] = $get_data->saleCode;
                $data['buyer'] = $get_data->userId;
                $data['shipping'] = '';
                $data['payment_type'] = $get_data
                    ->payment->method;
                $data['payment_status'] = $paymentstatus;
                $data['payment_timestamp'] = time();
                $data['grand_total'] = $get_data->grand_total - ($get_data->payment->delivery_fees + $get_data->payment->delivery_tips + $get_data->payment->tax);
                $data['sale_datetime'] = time();
                $data['delivary_datetime'] = time();
                $data['delivery_status'] = json_encode($deliveray_status);
                $data['viewed'] = 'no';
                $data['status'] = 'Placed';
                $data['delivery_slot'] = $get_data->deliveryTimeSlot;
                $data['vendor'] = $get_data->shopId;
                $data['payment_details'] = json_encode($get_data->payment);
                $data['driver_charge'] = $get_data
                    ->payment->delivery_fees;
                $data['order_type'] = $get_data->deliverType;
                $data['focus_id'] = $get_data->focusId;
                $this->db->insert('sale', $data);
                

                $data1['invoice_id'] = $get_data->saleCode;
                $data1['vendor_id'] = $get_data->shopId;
                $data1['order_date'] = $data['sale_datetime'];
                $data1['order_amount'] = $data['grand_total'];
                $data1['driver_tips'] = $get_data->payment->delivery_tips;

                $data1['commission'] = $this ->Api_model->getcommission_amount($data['grand_total'],   $get_data->shopId);
                $data1['settlement_value'] = ($data['grand_total'] - $data1['commission']);
                $data1['timestamp'] = time();
                $data1['status'] = 'due';
                $data1['method'] = $get_data->payment->method;
                $data1['driver_comission'] = $this->Api_model->getcommission_deliveryamount($get_data->payment->delivery_fees);
                $data1['driver_fees'] = $get_data->payment->delivery_fees;
                $data1['km'] = $get_data->km;
                $data1['cus_trans'] = 1;
                $data1['buyer_id'] = $get_data->userId;
                $data1['delivery_slot'] = $get_data->deliveryTimeSlot;
                $data1['order_type'] = $get_data->deliverType;
                
                $this->db->insert('vendor_invoice', $data1);
                
                if($get_data->payment->method!='online'){
                    
                 $wallet =  $this->Api_model->vendorWalletAmount($get_data->shopId,1);
                 if($wallet=='no'){
                     $data2['vendor_id'] = $get_data->shopId;
                     $data2['balance'] = $get_data->wallet + $data1['settlement_value'];
                     $data2['last_update'] = time();
                     $data2['totalamount'] = $data['grand_total'];
                     $this->db->insert('vendor_wallet',$data2);
                 } else {
                     
                     $data2['vendor_id'] = $get_data->shopId;
                     $data2['balance'] = $get_data->wallet + $data1['settlement_value'];
                     $data2['last_update'] = time();
                     $data2['totalamount'] = $data['grand_total'];
                     $this->db->where('vendor_wallet_id',$get_data->shopId);
                     $this->db->update('vendor_wallet',$data2);
                 }
                }
                
          
                
                

                $responce = array(
                    'success' => true,
                    'data' => $get_data->saleCode,
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
        else if ($para1 == 'ordertrack')
        {

            $loop = $this
                ->db
                ->get_where('sale', array(
                'sale_code' => $para2
            ))->result_array();

            foreach ($loop as $row)
            {
                $address[] = json_decode($row['shipping_address'], true);
                $status[] = json_decode($row['delivery_status'], true);
                $data[] = array(
                    'status' => $row['status'],
                    'id' => $row['sale_code'],
                    'address' => $address,
                    'statusManager' => $status,
                    'otp' => $row['otp'],
                    'dateTime' => $row['sale_datetime']
                );
            }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'ordertrack retrieved successfully',
            );
        }
        else if ($para1 == 'orderDetails')
        {

            $loop = $this
                ->db
                ->get_where('sale', array(
                'sale_code' => $para2
            ))->result_array();

            foreach ($loop as $row)
            {
                $addressShop = array(
                    'id' => $row['vendor'],
                    'addressSelect' => $this
                        ->Api_model
                        ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'address1') ,
                    'username' => $this
                        ->Api_model
                        ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'display_name') ,
                    'phone' => $this
                        ->Api_model
                        ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'phone') ,
                    'latitude' => floatval($this
                        ->Api_model
                        ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'latitude')) ,
                    'longitude' => floatval($this
                        ->Api_model
                        ->singleselectbox('vendor', 'vendor_id', $row['vendor'], 'longitude')) ,
                );

                $data = array(
                    'id' => $row['sale_id'],
                    'productDetails' => json_decode($row['product_details']) ,
                    'addressUser' => json_decode($row['shipping_address']) ,
                    'addressShop' => $addressShop,
                    'payment' => json_decode($row['payment_details']) ,
                    'orderDate' => date("F j, Y", $row['sale_datetime']) ,
                    'status' => $row['status'],
                    'orderType' => $row['order_type'],
                    'delivery_slot' => $row['delivery_slot'],
                    'userId' => '12',
                    'saleCode' => $row['sale_code'],
                    'instanceDelivery' => false,
                    'rating' => $row['rating'],
                    'shopTypeId' => $this
                        ->Api_model
                        ->singleselectbox('shop_focus', 'shop_focus_id', $row['focus_id'], 'shop_type')
                );

            }
            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'invoice details retrieved successfully',
            );

        }

        echo json_encode($responce);
    }
    
 
    
    
    
    /** review **/
    function review($para1 = '', $para2 = '', $para3 = '')
    {

        if ($para1 == 'do_add')
        {
            if ($this
                ->Api_model
                ->singleselectbox('user', 'user_id', $para2, 'token') == $this
                ->input
                ->get('api_token', true))
            {
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                $data['product_id'] = $get_data->product_id;
                $data['user_id'] = $get_data->user_id;
                $data['ratings'] = $get_data->rate;
                $data['comments'] = $get_data->review;
                $data['date'] = time();
                $this
                    ->db
                    ->insert('comments', $data);

                $fivecount = $this
                    ->Api_model
                    ->count_2wcopt('comments', 'ratings', 5, 'product_id', $get_data->product_id);
                $fourcount = $this
                    ->Api_model
                    ->count_2wcopt('comments', 'ratings', 4, 'product_id', $get_data->product_id);
                $threecount = $this
                    ->Api_model
                    ->count_2wcopt('comments', 'ratings', 3, 'product_id', $get_data->product_id);
                $twocount = $this
                    ->Api_model
                    ->count_2wcopt('comments', 'ratings', 2, 'product_id', $get_data->product_id);
                $onecount = $this
                    ->Api_model
                    ->count_2wcopt('comments', 'ratings', 1, 'product_id', $get_data->product_id);

                $total_rateing = (5 * $fivecount + 4 * $fourcount + 3 * $threecount + 2 * $twocount + 1 * $onecount) / ($fivecount + $fourcount + $threecount + $twocount + $onecount);

                $data2['rating_total'] = $total_rateing;

                $data2['rating_user'] = intval($fivecount + $fourcount + $threecount + $twocount + $onecount);
                $this
                    ->db
                    ->where('product_id', $get_data->product_id);
                $this
                    ->db
                    ->update('product', $data2);
                $responce = array(
                    'success' => true,
                    'data' => '',
                    'message' => 'Rating insert successfully',
                );
            }
            else
            {

                $responce = $this
                    ->Api_model
                    ->tokenfailed();
            }

        }
        else if ($para1 == 'check_rating')
        {
            if ($this
                ->Api_model
                ->singleselectbox('user', 'user_id', $para3, 'token') == $this
                ->input
                ->get('api_token', true))
            {
                $loop = $this
                    ->Api_model
                    ->count_2wcopt('comments', 'user_id', $para3, 'product_id', $para2);

                if ($loop > 0)
                {
                    $result = 'already';
                }
                else
                {
                    $result = 'no';
                }

                $responce = array(
                    'success' => true,
                    'data' => $result,
                    'message' => 'Rating check successfully',
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

            $loop = $this
                ->db
                ->get_where('comments', array(
                'product_id' => $para2
            ))->result_array();
            foreach ($loop as $row)
            {
                $data[] = array(
                    'comments_id' => $row['comments_id'],
                    'user_id' => $row['user_id'],
                    'name' => $this
                        ->Api_model
                        ->singleselectbox('user', 'user_id', $row['user_id'], 'username') ,
                    'rating' => $row['ratings'],
                    'comments' => $row['comments'],
                    'date' => intval($row['date']) ,
                    'image' => '',
                );
            }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'Rating check successfully',
            );

        }

        echo json_encode($responce);
    }

    /** profileupdate **/
    function profileupdate($para1 = '', $para2 = '')
    {
        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);

        if ($this
            ->Api_model
            ->singleselectbox('user', 'user_id', $para1, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            $data['username'] = $get_data->name;
            $data['phone'] = $get_data->phone;
            $data['selected_address'] = $get_data->selected_address;
            $data['latitude'] = $get_data->latitude;
            $data['longitude'] = $get_data->longitude;
            $data['address'] = json_encode($get_data->address);
            $data['faviourt'] = json_encode($get_data->wishList);
            $this
                ->db
                ->where('user_id', $get_data->id);
            $this
                ->db
                ->update('user', $data);
            $responce = array(
                'success' => true,
                'data' => 'success',
                'message' => 'profile update successfully',
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
    
    
    
 
    
    /** register **/
    function register()
    {

        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);
        $loop = $this
            ->db
            ->get_where('user', array(
            'email' => $get_data->email_id
        ))
            ->result_array();
        if (count($loop) == 0)
        {

            $data['username'] = $get_data->name;
            $data['email'] = $get_data->email_id;
            $data['phone'] = $get_data->phone;
            $data['password'] = sha1($get_data->password);
            $data['token'] = '';
            $data['date'] = time();
            $data['status'] = 'success';
            $data['address'] = json_encode([]);
            $data['image'] = 'noimg';
            $data['faviourt'] = json_encode([]);
            $this
                ->db
                ->insert('user', $data);
            $data = 'success';
        }
        else
        {
            $data = 'Already Registered';
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'register retrieved successfully',
        );
        echo json_encode($responce);
    }
    /** address **/
    function address($para1 = '', $para2 = '')
    {
        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);
        if ($para1 == 'do_add')
        {
            $data['username'] = $get_data->username;
            $data['phone'] = $get_data->phone;
            $data['address_l1'] = $get_data->address_l1;
            $data['address_l2'] = $get_data->address_l2;
            $data['city'] = $get_data->city;
            $data['state'] = $get_data->state;
            $data['pincode'] = $get_data->pincode;
            $data['userid'] = $get_data->userid;
            $data['isDefault'] = false;
            $this
                ->db
                ->insert('address', $data);

            $responce = array(
                'success' => true,
                'data' => $get_data->phone,
                'message' => 'address added successfully',
            );

        }
        else if ($para1 == 'list')
        {

            $loop = $this
                ->db
                ->get_where('address', array(
                'userid' => $para2
            ))->result_array();
            if (count($loop) > 0)
            {
                if ($row['isDefault'] == 'true')
                {
                    $isselected = true;
                }
                else
                {
                    $isselected = false;
                }
                foreach ($loop as $row)
                {
                    $data[] = array(
                        'id' => $row['address_id'],
                        'username' => $row['username'],
                        'phone' => $row['phone'],
                        'address_l1' => $row['address_l1'],
                        'address_l2' => $row['address_l2'],
                        'city' => $row['city'],
                        'state' => $row['state'],
                        'pincode' => $row['pincode'],
                        'userid' => $row['userid'],
                        'isDefault' => $isselected,
                    );

                }
            }
            else
            {

                $data[] = array(
                    'id' => 'No_data',
                    'username' => '',
                    'phone' => '',
                    'address_l1' => '',
                    'address_l2' => '',
                    'city' => '',
                    'state' => '',
                    'pincode' => '',
                    'userid' => '',
                    'isDefault' => false,
                );
            }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'address retrieved successfully',
            );
        }
        echo json_encode($responce);
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
            ->get_where('user', array(
            'email' => $get_data->email,
            'password' => sha1($get_data->password) ,
            'status' => 'success'
        ))
            ->result_array();
        if (count($loop) > 0)
        {
            foreach ($loop as $row)
            {
                if (file_exists('uploads/user_image/' . $row['image']))
                {
                    $img = base_url() . 'uploads/user_image/' . $row['image'];
                }
                else
                {
                    $img = 'no_image';
                }

                $address = [];

                $data1['token'] = sha1($row['user_id']);

                $data = array(
                    'id' => $row['user_id'],
                    'name' => $row['username'],
                    'email' => $row['email'],
                    'api_token' => $data1['token'],
                    'device_token' => $row['device_id'],
                    'phone' => $row['phone'],
                    'status' => $row['status'],
                    'latitude' => floatval($row['latitude']) ,
                    'longitude' => floatval($row['longitude']) ,
                    'auth' => true,
                    'address' => json_decode($row['address'], true) ,
                    'selected_address' => $row['selected_address'],
                    'favoriteShop' => json_decode($row['favorite_store'], true),	
                    'image' => $img,
                    'walletAmount' =>  $this->Api_model->walletAmount($row['user_id']),
                );
            }
            $this
                ->db
                ->where('user_id', $row['user_id']);
            $this
                ->db
                ->update('user', $data1);

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
    
    
    function updatePassword($para1='', $para2=''){              
        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);
      
          $data['password'] = sha1($get_data->password);
          $this->db->where('email', $get_data->email);
          $this->db->update('user', $data);
          $response = array(
              'data' => true,
          );

        echo json_encode($response);
    }
    
    
    	function favoriteShop($para1='', $para2='', $para3=''){
	
	
	   if($para1=='do_add'){	
	        if($this->Api_model->singleselectbox('user','user_id',$para2,'token')== $this->input->get('api_token', TRUE)){	
		     $content_data = file_get_contents("php://input");
          $get_data     = json_decode($content_data);
			$data['favorite_store'] = json_encode($get_data->favoriteShop);
			$this->db->where('user_id',$para2);
			$this->db->update('user',$data);
			$responce = array(
		            'status' => 'success'
	          );
			}else{
				$responce = $this->Api_model->tokenfailed();
			}
			 echo json_encode($responce);
	   }else if($para1=='list'){
		  
         if($this->Api_model->singleselectbox('user','user_id',$para2,'token')== $this->input->get('api_token', TRUE)){	
		
		  $loop2 =   $this->db->get_where('user',array('user_id'=>$para2))->result_array();
		   foreach($loop2 as $row2){
		       $id  =  json_decode($row2['favorite_store'],true);
		foreach($id as $shopId){
          
         
        $loop = $this->db->get_where('vendor',array('vendor_id'=>$shopId))->result_array();
              
            if (count($loop) > 0)
            {
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
                    $distance = number_format($this
                        ->Api_model
                        ->distance($this->myLat, $this->myLon, $row['latitude'], $row['longitude'], "k") , 2);
                    $data[] = array(
                        'shopId' => $row['vendor_id'],
                        'shopName' => $row['display_name'],
                        'subtitle' => $row['subtitle'],
                        'locationMark' => $row['address1'],
                        'rate' => '2',
                        'distance' => $distance,
                        'logo' => $img,
                        'cover' => $coverImage,
                        'latitude' => $row['latitude'],
                        'longitude' => $row['longitude'],
                        'openStatus' => $row['livestatus']=='true'?true:false,
                        'shopType' => $row['type'],
                        'focusType' => $row['focus_id'],
                        'marketKey' => $row['market_camping_key'],
                        'marketValue' => $row['market_camping_value'],
                        'instant' => $row['instant']==1?true:false,
                        'takeaway' => $row['takeaway']==1?true:false,
                        'schedule' => $row['schedule']==1?true:false,
                        
                    );
                }

            }
            
           
           
            }

           }
		    $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'order retrieved successfully',
						);
		 }else{
			$responce = $this->Api_model->tokenfailed();
		 }
		
		 
		  echo json_encode($responce);	
	   }
	}
	
    
    
    /** smart login **/
    
       function smartLogin($para1='', $para2=''){
        $content_data = file_get_contents("php://input");
        $get_data = json_decode($content_data);
        if($get_data->regVia=='GMail' || $get_data->regVia=='Phone'){
               if($get_data->regVia=='GMail'){
              $loop = $this->db->get_where('user', array('email' => $get_data->email_id)) ->result_array();
               } else if($get_data->regVia=='Phone'){
              $loop = $this->db->get_where('user', array('phone' => $get_data->phone)) ->result_array();      
               }
           
         if (count($loop) == 0)
        {   
         $data['username'] =  $get_data->name;
         $data['email'] = $get_data->email_id;
         $data['phone'] = $get_data->phone;
         $data['regVia'] =  $get_data->regVia;   
         $data['token'] = sha1( $get_data->email_id);
         $data['date'] = time();
         $data['status'] = 'success';
         $data['address'] = json_encode([]);
         $data['image'] = 'noimg';
         $data['faviourt'] = json_encode([]);
         $data['image'] = $get_data->img;   
          $this ->db ->insert('user', $data);
             
             if($get_data->regVia=='GMail'){
          $data =   $this->Api_model->smartuseremail($get_data->email_id,$get_data->regVia );
             } else if($get_data->regVia=='Phone'){
           $data =   $this->Api_model->smartuseremail($get_data->phone,$get_data->regVia );       
             }
            
             $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'profile retrieved successfully',
        ); 
             
         } else{
             
         $data['username'] = $get_data->name;
         $data['email'] = $get_data->email_id;
         $data['phone'] = $get_data->phone; 
         $data['image'] = $get_data->img;   
         $data['token'] = sha1( $get_data->phone);
            $this ->db ->where('email', $get_data->email_id);
            $this ->db ->update('user', $data);
               if($get_data->regVia=='GMail'){
          $data =   $this->Api_model->smartuseremail($get_data->email_id,$get_data->regVia );
             } else if($get_data->regVia=='Phone'){
           $data =   $this->Api_model->smartuseremail($get_data->phone,$get_data->regVia );       
             }
               $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'profile retrieved successfully',
        ); 
             
         }
            
             
        }
        
        
          echo json_encode($responce);
    }
    
    
    
   /** recharge **/ 
    function recharge(){
		
		  $content_data = file_get_contents("php://input");
          $get_data     = json_decode($content_data);
		
		 $balance = $this->Api_model->singleselectbox('wallet','user_id',$get_data->user_id,'balance');
         $amount = $balance + $get_data->amount;
        	
		
		  $data['user_id']       = $get_data->user_id;
		  $data['balance']       = $amount;
		  $data['last_update']    = time();
	      $data['lastamount']     = $get_data->amount;
		 
        if($balance!=''){
	
		  $this->db->where('user_id',$get_data->user_id);
		  $this->db->update('wallet',$data);
        } else{
        
		     $this->db->insert('wallet',$data);
		  
        }
		  
		  $data1['user_id']        =  $get_data->user_id;
		  $data1['amount']         =  $get_data->amount;
		  $data1['balance']        =   $amount;
		  $data1['status']         =  'success';
		  $data1['date']           =  time();
		  $data1['type']           =  $get_data->type;
		  $this->db->insert('wallet_transactions',$data1);
		  
		    $responce = array(
	                     'success' => true,
						 'data' => 'success',
						 'message' => 'register retrieved successfully',
						);
	   echo json_encode($responce);	
	}
    
    
    
    /** profileimage **/
    function profileimage($para1 = '', $para2 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('user', 'user_id', $para1, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            $id = $para1;
            $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $data_banner['image'] = 'user_' . $id . '.' . $ext;
            $this
                ->crud_model
                ->file_up("image", "user", $id, '', 'no', '.jpg');
            $this
                ->db
                ->where('user_id', $id);
            $this
                ->db
                ->update('user', $data_banner);
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
    /** sendimage **/
    function sendimage($para1 = '', $para2 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('user', 'user_id', $para1, 'token') == $this
            ->input
            ->get('api_token', true))
        {
            $id = $para2;
            $path = $_FILES['image']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $data_banner['p_image'] = 'sales_' . $id . '.' . $ext;
            $this
                ->db
                ->where('sale_code', $id);
            $this
                ->db
                ->update('sale', $data_banner);
            $this
                ->crud_model
                ->file_up("image", "sales", $id, '', 'no', '.jpg');

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
    /** home_settings **/
    function home_settings($para1 = '', $para2 = '', $para3='')
    {

        if ($para1 == 'topvendor')
        {
            $this
                ->db
                ->limit(10);
            $this->myLat = $this
                ->input
                ->get('myLat', true);
            $this->myLon = $this
                ->input
                ->get('myLon', true);

            $cover_radius = $this
                ->Api_model
                ->get_type_name_by_id('general_settings', '93', 'value');

            if($para2==''){
            $loop = $this
                ->db
                ->query("SELECT   cover_image, vendor_id, latitude, longitude,display_name, subtitle, address1, logo,type,focus_id,livestatus,  market_camping_key,market_camping_value,instant,takeaway,schedule, SQRT(
    POW(69.1 * (latitude - $this->myLat), 2) +
    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM vendor WHERE livestatus ='true' ORDER BY distance")->result_array();
//FROM vendor WHERE livestatus ='true' HAVING distance < $cover_radius ORDER BY distance")->result_array();
            }
            else {
               
              $loop = $this
                ->db
                ->query("SELECT  cover_image, vendor_id, latitude, longitude, display_name, subtitle, address1, logo,type,focus_id,livestatus, market_camping_key,market_camping_value,instant,takeaway,schedule, SQRT(
    POW(69.1 * (latitude - $this->myLat), 2) +
    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM vendor WHERE livestatus ='true' AND focus_id =$para2 ORDER BY distance")->result_array();   
//FROM vendor WHERE livestatus ='true' AND focus_id =$para2 HAVING distance < $cover_radius ORDER BY distance")->result_array();   
            }

            if (count($loop) > 0)
            {
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
                    $distance = number_format($this
                        ->Api_model
                        ->distance($this->myLat, $this->myLon, $row['latitude'], $row['longitude'], "k") , 2);
                    $data[] = array(
                        'shopId' => $row['vendor_id'],
                        'shopName' => $row['display_name'],
                        'subtitle' => $row['subtitle'],
                        'locationMark' => $row['address1'],
                        'rate' => '2',
                        'distance' => $distance,
                        'logo' => $img,
                        'cover' => $coverImage,
                        'latitude' => $row['latitude'],
                        'longitude' => $row['longitude'],
                        'openStatus' => $row['livestatus']=='true'?true:false,
                        'shopType' => $row['type'],
                        'focusType' => $row['focus_id'],
                        'marketKey' => $row['market_camping_key'],
                        'marketValue' => $row['market_camping_value'],
                        'instant' => $row['instant']==1?true:false,
                        'takeaway' => $row['takeaway']==1?true:false,
                        'schedule' => $row['schedule']==1?true:false,
                        
                    );
                }

            }
            else
            {

                $data[] = array(
                    'shopId' => 'no_data',
                    'shopName' => '',
                    'subtitle' => '',
                    'locationMark' => '',
                    'rate' => '',
                    'distance' => '',
                    'logo' => '',
                    'cover' => '',
                    'latitude' => '',
                    'longitude' => '',
                    'openStatus' => '',
                    'shopType' => '',
                    'focusType' => '',
                );

            }

            $responce = array(
                'data' => $data,
                'message' => 'vendor retrieved successfully',
                'success' => true
            );

        }  else if ($para1 == 'shoptype')
        {
            $this
                ->db
                ->order_by('shop_focus_id', 'asc');
            $this
                ->db
                ->select('shop_focus_id,title,preview_image,cover_image,shop_type');
            $loop = $this
                ->db
                ->get_where('shop_focus')
                ->result_array();
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['shop_focus_id'],
                    'title' => $row['title'],
                    'previewImage' => $row['preview_image'] != '' ? base_url() . 'uploads/preview_image/' . $row['preview_image'] : 'no_image',
                    'coverImage' => $row['cover_image'] != '' ? base_url() . 'uploads/shoptypecover_image/' . $row['cover_image'] : 'no_image',
                    'shopType' => $row['shop_type'],
                );
            }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'shoptype retrieved successfully',
            );
        }
       
       
        echo json_encode($responce);

    }
    /** logistics_pricing **/
    function logistics_pricing($para1 = '', $para2 = '')
    {
        if ($para1 == 'get')
        {

            $this
                ->db
                ->order_by('from_range', 'asc');
            $loop = $this
                ->db
                ->get_where('logistics_pricing')
                ->result_array();
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['logistics_pricing_id'],
                    'fromRange' => $row['from_range'],
                    'toRange' => $row['to_range'],
                    'amount' => $row['amount'],
                );
            }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'logistics pricing retrieved successfully',
            );
        }
        echo json_encode($responce);
    }
    
    
      	function h_subcategories($para1='',$para2=''){
	    	       $this->db->order_by('h_sub_category_id','asc');
		 $loop = $this->db->get_where('h_sub_category',array('h_category_id'=>$para1))->result_array();
		 foreach($loop as $row){
             $data[] =  array(
			        'id' => $row['h_sub_category_id'],
					'name' => $row['sub_category_name'],
					'image' =>  base_url().'uploads/h_sub_category_image/'.$row['image'],
			   ); 
		 }
	  
	   
	 
	  
	   $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'subcategories retrieved successfully',
						);
	   echo json_encode($responce);
	}
    
    
    function h_categories($para1='',$para2=''){
	    	       $this->db->order_by('h_category_id','asc');
		 $loop = $this->db->get_where('h_category')->result_array();
		 foreach($loop as $row){
             $data[] =  array(
			        'id' => $row['h_category_id'],
					'name' => $row['category_name'],
					'image' =>  base_url().'uploads/h_category_image/'.$row['image'],
			   ); 
		 }
	  
	   
	 
	  
	   $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'categories retrieved successfully',
						);
	   echo json_encode($responce);
	}
    
    
    /** logistics **/
    function logistics($para1 = '', $para2 = '')
    {
        if ($para1 == 'items')
        {

            $this
                ->db
                ->order_by('packageitem_id', 'asc');
            $loop = $this
                ->db
                ->get_where('packageitem')
                ->result_array();
            foreach ($loop as $row)
            {
                $data[] = array(
                    'id' => $row['packageitem_id'],
                    'name' => $row['name'],
                    'isSelected' => false,
                );
            }

            $responce = array(
                'success' => true,
                'data' => $data,
                'message' => 'logistics  retrieved successfully',
            );
        }
        echo json_encode($responce);
    }
    /** tips **/
    function tips($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->order_by('tips_id', 'asc');
        $loop = $this
            ->db
            ->get('tips')
            ->result_array();

        foreach ($loop as $row)
        {
            $data[] = array(
                'amount' => intval($row['amount']) ,
                'selected' => false,
            );
        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'tips retrieved successfully',
        );
        echo json_encode($responce);
    }
    /** category_wise_restaurantproduct **/
    function category_wise_restaurantproduct($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->select('category_id, category_name, banner');
        $loop = $this
            ->db
            ->get_where('category', array(
            'data_vendors' => $para1
        ))->result_array();

        foreach ($loop as $row)
        {
            $this
                ->db
                ->select('restaurantproduct_id, title , rating_num');
            $data1 = [];
            $this
                ->db
                ->order_by('rand()');

            $loop1 = $this
                ->db
                ->get_where('restaurantproduct', array(
                'category' => $row['category_id']
            ))->result_array();
            foreach ($loop1 as $row1)
            {

                $variant = $this
                    ->Api_model
                    ->twowloop('variant', 'product_id', $row1['restaurantproduct_id'], 'type', 2);

                $variants = [];
                $i = 0;
                foreach ($variant as $row2)
                {
                    if ($i == 0)
                    {
                        $selected = true;

                    }
                    else
                    {
                        $selected = false;
                    }
                    if($row2['image_type'] == 'external') {
                        $image = $row2['image'];
                    } else if($row2['image_type'] == 'no_image' || $row2['image_type'] == '') {
                        $image = base_url() . 'uploads/restaurantproduct_image/' . $row2['image'];
                    }

                    $i++;
                    $variants[] = array(
                        'variant_id' => $row2['variant_id'],
                        'product_id' => $row2['product_id'],
                        'quantity' => $row2['quantity'],
                        'name' => $row2['name'],
                        'minPurchase' => $row2['min_purchase'],
                        'maxPurchase' => $row2['max_purchase'],
                        'tax' =>  doubleval(['tax']),
                        'discount' => $row2['discount'],
                        'unit' => $row2['unit'],
                        'image' => $image,
                        'num_of_imgs' => intval($row1['num_of_imgs']) ,
                        'sale_price' => $row2['sale_price'],
                        'strike_price' => $row2['strike_price'],
                        'type' => $row2['type'],
                        'selected' => $selected,
                        'foodType' => $row2['food_type']
                    );
                }

                $addon = $this
                    ->Api_model
                    ->twowloop('addon', 'product_id', $row1['restaurantproduct_id'], 'type', 2);
                
                $addons = [];
                $categoryAddons = [];

                foreach ($addon as $row3)
                {
                    $addons[] = array(
                        'addon_id' => $row3['addon_id'],
                        'product_id' => $row3['product_id'],
                        'name' => $row3['name'],
                        'price' => $row3['sales_price'],
                        'type' => $row3['type'],
                        'selected' => false,
                        'foodType' => $row3['food_type'],
                        'category' => $row3['category']
                    );
                    array_push($categoryAddons, $row3['category']);
                }

                $data1[] = array(
                    'id' => $row1['restaurantproduct_id'],
                    'product_name' => $row1['title'],
                    'variant' => $variants,
                    'addon' => $addons,
                    'categoryAddon' => $categoryAddons
                );
            }

            $data[] = array(
                'id' => $row['category_id'],
                'category_name' => $row['category_name'],
                'productdetails' => $data1,
            );
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'subcategories retrieved successfully',
        );
        echo json_encode($responce);
    }
    /** rating **/
    function rating($para1 = '', $para2 = '', $para3 = '')
    {

        if ($this
            ->Api_model
            ->singleselectbox('user', 'user_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            if ($para1 == 'shoprating')
            {

                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);

                $data2['rating'] = $get_data->rate;
                $this
                    ->db
                    ->where('sale_code', $para3);
                $this
                    ->db
                    ->update('sale', $data2);

                $data['rating'] = $get_data->rate;
                $data['taste'] = $get_data->taste;
                $data['packing'] = $get_data->packing;
                $data['portion'] = $get_data->portion;
                $data['message'] = $get_data->message;
                $data['buyer_id'] = $get_data->buyer;
                $data['vendor_id'] = $get_data->vendor;
                $data['date'] = time();
                $this
                    ->db
                    ->insert('shop_rating', $data);

                $preview = $this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $data['vendor_id'], 'rating');
                $total = ($this
                    ->Api_model
                    ->singleselectbox('vendor', 'vendor_id', $data['vendor_id'], 'total_rating') + 1);

                $currentrating = ($preview * $pretotal) + $data['rating'] / $total;

                $data1['rating'] = $currentrating;
                $data1['total_rating'] = $total;
                $this
                    ->db
                    ->where('vendor_id', $data['vendor_id']);
                $this
                    ->db
                    ->update('vendor', $data1);

                $responce = array(
                    'success' => true,
                );

            }
            else if ($para1 == 'driverrating')
            {
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                $data['rating'] = $get_data->rate;
                $data['message'] = $get_data->message;
                $data['buyer_id'] = $get_data->buyer;
                $data['vendor_id'] = $get_data->driver;
                $data['order_id'] = $get_data->orderId;
                $data['date'] = time();
                $this
                    ->db
                    ->insert('driver_rating', $data);

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
    /** cancelOrder **/
    function cancelOrder($para1 = '', $para2 = '')
    {
        if ($this
            ->Api_model
            ->singleselectbox('user', 'user_id', $para2, 'token') == $this
            ->input
            ->get('api_token', true))
        {

            if ($para1 == 'cancelled')
            {
                $content_data = file_get_contents("php://input");
                $get_data = json_decode($content_data);
                $data['order_id'] = $get_data->orderId;
                $data['user_id'] = $get_data->userId;
                $data['shop_id'] = $get_data->shopId;
                $data['amount'] = $get_data->amount;
                $data['message'] = $get_data->message;
                $data['date'] = time();
                $this
                    ->db
                    ->insert('cancelled_order', $data);
                $responce = array(
                    'success' => true,
                );

            }
            else if ($para1 == 'cancel_update')
            {

                $this
                    ->db
                    ->where('sale_code', $para3);
                $this
                    ->db
                    ->update('sale', $data);

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

    /** bestof **/
    function bestof($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->limit(4);
        $this
            ->db
            ->select('categoryid, bestof_id');
        $loop = $this
            ->db
            ->get_where('bestof')
            ->result_array();

        foreach ($loop as $row)
        {
            $this
                ->db
                ->select('product_id, title , sale_price , purchase_price, discount, rating_num, num_of_imgs, color, unit');
            $data1 = [];
            $this
                ->db
                ->order_by('rand()');
            $this
                ->db
                ->limit(10);
            $loop1 = $this
                ->db
                ->get_where('product', array(
                'category' => $row['categoryid']
            ))->result_array();
            foreach ($loop1 as $row1)
            {
                if ($row1['color'] == 'null')
                {
                    $color1 = 'no';
                }
                else
                {
                    $color1 = 'yes';
                }
                $data1[] = array(
                    'id' => $row1['product_id'],
                    'product_name' => $row1['title'],
                    'price' => $row1['sale_price'],
                    'strike' => $row1['purchase_price'],
                    'tax' => doubleval(['tax']),
                    'offer' => intval($row1['discount']) ,
                    'rating' => intval($row1['rating_num']) ,
                    'image' => base_url() . 'uploads/product_image/product_' . $row1['product_id'] . '_1_thumb.jpg',
                    'num_of_imgs' => intval($row1['num_of_imgs']) ,
                    'color' => $color1,
                    'unit' => $row1['unit'],
                );
            }

            $data[] = array(
                'id' => $row['bestof_id'],
                'category' => $this
                    ->Api_model
                    ->singleselectbox('category', 'category_id', 61, 'category_name') ,
                'productdetails' => $data1,
            );
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'subcategories retrieved successfully',
        );
        echo json_encode($responce);
    }
    /** categories **/
    function categories($para1 = '', $para2 = '')
    {
        $this
            ->db
            ->order_by('category_id', 'asc');
        $loop = $this
            ->db
            ->get_where('category', array(
            'data_vendors' => $para1
        ))->result_array();
        foreach ($loop as $row)
        {
            $data[] = array(
                'id' => $row['category_id'],
                'name' => $row['category_name'],
                'image' => base_url() . 'uploads/category_image/' . $row['banner'],
            );
        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'Categories retrieved successfully',
        );
        echo json_encode($responce);
    }
    /** sub_categories **/
    function sub_categories($para1 = '', $para2 = '')
    {
        $this
            ->db
            ->order_by('sub_category_id', 'RANDOM');
        $this
            ->db
            ->limit(10);
        $this
            ->db
            ->order_by('sub_category_id', 'asc');
        $loop = $this
            ->db
            ->get_where('sub_category')
            ->result_array();
        foreach ($loop as $row)
        {
            $data[] = array(
                'id' => $row['sub_category_id'],
                'name' => $row['sub_category_name'],
                'image' => base_url() . 'uploads/sub_category_image/' . $row['banner'],
            );
        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'Categories retrieved successfully',
        );
        echo json_encode($responce);
    }

    function slider($para1 = '', $para2 = '')
    {

        $this->db->order_by('banner_master_id', 'asc');
        $loop = $this ->db ->get_where('banner_master', array('type' => $para1))->result_array();
        foreach ($loop as $row)
        {
            $data[] = array(
                'id' => $row['banner_master_id'],
                'slider_text' => $row['title'],
                'redirect_type' => $row['redirect_type'],
                'para' => $row['para'],
                'superCategoryId' => $row['superCategoryId'],
                'image' => base_url() . 'uploads/banner_master_image/' . $row['image'],
            );
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'slider retrieved successfully',
        );
        echo json_encode($responce);
    }

    function trends($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->order_by('trends_id', 'asc');
        $loop = $this
            ->db
            ->get_where('trends')
            ->result_array();
        foreach ($loop as $row)
        {
            $data[] = array(
                'id' => $row['trends_id'],
                'name' => $row['title'],
                'image' => base_url() . 'uploads/trends_image/' . $row['image'],
            );
        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'trends retrieved successfully',
        );
        echo json_encode($responce);
    }

    function category_wise_product1($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->select(' product.title, product.product_id, product.product_type,sub_category.sub_category_id,sub_category.sub_category_name');
        $this
            ->db
            ->from('sub_category');
        $this
            ->db
            ->where("sub_category.category", $para1);
        $this
            ->db
            ->join('product', 'product.sub_category = sub_category.sub_category_id');
        $this
            ->db
            ->where("product.category", $para1);
        $query = $this
            ->db
            ->get()
            ->result_array();

        foreach ($query as $row)
        {
            echo $row['sub_category_id'];
            echo 'br';
            echo $row['title'];
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'subcategories retrieved successfully',
        );
        echo json_encode($responce);

    }

    /** category wise product - groceries & pharmacy*/
    function category_wise_product($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->select('sub_category_id,sub_category_name');
        $loop = $this
            ->db
            ->get_where('sub_category', array(
            'category' => $para1
        ))->result_array();
        foreach ($loop as $row)
        {
            $this
                ->db
                ->select('product_id, title ,  product_type');
            $data1 = [];
            $this
                ->db
                ->order_by('rand()');
            $loop1 = $this
                ->db
                ->get_where('product', array(
                'category' => $para1,
                'sub_category' => $row['sub_category_id']
            ))->result_array();
            foreach ($loop1 as $row1)
            {

                $variant = $this
                    ->Api_model
                    ->twowloop('variant_2', 'product_id', $row1['product_id'], 'type', $row1['product_type']);

                $variants = [];
                $i = 0;
                foreach ($variant as $row2)
                {
                    if ($i == 0)
                    {
                        $selected = true;

                    }
                    else
                    {
                        $selected = false;
                    }

                    $i++;
                    $variants[] = array(
                        'variant_id' => $row2['variant_2_id'],
                        'product_id' => $row2['product_id'],
                        'quantity' => $row2['quantity'],
                        'name' => $row2['name'],
                        'unit' => $row2['unit'],
                        'sale_price' => $row2['sale_price'],
                        'strike_price' => $row2['strike_price'],
                        'minPurchase' => $row2['min_purchase'],
                        'maxPurchase' => $row2['max_purchase'],
                        'discount' => $row2['discount'],
                        'tax' =>  doubleval(['tax']),
                        'type' => $row2['type'],
                        'selected' => $selected,
                        'image' => base_url() . 'uploads/product_image/' . $row2['image'],

                    );
                }

                $data1[] = array(

                    'id' => $row1['product_id'],
                    'product_name' => $row1['title'],
                    'variant' => $variants,
                    'productType' => $row1['product_type'],
                );

            }

            $data[] = array(
                'id' => $row['sub_category_id'],
                'subcategory_name' => $row['sub_category_name'],
                'image' => base_url() . 'uploads/sub_category_image/' . $row['banner'],
                'productdetails' => $data1,
            );
        }

        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'subcategories retrieved successfully',
        );
        echo json_encode($responce);
    }
    
    
    function category_ads($para1 = '', $para2 = '')
    {

        $this
            ->db
            ->order_by('categoryads_id', 'asc');
        $loop = $this
            ->db
            ->get_where('categoryads', array(
            'category' => $para1
        ))->result_array();
        foreach ($loop as $row)
        {
            $data[] = array(
                'id' => $row['categoryads_id'],
                'image' => base_url() . 'uploads/categoryads_image/' . $row['image'],
            );
        }
        $responce = array(
            'success' => true,
            'data' => $data,
            'message' => 'category_ads retrieved successfully',
        );
        echo json_encode($responce);
    }

    
    
    function listprovider($para1='', $para2=''){
	
	  $this->myLon = $this->input->get('myLon', TRUE);
	  $this->myLat = $this->input->get('myLat', TRUE);

      $loopcategory = $this->db->get_where('provider_databook',array('categoryId'=>$para1,'subcategoryId'=>$para2))->result_array();
      $distanceradius = $this->Api_model ->get_type_name_by_id('general_settings', '93', 'value'); 
      foreach($loopcategory as $row2){
	  $providerid = $row2['userid'];
      $services=[];
	 
	  $services[] = array(
	 	  
	               	    "services_name" => $row2['categoryName'],
					     "amount" => $row2['chargePreHrs'],
						 "servicesid" => $row2['provider_databook_id'],
	 
	          );
           $sql = $this
            ->db
            ->query("SELECT latitude, longitude, provider_id, username,mobile,address1, image, SQRT(
    POW(69.1 * (latitude - $this->myLat), 2) +
    POW(69.1 * ($this->myLon - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM provider WHERE provider_id = $providerid AND livestatus ='true'  HAVING distance < $distanceradius ORDER BY distance")->result_array();
          
         
     if (count($sql) > 0)
        {

            foreach ($sql as $row)
            {
                
                $loopservice =  $this->db->get_where('provider_databook',array('userid'=>$row['provider_id']))->result_array();
			 foreach($loopservice as $row1){
				 if($row1['categoryId'] != $para1 && $row1['subcategoryId'] != $para2){
					 $charges = $row1['chargePreHrs'];
$services[] = array(		  
	               	    "services_name" => $row1['categoryName'],
					     "amount" => $row1['chargePreHrs'],
						 "servicesid" => $row1['provider_databook_id'],

				   );
				 }
			 }
                
                 $data[] = array(
			             'vendor_id' => $row['provider_id'],
			             'name' => $row['username'],
						 'subtitle' => 'qwq',
						 'rating' => '5',
						 'outof' => '4',
						 'latitude' => $row['latitude'],
						 'longitude' => $row['longitude'],
						 'distance' => 45.0,
						 'mobile' => $row['mobile'],
						 'image' =>  base_url().'uploads/provider_image/'.$row['image'],
						 'address' => $row['address1'],
						 "chargephrs" =>  $charges,
						 "service" => $services,
      
	
			             );
                
            }
       } else {
         
	
	 
	 $data[] = array(
			             'vendor_id' => 'no_data',
			             'name' => '',
						 'subtitle' => '',
						 'rating' => '',
						 'outof' => '',
						 'latitude' => '',
						 'longitude' => '',
						 'distance' => 0.0,
						 'mobile' => '',
						 'image' => '',
						 'address' => '',
						 "chargephrs" =>  '',
						 "service" => [],
      
	
			             );
     }
          
      }
        		       
	  $responce = array(
	                     'success' => true,
						 'data' => $data,
		  				 'datavalue' => $distanceradius,
						 'message' => 'provider retrieved successfully',
						);
	       echo json_encode($responce);
        
        
}
    
    
    function HBook($para1='', $para2=''){
		 if($para1=='do_add'){
           
		  $content_data      = file_get_contents("php://input");
          $get_data          = json_decode($content_data);
		  $general[]         = $get_data;
		  $statusmanage[]    = array(
		                        'status' => 'pending',
								'time'   => $get_data->bookingtime
								);
             
		  $data['user_id']  = $get_data->userid;
		  $data['provider_id']     = $get_data->providerId;
		  $data['phone']     = $get_data->providerMobile;
	      $data['detail']  =  json_encode($general);
		  $data['date']      = time();
		  $data['status']    = 'pending';
		  $data['statusmanage']  = json_encode($statusmanage);
		  $data['bookid']    = $get_data->bookId;
		  $data['category_id']    = $get_data->categoryId;
		  $this->db->insert('booking',$data);
		   $responce = array(
	                     'success' => true,
						 'data' => 'success',
						 'message' => 'booked  successfully',
						);
	  
		 } else if($para1=='list'){
			  
			  
			   $loop = $this->db->get_where('booking',array('user_id'=>$para2))->result_array();
			   
			  
			   foreach($loop as $row){
				   
				   $loop2 = json_decode($row['detail'],true);
				   foreach($loop2 as $row2){
			       $data[] =  array(
			            'userid' => $row2['userid'],
					    'providerid' => $row2['providerid'],
					    'categoryid' => $row2['categoryid'],
					    'category_name' => $row2['category_name'],
					    'subcategoryid' => $row2['subcategoryid'],
					    'subcategory_name' => $row2['subcategory_name'],
					    'longtitude' => $row2['longtitude'],
					    'latitude' => $row2['latitude'],
						'time' => $row2['time'],
						'address' => $row2['address'],
						'description' => $row2['description'],
						'service' => $row2['service'],
						'chargeperhrs' => $row2['chargeperhrs'],
						'providename' => $row2['providename'],
						'providermobile' => $row2['providermobile'],
						'providerimage' => base_url().'uploads/trends_image/trends_2.jpg',
						'status' => $row['status'],
						'bookingtime' => intval($row['date']),
			           );
				   }
			   }
			   
			   $responce = array(
	                     'success' => true,
						 'data' => $data,
						 'message' => 'order retrieved successfully',
						);
		 } else if($para1=='statusupdate'){
			 
			 
		 }
		 
		  echo json_encode($responce);	
	}
    
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

