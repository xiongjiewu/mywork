<?php
class Feedback extends CI_Model {

    private $_filedArr = array("id","userId","userName","time","title","content","type","reply","del");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

    public function getFeedbackInfoListByUserId($userId,$reply = null,$del = 0,$type = 1,$offset = 0,$limit = 10)
    {
        $userId = intval($userId);
        if (empty($userId)) {
            return false;
        }
        $fildStr = $this->_getFiledStr();
        $where = "userId = ? and del = ?";
        $data = array($userId,$del);
        if (isset($reply) && ($reply == "0")) {
            $where .= " and reply = 0";
        } elseif (isset($reply) && ($reply == "1")) {
            $where .= " and reply > 0";
        }
        $where .= " and type = {$type}";
        $sql = "select {$fildStr} from `tbl_userFeedback` where {$where} order by time desc limit {$offset},{$limit};";
        $query = $this->db->query($sql,$data);
        return $query->result_array();
    }

    public function getFeedbackInfoCountByUserId($userId,$reply = null,$del = 0,$type = 1)
    {
        $userId = intval($userId);
        if (empty($userId)) {
            return false;
        }
        $where = "userId = ? and del = ?";
        $data = array($userId,$del);
        if (isset($reply) && ($reply == "0")) {
            $where .= " and reply = 0";
        } elseif (isset($reply) && ($reply == "1")) {
            $where .= " and reply > 0";
        }
        $where .= " and type = {$type}";
        $sql = "select count(1) as cn from `tbl_userFeedback` where {$where};";
        $query = $this->db->query($sql,$data);
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    public function updateUserFeedBackInfoById($uId,$idArr = array())
    {
        $uId = intval($uId);
        if (empty($uId) || empty($idArr)) {
            return false;
        }
        $idArr = array_unique($idArr);
        $idStr = implode(",",$idArr);
        $where = "id in ({$idStr}) and userId = {$uId}";
        $sql = $this->db->update_string('tbl_userFeedback', array("del" =>1), $where);
        return $this->db->query($sql);
    }

    public function getFeedBackInfosByIds($ids = array(),$replyFlg = true)
    {
        if (empty($ids)) {
            return false;
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $ids = array_unique($ids);
        $idsStr = implode(",",$ids);
        $where = "id in ({$idsStr}) and del = 0";
        if ($replyFlg) {
            $where .= " and reply = 0";
        }
        $sql = "select {$this->_getFiledStr()} from `tbl_userFeedback` where {$where};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function updateFeedbackInfo($data = array(),$where = array())
    {
        if (empty($data) || !is_array($data) || empty($where) || !is_array($where)) {
            return false;
        }
        $whereArr = array();
        foreach($where as $key => $val) {
            $whereArr[] = "{$key} = {$val}";
        }
        $whereStr = implode(" and ",$whereArr);
        $sql = $this->db->update_string('tbl_userFeedback', $data, $whereStr);
        return $this->db->query($sql);
    }

    public function insertFeedbackInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_userFeedback',$info);
        return $this->db->insert_id();
    }

    public function getFeedbackInfoList($offset = 0,$limit = 10,$type = null,$reply = null) {
        $where = array();
        $where[] = "del = 0";
        if ($type !== null) {
            $where[] = "type = {$type}";
        }
        if ($reply === 0) {
            $where[] = "reply = 0";
        } elseif ($reply > 0) {
            $where[] = "reply > 0";
        }
        $whereStr = implode(" and ",$where);
        $sql = "select {$this->_getFiledStr()} from `tbl_userFeedback` where {$whereStr} limit {$offset},{$limit};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function getFeedbackInfoCount($type = null,$reply = null) {
        $where = array();
        $where[] = "del = 0";
        if ($type !== null) {
            $where[] = "type = {$type}";
        }
        if ($reply === 0) {
            $where[] = "reply = 0";
        } elseif ($reply > 0) {
            $where[] = "reply > 0";
        }
        $whereStr = implode(" and ",$where);
        $sql = "select count(1) as cn from `tbl_userFeedback` where {$whereStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

}