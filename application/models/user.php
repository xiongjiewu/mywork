<?php
class User extends CI_Model {
    function __construct()
    {
        parent::__construct();
        $this->load->database();
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
        $sql = "select * from `tbl_user` where {$keyStr} limit 1;";
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
        $whereArr[] = "status = 0";
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
        $sql = "select * from `tbl_user` where id in ({$userIdStr}) and status = 0;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }
}