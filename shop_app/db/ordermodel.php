<?php
	
    /**
	* This Function is used for adding products to order
	* @author Muhammad Faisal Siddiq
	*/
	function add_order_product($dat){
		if(isset($_SESSION["user_id"])){
           $user_id=$_SESSION["user_id"];
			if(isset($dat['subcat_id'])&&isset($dat['quantity'])&&isset($dat['shop_id'])){
				$ord_id="";
				$update_old_order=false;
				if(isset($_SESSION["order_id"])){
				 $ord_id=$_SESSION["order_id"];
				 $order_id_awaiting_payment=exists("snax_orders","order_id,user_id","order_id=".$ord_id." AND user_id=".$user_id." AND status=1");
				 if(isset($order_id_awaiting_payment->order_id)){
				 $update_old_order=true;
				  }
				}
				if($update_old_order){ //incase session order_id is maintained
				 
				  $or_dat=array();
				  $or_dat["update_on"]=timestamp();
				  $or_dat["update_by"]=$user_id;
			      $res=update("snax_orders",$or_dat,"order_id=".$ord_id); 
				  
				  $prod_data=array();
				  $prod_data["ondate"]=timestamp();
				  $whr="order_id=".$ord_id." AND subcat_id=".$dat["subcat_id"];
				  $subcat_exists=exists("snax_order_products","subcat_id,quantity",$whr);
				  if($subcat_exists){
					  $prod_data["quantity"]=$subcat_exists->quantity+$dat["quantity"];
					  update("snax_order_products",$prod_data,$whr);
				  }
				  else{
					  $prod_data["subcat_id"]=$dat["subcat_id"];
					  $prod_data["quantity"]=$dat["quantity"];
					  $prod_data["order_id"]=$ord_id; 
					  $price=select("snax_products_sub_categories","price","sub_cat_id=".$dat["subcat_id"]);
					  $prod_data["price"]=$price->price/**$prod_data["quantity"]*/;
					  $tax=select("snax_variable","var_value","var_name='tax'");
					  if(isset($tax->var_value)){
					   $prod_data["tax"]=$prod_data["price"]*($tax->var_value/100);
					  }
					  else{
					   $prod_data["tax"]=0;
					  }
					  insert("snax_order_products",$prod_data);
				  }
				  $res=array();
				  $res["status"]=1;
				  $res["message"]="Product added to cart";	
				  return $res;
				  
				}
				else{
				  //incase session order_id is not maintained
				  $data=array();		
				  $data['shop_id']=$dat["shop_id"];
				  $data['user_id']=$user_id;
				  $data['ondate']=timestamp();
				  $data["update_on"]=timestamp();
				  $data["update_by"]=$user_id;
				  $data['status']=1;
				  $insert_ord_id=insert("snax_orders",$data);
				  $_SESSION["order_id"]=$insert_ord_id;
				  $prod_data=array();
				  $prod_data["subcat_id"]=$dat["subcat_id"];
				  $prod_data["quantity"]=$dat["quantity"];
				  $prod_data["order_id"]=$insert_ord_id;
				  $prod_data["ondate"]=timestamp();
				  $price=select("snax_products_sub_categories","price","sub_cat_id=".$dat["subcat_id"]);
				  $prod_data["price"]=$price->price/**$prod_data["quantity"]*/;
				  $tax=select("snax_variable","var_value","var_name='tax'");
				  if(isset($tax->var_value)){
					   $prod_data["tax"]=$prod_data["price"]*($tax->var_value/100);
				  }
				  else{
					   $prod_data["tax"]=0;
				  }
				  insert("snax_order_products",$prod_data);
				  $res=array();
				  $res["status"]=1;
				  $res["order_id"]=$insert_ord_id;
				  $res["message"]="Product added to cart";	
				  return $res;
				}
			}
			else{
				  $res=array();
				  $res["status"]=0;
				  $res["message"]="Product cannot be added";	
				  return $res;
			
			}
		}
		else{
		          $res=array();
				  $res["status"]=0;
				  $res["message"]="Please Login before placing order";	
				  return $res;
		}
	}
	
	/**
	* This Function is used to Removing products from Orders
	* @author Muhammad Faisal Siddiq
	*/
	function remove_order_product($dat){
		if(isset($dat["order_prod_id"])){
			$where="order_prod_id=".$dat["order_prod_id"];
		    $del=delete("snax_order_products",$where);
				if($del){
					$res=array();
					$res["status"]=1;
					$res["message"]="Order Product deleted successfully";	
					return $res;
				}
				else{
					$res=array();
					$res["status"]=0;
					$res["message"]="Order Product cannot be deleted";	
					return $res;
				}
		}
		else{
				$res=array();
				$res["status"]=0;
				$res["message"]="Order Product cannot be deleted";	
				return $res;
		}
	}
	
	/**
	* This Function is used for updating order products
	* @author Muhammad Faisal Siddiq
	*/
	function update_order_product($dat){
	  if (isset($dat['order_prod_id']) && isset($dat['quantity'])){
		  $data=array();
		  $data["quantity"]=$dat["quantity"];
		  $where='order_prod_id='.$dat['order_prod_id'];
	      $res=update("snax_order_products",$data,$where) ;
		  if($res){
		        $res=array();
				$res["status"]=1;
				$res["message"]="Order Product updated";	
				return $res;
		  }
		  else{
		        $res=array();
				$res["status"]=0;
				$res["message"]="Order Product cannot be updated";	
				return $res;
		  }
	  }
	}
	
    /**
	* This Function is used to getting Cart Products Listings
	* @author Muhammad Faisal Siddiq
	*/
	function cart_listing(){
		if(isset($_SESSION["user_id"])&&isset($_SESSION["order_id"])){
			 $user_id=$_SESSION["user_id"];
			 $order_id=$_SESSION["order_id"];
			 $strSql="SELECT snax_order_products.order_prod_id,snax_order_products.subcat_id,snax_order_products.price,
			                 snax_order_products.tax,snax_order_products.quantity,snax_products_sub_categories.title as subcat_title,
							 snax_products_categories.title as cat_title
			                 FROM `snax_order_products` 
							 left join snax_products_sub_categories on snax_products_sub_categories.sub_cat_id=snax_order_products.subcat_id
							 left join snax_products_categories on snax_products_categories.cat_id=snax_products_sub_categories.cat_id
							 Where snax_order_products.order_id=".$order_id." AND status=1";				 
			$rsSql = mysql_query($strSql);
		    $data=array();
		    if(mysql_num_rows($rsSql)>0){
			  while($row=mysql_fetch_object($rsSql)){
				 $row->status=1;
				 $data[]=$row;
			  }
			  return $data;	
		    }
			else{
				$res=array();
				$res["status"]=0;
				$res["message"]="Order has no products";	
				return $res;
			
			}
		}
	   else if(isset($_SESSION["user_id"])&&!isset($_SESSION["order_id"])){
			$res=array();
			$res["status"]=0;
			$res["message"]="You dont have products in your cart";	
			return $res;
		}
		else{
			$res=array();
			$res["status"]=0;
			$res["message"]="Login to see your cart listing";	
			return $res;
		}
	}
	
	/**
	* This Function is used to Updating Order Status
	* @author Muhammad Faisal Siddiq
	*/
	function update_order_status($dat){
		if(isset($_SESSION["user_id"])){
			if(isset($dat["order_id"]) && isset($dat["status"])){
			      $or_dat=array(); 
				  $or_dat["status"]=$dat["status"];
				  $or_dat["update_on"]=timestamp();
				  $or_dat["completed_date"]=timestamp();
				  $or_dat["completedby"]=$_SESSION["user_id"];
			      $res=update("snax_orders",$or_dat,"order_id=".$dat["order_id"]) ;
			      $res=array();
				  $res["status"]=1;
				  $res["message"]="Status Updated Successfully";	
				  return $res;
			}
			else{
			 $res=array();
			 $res["status"]=0;
			 $res["message"]="Either Status or Order Id is missing";	
			 return $res;
			}
		}
        else{
		     $res=array();
			 $res["status"]=0;
			 $res["message"]="Login to do update Order Status";	
			 return $res;
		}
	
	}
	
	/**
	* This Function is used to Getting Order Details
	* @author Muhammad Faisal Siddiq
	*/
	function get_order_details($order_id,$user_id){
	        $strSql="SELECT snax_orders.order_id,price*quantity as tprice,subcat_id,tax*quantity as ttax
			                FROM `snax_orders` 
							left join snax_order_products on snax_order_products.order_id=snax_orders.order_id
							Where snax_orders.order_id=".$order_id." AND snax_orders.user_id=".$user_id." AND status=1";				 
			$rsSql = mysql_query($strSql);
		    $data=array();
		    if(mysql_num_rows($rsSql)>0){
				$amount="";
			  while($row=mysql_fetch_object($rsSql)){
				 $amount+=$row->tprice+$row->ttax;
			  }
			  return $amount;	
		    }
			else{
				return false;
			}
	}
	
	/**
	* This Function is used to Managing User Transactions
	* @author Muhammad Faisal Siddiq
	*/
	function user_trasactions($dat){
		if(isset($_SESSION["user_id"])){
		   $user_id=$_SESSION["user_id"];
		   if(!isset($dat["type"])&&!isset($dat["amount"])&&!isset($dat["status"])&&!isset($dat["method_type"])&&!isset($dat["detail"])){
		        $res=array();
				$res["status"]=0;
				$res["message"]="All in put fields are required: type,amount,status,method_type,detail";	
				return $res;
		   }
		   if(isset($_SESSION["order_id"])){
		      $order_id=$_SESSION["order_id"];
			  $user_data=array();
		      $user_data["order_id"]=$order_id;
			  $user_data["user_id"]=$user_id;
			  $user_data["type"]=$dat["type"];
			  $user_data["amount"]=$dat["amount"];
			  $user_data["ondate"]=timestamp();
			  $user_data["status"]=$dat["status"];
			  $user_data["method_type"]=$dat["method_type"];
			  $user_data["detail"]=$dat["detail"];
			  $user_credit=exists("snax_user_transactions","amount","user_id=".$user_id);
			  $update=false;
			  if(isset($user_credit->amount)){
			   $user_data["amount"]=$dat["amount"]+$user_credit->amount;
			   $update=true;
			  }
			  $order_amount=get_order_details($order_id,$user_id);
			  if($order_amount && $order_amount>$user_data["amount"]){
				  if($update)
					update("snax_user_transactions",$user_data);
				  else
				   insert("snax_user_transactions",$user_data);			
			  $res=array();
			  $res["status"]=1;
			  $res["message"]="Your order has amount greater than amount you payed but amount has been added to your account. Pay more amount to get your QR Image";
			  return $res;
			  }
			  if($order_amount&&$update){
				    $user_data["amount"]=$user_data["amount"]-$order_amount;
				    update("snax_user_transactions",$user_data,"user_id=".$user_id);
				    $data=array();
					$tempDir = 'QRR/temp/'; 
					$codeContents = generate_qr_code(); 
					$fileName = 'img_'.md5($codeContents).'.png'; 		 
					$pngAbsoluteFilePath = dirname(__FILE__)."/".$tempDir.$fileName; 
					$urlRelativeFilePath = SITEURL."db/QRR/temp/".$fileName; 
					// generating 
					if (!file_exists($pngAbsoluteFilePath)) { 
						QRcode::png($codeContents, $pngAbsoluteFilePath); 
					} 
					$data["qr_code"]=$codeContents;
					$data["qr_image"]=$urlRelativeFilePath;
					$data["update_on"]=timestamp();
					$data["update_by"]=$user_id;
					$data["status"]=2;
					update("snax_orders",$data,"order_id=".$order_id);
					$res=array();
					$res["status"]=1;
					$res["qr_image"]=$urlRelativeFilePath;
					$res["message"]="Payment done succesfully";
					return $res;				   
			  }
			  else if($order_amount&&!$update){
				   $user_data["amount"]=$dat["amount"]-$order_amount;
				   insert("snax_user_transactions",$user_data);
					$data=array();
					$tempDir = 'QRR/temp/'; 
					$codeContents = generate_qr_code(); 
					$fileName = 'img_'.md5($codeContents).'.png'; 		 
					$pngAbsoluteFilePath = dirname(__FILE__)."/".$tempDir.$fileName; 
					$urlRelativeFilePath = SITEURL."db/QRR/temp/".$fileName; 
					// generating 
					if (!file_exists($pngAbsoluteFilePath)) { 
						QRcode::png($codeContents, $pngAbsoluteFilePath); 
					} 
					$data["qr_code"]=$codeContents;
					$data["qr_image"]=$urlRelativeFilePath;
					$data["update_on"]=timestamp();
					$data["update_by"]=$user_id;
					$data["status"]=2;
					update("snax_orders",$data,"order_id=".$order_id);
					$res=array();
					$res["status"]=1;
					$res["qr_image"]=$urlRelativeFilePath;
					$res["message"]="Payment done succesfully";
					return $res;
			  }
			  else{
				    if($update)
				      update("snax_user_transactions",$user_data,"user_id=".$user_id);
			        else
					  insert("snax_user_transactions",$user_data);			
					$res["status"]=1;
					$res["message"]="Your order does not have any bill but amount has been added to your account";
					return $res;
			  }
		   }
		   else{				 
				  $user_data=array();
				  $user_data["user_id"]=$user_id;
				  $user_data["type"]=$dat["type"];
				  $user_data["amount"]=$dat["amount"];
				  $user_data["ondate"]=timestamp();
				  $user_data["status"]=$dat["status"];
				  $user_data["method_type"]=$dat["method_type"];
				  $user_data["detail"]=$dat["detail"];
				  $user_credit=exists("snax_user_transactions","amount","user_id=".$user_id);
				  $update=false;
				  if(isset($user_credit->amount)){
				   $user_data["amount"]=$dat["amount"]+$user_credit->amount;
				   $update=true;
				  }  
				  if($update)
				      update("snax_user_transactions",$user_data,"user_id=".$user_id);
			        else
					  insert("snax_user_transactions",$user_data);			
				 $res["status"]=1;
				 $res["message"]="Amount has been added to your account";
				 return $res;  			   
		       }
			}
			else{
				$res=array();
				$res["status"]=0;
				$res["message"]="Login to do transaction";	
				return $res;
			}
	  }
	  
	/**
	* This Function is used to Order Creation
	* @author Muhammad Faisal Siddiq
	*/
	  function create_new_order($dat){
		 
		  // Order main details server side validation
		 if(!isset($dat["theatre_id"])&&!isset($dat["movie_id"])&&!isset($dat["shift_time"])&&!isset($dat["auth_token"])&&
		    !isset($dat["total_amount"])&&!isset($dat["paymethod"])){
		        $res=array();
				$res["status"]=0;
				$res["message"]="All in put fields are required: auth_token,theatre_id,movie_id,shift_time,auth_token,total_amount,paymethod";	
				return $res;
		   } 
		   
		    // Order products details server side validation
		 if(!isset($dat["cat_id"])&&!isset($dat["subcat_id"])&&!isset($dat["quantity"])&&!isset($dat["price"])&&
		    !isset($dat["extra_id"]) &&!isset($dat["tax"])){
		        $res=array();
				$res["status"]=0;
				$res["message"]="All in put fields are required:cat_id,subcat_id,quantity,price,extra_id,tax";	
				return $res;
		   }
		   		   
		   $where="auth_token='".$dat['auth_token']."'";
		   $user=select("snax_user","user_id",$where);
		   if($user){			   
			    $trans_data["user_id"]=$user->user_id;
				$trans_data["method_type"]=$dat["paymethod"];
				$trans_data["amount"]=$dat["total_amount"];
				if(isset($dat["paypal_transcation_id"]) && !empty($dat["paypal_transcation_id"])){
				 $trans_data["paypal_transcation_id"]=$dat["paypal_transcation_id"];
				 $whr_common="paypal_transcation_id='". $trans_data["paypal_transcation_id"]."'";	
				}
				if(isset($dat["reward_card_id"]) && !empty($dat["reward_card_id"])){
				 $trans_data["reward_card_id"]=$dat["reward_card_id"];	
				 $whr_common="reward_card_id='". $trans_data["reward_card_id"]."'";	
				}
				$trans_data["status"]=1;
				$trans_data["type"]="online";
				$trans_data["ondate"]=timestamp();
				insert("snax_user_transactions",$trans_data);// Inserting the payment method and amounts details in transaction table
				
				$ord_data["user_id"]=$user->user_id;
				$ord_data["theatre_id"]=$dat["theatre_id"];
				$ord_data["ondate"]=timestamp();
				$ord_data["movie_id"]=$dat["movie_id"];
				$ord_data["shift_time"]=$dat["shift_time"];
				$ord_data["status"]=1;//Pending Status
				$insert_id=insert("snax_orders",$ord_data);// Inserting the order details in order table
				for($i=0;$i<sizeof($dat["cat_id"]);$i++){
					$order_prod_data["order_id"]=$insert_id;
					$order_prod_data["cat_id"]=$dat["cat_id"][$i];
					$order_prod_data["extra_id"]=$dat["extra_id"][$i];
					$order_prod_data["subcat_id"]=$dat["subcat_id"][$i];
				    $order_prod_data["quantity"]=$dat["quantity"][$i];
				    $order_prod_data["ondate"]=timestamp();				   
				    $order_prod_data["price"]=$dat['price'][$i];					
				    $tax=select("snax_variable","var_value","var_name='tax'");
				    if(isset($tax->var_value)){
				 	   $order_prod_data["tax"]=$order_prod_data["price"][$i]*($tax->var_value/100);
				    }
				    else{
					   $order_prod_data["tax"]=0;
				    }
				    insert("snax_order_products",$order_prod_data);					
				}
				
				    $order_amount=get_order_details($insert_id,$user->user_id);
				if($order_amount<=$dat["total_amount"]){
				    $user_data["amount"]=$user_data["amount"]-$order_amount;
				    update("snax_user_transactions",$user_data,"user_id=".$user->user_id." AND ".$whr_common);
				    $data=array();
					$tempDir = 'QRR/temp/'; 
					$codeContents = generate_qr_code(); 
					$fileName = 'img_'.md5($codeContents).'.png'; 		 
					$pngAbsoluteFilePath = dirname(__FILE__)."/".$tempDir.$fileName; 
					$urlRelativeFilePath = /*SITEURL.*/"db/QRR/temp/".$fileName; 
					// generating 
					if (!file_exists($pngAbsoluteFilePath)) { 
						QRcode::png($codeContents, $pngAbsoluteFilePath); 
					} 
					$data["qr_code"]=$codeContents;
					$data["qr_image"]=$urlRelativeFilePath;
					$data["update_on"]=timestamp();
					$data["update_by"]=$user_id;
					$data["status"]=2;
					update("snax_orders",$data,"order_id=".$insert_id);
					$res=array();
					$res["status"]=1;
					$res["qr_image"]=$urlRelativeFilePath;
					$res["message"]="Payment done succesfully";
					return $res;				   
			  }
			  else{
			        $res=array();
					$res["status"]=0;
					$res["message"]="Your order has amount greater than amount you payed but amount has been added to your account. Pay more amount to get your QR Image";
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