<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CallbackModel extends CI_Model {

    protected $table_callback;
    
    public function __construct()
    {
        parent::__construct();
        $this->table_callback   = "callback";
    }

    /* ============= CALLBACK START ============= */
    public function callback_row($where_arr)
    {
        return $this->db->where($where_arr)->get($this->table_callback)->row();
    }

    public function callback_insert($insert_data)
    {
        return $this->db->insert($this->table_callback,$insert_data);
    }

    public function callback_update($where_arr,$update_data)
    {
        return $this->db->where($where_arr)->update($this->table_callback,$update_data);
    }
    /* ============= CALLBACK END ============= */
}