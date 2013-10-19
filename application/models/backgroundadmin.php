<?php
/**
 * 电影查询主要model
 * Class Backgroundadmin
 */
class Backgroundadmin extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private $_detailFildInfo = array(//detailInfo表各字段信息
        "name" => array("null" => false,'title' => "名称"),
        "type" => array("null" => false,'title' => "类型"),
        "jieshao" => array("null" => false,'title' => "简介"),
        "zhuyan" => array("null" => false,'title' => "主演"),
        "time0" => array("null" => true,'title' => "本周提供观看链接时间"),
        "time1" => array("null" => true,'title' => "中国上映时间"),
        "time2" => array("null" => true,'title' => "欧美上映时间"),
        "time3" => array("null" => true,'title' => "港台上映时间"),
        "diqu" => array("null" => false,'title' => "地区"),
        "nianfen" => array("null" => false,'title' => "年份"),
        "daoyan" => array("null" => false,'title' => "导演"),
        "shichang" => array("null" => false,'title' => "时长"),
        "image" => array("null",'title' => "图片")
    );

    public function checkDetail($info = array())
    {
        $result = array(
            "code" => false,
            "error" => "参数错误！",
        );
        if (empty($info)) {
            return $result;
        }
        foreach($this->_detailFildInfo as $infoKey => $infoVal) {
            if (!$infoVal['null'] && !$info[$infoKey]) {
                $result['code'] = false;
                $result['error'] = $infoVal['title'] . "参数错误";
                break;
            } else {
                $result['code'] = true;
            }
        }
        return $result;
    }

    /** 插入电影信息
     * @param array $info
     * @return array
     */
    public function insertDetailInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_detailInfo',$info);
        return $this->db->insert_id();
    }

    /** 插入观看信息链接
     * @param array $info
     * @return bool
     */
    public function inserWatchLink($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_watchLink ',$info);
        return $this->db->insert_id();
    }
    /** 插入下载信息链接
     * @param array $info
     * @return bool
     */
    public function inserDownLink($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_downLoad ',$info);
        return $this->db->insert_id();
    }

    /** 获取电影列表
     * @param int $offset
     * @param int $limit
     * @param int $del
     * @param bool $desc
     * @return mixed
     */
    public function getDetailInfoList($offset = 0,$limit = 20,$del = 0,$desc = false)
    {
        if (!$desc) {
            $desc = "order by id asc";
        }
        $sql = "select * from `tbl_detailInfo` where del = ? {$desc} limit ?,?;";
        $query = $this->db->query($sql,array($del,$offset,$limit));
        return $query->result_array();
    }

    public function getDetailInfoListByTime($sTime,$eTime,$del = 0,$desc = false)
    {
        if (!$desc) {
            $desc = "order by id asc";
        }
        $sql = "select * from `tbl_detailInfo` where time1>={$sTime} and time1<={$eTime} and del = ? {$desc};";
        $query = $this->db->query($sql,array($del));
        return $query->result_array();
    }

    public function getDetailInfo($id = array(),$del = null,$all = false)
    {
        if (empty($id)) {
            return false;
        }
        if (!is_array($id)) {
            $id = array($id);
        }
        $id = array_unique($id);
        $idStr = implode(",",$id);
        $where = "where id in ({$idStr})";
        if (isset($del) && ($del !== null)) {
            $where .= " and del = {$del}";
        }
        $sql = "select * from `tbl_detailInfo` {$where}";
        if (!$all) {
            $sql .= " limit 1";
        }
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if (!$all) {
            return !empty($result[0]) ? $result[0] : false;
        } else {
            return $result;
        }
    }

    /**
     * 批量获取电影观看链接
     * @param array $id
     * @return bool
     */
    public function getWatchLinkInfoByInfoId($id = array())
    {
        if (empty($id)) {
            return false;
        }
        if (!is_array($id)) {
            $id = array($id);
        }
        $id = array_unique($id);
        $idStr = implode(",",$id);
        $sql = "select * from `tbl_watchLink` where infoId in ({$idStr}) AND del = 0 ;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    /**
     * 根据表tbl_watchLink id获取观看链接
     * @param array $id
     * @return bool
     */
    public function getWatchLinkInfo($id = array())
    {
        if (empty($id)) {
            return false;
        }
        if (!is_array($id)) {
            $id = array($id);
        }
        $id = array_unique($id);
        $idStr = implode(",",$id);
        $sql = "select * from `tbl_watchLink` where id in ({$idStr});";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    public function getDownLoadLinkInfoByInfoId($id = array())
    {
        if (empty($id)) {
            return false;
        }
        if (!is_array($id)) {
            $id = array($id);
        }
        $id = array_unique($id);
        $idStr = implode(",",$id);
        $sql = "select * from `tbl_downLoad` where infoId in ({$idStr}) AND del = 0 ;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    public function getDetailInfoCount($del = 0,$other = null)
    {
        $sql = "select count(1) as cn from `tbl_detailInfo` where del = ?";
        if ($other) {
            $sql .= " {$other}";
        }
        $query = $this->db->query($sql,array($del));
        $result = $query->result_array();
        return $result[0]['cn'];
    }

    public function insertNewestInfo($info)
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_newest ',$info);
        return $this->db->insert_id();
    }

    public function updateNewestInfo($data = array(),$type = 1)
    {
        if (empty($data)) {
            return false;
        }
        $where = "type = {$type}";
        $sql = $this->db->update_string('tbl_newest', $data, $where);
        return $this->db->query($sql);
    }

    public function getNewestInfo($type = 1) {
        $sql = "select * from `tbl_newest` where type = ?;";
        $query = $this->db->query($sql,array($type));
        return $query->result_array();
    }

    public function updateDetailInfoById($id = array(),$data = array()) {
        if (empty($id) || empty($data)) {
            return false;
        }
        $id = is_array($id) ? $id : array($id);
        $str = implode(",",$id);
        $where = "id in ({$str})";
        $sql = $this->db->update_string('tbl_detailInfo', $data, $where);
        return $this->db->query($sql);
    }

    public function updateDetailInfo($id,$data = array())
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $where = "id = {$id}";
        $sql = $this->db->update_string('tbl_detailInfo', $data, $where);
        return $this->db->query($sql);
    }

    public function deleteWatchLinkInfoByInfoId($id)
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "delete from tbl_watchLink where infoId = {$id};";
        return $this->db->query($sql);
    }
    public function deleteDownLoadInfoByInfoId($id)
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "delete from tbl_downLoad where infoId = {$id};";
        return $this->db->query($sql);
    }

    public function deleteDetailInfoById($id = array(),$del = 1) {
        if (empty($id)) {
            return false;
        }
        $id = is_array($id) ? $id : array($id);
        $str = implode(",",$id);
        $sql = "delete from tbl_detailInfo where id in ({$str}) and del = {$del};";
        return $this->db->query($sql);
    }

    public function getDetailInfoByType($type,$offset,$limit,$del = 0)
    {
        $type = intval($type);
        $offset = intval($offset);
        $limit = intval($limit);
        if (!isset($offset) || !isset($limit)) {
            return false;
        }
        $typeStr = "";
        if ($type != null) {
            $typeStr = "type = {$type} and ";
        }
        $sql = "select * from `tbl_detailInfo` where {$typeStr} del = {$del} order by createtime desc limit {$offset},$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function getDetailInfoCountByType($type = null,$del = 0)
    {
        $type = intval($type);
        $typeStr = "";
        if ($type != null) {
            $typeStr = "type = {$type} and ";
        }
        $sql = "select count(1) as cn from `tbl_detailInfo` where {$typeStr} del = {$del};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    public function getDetailInfoByNianFen($nianfen,$offset,$limit,$del = 0)
    {
        $nianfen = intval($nianfen);
        $offset = intval($offset);
        $limit = intval($limit);
        if (!isset($offset) || !isset($limit)) {
            return false;
        }
        $where = "";
        if (!empty($nianfen)) {
            $where .= "nianfen = {$nianfen} and ";
        }
        $where .= "del = {$del}";
        $sql = "select * from `tbl_detailInfo` where  {$where} order by createtime desc limit {$offset},$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function getDetailInfoCountByNianFen($nianfen,$del = 0)
    {
        $nianfen = intval($nianfen);
        $where = "";
        if (!empty($nianfen)) {
            $where .= "nianfen = {$nianfen} and ";
        }
        $where .= "del = {$del}";
        $sql = "select count(1) as cn from `tbl_detailInfo` where {$where};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    public function getDetailInfoByDiQ($diqu,$offset,$limit,$del = 0)
    {
        $diqu = intval($diqu);
        $offset = intval($offset);
        $limit = intval($limit);
        if (!isset($offset) || !isset($limit)) {
            return false;
        }
        $where = "";
        if (!empty($diqu)) {
            $where .= "diqu = {$diqu} and ";
        }
        $where .= "del = {$del}";
        $sql = "select * from `tbl_detailInfo` where {$where} order by createtime desc limit {$offset},$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function getDetailInfoCountByDiQu($diqu,$del = 0)
    {
        $diqu = intval($diqu);
        $where = "";
        if (!empty($diqu)) {
            $where .= "diqu = {$diqu} and ";
        }
        $where .= "del = {$del}";
        $sql = "select count(1) as cn from `tbl_detailInfo` where {$where}";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    /**
     * 根据名称右匹配电影信息
     * @param $searchW
     * @param int $limit
     * @param bool $other
     * @return bool
     */
    public function getDetailInfoBySearchW($searchW,$limit=10,$other = false)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where name like '{$searchW}%'";
        if ($other) {
            $sql .= " and del = 0 {$other} limit {$limit}";
        } else {
            $sql .= " and del = 0 limit {$limit}";
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 根据名称拼音右匹配电影信息
     * @param $searchW
     * @param int $limit
     * @param bool $other
     * @return bool
     */
    public function getDetailInfoBySearchPinYin($searchW,$limit=10,$other = false)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where pinyin like '{$searchW}%'";
        if ($other) {
            $sql .= " and del = 0 {$other} limit {$limit}";
        } else {
            $sql .= " and del = 0 limit {$limit}";
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 根据电影名称搜索信息
     * @param $searchW
     * @param int $limit
     * @param bool $other
     * @return bool
     */
    public function getDetailInfoByDyName($searchW,$limit=10,$other = false)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where name = '{$searchW}'";
        if ($other) {
            $sql .= " and del = 0 {$other} limit {$limit}";
        } else {
            $sql .= " and del = 0 limit {$limit}";
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 根据电影名称拼音搜索信息
     * @param $searchW
     * @param int $limit
     * @param bool $other
     * @return bool
     */
    public function getDetailInfoByDyPinYin($searchW,$limit=10,$other = false)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where pinyin = '{$searchW}'";
        if ($other) {
            $sql .= " and del = 0 {$other} limit {$limit}";
        } else {
            $sql .= " and del = 0 limit {$limit}";
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getDetailInfoBySearchZhuYan($searchW,$limit=10,$other = false)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where zhuyan like '{$searchW}%'";
        if ($other) {
            $sql .= " and del = 0 {$other} limit {$limit}";
        } else {
            $sql .= " and del = 0 limit {$limit}";
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getDetailInfoBySearchDaoYan($searchW,$limit=10,$other = false)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where daoyan like '{$searchW}%'";
        if ($other) {
            $sql .= " and del = 0 {$other} limit {$limit}";
        } else {
            $sql .= " and del = 0 limit {$limit}";
        }
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getHotYingDyInfos($limit)
    {
        $limit = intval($limit);
        if (empty($limit)) {
            return array();
        }
        $sql = "SELECT COUNT(infoId) AS cn,infoId FROM `tbl_yingping` GROUP BY infoId ORDER BY cn DESC limit {$limit};";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /** 获取top电影（经典电影）
     * @param $offset
     * @param $limit
     * @return bool
     */
    public function getTopMoviceInfo($offset,$limit) {
        $offset = intval($offset);
        $limit = intval($limit);
        if (!isset($offset) || empty($limit)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where topType = 1 and del = 0 limit {$offset},{$limit};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    public function getTopMoviceInfoCount() {
        $sql = "select count(1) as cn from `tbl_detailInfo` where topType = 1 and del = 0";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    /**
     * 根据条件，获取最新更新电影信息
     * @param string $condition
     * @param $offset
     * @param $limit
     * @param int $del
     * @return bool
     */
    public function getDetailInfoByCondition($condition = "",$offset,$limit,$del = 0)
    {
        $offset = intval($offset);
        $limit = intval($limit);
        if (!isset($offset) || !isset($limit)) {
            return false;
        }
        if (empty($condition)) {
            $where = "del = {$del}";
        } else {
            $where = "{$condition}";
        }
        $sql = "select * from `tbl_detailInfo` where {$where} limit {$offset},$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 根据条件获取电影信息总数
     * @param $condition 条件
     * @param string $type 最大还是最小id
     * @return bool
     */
    public function getDetailInfoCountByCondition($condition = "",$del = 0)
    {
        if (empty($condition)) {
            $where = "del = {$del}";
        } else {
            $where = "{$condition}";
        }
        $sql = "select count(1) as cn from `tbl_detailInfo` where {$where};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
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

    /**
     * 获取电影总数
     * @return int
     */
    public function getdyCount()
    {
        $sql = "select count(1) as cn from `tbl_detailInfo` where del = 0;";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    /**
     * 根据条件获取电影信息
     * @param $conditionStr
     * @return bool
     */
    public function getMovieInfoByCon($conditionStr) {
        if (empty($conditionStr)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where {$conditionStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    /**
     * 根据下载链接id获取下载信息
     * @param array $id
     * @return bool
     */
    public function getDownLoadLinkInfoid($id = array())
    {
        if (empty($id)) {
            return false;
        }
        if (!is_array($id)) {
            $id = array($id);
        }
        $id = array_unique($id);
        $idStr = implode(",",$id);
        $sql = "select * from `tbl_downLoad` where id in ({$idStr});";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
}