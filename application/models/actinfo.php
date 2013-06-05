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
            return false;
        }
        $sql = "select * from `tbl_actInfo` where name = ? and del = 0;";
        $query = $this->db->query($sql,array($actinName));
        $result = $query->result_array();
        return $result;
    }
}