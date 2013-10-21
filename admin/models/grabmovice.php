<?php

class GrabMovice extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private $_grabMoviceFildInfo = array(//GrabMovice表各字段信息
        "name" => array("null" => false,'title' => "名称"),
        "type" => array("null" => false,'title' => "类型"),
        "jieshao" => array("null" => false,'title' => "简介"),
        "zhuyan" => array("null" => false,'title' => "主演"),
        "time0" => array("null" => true,'title' => "本周提供观看链接时间"),
        "time1" => array("null" => true,'title' => "中国上映时间"),
        "time2" => array("null" => true,'title' => "欧美上映时间"),
        "time3" => array("null" => true,'title' => "港台上映时间"),
        "diqu" => array("null" => false,'title' => "地区"),
        "nianfen" => array("null" => true,'title' => "年份"),
        "daoyan" => array("null" => false,'title' => "导演"),
        "shichang" => array("null" => false,'title' => "时长"),
        "image" => array("null"=>false,'title' => "图片"),
        "webType" => array("null"=>false,'title' => "来源网站"),
        "webId" => array("null"=>false,'title' => "电影在来源网站中的id"),
    );
    private $_grabMovicefInfoFild = array(
        "id","name","type","jieshao","zhuyan","time0","time1","time2","time3","diqu","nianfen","daoyan","shichang","image","del","webType","webId"
    );

    private function _getFiledStr()
    {
        return implode(",",$this->_grabMovicefInfoFild);
    }
    public function checkGrabMovice($info = array())
    {
        $result = array(
            "code" => false,
            "error" => "参数错误！",
        );
        if (empty($info)) {
            return $result;
        }
        foreach($this->_grabMoviceFildInfo as $infoKey => $infoVal) {
            if (empty($infoVal['null']) && !$info[$infoKey]) {
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
    public function insertGrabMoviceInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_grabMovice',$info);
        return $this->db->insert_id();
    }

    /** 获取电影列表
     * @param int $offset
     * @param int $limit
     * @param int $del
     * @param bool $desc
     * @return mixed
     */
    public function getGrabMoviceInfoList($offset = 0,$limit = 20,$other = null,$del = 0,$desc = "")
    {
        if (!$desc) {
            $desc = "order by id asc";
        }
        if (!empty($other)) {
            $desc = " and " . $other;
        }
        $fildStr = implode(",",$this->_grabMovicefInfoFild);
        $sql = "select {$fildStr} from `tbl_grabMovice` where del = ? and inType = 0  {$desc} limit ?,?;";

        $query = $this->db->query($sql,array($del,$offset,$limit));
        return $query->result_array();
    }

    public function getGrabMoviceInfoCount($other =null,$del = 0)
    {
        $sql = "select count(1) as cn from `tbl_grabMovice` where del = ? and inType = 0";
        if (!empty($other)) {
            $sql .= " and " . $other;
        }
        $query = $this->db->query($sql,array($del));
        $result = $query->result_array();
        return $result[0]['cn'];
    }

    public function getGrabMoviceInfo($id = array(),$del = null,$all = false)
    {
        if (empty($id)) {
            return false;
        }
        if (!is_array($id)) {
            $id = array($id);
        }
        $id = array_unique($id);
        $idStr = implode(",",$id);
        $where = "where id in ({$idStr}) and inType = 0";
        if ($del) {
            $where .= " and del = {$del}";
        }
        $fildStr = implode(",",$this->_grabMovicefInfoFild);
        $sql = "select {$fildStr} from `tbl_grabMovice` {$where}";
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

    public function updateGrabMoviceInfoById($id = array(),$data = array()) {
        if (empty($id) || empty($data)) {
            return false;
        }
        $id = is_array($id) ? $id : array($id);
        $str = implode(",",$id);
        $where = "id in ({$str})";
        $sql = $this->db->update_string('tbl_grabMovice', $data, $where);
        return $this->db->query($sql);
    }

    public function updateGrabMoviceInfo($id,$data = array())
    {
        $id = intval($id);
        if (empty($id) || empty($data) || !is_array($data)) {
            return false;
        }
        $where = "id = {$id}";
        $sql = $this->db->update_string('tbl_grabMovice', $data, $where);
        return $this->db->query($sql);
    }

    public function deleteGrabMoviceInfoById($id = array(),$del = 1) {
        if (empty($id)) {
            return false;
        }
        $id = is_array($id) ? $id : array($id);
        $str = implode(",",$id);
        $sql = "delete from `tbl_grabMovice` where id in ({$str}) and del = {$del};";
        return $this->db->query($sql);
    }

    public function getGrabMoviceInfoByType($type,$offset,$limit,$del = 0)
    {
        $type = intval($type);
        $offset = intval($offset);
        $limit = intval($limit);
        if (!isset($type) || !isset($offset) || !isset($limit)) {
            return false;
        }
        $sql = "select {$this->_getFiledStr()} from `tbl_grabMovice` where webType = {$type} and del = {$del} and inType = 0 limit {$offset},$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function getGrabMoviceInfoCountByType($type,$del = 0)
    {
        $type = intval($type);
        if (!isset($type)) {
            return false;
        }
        $sql = "select count(1) as cn from `tbl_grabMovice` where webType = {$type} and del = {$del} and inType = 0";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    public function getGrabMoviceInfoBySearchW($searchW,$limit=10)
    {
        if (!isset($searchW)) {
            return false;
        }
        $sql = "select {$this->_getFiledStr()} from `tbl_grabMovice` where name like '{$searchW}%' and del = 0 and inType = 0 limit {$limit};";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}