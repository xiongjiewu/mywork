<?php
class Admin extends CI_Model {

    private $_filedArr = array("id","userId","time","type");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function getAdminInfoByUserId($userId) {
        $userId = intval($userId);
        if (empty($userId)) {
            return false;
        }
        $sql = "select {$this->_getFiledStr()} from `tbl_admin` where userId = {$userId};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    public function getUserInfosByIds($userIds = array())
    {
        if (empty($userIds) || !is_array($userIds)) {
            return false;
        }
        $userIds = array_unique($userIds);
        $userIdStr = implode(",",$userIds);
        $sql = "select {$this->_getFiledStr()} from `tbl_admin` where userId in ({$userIdStr});";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
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
        $sql = $this->db->update_string('tbl_admin', $data, $whereStr);
        return $this->db->query($sql);
    }

    public function deleteAdminByUserId($userId){
        $userId = intval($userId);
        if (empty($userId)) {
            return false;
        }
        $sql = "delete from `tbl_admin` where userId = {$userId} limit 1;";
        return $this->db->query($sql);
    }

    public function insertAdminInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_admin',$info);
        return $this->db->insert_id();
    }
}