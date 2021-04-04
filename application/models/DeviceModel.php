<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeviceModel extends CI_Model {

    protected $table_device;

    public function __construct()
    {
        parent::__construct();

        $this->table_device = "device";        
    }

    /* ============= DEVICE START ============= */
    public function device_row($where_arr)
    {
        return $this->db->where($where_arr)->get($this->table_device)->row();
    }

    public function device_insert($insert_data)
    {
        return $this->db->insert($this->table_device,$insert_data);
    }

    public function device_update($where_arr,$update_data)
    {
        return $this->db->where($where_arr)->update($this->table_device,$update_data);
    }
    /* ============= DEVICE END ============= */

    public function device_token_row($where_arr)
    {
        return $this->db
            ->where($where_arr)
            ->join("token" , "token.device_id = device.id")
            ->get($this->table_device)
            ->row();
    }
}