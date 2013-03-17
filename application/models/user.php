<?php
class User extends CI_Model {

    private $_filedArr = array("id","userName","password","time","ip","photo","email");

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
}