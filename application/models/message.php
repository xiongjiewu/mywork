<?php
class Message extends CI_Model {

    private $_filedArr = array("id","userId","time","content","del","is_read");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function insertMessageInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_message',$info);
        return $this->db->insert_id();
    }

    public function getMessageInfoByFiled($queryInfo = array())
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
        $sql = "select {$this->_getFiledStr()} from `tbl_message` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    public function getMessageListByFiled($queryInfo = array(),$offset = 0,$limit = 10)
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
        $sql = "select {$this->_getFiledStr()} from `tbl_message` where {$keyStr} order by time desc limit {$offset},{$limit};";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function getMessageCountByFiled($queryInfo = array())
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
        $sql = "select count(1) as cn from `tbl_message` where {$keyStr};";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    public function getMessageInfoByIds($uid,$ids = array())
    {
        $uid = intval($uid);
        if (empty($uid) || empty($ids) || !is_array($ids)) {
            return false;
        }
        $ids = array_unique($ids);
        $idStr = implode(",",$ids);
        $sql = "select {$this->_getFiledStr()} from `tbl_message` where userId = ? and id in ({$idStr});";
        $query = $this->db->query($sql,array($uid));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function updateUserMessageInfoById($uId,$idArr = array(),$data = array())
    {
        $uId = intval($uId);
        if (empty($uId) || empty($idArr) || empty($data) || !is_array($data)) {
            return false;
        }
        $idArr = array_unique($idArr);
        $idStr = implode(",",$idArr);
        $where = "id in ({$idStr}) and userId = {$uId}";
        $sql = $this->db->update_string('tbl_message', $data, $where);
        return $this->db->query($sql);
    }
}