<?php
class Moviescore extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 获取相应类型的top电影总数
     * @param int $topType
     * @return int
     */
    public function getTopMovieCount($topType = 1) {
        $sql = "select count(1) as cn from `tbl_movieScore` where type = {$topType} and del = 0;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    /**
     * 根据类别获取top电影
     * @param int $topType
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function getTopMoviceInfoByType($topType = 1,$offset = 0, $limit = 10) {
        $sql = "select * from `tbl_movieScore` where type = {$topType} and del = 0 order by score desc limit {$offset},{$limit}";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
}