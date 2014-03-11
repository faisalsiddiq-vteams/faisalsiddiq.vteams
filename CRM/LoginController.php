<?php 
	class LoginController extends Zend_Controller_Action {
	
	/***********************************
	*  init function 
	************************************/
	public function init() {
			$this->session = new Zend_Session_Namespace('user_sess');			
	}	
	
	/**
	 * This Action is used for User Login
	 * @author Muhammad Faisal Siddiq
	*/	
	public function indexAction() {
		if($this->session->users['email_id']){
			$this->_redirect('/order/orderlookup/');
		}			
		$this->_helper->layout->disableLayout();				/* disable layout for this Action*/
		$this->view->headTitle('CRM > Login');
		if(isset($_COOKIE['languageset']) )
		{
		 $data = $_COOKIE['languageset'];
		 if($data['rememberlang'] == 1) {
			$this->_cookieLogin($_COOKIE['languageset']);
		 }
		}	
		$form = new forms_login_loginForm();
		$users = new UserModel();
		$this->view->form = $form;
		if($this->_request->isPost()){				   
			if($form->isValid($this->getRequest())) {
				$data = array(								
						 'user_name'=>$this->_request->getPost('email_id'),
						 'password'=>$this->_request->getPost('password'));
				$checked = $this->_request->getPost('checkbox');
				$auth = Zend_Auth::getInstance();
				$authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'users');
				$authAdapter->setIdentityColumn('user_name')
							->setCredentialColumn('password');
							
				$authAdapter->setIdentity($data['user_name'])
							->setCredential($data['password']);
				
				$result = $auth->authenticate($authAdapter);
				if($result->isValid())
				{	
					$storage = new Zend_Auth_Storage_Session();
					$storage->write($authAdapter->getResultRowObject());
					$data = $storage->read();
					if($data->status !='Active')
					{
						$this->view->message = 'User is not active';
						 Zend_Auth::getInstance()->clearIdentity();
						 return;
					}else {						

						if($checked == 1)
						{
							setcookie('languageset[rememberlang]', 1, time()+(60*60*24*30),'/');
							setcookie('languageset[elang]', md5($data->user_name), time()+(60*60*24*30),'/');
							setcookie('languageset[plang]', md5($data->password), time()+(60*60*24*30),'/');							
						}
						elseif($checked == 0)
						{
							setcookie('languageset[rememberlang]', 0);													
						}
						
						$this->session->users = array();
						
						$this->session->users['email_id'] 	=$data->user_name;
						$this->session->users['username'] 	=$data->first_name .' '.$data->last_name;
						$this->session->users['user_id'] 	=$data->id;
						$this->session->users['client_name'] ="3LINX - CRM";
						$this->session->users['client_id']	="CRM";
						$this->session->users['groups_id'] 	='';							
						$this->_redirect('/company/list/');							
					}					
				}
				else 
				{
					$this->view->message = 'Invalid user name or password.';
				}			
			}
		}
	}
		
    /**
	 * This Action is used for User Logout
	 * @author Muhammad Faisal Siddiq
	*/	
	public function logoutAction() {
		$this->_helper->layout->disableLayout();	
		Zend_Auth::getInstance()->clearIdentity();
		unset($this->session->users);
		setcookie('languageset[rememberlang]',0,time()+(60*60*24*30),'/');
		$this->_redirect('/login/index');					 
	}
	
	/**
	 * This Function is used for User Logout
	 * @author Muhammad Faisal Siddiq
	*/	
	public function logoutuser() {
		$this->_helper->layout->disableLayout();	
		$validator = new application_controllers_MyValidation(); 
		$validator->updateData("users",array("login"=>""),"user_name='".$this->session->users['email_id']."'");
		Zend_Auth::getInstance()->clearIdentity();
		unset($this->session->users);
		setcookie('languageset[rememberlang]',0,time()+(60*60*24*30),'/');
		exit("success");
	}		
		
	/**
	 * This Function is used for sending password to user who lost the password
	 * @author Muhammad Faisal Siddiq
	*/
	public function forgetpasswordAction(){
		$this->_helper->layout->disableLayout();				/* disable layout for this Action*/
		$this->view->placeholder('heading')->set('Forgot Password');
		$this->view->headTitle('3LINX > Forgot Password');
		$this->view->placeholder('buttonCaption')->set('Submit');
		$form = new forms_login_loginForm();
		$form->removeElement('password');
		$form->removeElement('checkbox');
		$this->view->form = $form;			
		/* getting mail stuff from registry*/
		global $config;
		$mail_sub = new Zend_Config_Ini('application/config.ini', 'mail');
		$base_url = $config->site_url;
		$mailFrom = $config->mail_from;
		$users = new UserModel();			
		if($this->_request->isPost())
		{
		  if($form->isValid($this->getRequest())) {
				$data = array(								
				 'email_id'=>$this->_request->getPost('email_id')													
				);
				list($usec,$sec) = explode(' ',microtime());
				// Seed the random number generator with above timings
				mt_srand((float) $sec + ((float) $usec * 1000000));
				// Generate hash using GLOBALS and PID
				$key = sha1(uniqid(mt_rand(),true));
				$this->_varKey($data['email_id'],$key);
				# body for mail : it comes from registry
				$mail_body = str_ireplace("[change_password_link]","<a href='".$base_url."/login/recoverpass/".$key."'>".'Recover Password'."</a>",$mail_sub->password_body);
				$transport = new Zend_Mail_Transport_Smtp('kayadevelopmentcenter.com');
				$mail = new Zend_Mail('UTF-8');
				$mail->setBodyHtml($mail_body,'UTF-8',Zend_Mime::ENCODING_8BIT);
				$mail->setFrom($mailFrom, '3LINX');
				$mail->addTo($data['email'],'password');					
				$mail->setSubject('3LINX password recovery');
				$mail->send($transport);
				$this->view->message = 'Mail send to your email address for password recovery';
			}
		}
	}
		
	/**
	 * This Function is used for password Recovery
	 * @author Muhammad Faisal Siddiq
	*/
	public function recoverpassAction() {
		$this->_helper->layout->disableLayout();				/* disable layout for this Action*/
		$this->view->placeholder('heading')->set('Recover Password');
		$this->view->headTitle('3LINX > Recover Password');
		$this->view->placeholder('buttonCaption')->set('Submit');
		$lengthValid = new Zend_Validate_Between(5,50);
		$varkey = $this->_request->getParam('id');
		if($varkey == "") {
			$this->_redirect('/login/index');
		}			
		$this->view->varkey = $varkey;
		if($this->_request->isPost())
		{
		  $password = $this->_request->getParam('password');
		  $conpassword = $this->_request->getParam('conpassword');
		  if($password != $conpassword) {
			$this->view->message = 'Password and Confirm Password do not Match';
			return;
		  }
		  if($password =="" || $conpassword == "") {
			$this->view->message = 'Value is Required and cant be Empty';
			return;
		  }
		  if(strlen($password)<6) {
			 $this->view->message = 'Password Length must be Greater than 5 characters';
			 return;
		  }
		  $this->_updatePassword($password,$varkey);
		  $this->view->message = 'your password is changed'.' <a href="'.$this->view->baseUrl().'/login/index">Login</a> '.'with your new password';
		}
	}
		
	/**
	 * This Function is used for updating verification key
	 * @author Muhammad Faisal Siddiq
	*/
		
	private function _varKey($email,$key) {
	  $users = new UserModel();
	  $data = array('varification_key'=>$key);
	  $users->update($data,"email = '$email'");	
	}

	 /**
	 * This Function is used for updating password
	 * @author Muhammad Faisal Siddiq
	 */
	 
	 private function _updatePassword($password,$varkey) {
		$users = new UserModel();
		$data = array('password'=>$password);				
		$users->update($data,"varification_key = '$varkey'");
			
	 }
		
		
	 /**
	 * This Action is used For Backgroung image reaizing(accessed via ajax)
	 * @author Muhammad Faisal Siddiq
	 */
		public function resizeAction(){
			$this->_helper->layout->disableLayout();
			$image = $this->_request->getParam('id');
			$image = explode("::",$image);
			if(count($image)>1) {
				include('class/SimpleImage.php');
				$mail_sub = new Zend_Config_Ini('application/config.ini', 'production');
				$base_url = $mail_sub->site_url;
				$obj = new SimpleImage();
				$obj->load($base_url."/common/images/".$image[0]);
				$obj->resize($image[1],$image[2]);
				$obj->save(realpath("")."/common/cache/picture2.jpg");
				echo "success";
			}
			exit;
		}
 }
