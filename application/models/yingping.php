<?php
class Yingping extends CI_Model {

    private $_fileArr = array("id","infoId","userId","userName","time","content","del","ding");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function insertYingpingInfo($info = array())
    {
        if (empty($info)) {
            return false;
        }
        $this->db->insert('tbl_yingping',$info);
        return $this->db->insert_id();
    }

    public function getDyYingpingCount($dyId,$del = 0)
    {
        $dyId = intval($dyId);
        if (empty($dyId)) {
            return false;
        }
        $sql = "select count(1) as cn from `tbl_yingping` where infoId = ? and del = ?;";
        $query = $this->db->query($sql,array($dyId,$del));
        $result = $query->result_array();
        return empty($result[0]['cn']) ? 0 : $result[0]['cn'];
    }

    public function getYingPingInfoByDyId($dyId,$offset = 0,$limit = 10,$desc = "order by time desc")
    {
        $dyId = intval($dyId);
        if (empty($dyId)) {
            return false;
        }
        $fildStr = implode(",",$this->_fileArr);
        $sql = "select {$fildStr} from `tbl_yingping` where infoId = ? and del = 0 {$desc}";
        if (isset($limit) && ($limit > 0)) {
            $sql .= " limit {$offset},{$limit};";
        }
        $query = $this->db->query($sql,array($dyId));
        $result = $query->result_array();
        return empty($result) ? array() : $result;
    }

    public function getYingPingCountByDyId($dyId,$del = 0)
    {
        $dyId = intval($dyId);
        if (empty($dyId)) {
            return false;
        }
        $sql = "select count(1) as cn from `tbl_yingping` where infoId = ? and del = {$del};";
        $query = $this->db->query($sql,array($dyId));
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }

    public function updateYingpingInfoById($id,$data = array())
    {
        $id = intval($id);
        if (empty($id) || empty($data) || !is_array($data)) {
            return false;
        }
        $dataStr = array();
        foreach($data as $dataKey => $dataVal) {
            $dataStr[] = "{$dataKey} = {$dataVal}";
        }

        $setStr = implode(",",$dataStr);
        $sql = "update `tbl_yingping` set {$setStr} where id = {$id} limit 1;";
        return $this->db->query($sql);
    }

    /** 根据条件获取影评信息
     * @param array $queryInfo 条件数组
     * @return array|bool
     */
    public function getYingPingInfoByFiled($queryInfo = array())
    {
        if (empty($queryInfo) || !is_array($queryInfo)) {
            return false;
        }
        $keyArr = $valArr = array();
        foreach($queryInfo as $infoKey => $infoVal) {
            $keyArr[] = "{$infoKey} = ?";
            $valArr[] = $infoVal;
        }
        $keyStr = implode(" and ",$keyArr);
        $fildStr = implode(",",$this->_fileArr);
        $sql = "select {$fildStr} from `tbl_yingping` where {$keyStr} limit 1;";
        $query = $this->db->query($sql,$valArr);
        $result = $query->result_array();
        return empty($result[0]) ? array() : $result[0];
    }
}