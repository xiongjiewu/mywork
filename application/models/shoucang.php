<?php
class Shoucang extends CI_Model {

    private $_filedArr = array("id","infoId","userId","time","del");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function getUserShoucangInfo($uId)
    {
        $uId = intval($uId);
        if (empty($uId)) {
            return false;
        }
        $sql = "select {$this->_getFiledStr()} from `tbl_shoucang` where userId = {$uId} and del = 1;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }
}