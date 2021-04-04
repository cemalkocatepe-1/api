<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Europe/Istanbul');
		$this->load->library('Platformverify');

		$this->load->model('DeviceModel');
		$this->load->model('TokenModel');
		$this->load->model('SubscriptionModel');
		$this->load->model('CallbackModel');
		$this->load->model('ApplicationModel');
	}

	public function index()
	{
		$this->load->view('api');
	}

	public function regiser()
	{
		$response = array(
			"status" => "false",
			"message" => "Error",
			"data" => null,
		);

		if($_SERVER["REQUEST_METHOD"] == "POST"){
			$uid				= $this->input->post('uid');
			$appId				= $this->input->post('appId');
			$language			= $this->input->post('language');
			$operating_system	= $this->input->post('operating_system');
			// $operating_system	= $this->agent->platform(); // CI3 Platform user_agent kütüphanesinden alınabiliyor ancak test için parametre olarak bırakıyorum.

	
			$this->form_validation->set_rules('uid', 'Uid', 'trim|required');
			$this->form_validation->set_rules('appId', 'AppId', 'trim|required|in_list[1,2,3,4,5]');
			$this->form_validation->set_rules('language', 'Language', 'trim|required');
			$this->form_validation->set_rules('operating_system', 'Operating System', 'trim|required');
			
			if ($this->form_validation->run() == FALSE) {
				$str = array("<p>","</p>"); $replace = "";
				$message = array(
					"uid"				=> str_replace($str,$replace,form_error("uid")),
					"appId"				=> str_replace($str,$replace,form_error("appId")),
					"language" 			=> str_replace($str,$replace,form_error("language")),
					"operating_system"	=> str_replace($str,$replace,form_error("operating_system")),
				);
				$response = array(
					"status" => "false",
					"message" => $message,
					"data" => null,
				);
			} else {				
				$row_device = $this->DeviceModel->device_token_row(array("device.uid" => $uid , "device.appId" => $appId));
				if (empty($row_device)) {
					$device_data = array(
						"uid" => $uid,
						"appId" => $appId,
						"language" => $language,
						"operating_system" => $operating_system
					);

					if(! $this->DeviceModel->device_insert($device_data)){
						$response = array(
							"status" => "false",
							"message" => "Hata : DB kayıt sırasında bilinmeyen hata oluştu!",
							"data" => null,
						);
					}else{
						$device_id = $this->db->insert_id();
						$token_row = $this->TokenModel->token_row(array("device_id" => $device_id));

						if(!empty($token_row)){
							$client_token = $token_row->client_token;
						}else{
							$client_token = $this->client_token(uniqid());

							$token_data = array(
								"device_id"		=> $device_id,
								"client_token"	=> $client_token,
								"created_at"	=>	date("Y-m-d H:i:s"),
								"updated_at"	=>	date("Y-m-d H:i:s"),
							);
							$this->TokenModel->token_insert($token_data);
						}

						$data["client_token"] = $client_token;

						$response = array(
							"status" => "true",
							"message" => "OK",
							"data" => $data,
						);
					}
				} else {
					$data["client_token"] = $row_device->client_token;

					$response = array(
						"status" => "true",
						"message" => "OK",
						"data" => $data,
					);
				}
			}
		}else{
			$response = array(
				"status" => "false",
				"message" => "Gönderilen method geçersiz.",
				"data" => null,
			);
		}
		
		$this->response($response);
	}
	
	public function purchase()
	{
		$response = array(
			"status" => "false",
			"message" => "Error",
			"data" => null,
		);
		
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			$client_token	= $this->input->post('client_token');
			$receipt		= $this->input->post('receipt');
	
			$this->form_validation->set_rules('client_token', 'Client Token', 'trim|required');
			$this->form_validation->set_rules('receipt', 'Receipt', 'trim|required');
			
			if ($this->form_validation->run() == FALSE) {
				$str = array("<p>","</p>"); $replace = "";
				$message = array(
					"client_token"	=> str_replace($str,$replace,form_error("client_token")),
					"receipt"		=> str_replace($str,$replace,form_error("receipt")),
				);
				$response = array(
					"status" => "false",
					"message" => $message,
					"data" => null,
				);
			} else {
				$device_row = $this->DeviceModel->device_token_row(array("token.client_token" => $client_token));

				if (empty($device_row)) {
					$response = array(
						"status" => "false",
						"message" => "Hata : Geçersiz client_token gönderildi.",
						"data" => null,
					);
				} else {
					if ($device_row->operating_system == "İOS") {
						$verify_response = $this->platformverify->ios_verify($receipt); // Burada platform doğrulama isteği atılıyor normalde dış bir servis olacağı için curl ile yapılmalı ancak test amaçlı bu şekilde yaptım
					} elseif($device_row->operating_system == "Android") {
						$verify_response = $this->platformverify->google_verify($receipt); // Burada platform doğrulama isteği atılıyor normalde dış bir servis olacağı için curl ile yapılmalı ancak test amaçlı bu şekilde yaptım
					} else {
						$verify_response["status"] = "false";
					}

					if ($verify_response["status"] == "false") {
						$response = array(
							"status" => "false",
							"message" => "Hata : Platform doğrulanamadı.",
							"data" => null,
						);
					} else {
						$subscription_row = $this->SubscriptionModel->subscription_row(array("device_id" => $device_row->device_id , "expired_at <"	=> date("Y-m-d H:i:s")));

						if (!empty($subscription_row)) {
							$subscription_data = array(
								"device_id"		=> $device_row->device_id,
								"receipt"		=> $receipt,
								"is_status"		=> 2, // 1 = started , 2 = renewed , 3 = canceled
								"expired_at"	=> $verify_response["expired_at"],
								"created_at"	=> date("Y-m-d H:i:s"),
								"updated_at"	=> date("Y-m-d H:i:s"),
							);
							if (! $this->SubscriptionModel->subscription_update(array("device_id" => $device_row->device_id),$subscription_data)) {
								$response = array(
									"status" => "false",
									"message" => "Hata : DB kayıt sırasında bilinmeyen hata oluştu!",
									"data" => null,
								);
							} else {								
								$row_application = $this->ApplicationModel->application_row(array("id" => $device_row->appId));

								if (empty($row_application)) {
									$response = array(
										"status" => "false",
										"message" => "Hata : Application bulunamadı!",
										"data" => null,
									);
								} else {
									$callback_data = array(
										"event"			=> "2",
										"is_status"		=> "1",
										"updated_at"	=> date("Y-m-d H:i:s"),
									);
									
									if (! $this->CallbackModel->callback_update(array("device_id" => $device_row->device_id , "appId" => $device_row->appId),$callback_data)) {
										$response = array(
											"status" => "false",
											"message" => "Hata : Callback fonksiyonu için servis db eklenemedi!",
											"data" => null,
										);
									} else {
										$callback = $this->callback($device_row->device_id, $device_row->appId, "2", $row_application->endpoint); // Callback Fonsiyonu

										$response = array(
											"status" => "true",
											"message" => "OK",
											"data" => $verify_response,
										);
									}
								}
							}
						} else {
							$subscription_row = $this->SubscriptionModel->subscription_row(array("device_id" => $device_row->device_id));

							if (!empty($subscription_row)) {
								$response = array(
									"status" => "true",
									"message" => "OK",
									"data" => array(
										"status"		=> "true",
										"expired_at"	=> $subscription_row->expired_at,
									),
								);
							} else {
								$subscription_data = array(
									"device_id"		=> $device_row->device_id,
									"receipt"		=> $receipt,
									"is_status"		=> 1, // 1 = started , 2 = renewed , 3 = canceled
									"expired_at"	=> $verify_response["expired_at"],
									"created_at"	=> date("Y-m-d H:i:s"),
									"updated_at"	=> date("Y-m-d H:i:s"),
								);

								if (! $this->SubscriptionModel->subscription_insert($subscription_data)) {
									$response = array(
										"status" => "false",
										"message" => "Hata : DB kayıt sırasında bilinmeyen hata oluştu!",
										"data" => null,
									);
								} else {
									$row_application = $this->ApplicationModel->application_row(array("id" => $device_row->appId));

									if (empty($row_application)) {
										$response = array(
											"status" => "false",
											"message" => "Hata : Application bulunamadı!",
											"data" => null,
										);
									} else {
										$callback_data = array(
											"device_id"		=> $device_row->device_id,
											"appId"			=> $device_row->appId,
											"event"			=> "1",
											"is_status"		=> "1",
											"created_at"	=> date("Y-m-d H:i:s"),
											"updated_at"	=> date("Y-m-d H:i:s"),
										);
										if (! $this->CallbackModel->callback_insert($callback_data)) {
											$response = array(
												"status" => "false",
												"message" => "Hata : Callback fonksiyonu için servis db eklenemedi!",
												"data" => null,
											);
										} else {
											$callback = $this->callback($device_row->device_id, $device_row->appId, "1", $row_application->endpoint); // Callback Fonsiyonu

											$response = array(
												"status" => "true",
												"message" => "OK",
												"data" => $verify_response,
											);
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			$response = array(
				"status" => "false",
				"message" => "Gönderilen method geçersiz.",
				"data" => null,
			);
		}

		$this->response($response);
	}

	public function check_subsciption()
	{
		$response = array(
			"status" => "false",
			"message" => "Error",
			"data" => null,
		);

		if($_SERVER["REQUEST_METHOD"] == "POST"){
			$client_token	= $this->input->post('client_token');
	
			$this->form_validation->set_rules('client_token', 'Client Token', 'trim|required');
			
			if ($this->form_validation->run() == FALSE) {
				$str = array("<p>","</p>"); $replace = "";
				$message = array(
					"client_token"	=> str_replace($str,$replace,form_error("client_token")),
				);
				$response = array(
					"status" => "false",
					"message" => $message,
					"data" => null,
				);
			} else {
				$row = $this->TokenModel->token_device_subscription_row(array("token.client_token" => $client_token));
				
				if (!empty($row)) {
					$response = array(
						"status" => "true",
						"message" => "OK",
						"data" => $row,
					);
				} else {
					$response = array(
						"status" => "false",
						"message" => "Hata : Kayıtlı abonelik bulunamadı.",
						"data" => null,
					);
				}
			}
		} else {
			$response = array(
				"status" => "false",
				"message" => "Gönderilen method geçersiz.",
				"data" => null,
			);
		}
		$this->response($response);
	}

	public function callback($device_id , $app_id , $event , $url)
	{
		$data = array(
			"deviceId"	=> $device_id,
			"appId"		=> $app_id,
			"event"		=> $event,
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		
		if ($http_code == 200 || $http_code == 201) {
			return true;
		} else {
			$callback_data = array(
				"is_status"		=> "0",
				"updated_at"	=> date("Y-m-d H:i:s"),
			);
			$this->CallbackModel->callback_update(array("device_id" => $device_id),$callback_data);
			return false;
		}
	}

	private function client_token($uid)
	{
		$pass = $uid."+".md5(rand(1,99999));
		$client_token = password_hash($pass , PASSWORD_DEFAULT);
		return $client_token;
	}

	private function response($response)
	{
		header("Content-Type: application/json; charset=UTF-8");
		// header("Access-Control-Max-Age: 600");
		echo json_encode($response);
	}
}