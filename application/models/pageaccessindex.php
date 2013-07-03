<?php
class Pageaccessindex extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insertPageIndexInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_pageAccessIndex',$info);
        return $this->db->insert_id();
    }
}