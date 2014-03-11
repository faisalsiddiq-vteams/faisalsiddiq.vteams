<?php
class CompanyController extends Zend_Controller_Action
{
	public function init()
	{
		$this->session = new Zend_Session_Namespace('user_sess');
		if(!$this->session->users['email_id']){
			$this->_redirect('/login/index');
		}	
		$this->model 	= new CompanyModel();
		$this->validator= new application_controllers_MyValidation();
		$this->_helper->layout->setLayout('layout');	
		$this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	 /**
	 * This Action is used to add Companies via Web Form
	 * @author Muhammad Faisal Siddiq
	 */
	 
	public function formAction() {		
	   $this->view->headTitle("Pipline > Companies > Create Company > Web form");
	   $id=$this->_request->getParam('id');
	   $this->view->id=$id;
	   if($id)
	    $action="Edit Company";
	   else 
	    $action="Create Company";
	   $breadCrumb = '<a href="#.">Pipline</a> &gt; <a href="'.$this->view->baseUrl().'/company/list/">Companies</a> &gt;'.
	   " $action > Web Form";
	   $this->view->placeholder('breadCrumb')->set($breadCrumb);
	   $common_m = new CommonModel();
	   if($id){
	    $data_arr=explode("-",$id);
	    $id=$data_arr[0];
	    $tab_id=$data_arr[1];
	    $created_date='';
		$created_user_id='';
	    $comp_data=$common_m->getTableData("company","*","id ='".$id."'");
		if($comp_data){
		 $created_date=$comp_data[0]['create_date'];
		 $created_user_id=$comp_data[0]['created_userid'];
		 $this->view->comp_data=$comp_data[0];
		}
        $common_m 	= new CommonModel();
		$states_data=$common_m->getTableData("states",array('state_code','state_name'));
		$this->view->states=$states_data;
		$country_data=$common_m->getTableData("countries",array('country_id','country_name'));
		$this->view->countries=$country_data;
		$contact_data=$common_m->getTableData("contacts","*","company_id ='".$id."'");
		$this->view->contact_data=$contact_data;
	   }	   
		$addform= new forms_company_addeditForm();
		$this->view->form=$addform;
		$contact_msg='';
		$company_msg='';
		$company_count=0;
		if($this->_request->isPost()){
		$company_arr['stage_id']=$this->_request->getParam('stage');
		$company_arr['company_name']=$this->_request->getParam('company_name');
		$company_arr['source']=$this->_request->getParam('source');
		$company_arr['client_id']=$this->_request->getParam('client_id');
		$company_arr['industry']=$this->_request->getParam('industry');
		$company_arr['client_joindate']=$this->_request->getParam('client_joindate');
		$company_arr['website']=$this->_request->getParam('company_website');
		$company_arr['address1']=$this->_request->getParam('company_address1');
		$company_arr['address2']=$this->_request->getParam('company_address2');
		$company_arr['city']=$this->_request->getParam('company_city');
		$company_arr['state']=$this->_request->getParam('company_state');
		$company_arr['zip']=$this->_request->getParam('company_zip');
		$company_arr['country_id']=$this->_request->getParam('country_id');
		$company_arr['tel']=$this->_request->getParam('company_tell');
		$company_arr['fax']=$this->_request->getParam('company_fax');
		$company_arr['create_date']=date("Y-m-d H:i:s");
		$company_arr['update_date']=date("Y-m-d H:i:s");
		$company_arr['created_userid']=$this->session->users['user_id'];
	    $company_arr['updated_userid']=$this->session->users['user_id'];	
		$client_join_date=explode("-",$company_arr['client_joindate']);
		$company=$common_m->getTableData("company",array("id","company_name"),"company_name='".$company_arr['company_name']."'");
	    $company_count=count($company);
	    if($company_count==0){	
		     $company_id= $this->model->insert($company_arr);
		     $company_msg="1 Company : <b>".$company_arr['company_name']."</b> has been added. ";
		 }
		 else{
		  if($id){
		     $company_arr['create_date']=$created_date;
			 $company_arr['created_userid']=$created_user_id;
			 $this->model->update($company_arr,"id='".$id."'");
			 $company_msg="1 Company : <b>".$company_arr['company_name']."</b> has been Updated. ";
			 $company_id=$id;
	      }
		  else
		     $company_id=$company[0]['id'];
		}
		 $count      = $this->_request->getParam('contact_count');
		 $first_name = $this->_request->getParam('contact_first_name');	
		 $last_name  = $this->_request->getParam('contact_last_name');
		 $title      = $this->_request->getParam('contact_title');
		 $adres1     = $this->_request->getParam('contact_address1');
		 $adres2     = $this->_request->getParam('contact_address2');
		 $city       = $this->_request->getParam('contact_city');
		 $state      = $this->_request->getParam('contact_state');
		 $zip        = $this->_request->getParam('contact_zip');
		 $country_id = $this->_request->getParam('contact_country'); 
		 $tel        = $this->_request->getParam('contact_tell');
		 $mobile     = $this->_request->getParam('contact_mobile');
		 $email      = $this->_request->getParam('contact_email'); 
		 $skype      = $this->_request->getParam('contact_skype');
		 $linkedin   = $this->_request->getParam('contact_linkedin');   
		 $twitter    = $this->_request->getParam('contact_twitter');
		 $i=0;
		 if($id && ($company_count!=0))
		  $common_m->deleteData("contacts","company_id='".$id."'");
		 if(isset($count)){
		   foreach($count as $count){
			$insert_contact_data = array(
			'company_id'	=>$company_id,
			'first_name'	=>$first_name[$i],
			'last_name'		=>$last_name[$i],
			'title'		    =>$title[$i],
			'address1'      =>$adres1[$i],
			'address2'      =>$adres2[$i],
			'city'		    =>$city[$i],
			'state'         =>$state[$i],
			'zip'           =>$zip[$i],
			'country_id'    =>$country_id[$i],
			'tel'		    =>$tel[$i],
			'mobile'		=>$mobile[$i],
			'email'		    =>$email[$i],
			'skype_id'		=>$skype[$i],
			'linkedIn'		=>$linkedin[$i],
			'twitter'		=>$twitter[$i],
			'create_date'	=>date("Y-m-d H:i:s"),							
			'created_userid'=>$this->session->users['user_id'],	
			'updated_userid'=>$this->session->users['user_id']
			);				
		   $common_m->insertData("contacts",$insert_contact_data);
		   $i++;
		  }
		 }
		 $notes = $this->_request->getParam('notes');
		 if(strlen(trim($notes))){
		   if($notes){
			 $insert_notes=array("company_id"    =>$company_id,
								 "description"   =>$notes,
								 "create_date"	 =>date("Y-m-d H:i:s"),							
								 "created_userid"=>$this->session->users['user_id']);
			 $common_m->insertData("notes",$insert_notes); 
		   }
		 }
		 $company_name=$common_m->getTableData("company","company_name","id='".$company_id."'");
		 $contact_msg="$i Contact added for Company: <b>".$company_name[0]['company_name']."</b> <br>";
		 $msg=$company_msg.$contact_msg;
	     $message = ' <img src="'.$this->view->baseUrlCom().'/images/message-icon-upload.png" /> '.$msg.'. <br>';
		 $this->flashMessenger->addMessage($message);
		 if(isset($tab_id))
		  $this->_redirect("company/list/$tab_id");
		 else
		  $this->_redirect("company/list/2");
		}
	}
	
	/**
	* Get all the available stages for companies
	* @author  Muhammad Faisal Siddiq
	*/
	
	public function getStages($id){
		$stages_data=$this->validator->getTableData("stages",array("stage_id","tab_id"),"tab_id='".$id."' ",
		"stages.stage_id DESC");
		if(count($stages_data)>1){
		$stages="";
		foreach ($stages_data as $stage){
			if($stages)
				$stages.=" OR stage_id='".$stage['stage_id']."' ";
			else 
				$stages.="stage_id='".$stage['stage_id']."' ";
		   }
		   $where="($stages)";		 
		}else{
		 if(isset($stages_data[0]['stage_id']))
			$where="stage_id='".$stages_data[0]['stage_id']."' ";
		 else
			$where='';
		}	
		return $where;
	}
	
	/**
	* ListAction Retrieve all the records from the table.
	* This function is used to list down the data of Pieplines
	* @author  Muhammad Faisal Siddiq
	*/
	
	public function listAction()	{
		$this->view->headTitle("Pipeline > View Pipeline");
		$breadCrumb = '<a href="#.">Pipeline</a> &gt; <a href="#.">View Pipeline</a>';
		$this->view->placeholder('breadCrumb')->set($breadCrumb);
		$tabs=$this->validator->getTableData("tabs",array("id","name"));
		$this->view->tabs=$tabs;
		$id=$this->_request->getParam('id');
		$this->view->id=$id;				
		$where=$this->getStages($id);
		$sdata_array=array();
		$i=0;
		foreach ($tabs as $tab_data){	
			$where_count=$this->getStages($tab_data['id']);
			$item = $this->validator->getTableData("company",array("COUNT(stage_id) as total"),$where_count);
			$sdata_array[$i]=$item[0]['total'];			
			$i++;
		}
		$this->view->records_count=$sdata_array;		
		if(!$this->_request->isPost())
		{
			if($id==0) {
				$this->session->sort_col='company_name';
				$this->session->order_by='ASC';
				$this->session->filterText='';
				$this->session->filterId='';
				$errorMessage = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
				if(empty($errorMessage)){
					$this->_redirect('/company/list/2');
				}
			}
			if($this->session->sort_col =="" || $this->view->getReferer('/company/list/')==FALSE){
				$this->session->sort_col = "company_name";
				}

			$condition='1=1';
			if($this->session->filterText!="")
				$condition .=" AND company_name LIKE '".trim($this->session->filterText)."%'";
			if($this->session->filterId!="")
			  $condition .=" AND company.client_id ='".$this->session->filterId."'";
			if($id==4)
			 $this->session->sort_col="stage_id DESC";
			else
			 $this->session->sort_col="company_name ASC";
			$this->view->records	= $this->_performSearch($condition,$where,"getAllValues",$this->session->sort_col);
		}
		else
		{
			$orderby = '';
			if($this->_request->getParam('sort_col')!="") {
				$sort_col = $this->_request->getParam("sort_col");
				if($sort_col != '') {
					$this->session->sort_col = $sort_col;
					if($this->session->order_by == 'DESC')
						$this->session->order_by = 'ASC';
					else
						$this->session->order_by = 'DESC';
				} else {
					$this->session->sort_col ='';
					$order_by = ''; 
				}
				$orderby = $this->session->sort_col.' '.$this->session->order_by;
			}		
			$search_txt = trim($this->_request->getParam('search_txt'));
			$condition  = 1;
			$this->view->records = $this->_performSearch($condition,$where,'getAllValues',$orderby);
		}
	}
	
	/**
	* ListAction Retrieve all the records from the table.
	* This function is used to list down the data of Companies
	* Also via this Action notes can be added with the company
	* @author  Muhammad Faisal Siddiq
	*/
	
	public function viewAction(){
		$this->view->headTitle("Pipline - Companies > Manage Company ");
		$breadCrumb = '<a href="#.">Companies</a> &gt; <a href="'.$this->view->baseUrl().
		'/company/list/">Manage Company</a> &gt; <a href="#.">Company Detail</a>';
		$this->view->placeholder('breadCrumb')->set($breadCrumb);		
		$id=$this->_request->getParam('id');
		$common_m 	= new CommonModel();
		if($this->_request->isPost()){
		$id=$this->_request->getParam('company_id');
		$notes=$this->_request->getParam('notes');
		$insert_notes = array("company_id"    =>$id,
							  "description"   =>$notes,
							  "create_date"	  =>date("Y-m-d H:i:s"),							
							  "created_userid"=>$this->session->users['user_id']);
		if($this->_request->getParam('notes'))
		 $common_m->insertData("notes",$insert_notes);
		 $this->_redirect("company/view/$id");
		}
		$company_data=$this->model->getcompanydetail("company.id=$id"); 
		if($company_data)
		 $this->view->company_data=$company_data;
		$where="company_id='".$id."'";
		$contact_data=$this->model->getcontactsdetail($where);
		if($contact_data)
		 $this->view->contact_data=$contact_data;
		$company_notes=$this->model->getcompanynotes($where);
		if($company_notes)
		 $this->view->company_notes=$company_notes;
    }
	
	/**
	 * This function is used for Making the Select DDM for countries and states via ajax
	 * @author Muhammad Faisal Siddiq
	 *
	*/
	
	public function statecountryAction(){
		$id=$this->_request->getParam('id');
        $common_m 	= new CommonModel();
		if($id=="state"){
		$states_dat=$common_m->getTableData("states",array('state_code','state_name')) ;
		$options = '<option value="">Select State</option>';
		 foreach($states_dat as $option) {
			$options .=' <option value="'.$option['state_code'].'">'.$option['state_code'].' - '.$option['state_name'].'</option>';
		 }
		}
		elseif($id=="country"){
		$country_dat=$common_m->getTableData("countries",array('country_id','country_name')) ;
		$options = '<option value="">Select Country</option>';
		 foreach($country_dat as $option) {
			$options .=' <option value="'.$option['country_id'].'">'.$option['country_name'].'</option>';
		 }
		}
		echo $options;
		exit;
	}
	
    /**
	 * This function is used to jump between different pages for orders list tab.
	 * @author  Muhammad Faisal Siddiq
   */
   
    public function gotoAction() {
		if($this->_request->isPost()) {
			if($this->_request->getParam("perPage")!='')
				$this->session->perPage	= $this->_request->getParam("perPage");				
			if($this->_request->getParam('client_id')!='')
				$this->session->filterId = $this->_request->getParam('client_id');
			if($this->_request->getParam('search_txt')!='')
				$this->session->filterText = trim($this->_request->getParam('search_txt'));			
			$goto = $this->_request->getParam("go_value");
			$page = $this->_request->getParam('goto');
			$id = $this->_request->getParam("param_id");
				$page = 'list';
			if(is_numeric($goto))
				$this->_redirect("/company/$page/$id/".$goto);
			else
				$this->_redirect('/company/$page/');
		}
	}
	
	/**
	 * This function is used to handle the different searching conditions.
	 *
	 * @author 	Muhammad Faisal Siddiq
	 * @param 	String $condition
	 * @param 	Int $perPage
	 * @param 	String $where
	 * @return 	records in paginator
	 */
	 
	private function _performSearch($condition,$where,$model='',$orderby='') {
		$pageRange	=10;
		if($this->session->perPage!='')
			$perPage = $this->session->perPage;
		else
			$perPage = 25;
		if($model=='')
			$model="getAllValues";
		
		$where_clause = '1';
		if($where)
			$where_clause .=" AND $where";
		if($condition)
			$where_clause .=" AND $condition";

		$result 	= $this->model->$model($where_clause,$orderby);
		$paginator = new Zend_Paginator(
			new Zend_Paginator_Adapter_DbSelect($result)
		);
		$paginator->setItemCountPerPage($perPage)
				  ->setPageRange($pageRange)
				  ->setCurrentPageNumber($this->_getParam('page'));

		$this->view->cntActive = 3;
		$this->view->cntInactive = 2;
		$this->view->cntOnhold	= 6;
		return $paginator;
	}
	
	 /**
	 * This function is used to delete the selected records.
	 * Companies in any stage like target, lead, qualified, active conversation, final negotiation, client tabs
	 * can be deleted via this action
	 * @author Muhammad Faisal Siddiq
	 */
	 
	public function moveAction() {
		$status_action_chk='';
		$where='';
		$i=0;
		$message='';
		$change_stage=array();
		$del_company='';
		$move_company='';
		$notnow_company='';
		$never_company='';
		$where_common='';
		$move='';
		$common_m = new CommonModel();
		$id=$this->_request->getParam('id');
		/*
		* Delete Action
		*/		
		if($this->_request->getParam("frm_save")=='Delete') {
		 $status_action_chk = $this->_request->getParam('status_action');
		 if($status_action_chk) {
			foreach($status_action_chk as $row) {
				$result = $this->validator->getTableData("company",array("id","company_name"),"id='".$row."'");
				if($result){
					$i++;
					if($i==1){
						$str="id=".$row;
						$str_common="company_id=".$row;
						$del_company .=$result[0]['company_name'];
					}else {
						$str=" OR id=".$row;
						$str_common=" OR company_id=".$row;
						$del_company .=", ".$result[0]['company_name'];
					}
					$where.=$str;
					$where_common.=$str_common;
					
				}
			}
			if($where){
				$this->validator->deleteData("company",$where);
				$this->validator->deleteData("contacts",$where_common);
				$this->validator->deleteData("notes",$where_common);
			}
		}
	 }	
	 /*
	 * Move action
	 */
	 elseif($this->_request->getParam("frm_save")=='Move') {
		$move_to_stage=$this->_request->getParam('stage_id');
		$status_action_chk = $this->_request->getParam('status_action');
		if($status_action_chk) {
			foreach($status_action_chk as $row) {
				$result = $this->validator->getTableData("company",array("id","company_name"),"id='".$row."'");
				if($result){
					$i++;
					if($i==1){
						$str="id=".$row;
						$move_company .=$result[0]['company_name'];
					}else {
						$str=" OR id=".$row;
						$move_company .=", ".$result[0]['company_name'];
					}
					$where.=$str;
				}
			}
			if($where){
				$change_stage = array("stage_id"=>$move_to_stage);
				$result = $this->validator->getTableData("stages",array("name"),"stage_id=".$move_to_stage);
				if(isset($result[0]['name']))
				 $move=$result[0]['name'];
				else
				 $move="Next";
				$common_m->updateData("company",$change_stage,$where);
			}
		  }
		}	
		if($del_company)
			$message .= '<br><img src="'.$this->view->baseUrlCom().'/images/message-icon-ok.png" /> '." Company<b>$del_company</b> deleted. <br>";
		if($move_company)
			$message .= '<br><img src="'.$this->view->baseUrlCom().'/images/message-icon-ok.png" /> '." Company<b>$move_company</b> moved to $move. <br>";		
		if($notnow_company)
			$message .= '<br><img src="'.$this->view->baseUrlCom().'/images/message-icon-ok.png" /> '." Company <b>$notnow_company</b> put in Not Now stage. <br>";
		if($never_company)
			$message .= '<br><img src="'.$this->view->baseUrlCom().'/images/message-icon-ok.png" /> '." Company <b>$never_company</b> put in never change stage. <br>";				
		if($message)
			$this->flashMessenger->addMessage($message);
		if($id)
			 $this->_redirect("/company/list/$id");
		else
			$this->_redirect("/company/list/2");
	}
}