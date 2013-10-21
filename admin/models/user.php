<?php
class User extends CI_Model {

    private $_filedArr = array("id","userName","password","time","ip","photo","email","status");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function getUserInfoByFiled($queryInfo = array())
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
        $sql = "select {$this->_getFiledStr()} from `tbl_user` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    public function insertUserInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_user',$info);
        return $this->db->insert_id();
    }

    public function updateUserInfo($data = array(),$where = array())
    {
        if (empty($data) || !is_array($data) || empty($where) || !is_array($where)) {
            return false;
        }
        $whereArr = array();
        foreach($where as $key => $val) {
            $whereArr[] = "{$key} = {$val}";
        }
        $whereStr = implode(" and ",$whereArr);
        $sql = $this->db->update_string('tbl_user', $data, $whereStr);
        return $this->db->query($sql);
    }

    public function getUserInfosByIds($userIds = array())
    {
        if (empty($userIds) || !is_array($userIds)) {
            return false;
        }
        $userIds = array_unique($userIds);
        $userIdStr = implode(",",$userIds);
        $sql = "select {$this->_getFiledStr()} from `tbl_user` where id in ({$userIdStr});";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function getUserList($offset = 0,$limit = 10) {
        $sql = "select {$this->_getFiledStr()} from `tbl_user` limit {$offset},{$limit};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function getUserCount() {
        $sql = "select count(1) as cn from `tbl_user`;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    public function getUserInfoBySearchW($searchW)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select {$this->_getFiledStr()} from tbl_user where userName = '{$searchW}' limit 1;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}