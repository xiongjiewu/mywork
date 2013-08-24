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

    /**
     * 更新人物信息
     * @param array $data
     * @param array $where
     * @return bool
     */
    public function updateInfoByFiled($data = array(),$where = array())
    {
        if (empty($data) || !is_array($data) || empty($where) || !is_array($where)) {
            return false;
        }
        $whereArr = array();
        foreach($where as $key => $val) {
            $whereArr[] = "{$key} = {$val}";
        }
        $whereStr = implode(" and ",$whereArr);
        $sql = $this->db->update_string('tbl_character', $data, $whereStr);
        return $this->db->query($sql);
    }

    /**
     * 根据条件，获取人物信息
     * @param string $condition
     * @param $offset
     * @param $limit
     * @param int $del
     * @return bool
     */
    public function getCharacterInfoByCondition($condition = "",$offset,$limit)
    {
        $offset = intval($offset);
        $limit = intval($limit);
        if (!isset($offset) || !isset($limit)) {
            return array();
        }

        $where = "{$condition}";
        $sql = "select * from `tbl_character` where {$where} limit {$offset},$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}