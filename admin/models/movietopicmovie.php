<?php

class Movietopicmovie extends CI_Model {

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
    public function insertTopicMovieInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_movieTopicMovie',$info);
        return $this->db->insert_id();
    }

    /**
     * 获取专题电影信息
     * @return mixed
     */
    public function getTopicMovieInfo($infoId,$topicId,$movieType = 1)
    {
        $infoId = intval($infoId);
        $topicId = intval($topicId);
        if (empty($infoId) || empty($topicId)) {
            return false;
        }
        $sql = "select * from `tbl_movieTopicMovie` where infoId = ? and topicId = ? and movieType = ? and del = 0 limit 1;";
        $query = $this->db->query($sql,array($infoId,$topicId,$movieType));
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    /**
     * 获取全部专题电影信息
     * @return mixed
     */
    public function getTopicMovieListByTopicId($topicId,$offset = 0,$limit = 10,$movieType = 1,$status = -1)
    {
        $topicId = intval($topicId);
        if (empty($topicId)) {
            return false;
        }
        $sql = "select * from `tbl_movieTopicMovie` where topicId = ? and movieType = ? and del = 0";
        if (($status == 0) || ($status == 1)) {
            $sql .= " and status = {$status}";
        }
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql,array($topicId,$movieType));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    /**
     * 获取全部专题电影信息总数
     * @return mixed
     */
    public function getTopicMovieListCountByTopicId($topicId,$movieType = 1,$status = -1)
    {
        $topicId = intval($topicId);
        if (empty($topicId)) {
            return false;
        }
        $sql = "select count(1) as cn from `tbl_movieTopicMovie` where topicId = ? and movieType = ? and del = 0";
        if (($status == 0) || ($status == 1)) {
            $sql .= " and status = {$status}";
        }
        $query = $this->db->query($sql,array($topicId,$movieType));
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    /**
     * 获取全部专题电影信息
     * @return mixed
     */
    public function getTopicMovieList($offset = 0,$limit = 10,$movieType = 1,$status = -1)
    {
        $sql = "select * from `tbl_movieTopicMovie` where movieType = ? and del = 0";
        if (($status == 0) || ($status == 1)) {
            $sql .= " and status = {$status}";
        }
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql,array($movieType));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    /**
     * 获取全部专题电影信息总数
     * @return mixed
     */
    public function getTopicMovieListCount($movieType = 1,$status = -1)
    {
        $sql = "select count(1) as cn from `tbl_movieTopicMovie` where movieType = ? and del = 0";
        if (($status == 0) || ($status == 1)) {
            $sql .= " and status = {$status}";
        }
        $query = $this->db->query($sql,array($movieType));
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    /**
     * 获取具体专题电影信息
     * @return mixed
     */
    public function getOneTopicMovieInfo($id)
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "select * from `tbl_movieTopicMovie` where id = ? and del = 0 limit 1;";
        $query = $this->db->query($sql,array($id));
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }

    /**
     * 根据id更新专题电影信息
     * @param $id
     * @param array $data
     * @return bool
     */
    public function updateTopicMovieInfo($id,$data = array())
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        if (empty($data)) {
            return false;
        }
        $where = "id = {$id}";
        $sql = $this->db->update_string('tbl_movieTopicMovie', $data, $where);
        return $this->db->query($sql);
    }
}