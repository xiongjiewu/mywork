<?php
class Userviewingrecords extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insertUserViewingRecordsInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_userViewingRecords',$info);
        return $this->db->insert_id();
    }

    /** 获取电影列表
     * @param int $offset
     * @param int $limit
     * @param int $del
     * @param bool $desc
     * @return mixed
     */
    public function getUserViewingRecordsLastInfo($userId,$infoId)
    {
        $userId = intval($userId);
        $infoId = intval($infoId);
        if (empty($userId) || empty($infoId)) {
            return false;
        }

        $sql = "select * from `tbl_userViewingRecords` where userId = ? and infoId = ? order by createTime desc limit 1;";
        $query = $this->db->query($sql,array($userId,$infoId));
        $info = $query->result_array();
        return empty($info[0]) ? array() : $info[0];
    }
}