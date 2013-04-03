<?php
class Addlink extends CI_Model {

    private $_filedArr = array("id","infoId","type","link","time");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function insertUserInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_userGive',$info);
        return $this->db->insert_id();
    }
}