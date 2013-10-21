<?php

class Movietopic extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 插入专题信息
     * @param array $info
     * @return bool
     */
    public function insertTopicInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_movieTopic',$info);
        return $this->db->insert_id();
    }

    /**
     * 获取全部专题信息
     * @return mixed
     */
    public function getTopicInfoList($topicType = 1)
    {
        $sql = "select * from `tbl_movieTopic` where topicType = {$topicType} and del = 0;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 获取专题信息
     * @return mixed
     */
    public function getTopicInfoListLimit($topicType = 1,$offset = 0,$limit = 10)
    {
        $sql = "select * from `tbl_movieTopic` where topicType = {$topicType} and del = 0";
        $sql .= " limit {$offset},{$limit}";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getTopicInfoListCount($topicType = 1)
    {
        $sql = "select count(1) as cn from `tbl_movieTopic` where topicType = {$topicType} and del = 0;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    /**
     * 更新专题/系列信息
     * @param $id
     * @param array $data
     * @return bool
     */
    public function updateTopicInfo($id,$data = array())
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        if (empty($data)) {
            return false;
        }
        $where = "id = {$id}";
        $sql = $this->db->update_string('tbl_movieTopic', $data, $where);
        return $this->db->query($sql);
    }

    /**
     * 获取专题/系列信息
     * @return mixed
     */
    public function getTopicMovieInfo($topicId)
    {
        $topicId = intval($topicId);
        if (empty($topicId)) {
            return false;
        }
        $sql = "select * from `tbl_movieTopic` where id = ? and del = 0 limit 1;";
        $query = $this->db->query($sql,array($topicId));
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    /**
     * 根据名字搜索专题/系列
     * @param $searchW
     * @param int $limit
     * @param int $del
     * @return bool
     */
    public function getTopicInfoBySearchW($searchW,$topicType =1 ,$limit=10,$del = 0)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select * from tbl_movieTopic where name like '{$searchW}%' and topicType = {$topicType} and del = {$del} limit {$limit};";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}