<?php
class Directorinfo extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 根据名称搜索导演与电影关联信息
     * @param $actinName
     * @return bool
     */
    public function getDirectorinfoByDirectorName($directorName) {
        $directorName = trim($directorName);
        if (empty($directorName)) {
            return false;
        }
        $sql = "select * from `tbl_directorInfo` where name = ? and del = 0;";
        $query = $this->db->query($sql,array($directorName));
        $result = $query->result_array();
        return $result;
    }
}