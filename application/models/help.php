<?php

class Help extends CI_Model {

    private $_filedArr = array("id","title","content","del");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }
    public function getHelpInfoList($offset = 0,$limit = 20,$del = 0)
    {
        $sql = "select {$this->_getFiledStr()} from `tbl_help` where del = ?  limit ?,?;";
        $query = $this->db->query($sql,array($del,$offset,$limit));
        return $query->result_array();
    }

    public function getHelpInfoCount($del = 0)
    {
        $sql = "select count(1) as cn from `tbl_help` where del = ?;";
        $query = $this->db->query($sql,array($del));
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    public function insertHelpInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_help',$info);
        return $this->db->insert_id();
    }

    public function getHelpInfoByFiled($queryInfo = array())
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
        $sql = "select {$this->_getFiledStr()} from `tbl_help` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    public function updateHelpkInfo($data = array(),$where = array())
    {
        if (empty($data) || !is_array($data) || empty($where) || !is_array($where)) {
            return false;
        }
        $whereArr = array();
        foreach($where as $key => $val) {
            $whereArr[] = "{$key} = {$val}";
        }
        $whereStr = implode(" and ",$whereArr);
        $sql = $this->db->update_string('tbl_help', $data, $whereStr);
        return $this->db->query($sql);
    }
}