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
        $sql = "select {$this->_getFiledStr()} from `tbl_shoucang` where userId = {$uId} and del = 0;";
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
        $sql = $this->db->update_string('tbl_shoucang', array("del" =>1), $where);
        return $this->db->query($sql);
    }

    public function insertShouCangInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_shoucang',$info);
        return $this->db->insert_id();
    }

    public function getInfoByFiled($queryInfo = array())
    {
        if (empty($queryInfo) || !is_array($queryInfo)) {
            return false;
        }
        $keyArr = $valArr = array();
        foreach($queryInfo as $infoKey => $infoVal) {
            $keyArr[] = "{$infoKey} = ?";
            $valArr[] = $infoVal;
        }
        $keyStr = implode(" and ",$keyArr);
        $sql = "select {$this->_getFiledStr()} from `tbl_shoucang` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }
    public function getInfoCountByFiled($queryInfo = array())
    {
        if (empty($queryInfo) || !is_array($queryInfo)) {
            return false;
        }
        $keyArr = $valArr = array();
        foreach($queryInfo as $infoKey => $infoVal) {
            $keyArr[] = "{$infoKey} = ?";
            $valArr[] = $infoVal;
        }
        $keyStr = implode(" and ",$keyArr);
        $sql = "select count(1) as cn from `tbl_shoucang` where {$keyStr};";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    public function getUserShoucangInfoByInfoIds($uId,$infoIds = array())
    {
        $uId = intval($uId);
        if (empty($uId) || empty($infoIds) || (!is_array($infoIds) && !intval($infoIds))) {
            return false;
        }
        if (!is_array($infoIds)) {
            $infoIds = array($infoIds);
        } else {
            $infoIds = array_unique($infoIds);
        }
        $sql = "select {$this->_getFiledStr()} from `tbl_shoucang` where userId = {$uId} and infoId in (" . implode(",",$infoIds) . ") and del = 0;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }
}