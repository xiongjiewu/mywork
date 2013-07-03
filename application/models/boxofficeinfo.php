<?php
/**
 * 票房榜class
 * added by xiongjiewu at 2013-06-28
 * Class Boxofficeinfo
 */
class Boxofficeinfo extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 根据条件获取排行榜信息
     * @param $condition
     * @return bool
     */
    public function getBoxofficeInfoByCondition($condition) {
        $where = "";
        if (!empty($condition)) {
            $where = " where {$condition}";
        }
        $sql = "select * from `tbl_boxOfficeInfo` " . $where . ";";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }
}