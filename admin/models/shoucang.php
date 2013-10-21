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

    public function updateUserShouCangInfoById($uId,$idArr = array())
    {
        $uId = intval($uId);
        if (empty($uId) || empty($idArr)) {
            return false;
        }
        $idArr = array_unique($idArr);
        $idStr = implode(",",$idArr);
        $where = "id in ({$idStr}) and userId = {$uId}";
        $sql = $this->db->update_string('tbl_shoucang', array("del" =>0), $where);
        return $this->db->query($sql);
    }
}