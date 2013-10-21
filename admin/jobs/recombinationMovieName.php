<?php
/**
 * 重组电影名称，只保留一个名字
 * added by xiongjiewu at 2013-05-08
 * Class deleteRepeatName
 */
include("jobBase.php");
class recombinationMovieName extends jobBase {
    private $_limit = 500;
    private $_id;
    private $_idFile;

    private $_jT;
    private $_fT;

    //清晰+是否免费
    private $_qingXi;
    private $_ms;

    //别名表示符
    private $_replaceStrArr = array("/","(","（");

    public function __construct()
    {
        parent::__construct();
        //id日志，记录跑到了哪个id
        $this->_idFile = "/home/www/logs/dianying/delete_repeat_movice_name.id";
        $font = $this->get_config_value("jT_fT");
        $this->_jT = $this->StringToArray($font['jt']);
        $this->_fT = $this->StringToArray($font['ft']);
        $this->_qingXi = $this->get_config_value("qingxiType");
        $this->_ms = $this->get_config_value("shoufeiType");
    }

    public function run() {
        $this->_id = 0;
        if (file_exists($this->_idFile)) {
            $this->_id = file_get_contents($this->_idFile);
        }
        if (empty($this->_id) || ($this->_id < 0)) {
            $this->_id = 0;
        }
        while(true) {
            $moviceInfos = $this->_getMoviceInfos();
            if (empty($moviceInfos)) {
                //已跑完，记入0，下次重新跑
                file_put_contents($this->_idFile,0);
                break;
            }
            foreach($moviceInfos as $moviceVal) {
                $row = false;
                $this->_id = $moviceVal['id'];
                //判断名称是否含有别名
                $name = $this->_getReplaceName($moviceVal['name']);
                if (!empty($name)) {
                    $name = str_replace("·","",$name);
                    $name = trim(str_replace(".","",$name));
                    if (!empty($name)) {
                        $row = $this->_updateMoviceNameById($moviceVal['id'],$name);
                    }
                } else {
                    $tName = $this->_replaceNT($moviceVal['name']);
                    if (!empty($tName)) {//过滤国语、粤语
                        $row = $this->_upMovieAndWatch($moviceVal['id'],$tName);
                    }
                }
                if (!empty($row)) {
                    var_dump("--{$moviceVal['id']}-do--\n");
                }
            }
            file_put_contents($this->_idFile,$this->_id);
        }
        exit;
    }

    /** 读取电影信息
     * @return array
     */
    private function _getMoviceInfos() {
        $sql = "select * from `tbl_detailInfo` where id > " . $this->_id ." order by id asc limit " . $this->_limit;
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** 更新名字
     * @param $id
     * @param $newName
     * @return bool|int
     */
    private function _updateMoviceNameById($id, $newName)
    {
        $id = intval($id);
        if (empty($id) || empty($newName)) {
            return false;
        }
        $sql = "update `tbl_detailInfo` set `name` = '{$newName}' where id = ? limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($id));
        return $stmt->rowCount();
    }

    //替换字符数组
    private $_rStr = array("Ⅱ" => 2,"III" => 3,"IV" => 4,"《" => "","》" => "");
    //需要过滤的末尾字符串
    private $_lStr = array("DVD","3D","3d","HD");

    /**
     * 获取处理后的电影名称
     */
    private function _getReplaceName($name) {
        $resName[0] = str_replace(" ","",$name);
        //繁体边简体
        foreach($this->_fT as $ftK => $ftV) {
            $resName[0] = str_replace($ftV,$this->_jT[$ftK],$resName[0]);
        }
        foreach($this->_rStr as $rsK => $rsV) {
            $resName[0] = str_replace($rsK,$rsV,$resName[0]);
        }

        //过滤尾部特殊字符
        $cName = $resName[0];
        foreach($this->_lStr as $lsV) {
            $resName[0] = preg_replace("/{$lsV}$/","",$resName[0]);
            if ($cName != $resName[0]) {//只过滤一次
                break;
            }
        }

        foreach($this->_replaceStrArr as $str) {
            //名称替换字符串
            $resName = explode($str,$resName[0]);
        }

        if ($resName[0] == $name) {//无变化
            return false;
        }
        return trim($resName[0]);
    }

    private $_nameT = array("国语","国语版","粤语","粤语版");

    /**
     * 过滤名称后面的$_nameT字符
     * @param $name
     * @return array
     */
    private function _replaceNT($name) {
        $tName = $name;
        $result = array();
        foreach($this->_nameT as $nTV) {
            $name = preg_replace("/{$nTV}$/","",$name);
            if ($tName != $name) {//只过滤一次
                $result["name"] = trim($name);
                $result["nt"] = $nTV;
                break;
            }
        }
        return $result;
    }

    /**
     * 更新电影名称以及观看链接
     * @param $id
     * @param $tName
     * @return bool|int
     */
    private function _upMovieAndWatch($id,$tName) {
        $name = $tName['name'];
        $row = $this->_updateMoviceNameById($id,$name);
        if (!empty($row)) {
            //观看链接
            $watchInfo = $this->_getInfo(array("infoId" => $id,"del" => 0),"all","tbl_watchLink");
            if (!empty($watchInfo)) {
                //更新链接观看备注
               foreach($watchInfo as $watchInfoVal) {
                   $qx = $this->_qingXi[$watchInfoVal['qingxi']];
                   $sf = $this->_ms[$watchInfoVal['shoufei']];
                   $beizhu = $watchInfoVal['beizhu'];
                   if (strpos($beizhu,$qx) === false) {
                       $beizhu .= $qx;
                   }
                   if (strpos($beizhu,$sf) === false) {
                       $beizhu .= $sf;
                   }

                   $beizhu .= $tName['nt'];
                   $this->_updateInfo(array("id" => $watchInfoVal['id']),array("beizhu" => $beizhu),"tbl_watchLink");
               }
            }
        }
        return $row;
    }
}

$do = new recombinationMovieName();
$do->run();