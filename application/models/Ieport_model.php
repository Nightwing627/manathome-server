
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Ieport_model extends CI_Model
{
	 
    function __construct()
    {
        parent::__construct();
    }
	
	
	
   
   function fetch_data($para1='', $para2='', $para3=''){
	   
	   if($para1=='sales'){
		   
		   $res[] = array(
		                'id'             =>  'ID',
			            'invoiceid'       => 'Invoice Id',
						'itemamount'      => 'Item Amount',
						'settlementvalue' => 'Settlement Value',
						'commission'      => 'Commission',
						'paymentmenthod'  => 'Payment Method',
						'paymentstatus'   => 'Payment Status',
						'date'            => 'Date',
						'transactionid'   => 'Transaction ID',  
			            ); 
						
						
			  $this->db->order_by('vendor_invoice_id','desc');
			  $loop =   $this->db->get_where('vendor_invoice',array('vendor_id'=>$para2))->result_array();
			  $i=0;
			foreach($loop as $row){
				$i++;
					$res[] = array(
				            'id' => $i,
							'invoiceid' => '#'.$row['invoice_id'],
							'itemamount'  => $row['order_amount'],
							'settlementvalue' => $row['settlement_value'],
							'commission' => $row['commission'],
							'paymentmenthod' =>   $row['method'],
							'paymentstatus' =>  $row['status'],
							'date' =>  date("F j, Y",$row['order_date']),
							'transactionid' => '1',
                            );
			
				
			}
	   } else if($para1=='orders'){
		    $res[]  = array(
				            'orderId' => 'Order Id',
							'status' => 'Status',
							'price'  => 'Price',
							'contact' => 'Contact',
							'username' => 'User Name',
							'paymentType' =>  'Payment Type',
							'details' => 'Product Details',
							'date' => 'Items',
							'Type' =>  'Order Type',
							);
		   
	         $this->db->select('sale_code, status, grand_total, buyer, payment_type, product_details, sale_datetime');
			  $this->db->order_by('sale_id','desc');
			  $loop =   $this->db->get_where('sale',array('vendor'=>$para2))->result_array();
			  foreach($loop as $row){
				  
				
			
				  $res[] = array(
				            'orderId' =>'#'.$row['sale_code'],
							'status' => $row['status'],
							'price'  => $row['grand_total'],
							'contact' => $this->db->get_where('user', array('user_id' => $row['buyer']))->row()->phone,
							'username' => $this->db->get_where('user', array('user_id' => $row['buyer']))->row()->username,
							'userId' =>   $row['buyer'],
							'paymentType' =>  $row['payment_type'],
							'details' => $row['product_details'],
							'date' => $date,
							'Type' =>  'instance',
							
				  );
				  
			  }
	   
	   }
	   
	   
	   
	    return $res;
   }
   
   
   function myproduct($para1='', $para2='', $para3=''){
	   if($para1=='export'){
		   
	            $res[]  = array(
				            'product_id' => 'product_id',
							'category_id' => 'category_id',
							'subcategory_id'  => 'subcategory_id',
							'product_name' => 'product_name',
							);
			 $this->db->select('product_id,category,sub_category,title');
			 $loop =   $this->db->get_where('product',array('added_by'=>json_encode(array('type'=>'vendor','id'=>$para2))))->result_array();
			  foreach($loop as $row){
				  
				
			
				  $res[] = array(
				            'product_id' => $row['product_id'],
							'category_id' => $row['category'],
							'subcategory_id'  => $row['sub_category'],
							'product_name' => $row['title'],							
				  );
			  }
			  
			  return $res;
	   } 
   }
   
   
    function myre_product($para1='', $para2='', $para3=''){
	   if($para1=='export'){
		   
	            $res[]  = array(
				            'product_id' => 'product_id',
							'category_id' => 'category_id',
							'product_name' => 'product_name',
							);
			 $this->db->select('restaurantproduct_id,category,title');
			 $loop =   $this->db->get_where('restaurantproduct',array('added_by'=>json_encode(array('type'=>'vendor','id'=>$para2))))->result_array();
			  foreach($loop as $row){
				  
				
			
				  $res[] = array(
				            'product_id' => $row['restaurantproduct_id'],
							'category_id' => $row['category'],
							'product_name' => $row['title'],							
				  );
			  }
			  
			  return $res;
	   } 
   }
   
   
   
      function myre_variant($para1='', $para2='', $para3=''){
	   if($para1=='export'){
		   
	            $res[]  = array(
				            'variant_id' => 'variant_id',
				            'product_id' => 'product_id',
							'name'           => 'name',
							'qunatity' => 'qunatity',
							'unit'  => 'unit',
							'sale_price' => 'sale price',
							'strike_price' => 'strike price',
						
							);
			 $this->db->select('variant_id,	product_id,	name, quantity,unit,	sale_price,strike_price');
			 $loop =   $this->db->get_where('variant',array('vendor_id'=>$para2))->result_array();
			  foreach($loop as $row){
				  
				
			
				   $res[]  = array(
				            'variant_id' => $row['variant_id'],
							'product_id' => $row['product_id'],
							'name' => $row['name'],
							'quantity'  => $row['quantity'],
							'unit' => $row['unit'],
							'sale price' => $row['sale_price'],
							'strike price' => $row['strike_price'],
						
							);
			  }
			  
			  return  $res;
	   } 
   }
   
   
      function myaddons($para1='', $para2='', $para3=''){
	   if($para1=='export'){
		   
	            $res[]  = array(
				            'addon_id' => 'addon id',
				            'product_id' => 'product id',
							'name' => 'name',
							'sales_price'  => 'sales price',
							'type' => 'type',
							'food_type' => 'food type',
						
							);
			 $this->db->select('addon_id,	product_id,name,sales_price,	type,food_type');
			 $loop =   $this->db->get_where('addon',array('vendor_id'=>$para2))->result_array();
			  foreach($loop as $row){
				  
				
			
				   $res[]  = array(
				            'addon_id' => $row['addon_id'],
				            'product_id' => $row['product_id'],
							'name' => $row['name'],
							'sales_price'  =>$row['sales_price'],
							'type' => $row['type'],
							'food_type' => $row['food_type'],
						
							);
			  }
			  
			  return  $res;
	   } 
   }
   
   
   
   function myvariant($para1='', $para2='', $para3=''){
	   if($para1=='export'){
		   
	            $res[]  = array(
				            'variant_id' => 'variant_id',
				            'product_id' => 'product_id',
							'qunatity' => 'qunatity',
							'unit'  => 'unit',
							'sale_price' => 'sale price',
							'strike_price' => 'strike price',
						
							);
			 $this->db->select('variant_2_id,	product_id,quantity,unit,	sale_price,strike_price');
			 $loop =   $this->db->get_where('variant_2',array('vendor_id'=>$para2))->result_array();
			  foreach($loop as $row){
				  
				
			
				   $res[]  = array(
				            'variant_id' => $row['variant_2_id'],
							'product_id' => $row['product_id'],
							'quantity'  => $row['quantity'],
							'unit' => $row['unit'],
							'sale price' => $row['sale_price'],
							'strike price' => $row['strike_price'],
						
							);
			  }
			  
			  return  $res;
	   } 
   }
   
    function singleselectbox($table,$coloum1,$para1,$select){
	    $this->db->select($select);
	    $loop = $this->db->get_where($table,array($coloum1=>$para1))->result_array();
		foreach($loop as $row){
		   return $row[$select];
		}
   }
   
   
   
    function fetch_vendor($para1='', $para2='', $para3=''){
	   if($para1=='export'){
		   
	            $res[]  = array(
				            'vendor_id' => 'Vendor id',
				            'name' => 'Name',
							'display_name' => 'Display Name',
							'Email' => 'Email',
							'Phone' => 'Phone',
							'address1'  => 'Address1',
							'Description' => 'Description',
							'subtitle' => 'subtitle',
							'create_timestamp' => 'Create',
							'rating' => 'Rating',
							'focus_id' => 'Focus id'
						
							);
			 $this->db->select('vendor_id,	name,display_name,email,	phone,address1,description,subtitle,create_timestamp,focus_id');
			 $loop =   $this->db->get_where('vendor')->result_array();
			  foreach($loop as $row){
				  
				
			
				   $res[]  = array(
				            'vendor_id' => $row['vendor_id'],
				            'name' => $row['name'],
							'display_name' => $row['display_name'],
							'Email' => $row['email'],
							'Phone' => $row['phone'],
							'address1'  => $row['address1'],
							'Description' => $row['description'],
							'subtitle' => $row['subtitle'],
							'create_timestamp' => date('d/m/Y ', $row['create_timestamp']),
							'rating' => $row['rating'],
							'focus_id' => $this->singleselectbox('shop_focus','shop_focus_id',$row['focus_id'],'title')
						
							);
			  }
			  
			  return  $res;
	   } 
   }
   
   function productaddcsv($data){
	 	
        $this->db->insert('product', $data);
		return $this->db->insert_id();
  
   }
   
      
     public function updateproductcsv($data, $id){
		$this->db->where('product_id',$id);
        $this->db->update('product', $data);
	}
	
	
	public function variantaddcsv($data){
		$this->db->insert('variant_2', $data);
		return $this->db->insert_id();
	}
	
	public function variantreaddcsv($data){
		$this->db->insert('variant', $data);
		return $this->db->insert_id();
	}
	
	
	public function updatevariantcsv($data, $id){
		$this->db->where('variant_2_id',$id);
        $this->db->update('variant_2', $data);
	}
	
	
   
}
?>
    