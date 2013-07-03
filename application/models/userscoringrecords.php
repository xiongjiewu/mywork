<?php
class Userscoringrecords extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 根据条件获取用户打分信息
     * @param $conditionStr
     * @return bool
     */
    public function getUserscoringrecordsInfoByCon($conditionStr) {
        if (empty($conditionStr)) {
            return false;
        }
        $sql = "select * from `tbl_userScoringRecords` where {$conditionStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    /**
     * 根据条件获取用户打分信息总数
     * @param $conditionStr
     * @return bool
     */
    public function getUserscoringrecordsCountByCon($conditionStr) {
        if (empty($conditionStr)) {
            return false;
        }
        $sql = "select count(1) as cn from `tbl_userScoringRecords` where {$conditionStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    /**
     * 插入用户打分信息
     * @param array $info
     * @return bool
     */
    public function insertUserscoringrecordsInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_userScoringRecords',$info);
        return $this->db->insert_id();
    }
}