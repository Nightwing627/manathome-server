<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function settings($where, $id, $value)
    {
        $data['value'] = $value;
        $this
            ->db
            ->where($where, $id);
        $this
            ->db
            ->update('settings', $data);
    }

    function contactaddress($id, $select)
    {
        $loop = $this
            ->db
            ->get_where('settings', array(
            'settings_id' => $id
        ))->result_array();
        foreach ($loop as $row)
        {
            return $row[$select];
        }
    }

    function getAddonCategory($vendorId)
    {
        $loop = $this
            ->db
            ->get_where('category', array(
                'data_vendors' => $vendorId,
                'is_addon'  => 1
            ))->result_array();

        $category = [];
        foreach ($loop as $row)
        {
            array_push($category, $row['category_name']);
        }

        return $category;
    }

    function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => & $v)
        {
            if ($diff->$k)
            {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            }
            else
            {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    function count_wc($table, $coloum1, $para1)
    {
        $this
            ->db
            ->select($table . 'id');
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1
        ))->result_array();
        return count($loop);
    }
    
  
    
    function vendorWalletAmount($vendor_id='', $para2=''){
           $this->db->select('balance');
           $loop = $this->db->get_where('vendor_wallet', array('vendor_id' =>$vendor_id)) ->result_array();
          if(count($loop)>0){
             foreach($loop as $row){
                return $row['balance'];
             }
          }else{
               return 'no';
          }
    }
    
    
    function walletAmount($userId){
           $this->db->select('balance');
           $loop = $this ->db->get_where('wallet', array('user_id' => $userId)) ->result_array();
          if(count($loop)>0){
             foreach($loop as $row){
                return $row['balance'];
             }
          }else{
               return '0';
          }
        
        
    }
    
    
    function smartuseremail($logKey, $type){
        if($type=='GMail'){
          $loop = $this ->db->get_where('user', array('email' => $logKey, 'status' => 'success')) ->result_array();
        }else if($type=='Phone'){
              $loop = $this->db->get_where('user', array('phone'=> $logKey, 'status' => 'success')) ->result_array();      
        }
        
        foreach($loop as $row){
         $data = array(
                    'id' => $row['user_id'],
                    'name' => $row['username'],
                    'email' => $row['email'],
                    'api_token' =>  $row['token'],
                    'device_token' => '',
                    'phone' => $row['phone'],
                    'status' => $row['status'],
                    'latitude' => floatval($row['latitude']) ,
                    'longitude' => floatval($row['longitude']) ,
                    'auth' => true,
                    'loginVia' => $type,
                    'address' => json_decode($row['address'], true) ,
                    'selected_address' => $row['selected_address'],
                );
        }
        
        return $data;
    }

    function counter($table)
    {
        $this
            ->db
            ->select($table . 'id');
        $loop = $this
            ->db
            ->get_where($table)->result_array();
        return count($loop);
    }

    function check_spelling($input)
    {
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'libraries/');
        require "phpspellcheck/include.php";

        if ((trim($input)) == "")
        {
            return "";
        }

        $spellcheckObject = new PHPSpellCheck();
        $spellcheckObject->LicenceKey = "TRIAL";
        $spellcheckObject->DictionaryPath = ("application/libraries/phpspellcheck/dictionaries/");
        $spellcheckObject->LoadDictionary("English (International)"); //OPTIONAL//
        $spellcheckObject->LoadCustomDictionary("custom.txt");
        return $suggestionText = $spellcheckObject->didYouMean($input);

    }

    function getcommission_amount($grandtotal,  $vendor_id)
    {
        
     $plan_id = $this->db->get_where('vendor',array('vendor_id' => $vendor_id))->row()->plan;
      

        $loop = $this->db->get_where('vendor_membership', array('vendor_membership_id' =>  $plan_id  ))->result_array();
       
        foreach ($loop as $row)
        {
          
            $commission_amount = ($grandtotal * $row['commision'])/100;
        }
        return  round($commission_amount, 2);
    }

    function getcommission_deliveryamount($driverfees)
    {

        $loop = $this
            ->db
            ->get_where('general_settings', array(
            'general_settings_id' => 95
        ))
            ->result_array();
        foreach ($loop as $row)
        {
            $commission_amount = $driverfees * $row['value'] / 100;
        }
        return round($commission_amount, 2);
    }

    function count_wcopt($table, $coloum1, $para1)
    {
        $this ->db ->select($table . '_id');
        $loop = $this ->db  ->get_where($table, array( $coloum1 => $para1 ))->result_array();
        return count($loop);
    }

    function count_wcoptjs($table, $coloum1, $para1, $select)
    {
        $this
            ->db
            ->select($table . 'id');
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1
        ))->result_array();
        foreach ($loop as $row)
        {
            return $lenght = count(json_decode($row[$select], true));
        }

    }

    function count_2wcopt($table, $coloum1, $para1, $coloum2, $para2)
    {
        $this
            ->db
            ->select($table . '_id');
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2
        ))->result_array();
        return count($loop);
    }

    function count_3wcopt($table, $coloum1, $para1, $coloum2, $para2, $coloum3, $para3)
    {
        $this
            ->db
            ->select($table . '_id');
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2,
            $coloum3 => $para3
        ))->result_array();
        return count($loop);
    }

    function count_4wcopt($table, $coloum1, $para1, $coloum2, $para2, $coloum3, $para3, $coloum4, $para4)
    {
        $this
            ->db
            ->select($table . '_id');
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2,
            $coloum3 => $para3,
            $coloum4 => $para4
        ))->result_array();
        return count($loop);
    }

    function sumof_sammaryReport($table, $coloum1, $para1, $select)
    {

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            'method !=' => 'cancelled'
        ))->result_array();

        foreach ($loop as $row)
        {
            $sum += $row[$select];
        }

        return $sum;
    }

    function sumof_sammaryReportdatewise($table, $coloum1, $para1, $coloum2, $para2, $coloum3, $para3, $select)
    {

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2,
            $coloum3 => $para3,
            'method !=' => 'cancelled'
        ))->result_array();

        foreach ($loop as $row)
        {
            $sum += $row[$select];
        }

        return $sum;
    }

    function sumof_sammaryReportadmin($table, $select)
    {

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            'method !=' => 'cancelled'
        ))->result_array();

        foreach ($loop as $row)
        {
            $sum += $row[$select];
        }

        return round($sum, 2);
    }

    function sumof_w2c($table, $coloum1, $para1, $coloum2, $para2, $select)
    {

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2
        ))->result_array();

        foreach ($loop as $row)
        {
            $sum += $row[$select];
        }

        return round($sum, 2);
    }

    function sumof_wc($table, $coloum1, $para1, $select)
    {

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1
        ))->result_array();

        foreach ($loop as $row)
        {
            $sum += $row[$select];
        }

        return $sum;
    }

    function sumof_sammaryReportwithc($table, $coloum1, $para1, $coloum2, $para2, $select)
    {

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2,
            'method !=' => 'cancelled'
        ))->result_array();

        foreach ($loop as $row)
        {
            $sum += $row[$select];
        }

        return $sum;
    }

    function sumof_sammaryReporthwithc($table, $coloum1, $para1, $coloum2, $para2, $coloum3, $para3, $select)
    {

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2,
            $coloum3 => $para3,
            'method !=' => 'cancelled'
        ))->result_array();

        foreach ($loop as $row)
        {
            $sum += $row['settlement_value'];
        }

        return $sum;
    }

    function count_monthreport($mon, $table, $coloum1, $para1, $type, $select)
    {
        $year = date('Y');
        if ($mon == '1')
        {
            $start = strtotime('01-1-' . $year);
            $end = strtotime('31-1-' . $year);
        }
        else if ($mon == '2')
        {
            $start = strtotime('01-2-' . $year);
            $end = strtotime('29-2-' . $year);
        }
        else if ($mon == '3')
        {
            $start = strtotime('01-3-' . $year);
            $end = strtotime('31-3-' . $year);
        }
        else if ($mon == '4')
        {
            $start = strtotime('01-4-' . $year);
            $end = strtotime('30-4-' . $year);
        }
        else if ($mon == '5')
        {
            $start = strtotime('01-5-' . $year);
            $end = strtotime('31-5-' . $year);
        }
        else if ($mon == '6')
        {
            $start = strtotime('01-6-' . $year);
            $end = strtotime('30-6-' . $year);
        }
        else if ($mon == '7')
        {
            $start = strtotime('01-7-' . $year);
            $end = strtotime('31-7-' . $year);
        }
        else if ($mon == '8')
        {
            $start = strtotime('01-8-' . $year);
            $end = strtotime('31-8-' . $year);
        }
        else if ($mon == '9')
        {
            $start = strtotime('01-9-' . $year);
            $end = strtotime('30-9-' . $year);
        }
        else if ($mon == '10')
        {

            $start = strtotime('01-10-' . $year);
            $end = strtotime('31-10-' . $year);
        }
        else if ($mon == '11')
        {
            $start = strtotime('01-11-' . $year);
            $end = strtotime('30-11-' . $year);
        }
        else if ($mon == '12')
        {
            $start = strtotime('01-12-' . $year);
            $end = strtotime('31-12-' . $year);
        }
        if ($type == 'count')
        {
            $this
                ->db
                ->select($table . '_id');
            $loop = $this
                ->db
                ->get_where($table, array(
                'order_date >=' => $start,
                'order_date <=' => $end,
                $coloum1 => $para1,
                'method !=' => 'cancelled'
            ))->result_array();
            return count($loop);
        }
        else if ($type == 'instance')
        {

            $this
                ->db
                ->select($table . '_id');
            $loop = $this
                ->db
                ->get_where($table, array(
                'sale_datetime >=' => $start,
                'sale_datetime <=' => $end,
                $coloum1 => $para1,
                'status !=' => 'cancelled',
                'order_type' => 1
            ))->result_array();
            return count($loop);
        }
        else if ($type == 'schedule')
        {
            $this
                ->db
                ->select($table . '_id');
            $loop = $this
                ->db
                ->get_where($table, array(
                'sale_datetime >=' => $start,
                'sale_datetime <=' => $end,
                $coloum1 => $para1,
                'status !=' => 'cancelled',
                'order_type' => 2
            ))->result_array();
            return count($loop);
        }
        else
        {
            $sum = 0;
            $this
                ->db
                ->select($select);
            $loop = $this
                ->db
                ->get_where($table, array(
                'order_date >=' => $start,
                'order_date <=' => $end,
                $coloum1 => $para1,
                'method !=' => 'cancelled'
            ))->result_array();
            foreach ($loop as $row)
            {
                $sum += $row[$select];
            }
            return $sum;
        }
    }

    function count_monthreportadmin($mon, $table, $type, $select)
    {
        $year = date('Y');
        if ($mon == '1')
        {
            $start = strtotime('01-1-' . $year);
            $end = strtotime('31-1-' . $year);
        }
        else if ($mon == '2')
        {
            $start = strtotime('01-2-' . $year);
            $end = strtotime('29-2-' . $year);
        }
        else if ($mon == '3')
        {
            $start = strtotime('01-3-' . $year);
            $end = strtotime('31-3-' . $year);
        }
        else if ($mon == '4')
        {
            $start = strtotime('01-4-' . $year);
            $end = strtotime('30-4-' . $year);
        }
        else if ($mon == '5')
        {
            $start = strtotime('01-5-' . $year);
            $end = strtotime('31-5-' . $year);
        }
        else if ($mon == '6')
        {
            $start = strtotime('01-6-' . $year);
            $end = strtotime('30-6-' . $year);
        }
        else if ($mon == '7')
        {
            $start = strtotime('01-7-' . $year);
            $end = strtotime('31-7-' . $year);
        }
        else if ($mon == '8')
        {
            $start = strtotime('01-8-' . $year);
            $end = strtotime('31-8-' . $year);
        }
        else if ($mon == '9')
        {
            $start = strtotime('01-9-' . $year);
            $end = strtotime('30-9-' . $year);
        }
        else if ($mon == '10')
        {

            $start = strtotime('01-10-' . $year);
            $end = strtotime('31-10-' . $year);
        }
        else if ($mon == '11')
        {
            $start = strtotime('01-11-' . $year);
            $end = strtotime('30-11-' . $year);
        }
        else if ($mon == '12')
        {
            $start = strtotime('01-12-' . $year);
            $end = strtotime('31-12-' . $year);
        }
        if ($type == 'count')
        {
            $this
                ->db
                ->select($table . '_id');
            $loop = $this
                ->db
                ->get_where($table, array(
                'order_date >=' => $start,
                'order_date <=' => $end,
                'method !=' => 'cancelled'
            ))->result_array();
            return count($loop);
        }
        else if ($type == 'instance')
        {

            $this
                ->db
                ->select($table . '_id');
            $loop = $this
                ->db
                ->get_where($table, array(
                'sale_datetime >=' => $start,
                'sale_datetime <=' => $end,
                'status !=' => 'cancelled',
                'order_type' => 1
            ))->result_array();
            return count($loop);
        }
        else if ($type == 'schedule')
        {
            $this
                ->db
                ->select($table . '_id');
            $loop = $this
                ->db
                ->get_where($table, array(
                'sale_datetime >=' => $start,
                'sale_datetime <=' => $end,
                'status !=' => 'cancelled',
                'order_type' => 2
            ))->result_array();
            return count($loop);
        }
        else
        {
            $sum = 0;
            $this
                ->db
                ->select($select);
            $loop = $this
                ->db
                ->get_where($table, array(
                'order_date >=' => $start,
                'order_date <=' => $end,
                'method !=' => 'cancelled'
            ))->result_array();
            foreach ($loop as $row)
            {
                $sum += $row[$select];
            }
            return $sum;
        }
    }

    function count_totalsalesreport($mon, $table, $select)
    {
        $year = date('Y');
        if ($mon == '1')
        {
            $start = strtotime('01-1-' . $year);
            $end = strtotime('31-1-' . $year);
        }
        else if ($mon == '2')
        {
            $start = strtotime('01-2-' . $year);
            $end = strtotime('29-2-' . $year);
        }
        else if ($mon == '3')
        {
            $start = strtotime('01-3-' . $year);
            $end = strtotime('31-3-' . $year);
        }
        else if ($mon == '4')
        {
            $start = strtotime('01-4-' . $year);
            $end = strtotime('30-4-' . $year);
        }
        else if ($mon == '5')
        {
            $start = strtotime('01-5-' . $year);
            $end = strtotime('31-5-' . $year);
        }
        else if ($mon == '6')
        {
            $start = strtotime('01-6-' . $year);
            $end = strtotime('30-6-' . $year);
        }
        else if ($mon == '7')
        {
            $start = strtotime('01-7-' . $year);
            $end = strtotime('31-7-' . $year);
        }
        else if ($mon == '8')
        {
            $start = strtotime('01-8-' . $year);
            $end = strtotime('31-8-' . $year);
        }
        else if ($mon == '9')
        {
            $start = strtotime('01-9-' . $year);
            $end = strtotime('30-9-' . $year);
        }
        else if ($mon == '10')
        {

            $start = strtotime('01-10-' . $year);
            $end = strtotime('31-10-' . $year);
        }
        else if ($mon == '11')
        {
            $start = strtotime('01-11-' . $year);
            $end = strtotime('30-11-' . $year);
        }
        else if ($mon == '12')
        {
            $start = strtotime('01-12-' . $year);
            $end = strtotime('31-12-' . $year);
        }

        $sum = 0;
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            'order_date >=' => $start,
            'order_date <=' => $end,
            'method !=' => 'cancelled'
        ))->result_array();
        foreach ($loop as $row)
        {
            $sum += $row[$select];
        }
        return $sum;

    }

    function twowloop($table, $coloum1, $para1, $coloum2, $para2)
    {
        $this
            ->db
            ->select($select);
        return $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2
        ))->result_array();
    }

    function returnSingleValue($table, $coloum1, $para1, $select) {

        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($coloum1, $para1);
        return $this->db->get()->row()->$select;
    }

    function singleselectbox($table, $coloum1, $para1, $select)
    {
        $this
            ->db
            ->select($select);
        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1
        ))->result_array();
        foreach ($loop as $row)
        {
            return $row[$select];
        }
    }

    function twoselectbox($table, $coloum1, $para1, $coloum2, $para2, $select)
    {

        $loop = $this
            ->db
            ->get_where($table, array(
            $coloum1 => $para1,
            $coloum2 => $para2
        ))->result_array();
        foreach ($loop as $row)
        {
            return $row[$select];
        }
    }

    function calculateTime($distance)
    {
        $avg_speed = (1 / 48);
        $hours = $avg_speed * $distance;
        $mins = $hours * 60;
        return round($mins) + 10;
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K")
        {
            return ($miles * 1.609344);
        }
        else if ($unit == "N")
        {
            return ($miles * 0.8684);
        }
        else
        {
            return $miles;
        }
    }

    function nearestDriver($para1 = '', $para2 = '')
    {

        $cover_radius = $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '97', 'value');

        $sql = $this
            ->db
            ->query("SELECT  latitude, longitude, name, driver_id, phone, SQRT(
    POW(69.1 * (latitude -  $para1 ), 2) +
    POW(69.1 * ($para2 - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM driver WHERE livestatus ='true' AND block = 'false' HAVING distance < $cover_radius ORDER BY distance LIMIT 1")->result_array();

        return $sql;
    }

    function nearestDriverwithVendor($para1 = '', $para2 = '', $para3 = '')
    {
 $cover_radius = $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '97', 'value');
        $sql = $this
            ->db
            ->query("SELECT  latitude, longitude, name, driver_id, phone, SQRT(
    POW(69.1 * (latitude -  $para1 ), 2) +
    POW(69.1 * ($para2 - longitude) * COS(latitude / 57.3), 2)) AS distance
FROM driver WHERE livestatus ='true' AND block = 'false' AND store_id = $para3  HAVING distance < $cover_radius ORDER BY distance  LIMIT 1")->result_array();
        return $sql;
    }

    function image_upload($filedata = '', $filename = '', $id = '')
    {

        if ($_FILES && $_FILES[$filedata]['name'])
        {
            $exe = pathinfo($_FILES[$filedata]["name"], PATHINFO_EXTENSION);
            $new_name = $filename . '_' . $id;
            $config['file_name'] = $new_name;
            $config['upload_path'] = './uploads/' . $filename . '_image/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 10024;
            $config['quality'] = '90%';
            $config['overwrite'] = true;
            $this
                ->load
                ->library('upload', $config);
            if (!$this
                ->upload
                ->do_upload($filedata))
            {
                $isUploadError = true;
                $response = array(
                    'status' => 'error',
                    'message' => $this
                        ->upload
                        ->display_errors()
                );
            }
            else
            {
                $uploadData = $this
                    ->upload
                    ->data();
                $fullPath = base_url('uploads/' . $uploadData['file_name']);
            }
        }

    }

    function tokenfailed()
    {
        $responce = array(
            'success' => false,
            'data' => 'invalid token',
        );
        return $responce;
    }

    function get_lat_long($address)
    {

        $address = str_replace(" ", "+", $address);

        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=AIzaSyDjJk2l2-0PxawqpgQ2BYVDNRbzqCvHMrw");

        $json1 = json_decode($json);

        $latitude = $json1->results[0]
            ->geometry
            ->location->lat;
        $longitude = $json1->results[0]
            ->geometry
            ->location->lng;
        return $latitude . '_' . $longitude;
    }

    function get_currency()
    {
        $currency_id = $this
            ->crud_model
            ->get_type_name_by_id('general_settings', '82', 'value');
        $currency_symbol = $this
            ->db
            ->get_where('currency_method', array(
            'currency_method_id' => $currency_id
        ))->row()->currency_symbol;
        return $currency_symbol;
    }

    /////////GET NAME BY TABLE NAME AND ID/////////////
    function get_type_name_by_id($type, $type_id = '', $field = 'name')
    {
        if ($type_id != '')
        {
            $l = $this
                ->db
                ->get_where($type, array(
                $type . '_id' => $type_id
            ));
            $n = $l->num_rows();
            if ($n > 0)
            {
                return $l->row()->$field;
            }
        }
    }

    function date_timestamp($date, $type)
    {
        $date = explode('-', $date);
        $d = $date[2];
        $m = $date[1];
        $y = $date[0];
        if ($type == 'start')
        {
            return mktime(0, 0, 0, $m, $d, $y);
        }
        if ($type == 'end')
        {
            return mktime(0, 0, 0, $m, $d + 1, $y);
        }
    }
}
?>
