<?php
class Notice extends CI_Model {

    private $_filedArr = array("id","userId","infoId","time","type","reply","del");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function insertNoticeInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_notice',$info);
        return $this->db->insert_id();
    }

    public function getNoticeInfoByFiled($queryInfo = array())
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
        $sql = "select {$this->_getFiledStr()} from `tbl_notice` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    public function getNoticeListByFiled($queryInfo = array(),$offset = 0,$limit = 10)
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
        $sql = "select {$this->_getFiledStr()} from `tbl_notice` where {$keyStr} limit {$offset},{$limit};";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function getNoticeCountByFiled($queryInfo = array())
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
        $sql = "select count(1) as cn from `tbl_notice` where {$keyStr};";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    public function getNoticeInfoByInfoIds($uid,$ids = array(),$reply = 0,$del = 0)
    {
        $uid = intval($uid);
        if (empty($uid) || empty($ids) || !is_array($ids)) {
            return false;
        }
        $ids = array_unique($ids);
        $idStr = implode(",",$ids);
        $sql = "select {$this->_getFiledStr()} from `tbl_notice` where userId = ? and infoId in ({$idStr}) and reply = ? and del = ?;";
        $query = $this->db->query($sql,array($uid,$reply,$del));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function updateUserNoticeInfoById($uId,$idArr = array())
    {
        $uId = intval($uId);
        if (empty($uId) || empty($idArr)) {
            return false;
        }
        $idArr = array_unique($idArr);
        $idStr = implode(",",$idArr);
        $where = "id in ({$idStr}) and userId = {$uId}";
        $sql = $this->db->update_string('tbl_notice', array("del" =>1), $where);
        return $this->db->query($sql);
    }
}