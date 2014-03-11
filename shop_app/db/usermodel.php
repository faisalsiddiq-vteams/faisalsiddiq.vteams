<?php
    /**
	* This Function is used for User Registration
	* @author Muhammad Faisal Siddiq
	*/
	function user_registration($dat){	
		$data=array();
		if(isset($dat["fname"]) &&isset($dat["lname"]) &&isset($dat["email"])&&isset($dat["password"])){
			$data['first_name']=$dat["fname"];
			$data['last_name']=$dat["lname"];
			if($dat["email"])
			 $data['email']=escape_qoutes($dat["email"]);
			else
			 $data['email']="";
			$data['password']=md5($dat["password"]);
			$data['status']=0;
			$data['user_type']="customer";
			$user_exists=exists("snax_user","*","email='".$data["email"]."'");
			if($user_exists){
			        $res=array();
					$res["status"]=0;
					$res["message"]="User already exists";	
					return $res;
			}
			else{
			    $insert_data=insert("snax_user",$data);
				if($insert_data){
					$res=array();
					$res["status"]=1;
					$res["message"]="User Registered Successfully";	
					return $res;	 
				}
				else{
					$res=array();
					$res["status"]=0;
					$res["message"]="Something went wrong";	
					return $res;
				 }
		   } 
		 }
		 else{
		            $res=array();
					$res["status"]=0;
					$res["message"]="All fields are required:fname,lname,email,password,paypal email";	
					return $res;
		  }
      }	
	 
	 /**
	* This Function is used for user Authentication
	* @author Muhammad Faisal Siddiq
	*/ 	
	 function user_authentication($dat){	
	      if(isset($dat["email"]) && isset($dat["password"]) && $dat["email"] && $dat["password"]){
			$data['email']=escape_qoutes($dat["email"]);
			$data['password']=$dat["password"];			
			if($data['email'] && $data['password']){
				 if($data['email']=='guest' && $data['password']=='guest'){
				  $auth_token=generate_auth_token();
				  $data["auth_token"]=$auth_token;
				  $data['first_name']="guest";
				  $data['last_name']="guest";
				  $data['status']=1;
				  $data['user_type']="guest";
				  $insert_id=insert("snax_user",$data);
				   if($insert_id){
					  $res=array();
					  $res["status"]=1;
					  $res["user_id"]=$insert_id;
					  $res["auth_token"]=$auth_token;
					  $res["message"]="Guest Successfully login";	
					  return $res;
				   }
				   else{
				      $res=array();
					  $res["status"]=0;
					  $res["user_id"]=0;
					  $res["auth_token"]=0;
					  $res["message"]="Guest login Failed";	
					  return $res;
				   }
				 }
				  $data['password']=md5($dat["password"]);
				  $where="email='".$data['email']."' AND password='".$data['password']."' AND status=1";
				  $responce=select("snax_user","user_id",$where);
				  $timestamp = timestamp();
		       if($responce){
				  $auth_token=generate_auth_token();
				  $dd["auth_token"]=$auth_token;
				  $dd["last_login"]=$timestamp;
				  $last_login=update("snax_user",$dd,$where) ; // Update auth token, Last Login incase User Login Successfully
				  $res=array();
				  $res["status"]=1;
				  $res["user_id"]=$responce->user_id;
				  $res["auth_token"]=$auth_token;
				  /*$_SESSION["user_id"]=$responce->user_id;			  
				  //storing the pending order id in session when user login
				  $ord=select("snax_orders","order_id","user_id=".$responce->user_id." AND status=1");
				  if(isset($ord->order_id)){
				    $_SESSION["order_id"]=$ord->order_id;
				  }*/
				  $res["message"]="Successfully login";	
				  return $res;
				  }
		      else{
				  $res=array();
				  $res["status"]=0;
				  $res["user_id"]=0;
			      $res["message"]="login Failed: Invalid Credentials or Your account is not activated Yet.";	
				  return $res;
				}
		     }		  
		}
		else{
		         $res=array();
				 $res["status"]=0;
				 $res["user_id"]=0;
				 $res["message"]="Email or Password is null";	
				 return $res;
		}
	  }	
	  
    /**
	* This Function is used for sending forgotton password
	* @author Muhammad Faisal Siddiq
	*/
	 function forgot_password($dat){
		 	if(isset($dat['email']) && $dat["email"]){ 	
				 $data['email']=escape_qoutes($dat["email"]);
				 $where="email='".$data['email']."'";
				 $responce=select("snax_user","user_id",$where);
				 if($responce){
					 $password=rand_string(8);
					 $dd["password"]=md5($password);
					 $last_login=update("snax_user",$dd,$where) ;// Update Password
					 $message = "Hello!\n\nYour password has been changed successfully. Your new password is ". $password."."; 
					 sendemail($data['email'],"Password Changed",$message);
					 $res=array();
					 $res["status"]=1;
					 $res["message"]="Your password has been changed. Check Your email.";	
					 return $res;
				 }
				 else{
					 $res=array();
					 $res["status"]=0;
					 $res["message"]="Invalid Email";	
					 return $res;
				 }	 
		   }
		   else{
		         $res=array();
				 $res["status"]=0;
				 $res["message"]="Email cannot be Null";	
				 return $res;
		   }		  
	  }	
	  
	  /**
	* This Function is used for getting Current balance
	* @author Muhammad Faisal Siddiq
	*/
	  function get_current_balance($dat){		  
		  if (isset($dat["auth_token"]) && !empty($dat["auth_token"])) {
			   $strSql="SELECT snax_user.user_id ,SUM(amount) as total_balance FROM `snax_user` inner join snax_user_transactions 
					   on snax_user.user_id=snax_user_transactions.user_id where auth_token='".$dat["auth_token"]."' 
					   Group BY snax_user_transactions.user_id";
			   $rsSql = mysql_query($strSql);
			   $data=array();
			   if (mysql_num_rows($rsSql)>0) {
				$row=mysql_fetch_object($rsSql);
				$row->status=1;
				$row->message="Success";
				return $row;  		
			   }
			   else {
			    $res=array();
				$res["status"]=0;
				$res["message"]="No transaction done";	
				return $res;
			   }		  
		  }
		  else {
		        $res=array();
				$res["status"]=0;
				$res["message"]="Auth Token cannot be Null";	
				return $res;
		  }
	  }
	  
	  /**
	* This Function is used for user info Updates
	* @author Muhammad Faisal Siddiq
	*/
	  function user_info_update($dat){	
		if(isset($dat["auth_token"]) && !empty($dat["auth_token"]) && isset($dat["fname"]) && !empty($dat["fname"]) &&
		   isset($dat["lname"]) && !empty($dat["lname"]) && isset($dat["password"]) && !empty($dat["password"])){ 	
		    $data['first_name']=$dat["fname"];
			$data['last_name']=$dat["lname"];
			$data['password']=md5($dat["password"]);
			$where="auth_token='".$dat['auth_token']."'";
		    $update_res=update("snax_user",$data,$where) ; // Update auth token, Last Login incase User Login Successfully
			  if($update_res){	
				$res=array();
				$res["status"]=1;
				$res["message"]="User Info updated successfully";
				return $res;
			  }
			  else{
			    $res=array();
			 	$res["status"]=0;
				$res["message"]="User Info cannot be updated";	
				return $res;		  
			  }
		 }
		 else{
		        $res=array();
				$res["status"]=0;
				$res["message"]="All fields are required";	
				return $res;
		
		 }
	  }
	  
    /**
	* This Function is used to Updating Adding Funds
	* @author Muhammad Faisal Siddiq
	*/
	  function add_funds($dat){
		 if (isset($dat["auth_token"]) && !empty($dat["auth_token"]) && isset($dat["amount"]) && !empty($dat["amount"])
		     && isset($dat["paypal_transcation_id"]) && !empty($dat["paypal_transcation_id"]) ) {
			$where="auth_token='".$dat['auth_token']."'";
			$user=select("snax_user","user_id",$where);
			if($user){
			 $data['user_id']=$user->user_id;
			 $data["amount"]=$dat["amount"];
			 $data["ondate"]=timestamp();
			 $data["method_type"]="paypal";
			 $data["type"]="online"; 
			 $data["paypal_transcation_id"]=$dat["paypal_transcation_id"];
			 insert("snax_user_transactions",$data);
			 $strSql="SELECT SUM(amount) as total_balance FROM `snax_user_transactions` where user_id=".$user->user_id." 
					  Group BY user_id";
			 $rsSql = mysql_query($strSql);
			 $data=array();
			 if (mysql_num_rows($rsSql)>0) {
				$row=mysql_fetch_object($rsSql);
				$row->status=1;
				$row->message="Success";
				return $row;  		
			   }
			}
			else{
			    $res=array();
			 	$res["status"]=0;
				$res["message"]="No user found";	
				return $res;
			}
		 }
		 else{
		        $res=array();
				$res["status"]=0;
				$res["message"]="All fields are required";	
				return $res;
		 
		 }
	  }
	  
	  /**
	* This Function is used to adding billing Address
	* @author Muhammad Faisal Siddiq
	*/
	  function add_billing_address($dat){
		if (isset($dat["auth_token"]) && !empty($dat["auth_token"]) && isset($dat["street"]) && !empty($dat["street"])
		     && isset($dat["suit"]) && !empty($dat["suit"]) && isset($dat["state"]) && !empty($dat["state"]) && 
			 isset($dat["city"]) && !empty($dat["city"])  && isset($dat["zip"]) && !empty($dat["zip"])) {
			$where="auth_token='".$dat['auth_token']."'";
			$user=select("snax_user","user_id",$where);
				if($user){			 
				 $data['user_id']=$user->user_id;
				 $data["street"]=$dat["street"];
				 $data["suit"]=$dat["suit"];
				 $data["state"]=$dat["state"];
				 $data["city"]=$dat["city"];
				 $data["zip"]=$dat["zip"];
		         $address_exists=exists("snax_user_billing_address","user_id","user_id=".$user->user_id);
				 if($address_exists){
					  $data["updatedate"]=timestamp();
					  update("snax_user_billing_address",$data,"user_id=".$user->user_id);
					  $res=array();
					  $res["status"]=1;
					  $res["message"]="Billing address updated successfully";	
					  return $res;
				 }
				 else{
					  $data["ondate"]=timestamp();
					  insert("snax_user_billing_address",$data);
					  $res=array();
					  $res["status"]=1;
					  $res["message"]="Billing address added successfully";	
					  return $res;	
				 }
				}
				else{
					$res=array();
					$res["status"]=0;
					$res["message"]="No user found";	
					return $res;
				}
			 }
			 else{
			    $res=array();
				$res["status"]=0;
				$res["message"]="All fields are required";	
				return $res;
			 }
	  }
	  
	  /**
	* This Function is used to Updating Reward Card Info
	* @author Muhammad Faisal Siddiq
	*/
	  function reward_card_info_update($dat){
		 if (isset($dat["auth_token"]) && !empty($dat["auth_token"]) && isset($dat["reward_card_id"]) && !empty($dat["reward_card_id"])
		     && isset($dat["account_number"]) && !empty($dat["account_number"])&& isset($dat["title"]) && !empty($dat["title"])) {
		    $data['reward_card_id']=$dat["reward_card_id"];
			$data['reward_account_no']=$dat["account_number"];
			$data['reward_card_title']=$dat["title"];
			$data['status']=1;
			$data["ondate"]=timestamp();
			$where="auth_token='".$dat['auth_token']."'";
			$user=select("snax_user","user_id",$where);
			if($user){
			   $res=array();	
			   $res["status"]=1;	
			   $data['user_id']=$user->user_id;
			   $data["type"]="online";
			   $data["method_type"]="reward_card";
			   $user_reward_trans_exists=exists("snax_user_transactions","user_id","user_id=".$user->user_id." AND reward_card_id='".
			   $data['reward_card_id']."'");
			   if($user_reward_trans_exists){
				 update("snax_user_transactions",$data,"user_id=".$user->user_id." AND reward_card_id='".$data['reward_card_id']."'");	
				 $res["message"]="reward card info update successfully";		   
			   }
			   else{
			     insert("snax_user_transactions",$data);
				 $res["message"]="reward card info added successfully";	
			   }			    			  		   
			   return $res; 
			}
			else{
			    $res=array();
			    $res["status"]=0;
			    $res["message"]="No user found";	
			    return $res; 
			}
		 }
		 else{
		        $res=array();
				$res["status"]=0;
				$res["message"]="All fields are required";	
				return $res;
		 
		 }
	  }
	  
	  /**
	* This Function is used to getting reward Card
	* @author Muhammad Faisal Siddiq
	*/
	   function get_reward_card($dat){
		 if (isset($dat["auth_token"]) && !empty($dat["auth_token"])) {
			$where="auth_token='".$dat['auth_token']."'";
			$user=select("snax_user","user_id",$where);
			if($user){
			   $res=array();	
			   $reward_cards_info=select("snax_user_transactions","reward_card_id,reward_account_no,reward_card_title,amount,status",
			   "user_id=".$user->user_id." AND method_type='reward_card'",1);
			   if($reward_cards_info){
				 return $reward_cards_info;				 
			   }
			   else{
			     $res=array();
			     $res["status"]=0;
			     $res["message"]="No Reward Card Exists for this user";	
			     return $res; 
			   }			    			  		   			   
			}
			else{
			    $res=array();
			    $res["status"]=0;
			    $res["message"]="No user found";	
			    return $res; 
			}
		 }
		 else{
		        $res=array();
				$res["status"]=0;
				$res["message"]="All fields are required";	
				return $res;
		 
		 }
	  }
	  
	  /**
	* This Function is used to Paying Via Reward Card
	* @author Muhammad Faisal Siddiq
	*/
	  function pay_via_reward_card($dat){
		  if (isset($dat["auth_token"]) && !empty($dat["auth_token"]) && isset($dat["reward_card_id"]) && !empty($dat["reward_card_id"])
		      && isset($dat["amount"]) && !empty($dat["amount"])) {
			  $where="auth_token='".$dat['auth_token']."'";
			  $user=select("snax_user","user_id",$where);
			  if($user){
				   $reward_card=exists("snax_user_transactions","amount","user_id=".$user->user_id." AND reward_card_id='".
				   $dat["reward_card_id"]."'");
				   if($reward_card){
				   $data["amount"]=$dat["amount"]+$reward_card->amount;
				   update("snax_user_transactions",$data,"user_id=".$user->user_id." AND reward_card_id='".
				   $dat["reward_card_id"]."'");	
				     $res=array();
					 $res["status"]=1;
					 $res["message"]="Reward Card Amount added successfully";	
					 return $res; 
				   }
				   else{
				     $res=array();
					 $res["status"]=0;
					 $res["message"]="No Reward Card found";	
					 return $res; 
				   }
				  }
			      else{
					 $res=array();
					 $res["status"]=0;
					 $res["message"]="No user found";	
					 return $res; 
			      }
		      }
		      else{
				$res=array();
				$res["status"]=0;
				$res["message"]="All fields are required";	
				return $res;
		  }
	  }