<?php
    /**
	* This Function is used to get categories Listings
	* @author Muhammad Faisal Siddiq
	*/
	function category_listing($dat){	
	   $where="status=1";
		if(isset($dat["cat_id"]) && $dat["cat_id"]){
		   $where.=" AND cat_id=".$dat["cat_id"]; 
		}
	   $data = select("snax_products_categories","*",$where,1);
		 if($data)
		   return $data;
		 else{
		   $res=array();
		   $res["status"]=0;
		   $res["message"]="Currently, Category listing is not available";	
		   return $res;
	    }
	}
	
	/**
	* This Function is used to get Sub categories Listings
	* @author Muhammad Faisal Siddiq
	*/
	function sub_category_listing($dat){
		 $where="snax_products_categories.status=1 AND snax_products_sub_categories.status=1";
		if(isset($dat["cat_id"]) && $dat["cat_id"]){
		   $where.=" AND snax_products_categories.cat_id=".$dat["cat_id"]; 
		}
	   $strSql="SELECT snax_products_categories.title as category_title,snax_products_sub_categories.* FROM `snax_products_categories` inner join snax_products_sub_categories on snax_products_categories.cat_id=snax_products_sub_categories.cat_id";
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
		  $res["message"]="Currently, Sub Category listing is not available";	
		  return $res;	
	   }
	}
	
	/**
	* This Function is used to get product extras Listings
	* @author Muhammad Faisal Siddiq
	*/
	function product_extras_listing($dat){	
	    $where="status=1";
		if(isset($dat["cat_id"]) && $dat["cat_id"]){
		   $where.=" AND snax_products_categories.cat_id=".$dat["cat_id"]; 
		}
	   $strSql="SELECT snax_products_categories.title as category_title,snax_products_extras.* FROM `snax_products_categories` inner join snax_products_extras on snax_products_categories.cat_id=snax_products_extras.cat_id";
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
		  $res["message"]="Currently, Product Extras listing is not available";	
		  return $res;	
	   }
	}