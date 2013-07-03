<?php
/**
 * 人物图片查询主要model
 * added by xiongjiewu at 2013-07-03
 * Class Backgroundadmin
 */
class Characterimg extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 根据条件获取人物剧照信息
     * @param $conditionStr
     * @return bool
     */
    public function getCharacterImgInfoByCon($conditionStr) {
        if (empty($conditionStr)) {
            return false;
        }
        $sql = "select * from `tbl_characterImg` where {$conditionStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    /**
     * 根据条件获取人物剧照总数
     * @param $conditionStr
     * @return bool
     */
    public function getCharacterImgCountByCon($conditionStr) {
        if (empty($conditionStr)) {
            return false;
        }
        $sql = "select count(1) as cn from `tbl_characterImg` where {$conditionStr};";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return empty($result[0]) ? 0 : $result[0]['cn'];
    }
}