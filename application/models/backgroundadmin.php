<?php

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
    private $_detailfInfoFild = array(
        "id","name","type","jieshao","zhuyan","time0","time1","time2","time3","diqu","nianfen","daoyan","shichang","image","del",
    );
    private $_watchLinkFild = array("id","infoId","link","player","qingxi","shoufei");
    private $_downLinkFild = array("id","infoId","link","size","type");

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
        $fildStr = implode(",",$this->_detailfInfoFild);
        $sql = "select {$fildStr} from `tbl_detailInfo` where del = ? {$desc} limit ?,?;";
        $query = $this->db->query($sql,array($del,$offset,$limit));
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
        if ($del) {
            $where .= " and del = {$del}";
        }
        $fildStr = implode(",",$this->_detailfInfoFild);
        $sql = "select {$fildStr} from `tbl_detailInfo` {$where}";
        if (!$all) {
            $sql .= " limit 1";
        }
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if (!$all) {
            return $result[0] ? $result[0] : false;
        } else {
            return $result;
        }
    }

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
        $fildStr = implode(",",$this->_watchLinkFild);
        $sql = "select {$fildStr} from `tbl_watchLink` where infoId in ({$idStr});";
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
        $fildStr = implode(",",$this->_downLinkFild);
        $sql = "select {$fildStr} from `tbl_downLoad` where infoId in ({$idStr});";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    public function getDetailInfoCount($del = 0,$other = null)
    {
        $sql = "select count(1) as c from `tbl_detailInfo` where del = ?";
        if ($other) {
            $sql .= " {$other}";
        }
        $query = $this->db->query($sql,array($del));
        $result = $query->result_array();
        return $result[0]['c'];
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
}