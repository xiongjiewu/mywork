<?php
/**
 * 将下载链接整合到电影job，电影名相似，演员有1个以上相同的默认为同一部电影，
 * 此时下载链接整合到电影
 * added by xiongjiewu 2013-06-15
 * Class conformDownLinkToMovieInfo
 */
include("jobBase.php");
class conformDownLinkToMovieInfo extends jobBase {
    private $_id;
    private $_limit = 500;
    private $_xiangtongC = 1;

    public function __construct()
    {
        parent::__construct();
        $this->_id = 0;
    }
    public function run() {
        while(true) {
            //需要处理的电影信息
            $movieInfos = $this->_getMoviceInfo($this->_id,$this->_limit,"all");
            if (empty($movieInfos)) {
                exit;
            }
            foreach($movieInfos as $movieVal) {
                $this->_id = $movieVal['id'];
                //获取名称相似的下载链接信息
                $downInfos = $this->_getMovieDownInfoByName($movieVal['name']);
                if (empty($downInfos)) {
                    continue;
                }
                $movieVal['zhuyan'] = str_replace("/","、",$movieVal['zhuyan']);
                $movieValZhuYanArr = explode("、",$movieVal['zhuyan']);
                foreach($downInfos as $downVal) {
                    if (empty($downVal) || empty($downVal['zhuyan']) || empty($downVal['downLink'])) {
                        continue;
                    }
                    $xt = 0;
                    //比较演员相同个数
                    foreach($movieValZhuYanArr as $zyVal) {
                        if (empty($zyVal)) {
                            continue;
                        }
                        if (strpos($downVal['zhuyan'],$zyVal) !== false) {
                            $xt++;
                        }
                    }
                    //演员大于等于规定个数，可以合并
                    if ($xt >= $this->_xiangtongC) {
                        $downLinkArr = array();
                        $downLinkArr['infoId'] = $movieVal['id'];
                        $downLinkArr['link'] = $downVal['downLink'];
                        $downLinkArr['sourceLink'] = $downVal['sourceLink'];
                        $downLinkArr['type'] = $downVal['webType'];
                        $lastId = $this->_insertInfo($downLinkArr,"tbl_downLoad");
                        if (!empty($lastId)) {
                            //更新下载链接字段，有下载链接标示
                            $this->_updateDetailInfo($movieVal['id'],array("exist_down" => 1));
                            //删除下载链接
                            $this->_updateInfo(array("id" => $downVal['id']),array("del" => 1),"tbl_grabMoviceDownInfo");
                            var_dump("下载链接[{$downVal['id']}]合并到电影[{$movieVal['id']}]成功!\n");
                        } else {
                            var_dump("下载链接[{$downVal['id']}]合并到电影[{$movieVal['id']}]失败!\n");
                        }
                    }
                }
            }
        }
    }

    /**
     * 根据电影和id获取其他名称一致的电影信息
     * @param $name
     * @param $id
     * @return array|bool
     */
    private function _getMovieDownInfoByName($name) {
        $name = trim($name);
        if (empty($name)) {
            return false;
        }
        $sql = "select * from `tbl_grabMoviceDownInfo` where name like '%" . $name . "%' and del = 0;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
$do = new conformDownLinkToMovieInfo();
$do->run();