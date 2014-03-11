<?php
    /**
	* This Function is used For getting theatre Listings
	* @author Muhammad Faisal Siddiq
	*/
	function theatrelisting($dat){	
	   $where="";
		if(isset($dat["theatre_id"]) && $dat["theatre_id"]){
		   $where="theatre_id=".$dat["theatre_id"]; 
		}
	   $data = select("snax_theatre","*",$where,1,"title");
		 if($data)
		   return $data;
		 else{
		   $res=array();
		   $res["status"]=0;
		   $res["message"]="Currently, Theatre listing is not available";	
		   return $res;
	    }
	}
	
	/**
	* This Function is used to getting theatre Movie Shifts Listings
	* @author Muhammad Faisal Siddiq
	*/
	function theatre_movie_shifts($dat){
		$where="";
		if(isset($dat["theatre_id"]) && $dat["theatre_id"]){
		   $where="theatre_id=".$dat["theatre_id"]; 
		}
	   $strSql="SELECT snax_theatre.title,snax_theatre.theatre_id,snax_theatre_movie_shift.shift_id as movie_id,snax_theatre_movie_shift.shift_time,snax_theatre_movie_shift.movie,snax_theatre_movie_shift.image,snax_theatre_movie_shift.description FROM `snax_theatre` inner join snax_theatre_movie_shift on snax_theatre.theatre_id=snax_theatre_movie_shift.fk_theatre_id";
	   if($where)
	      $strSql= $strSql." where ".$where;
	   $rsSql = mysql_query($strSql);
	   $data=array();
	   if(mysql_num_rows($rsSql)>0){
		 while($row=mysql_fetch_object($rsSql)){
			  $data[]=$row;
		 }
		  return $data;		
	   }
	   else{
		  $res=array();
		  $res["status"]=0;
		  $res["message"]="Currently, Theatre Movie listing is not available";	
		  return $res;	
	   }
	}
	
	/**
	* This Function is used for theatre Shops Listings
	* @author Muhammad Faisal Siddiq
	*/
	function theatre_shops_list($dat){	
	    $where="";
		if(isset($dat["theatre_id"]) && $dat["theatre_id"]){
		   $where="theatre_id=".$dat["theatre_id"]; 
		}
	   $strSql="SELECT snax_theatre.title as theater_title,snax_theatre.theatre_id,snax_theatre_shops.shop_id,snax_theatre_shops.title as shop_title,snax_theatre_shops.phone,snax_theatre_shops.fax,snax_theatre_shops.email,snax_theatre_shops.url FROM `snax_theatre` inner join snax_theatre_shops on snax_theatre.theatre_id=snax_theatre_shops.fk_theatre_id";
	   if($where)
	       $strSql= $strSql." where ".$where;
	   $rsSql = mysql_query($strSql);
	   $data=array();
	   if(mysql_num_rows($rsSql)>0){
		 while($row=mysql_fetch_object($rsSql)){
			  $data[]=$row;
		 }
		  return $data;		
	   }
	   else{
		  $res=array();
		  $res["status"]=0;
		  $res["message"]="Currently, Theatre Shops listing is not available";	
		  return $res;	
	   }
	}