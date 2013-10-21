<?php
class Usergive extends CI_Model {

    private $_filedArr = array("id","infoId","type","link","time","del");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function getUserGiveInfoByFiled($queryInfo = array())
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
        $sql = "select {$this->_getFiledStr()} from `tbl_userGive` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    public function getUserGiveListByFiled($queryInfo = array(),$offset = 0,$limit = 10)
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
        $sql = "select {$this->_getFiledStr()} from `tbl_userGive` where {$keyStr} limit {$offset},{$limit};";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function getUserGiveCountByFiled($queryInfo = array())
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
        $sql = "select count(1) as cn from `tbl_userGive` where {$keyStr};";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    public function getUserGiveInfoByIds($uid,$ids = array(),$del = 0)
    {
        $uid = intval($uid);
        if (empty($uid) || empty($ids) || !is_array($ids)) {
            return false;
        }
        $ids = array_unique($ids);
        $idStr = implode(",",$ids);
        $sql = "select {$this->_getFiledStr()} from `tbl_message` where userId = ? and id in ({$idStr}) and del = ?;";
        $query = $this->db->query($sql,array($uid,$del));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function updateUserGiveInfoById($idArr = array(),$data = array())
    {
        if (empty($idArr) || empty($data) || !is_array($data)) {
            return false;
        }
        $idArr = array_unique($idArr);
        $idStr = implode(",",$idArr);
        $where = "id in ({$idStr})";
        $sql = $this->db->update_string('tbl_userGive', $data, $where);
        return $this->db->query($sql);
    }
}