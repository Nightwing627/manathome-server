<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');



class Home_model extends CI_Model
{
   
    
    function __construct()
    {
        parent::__construct();
    }
	
	
    
    
   
   function singleselectbox($table,$coloum1,$para1,$select){
	    $this->db->select($select);
	    $loop = $this->db->get_where($table,array($coloum1=>$para1))->result_array();
		foreach($loop as $row){
		   return $row[$select];
		}
   }
   
   function twoselectbox($table,$coloum1,$para1,$coloum2,$para2,$select){
	   
	    $loop = $this->db->get_where($table,array($coloum1=>$para1,$coloum2=>$para2))->result_array();
		foreach($loop as $row){
		   return $row[$select];
		}
   }
   
     function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
      return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
  } else {
      return $miles;
  }
}
   
   
   function avg_rating($providerid){
	   $i=0;
	   $loop = $this->db->get_where('comments',array('providerId'=>$providerid))->result_array();
		foreach($loop as $row){
		   $totalrating += $row['rate'];
		   $i += 1;
		}
		
		 $rating = $totalrating/$i;
		
		return $rating;
   }
     
	 
  
   
    
}