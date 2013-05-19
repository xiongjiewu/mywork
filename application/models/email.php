<?php
class Email extends CI_Model {

    private $_filedArr = array("id","title","content","email","status");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function insertEmailInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_emailQueue',$info);
        return $this->db->insert_id();
    }
}