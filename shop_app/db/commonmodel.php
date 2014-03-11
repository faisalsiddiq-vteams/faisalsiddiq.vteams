<?php
  
    /**
	* This Generic Function is used For data insertion
	* @author Muhammad Faisal Siddiq
	*/
    function insert($table,$data) {		
		$strSql	='INSERT INTO `'.$table.'` SET ';
		$arrdata = array();
		foreach($data as $col=>$value){
		 $arrdata[] = $col . " = '". $value."'";	
		}
		$strSql .= implode(', ', $arrdata);
		$intCheck=mysql_query($strSql);		
		if(mysql_affected_rows()>0) 
			return mysql_insert_id();
		else
			return FALSE;
	}
	
	/**
	* This Generic Function is used For Checking data Existence
	* @author Muhammad Faisal Siddiq
	*/
	function exists($table,$selectval,$where){		
		$strSql = "SELECT ".$selectval." FROM `".$table."` where ".$where;		
		$rsSql = mysql_query($strSql);
		if($rsSql && mysql_num_rows($rsSql)>0){	
		   $row = mysql_fetch_object($rsSql);
			return $row;
		}
		else
			return FALSE;			
	}
	
	/**
	* This Generic Function is used For data Selection
	* @author Muhammad Faisal Siddiq
	*/
	function select($table,$coloumn,$where,$fetchall="",$orderby=""){		
		$strSql = "SELECT ".$coloumn." FROM `".$table."`";
		if($where)
		 $strSql = $strSql ." where ".$where;	
		if($orderby)
		 $strSql = $strSql ." order by ".$orderby;
		$rsSql = mysql_query($strSql);
		if($fetchall){
			$data=array();
			if(mysql_num_rows($rsSql)>0){
			    while($row=mysql_fetch_object($rsSql)){
				 $data[]=$row;
				}
				 return $data;		
		    }
			else{
				 return FALSE;	
			}
	    }
		else{
			$row = mysql_fetch_object($rsSql);
			if(mysql_num_rows($rsSql)>0)	
				return $row;
			else
				return FALSE;			
	    }	
	}
	
	/**
	* This Generic Function is used For Updating data
	* @author Muhammad Faisal Siddiq
	*/
	function update($table,$data,$where) 
	{
		$strSql="UPDATE `".$table."` SET ";
		$arrdata = array();
		foreach($data as $col=>$value){
		 $arrdata[] = $col . " = '". $value."'";	
		}
		$strSql .= implode(', ', $arrdata);
		$strSql .= ' WHERE '.$where;
		$intCheck=mysql_query($strSql);
		if($intCheck) 
			return $intCheck;
		else
			return FALSE;
	}	
	
	/**
	* This Generic Function is used For Records Deletion
	* @author Muhammad Faisal Siddiq
	*/
	function delete($table,$where)
	{
		$strSql="DELETE FROM `".$table."` WHERE ".$where;
		$iCheck=mysql_query($strSql);
		if(mysql_affected_rows()>0)
		 return TRUE;
		else 
		 return FALSE;
	}