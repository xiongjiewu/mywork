<?php

class Movietopicimg extends CI_Model {

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
    public function insertTopicMovieImgInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_movieTopicImg',$info);
        return $this->db->insert_id();
    }

    /**
     * 获取具体专题电相关图片信息
     * @return mixed
     */
    public function getTopicMovieImgByRelatedId($id,$type = 1)
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "select * from `tbl_movieTopicImg` where relatedId = ? and type = ? and del = 0;";
        $query = $this->db->query($sql,array($id,$type));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    /**
     * 根据id更新专题图片信息
     * @param $id
     * @param array $data
     * @return bool
     */
    public function updateTopicMovieImgInfo($id,$type = 1,$data = array())
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        if (empty($data)) {
            return false;
        }
        $where = "relatedId = {$id} and type = {$type}";
        $sql = $this->db->update_string('tbl_movieTopicImg', $data, $where);
        return $this->db->query($sql);
    }
}