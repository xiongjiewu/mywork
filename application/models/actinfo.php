<?php
class Actinfo extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 根据名称搜索演员与电影关联信息
     * @param $actinName
     * @return bool
     */
    public function getActinfoByActinName($actinName) {
        $actinName = trim($actinName);
        if (empty($actinName)) {
            return array();
        }
        $sql = "select * from `tbl_actInfo` where name = ? and del = 0;";
        $query = $this->db->query($sql,array($actinName));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    /**
     * 根据名称拼音搜索演员与电影关联信息
     * @param $actinName
     * @return bool
     */
    public function getActinfoByActinPinYin($actinName) {
        $actinName = trim($actinName);
        if (empty($actinName)) {
            return array();
        }
        $sql = "select * from `tbl_actInfo` where pinyin = ? and del = 0;";
        $query = $this->db->query($sql,array($actinName));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    /**
     * 根据名称搜索演员与电影关联信息
     * @param $actinName
     * @return bool
     */
    public function getActinfoByActinNameLimit($actinName,$offeset = 0,$limit = 10) {
        $actinName = trim($actinName);
        if (empty($actinName)) {
            return array();
        }
        $sql = "select * from `tbl_actInfo` where name = ? and del = 0 limit {$offeset},{$limit};";
        $query = $this->db->query($sql,array($actinName));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }
}