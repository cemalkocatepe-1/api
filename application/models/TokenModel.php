<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TokenModel extends CI_Model {

    protected $table_token;
    
    public function __construct()
    {
        parent::__construct();
        $this->table_token  = "token";
    }

    /* ============= TOKEN START ============= */
    public function token_row($where_arr)
    {
        return $this->db->where($where_arr)->get($this->table_token)->row();
    }

    public function token_insert($insert_data)
    {
        return $this->db->insert($this->table_token,$insert_data);
    }

    public function token_update($where_arr,$update_data)
    {
        return $this->db->where($where_arr)->update($this->table_token,$update_data);
    }
    /* ============= TOKEN END ============= */

    public function token_device_subscription_row($where_arr)
    {
        return $this->db
            ->select("device.uid,device.appId,subscription.is_status,subscription.expired_at")
            ->where($where_arr)
            ->join("device" , "device.id = token.device_id")
            ->join("subscription" , "subscription.device_id = token.device_id")
            ->get($this->table_token)
            ->row();
    }
}