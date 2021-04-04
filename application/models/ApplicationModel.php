<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApplicationModel extends CI_Model {

    protected $table_application;
    
    public function __construct()
    {
        parent::__construct();
        $this->table_application    = "application";
    }

    /* ============= APPLICATION START ============= */
    public function application_row($where_arr)
    {
        return $this->db->where($where_arr)->get($this->table_application)->row();
    }

    public function application_insert($insert_data)
    {
        return $this->db->insert($this->table_application,$insert_data);
    }

    public function application_update($where_arr,$update_data)
    {
        return $this->db->where($where_arr)->update($this->table_application,$update_data);
    }
    /* ============= APPLICATION END ============= */
}