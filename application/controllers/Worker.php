<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worker extends CI_Controller {

    
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
    

    public function cron_subsciption_update()
	{
		$response = array(
			"status" => "false",
			"message" => "Error",
			"data" => null,
		);

		$row_cron_list = $this->SubscriptionModel->subscription_device_result(array("subscription.is_status !=" => "3","subscription.expired_at <" => date("Y-m-d H:i:s")));
		
		if (!empty($row_cron_list) && count($row_cron_list) > 0) {

			foreach ($row_cron_list as $key => $item) {
				if ($item->operating_system == "İOS") {
					$rate_limit_verify = (substr($item->receipt, -2 , 2) % 2 == 0 && substr($item->receipt, -2 , 2) % 3 == 0) ? true : false; // rate-limit durumu için kontrol yapılıyor
					/* 
						Burada şöyle bir mantık hatası oluyor bir sayı hem 6 bölünüp
						hemde son rakamı tek sayı olamadığı için cron burada çalışmıyor.
						Ancak dökümanda bu şekilde yazıldığı için mantığı buraya bu şekilde kurdum dilerseniz aşağıdaki veya kısmını açabilirisniz.
					*/
					if ($rate_limit_verify == true /*|| 1==1*/) {
						$verify_response = $this->platformverify->ios_verify($item->receipt); // Burada platform doğrulama isteği atılıyor normalde dış bir servis olacağı için curl ile yapılmalı ancak test amaçlı bu şekilde yaptım
					} else {
						$verify_response["status"] = "false";
					}
				} elseif($item->operating_system == "Android") {										
					$rate_limit_verify = (substr($item->receipt, -2 , 2) % 2 == 0 && substr($item->receipt, -2 , 2) % 3 == 0) ? true : false; // rate-limit durumu için kontrol yapılıyor
					/* 
						Burada şöyle bir mantık hatası oluyor bir sayı hem 6 bölünüp 
						hemde son rakamı tek sayı olamadığı için cron burada çalışmıyor.
						Ancak dökümanda bu şekilde yazıldığı için mantığı buraya bu şekilde kurdum dilerseniz aşağıdaki veya kısmını açabilirisniz.
					*/
					if ($rate_limit_verify == true /*|| 1==1*/) {
						$verify_response = $this->platformverify->google_verify($item->receipt); // Burada platform doğrulama isteği atılıyor normalde dış bir servis olacağı için curl ile yapılmalı ancak test amaçlı bu şekilde yaptım
					} else {
						$verify_response["status"] = "false";
					}
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
					$subscription_data = array(
						"is_status"		=> 3, // 1 = started , 2 = renewed , 3 = canceled
						"updated_at"	=> date("Y-m-d H:i:s"),
					);
					
					if (! $this->SubscriptionModel->subscription_update(array("device_id" => $item->device_id),$subscription_data)) {
						$response = array(
							"status" => "false",
							"message" => "Hata : DB kayıt sırasında bilinmeyen hata oluştu!",
							"data" => null,
						);
					} else {
						$row_application = $this->ApplicationModel->application_row(array("id" => $item->appId));

						if (empty($row_application)) {
							$response = array(
								"status" => "false",
								"message" => "Hata : Application bulunamadı!",
								"data" => null,
							);
						} else {
							$callback_data = array(
                                "event"			=> "3",
								"is_status"		=> "1",
								"updated_at"	=> date("Y-m-d H:i:s"),
							);
							
							if (! $this->CallbackModel->callback_update(array("device_id" => $item->device_id , "appId" => $item->appId),$callback_data)) {
								$response = array(
									"status" => "false",
									"message" => "Hata : Callback fonksiyonu için servis db eklenemedi!",
									"data" => null,
								);
							} else {
								$callback = $this->callback($item->device_id, $item->appId, "3", $row_application->endpoint); // Callback Fonsiyonu
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
		} else {
			$response = array(
				"status" => "false",
				"message" => "Uyarı : Güncelleme işlemi için abone listesi bulunamadı.",
				"data" => null,
			);
		}

		$this->response($response);
	}

    private function response($response)
	{
		header("Content-Type: application/json; charset=UTF-8");
		// header("Access-Control-Max-Age: 600");
		echo json_encode($response);
	}

    public function callback($device_id , $app_id , $event , $url)
	{
		$data = array(
			"deviceId"	=> $device_id,
			"appId"		=> $app_id,
			"event"     => $event,
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
}