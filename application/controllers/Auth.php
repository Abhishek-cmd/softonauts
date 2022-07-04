<?php defined('BASEPATH') OR exit('No direct script access allowed');
	//
	class Auth extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->database();
			$this->load->model(array('all_model'));
			$this->load->helper(array('common_helper','url','language'));
		}

		public function index(){
			$this->load->view('admin/login');
		}

		public function login()
		{
			if(!empty($_POST)){

				$user_login_data = array(
		    		'username' => $_POST['email'],
		    		'password' => $_POST['password'],
		    		'fcm_id' => ''
		    	);

		    	$CI = & get_instance();
			    $CI->load->library('Curlphp');
			    $curl = new Curlphp();
		    	$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://softonauts.com/clients/Android/users-login?api_key=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.By2r2BwheJsbrEGrHOaMQwrrmlY7wHVFzWtuEmv39fM',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS => $user_login_data,
				));

				$response = curl_exec($curl);
				curl_close($curl);

		        $result = json_decode($response, true);

		        if($result['status'] == 'true'){
		        	$this->session->set_userdata('logged_in', TRUE);
					$this->session->set_userdata('user_id', $result['data']['id']);
					$this->session->set_userdata('user_full_name', $result['data']['first_name'].' '.$result['data']['last_name']);
					$this->session->set_userdata('user_email_id', $result['data']['email']);

		        	$str = json_encode($user_login_data);

		        	$some_data = [
						'section' => 'Login Request',
						'url' => LOGIN_USER,
						'method' => 'POST',
						'request' => $str,
						'response' => $response,
						'response_time' => '',
						'created_at' => date('Y-m-d H:i:s')
					];

					$this->all_model->insert('webservice_request_response_data', $some_data);
					$this->session->set_flashdata('success', 'Login user successfully');	
	                redirect('auth/dashboard','refresh');
				}else{
					$this->session->set_flashdata('error', 'Error Occurred');	
	                redirect('auth/login','refresh');
				}
			}else{
				$this->load->view('admin/login');
			}			
		}

		public function register(){
			if(!empty($_POST)){			
		    	$fullname = explode(" ", $_POST['fullname']);
		    	$first_name = !empty($fullname[0]) ? ucfirst($fullname[0]) : '';
		    	$middle_name = !empty($fullname[1]) ? ucfirst($fullname[1]) : '';
		    	$last_name = !empty($fullname[2]) ? ucfirst($fullname[2]) : '';
		    	$emailaddress = $_POST['emailaddress'];
		    	$password = $_POST['password'];

		    	$user_register_data = array(
		    		'first_name' => $first_name,
		    		'middle_name' => $middle_name,
		    		'last_name' => $last_name,
		    		'dob' => '09/09/2020',
		    		'gender' => 'Male',
		    		'contact_number' => '7698769878',
		    		'email' => $emailaddress,
		    		'address_one' => '1866 Deer Ridge Drive',
		    		'address_two' => '',
		    		'city' => 'Piscataway',
		    		'state' => 'NJ',
		    		'zipcode' => '08854',
		    		'password' => $password,
		    		'login_type' => 'internal',
		    		'ssn_digits' => '4554'
		    	);

		    	$CI = & get_instance();
			    $CI->load->library('Curlphp');
			    $curl = new Curlphp();
		    	$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => REGISTER_USER,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS => $user_register_data,
				  CURLOPT_HTTPHEADER => array('Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.By2r2BwheJsbrEGrHOaMQwrrmlY7wHVFzWtuEmv39fM'),
				));

				$response = curl_exec($curl);

		        $result = json_decode($response, true);

		        if($result['status'] == 'true'){
		        	$str = json_encode($user_register_data);
		        	$some_data = [
						'section' => 'Register Request',
						'url' => REGISTER_USER,
						'method' => 'POST',
						'request' => $str,
						'response' => $response,
						'response_time' => '',
						'created_at' => date('Y-m-d H:i:s')
					];

					$this->all_model->insert('webservice_request_response_data', $some_data);
					$insert_id = $this->all_model->insert('tbl_users', $user_register_data);
					$this->session->set_flashdata('success', 'Register user successfully');	
	                redirect('auth/login','refresh');
				}else{
					$this->session->set_flashdata('error', 'Error Occurred');	
	                redirect('auth/register','refresh');
				}
			}else{
				$this->load->view('admin/register');

			}			
		}

		public function dashboard(){
			$this->load->view('admin/dashboard');
		}

		// log the user out
	    public function logout()
		{
			$this->data['title'] = "Logout";
			// $logout = $this->ion_auth->logout();
			// unSetConnectionSession();
			session_destroy();
			$this->session->set_flashdata('success', 'You have successfully logged out!');
			redirect('auth/login');
		}

		public function drop_in_center(){
			$CI = & get_instance();
		    $CI->load->library('Curlphp');
		    $curl = new Curlphp();
	    	$curl = curl_init();

	    	$user_login_data = array(
	    		'user_id' => $_SESSION['user_id']
	    	);

	    	$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => NAVIGATION_LIST,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $user_login_data,
			  CURLOPT_HTTPHEADER => array('Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.By2r2BwheJsbrEGrHOaMQwrrmlY7wHVFzWtuEmv39fM'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);

	        $result = json_decode($response, true);

	        if($result['status'] == 'true'){
	            $str = json_encode($user_login_data);	        	
	        	$some_data = [
					'section' => 'Navigation slot request',
					'url' => NAVIGATION_LIST,
					'method' => 'POST',
					'request' => $str,
					'response' => $response,
					'response_time' => '',
					'created_at' => date('Y-m-d H:i:s')
				];

				$this->all_model->insert('webservice_request_response_data', $some_data);

	        	$data['navigator_list'] = $result['drop_in_navigator_list'];

	        	// echo 1;exit;
	        }else{
	        	$data['navigator_list'] = [];
	        	// echo 0;exit;
	        }

			$this->load->view('admin/drop_in_center', $data);
		}

		public function get_time_slots(){
			$location_data = array(
	    		'location_id' => $_POST['location_id']
	    	);

	    	$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => TIME_SLOTS,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $location_data,
			  CURLOPT_HTTPHEADER => array('Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.By2r2BwheJsbrEGrHOaMQwrrmlY7wHVFzWtuEmv39fM'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);

	        $result = json_decode($response, true);

	        // echo "<pre>";
	        // print_r($result);exit;

	        if($result['status'] == 'true'){
	        	$str = json_encode($location_data);
	        	$some_data = [
					'section' => 'Time Slot Request',
					'url' => TIME_SLOTS,
					'method' => 'POST',
					'request' => $str,
					'response' => $response,
					'response_time' => '',
					'created_at' => date('Y-m-d H:i:s')
				];

				$this->all_model->insert('webservice_request_response_data', $some_data);

	        	//$data['timeslots'] = $result['timeslots'];

	        	$test = '';

	        	if(!empty($result['timeslots'])){

	        		$i=0;
	        		$test .="<ul role='tablist'>";
                    foreach($result['timeslots'] as $key => $timeslot){
                    	$i++;

                    	$test .="
                            <li role='tab'><a href='#tabs-".$i."' class='text-center' role='presentation'>".$timeslot['show_date']."
                                    <br>
                                    <small class='fdc'>" .$timeslot['count']." slot available</small></a>
                            </li>";
                    }
                    $test .="</ul>";

                    $j=0;
                    foreach($result['timeslots'] as $timeslot_value){
                    	$j++;

	                    if($timeslot_value['count'] == 0){
	                    	$test .="
	                    	    <div>
			                        <div id='tabs-".$j."' role='tabpanel'>
			                            <div class='col-md-12 text-center mb-4'>
			                                <b>".$timeslot_value['show_date']."</b>
			                            </div>

			                            <div class='col-md-12 mb-2'><small>No slots Available</small></div>
			                        </div>
		                        </div>";
	                    }else{

	                    	// echo "<pre>";
                    		// print_r($key);
                    		// echo "<pre>";
                    		// print_r($timeslot_value);exit;

	                    	$test .="
	                    	    <div>
			                        <div id='tabs-".$j."' role='tabpanel'>
			                            <div class='col-md-12 text-center mb-4'>
			                                <b>".$timeslot_value['show_date']."</b>
			                            </div>
			                        </div>";

		                    	foreach($timeslot_value['slotes'] as $keys => $slotes){
		                    		// echo "<pre>";
		                    		// print_r($key);
		                    		// echo "<pre>";
		                    		// print_r($slotes);exit;

		                    		if(!empty($keys)){
		                    			$test .="
					                        <div class='col-md-12 mb-2'>
					                            <b>".ucfirst($keys).":</b> <small>".sizeof($slotes)." slots available</small>
					                        </div>";


					                    if(sizeof($slotes)>0){

					                    	$test.="<div class='col-md-12 mb-3'>";

											for($k=0;$k<sizeof($slotes);$k++){

												// echo $slotes[$k];

												$slot_val = str_replace(" ",'_',$slotes[$k]);
												// echo $slot_val;

												$url = base_url()."auth/appointment_details?user_id=".$_SESSION['user_id']."&event_id=".$_POST['location_id']."&date=".$timeslot_value['current_date']."&appiontment=".$slot_val;

												if($slotes[$k] == 'Booked'){
													$test .="<a href='javascript:void(0);'>
		                                                <button class='btn btn-sm btn-outline-primary mr-2'>$slotes[$k]</button></a>";
												}else{
													$test .="<a href=".$url.">
		                                                <button class='btn btn-sm btn-outline-primary mr-2'>$slotes[$k]</button></a>
		                                            ";
												}												

		                                        if($k == 6){
		                                        	$test .="<br/><br/>";
		                                        }else if($k == 12){
		                                        	$test .="<br/><br/>";
		                                        }
											}

											$test.="<br/></div>";
										}
		                    		}else{
		                    			$test .="
					                        <div class='col-md-12 mb-3'>
					                            <span>No slots Available</span>
					                        </div>";
		                    		}		                    	
			                    }

		                    $test .="</div>";
                        }                    
	        	    }
	        	}

                echo $test;
	        	// $this->load->view('admin/drop_in_center', $data);
	        	exit;
	        }else{
	        	$data['timeslots'] = [];
	        	// $this->load->view('admin/drop_in_center', $data);
	        	// echo 0;exit;
	        }

	        // $this->load->view('admin/drop_in_center', $data);
		}

		public function appointment_details(){
			// echo "<pre>";
			// print_r($_GET);exit;

			$data['user_id'] = $_GET['user_id'];
			$data['event_id'] = $_GET['event_id'];
			$data['date'] = str_replace('.', '/', $_GET['date']);
			$data['appiontment'] = str_replace('_', ' ', $_GET['appiontment']);

			$user_login_data = array(
	    		'user_id' => $data['user_id']
	    	);

	    	$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => NAVIGATION_LIST,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $user_login_data,
			  CURLOPT_HTTPHEADER => array('Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.By2r2BwheJsbrEGrHOaMQwrrmlY7wHVFzWtuEmv39fM'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);

	        $result = json_decode($response, true);

	        if($result['status'] == 'true'){

	        	// echo "<pre>";
	        	// print_r($result['drop_in_navigator_list']);exit;

	        	if(!empty($result['drop_in_navigator_list'])){
	        		$i=1;
                    foreach($result['drop_in_navigator_list'] as $key => $value){
                    	// $data['appointments'] = $value['appointment_type'][$key]['appointment_type'];

                    	
                    	// echo "<pre>";
                    	// print_r($data);exit;
                    	
                    	$i++;

                    	foreach($value['location_id'] as $keys => $location_id){

                    		if($location_id == $data['event_id']){

                    			// $data['appointments'] = $value['appointment_type'][$key]['appointment_type'];

                    			$navigator_name = $value['navigator_name'];
	                    	    $data['navigator_name'] = $navigator_name;

	                            $navigator_location = $value['navigator_location'][$keys];                           

	                            $location_id = $value['location_id'][$keys];



	                            $data['location_id'] = $location_id;

	                            if($location_id == $data['event_id']){
	                            	$data['location_place'] = $navigator_location;                            	
	                            }
                    		}                    		
                        }    

                        // exit;                 

                    }
	        	}

	        }

	        // echo "<pre>";
	        // print_r($data);exit;

			$this->load->view('admin/appointment_details', $data);
		}

		public function add_appointment(){
			if(!empty($_POST)){
				$data = array(
					"user_id" => $_POST['user_id'],
					"event_id" => $_POST['event_id'],
					"date" => $_POST['date'],
					"appiontment" => $_POST['appiontment'],
					"appiontment_type" => $_POST['appointment_type'],
					"created_at" => date("Y-m-d")
				);

				$this->all_model->insert('appointment_details', $data);
				$this->session->set_flashdata('success', 'Appointment confirmed successfully');	
	            redirect('auth/dashboard','refresh');
			}
		}


	}
?>