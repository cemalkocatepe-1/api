<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SubscriptionModel extends CI_Model {

    protected $table_subscription;
    
    public function __construct()
    {
        parent::__construct();
        $this->table_subscription   = "subscription";
    }    

    /* ============= SUBSCRIPTION START ============= */
    public function subscription_row($where_arr)
    {
        return $this->db->where($where_arr)->get($this->table_subscription)->row();
    }

    public function subscription_insert($insert_data)
    {
        return $this->db->insert($this->table_subscription,$insert_data);
    }

    public function subscription_update($where_arr,$update_data)
    {
        return $this->db->where($where_arr)->update($this->table_subscription,$update_data);
    }
    /* ============= SUBSCRIPTION END ============= */

    public function subscription_device_result($where_arr)
    {
        return $this->db
            ->select("subscription.device_id,device.appId,device.operating_system,subscription.receipt")
            ->where($where_arr)
            ->join("device" , "device.id = subscription.device_id")
            ->get($this->table_subscription)
            ->result();
    }
}