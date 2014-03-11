<?php
include 'config.php';
$apitype = isset($_REQUEST["api_type"]) ? $_REQUEST["api_type"] : ""; //Get the requesting Api Type
 switch($apitype){ //Call the appropriate method for the api type
	case "user_reg":
		$data = array();
		$data = user_registration($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "authentication":
		$data = array();
		$data = user_authentication($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "forgot_password":
		$data = array();
		$data = forgot_password($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "theatre_listing":
		$data = array();
		$data = theatrelisting($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "theatre_movie_shift":
		$data = array();
		$data = theatre_movie_shifts($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "theatre_shops_list":
		$data = array();
		$data = theatre_shops_list($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "category_listing":
		$data = array();
		$data = category_listing($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "sub_category_listing":
		$data = array();
		$data = sub_category_listing($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "product_extras_listing":
		$data = array();
		$data = product_extras_listing($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "order_product":
		$data = array();
		$data = add_order_product($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case "remove_product":
		$data = array();
		$data = remove_order_product($_REQUEST);
		$data = json_encode($data);
		echo $data;
	break;
	case  "cart_listing":
		$data = array();
		$data=cart_listing();
		$data = json_encode($data);
		echo $data;
		break;
	case  "update_order":
		$data = array();
		$data = update_order_product($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "user_transaction":
		$data = array();
		$data = user_trasactions($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "order_status":
		$data = array();
		$data = update_order_status($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "get_current_balance":
		$data = array();
		$data = get_current_balance($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "user_info_update":
		$data = array();
		$data = user_info_update($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "add_paypal_info":
		$data = array();
		$data = add_paypal_info($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "add_funds":
		$data = array();
		$data = add_funds($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "add_billing_address":
		$data = array();
		$data = add_billing_address($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "reward_card_info_update":
		$data = array();
		$data = reward_card_info_update($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "get_reward_card":
		$data = array();
		$data = get_reward_card($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "pay_via_reward_card":
		$data = array();
		$data = pay_via_reward_card($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "pay_via_reward_card":
		$data = array();
		$data = pay_via_reward_card($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	case  "create_new_order":
		$data = array();
		$data = create_new_order($_REQUEST);
		$data = json_encode($data);
		echo $data;
		break;
	default:
		$data=array();
		$data["status"]=0;
		$data["message"]="Invalid API Type";
		$data = json_encode($data);
		echo $data;
		break;
}