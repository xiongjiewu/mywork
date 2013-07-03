<?php
/**
 * 人物查询主要model
 * added by xiongjiewu at 2013-06-30
 * Class Backgroundadmin
 */
class Character extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 根据条件获取人物信息
     * @param $conditionStr
     * @return bool
     */
    public function getCharacterInfoByCon($conditionStr) {
        if (empty($conditionStr)) {
            return false;
        }
        $sql = "select * from `tbl_character` where {$conditionStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    /**
     * 根据条件获取人物总数
     * @param $conditionStr
     * @return bool
     */
    public function getCharacterCountByCon($conditionStr) {
        if (empty($conditionStr)) {
            return false;
        }
        $sql = "select count(1) as cn from `tbl_character` where {$conditionStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }
}