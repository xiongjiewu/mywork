<?php
/**
 * 第三方登录接口model
 * Class Appuser
 */
class Appuser extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 根据条件数组获取用户信息
     * @param $actinName
     * @return bool
     */
    public function getAppuserInfoByFiled($queryInfo = array())
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
        $sql = "select * from `tbl_appUser` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    /**
     * 根据key和type获取用户信息
     * @param $key
     * @param $type
     * @return bool
     */
    public function getAppUserInfoByKeyAndType($key,$type) {
        $key = trim($key);
        $type = intval($type);
        if (empty($type) || empty($type)) {
            return false;
        }
        return $this->getAppuserInfoByFiled(array("appKey"=>$key,"type"=>$type));
    }

    /**
     * 插入用户信息
     * @param array $info
     * @return array
     */
    public function insertAppuserInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_appUser',$info);
        return $this->db->insert_id();
    }

    public function updateAppUserInfo($data = array(),$where = array())
    {
        if (empty($data) || !is_array($data) || empty($where) || !is_array($where)) {
            return false;
        }
        $whereArr = array();
        foreach($where as $key => $val) {
            $whereArr[] = "{$key} = {$val}";
        }
        $whereStr = implode(" and ",$whereArr);
        $sql = $this->db->update_string('tbl_appUser', $data, $whereStr);
        return $this->db->query($sql);
    }

}