<?php       
	  function ConnectDB($strHost, $strDatabase, $strUser, $strPass){			
			$strLink=mysql_connect($strHost, $strUser, $strPass);
			if(!$strLink)
				return "Connection could not be made";
			$strDB=mysql_select_db($strDatabase,$strLink);
			if(!$strDB)
				return "Database not found.";
			return true;
	  }	
		
	  $db_connect_res=ConnectDB('localhost', 'app_db', 'root', '123');
	  if($db_connect_res!=1){
		  die($db_connect_res);
	   }
	   //Defining Site Url Globally
	   define("SITEURL","http://localhost/shop_app/");