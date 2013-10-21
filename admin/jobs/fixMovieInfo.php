<?php
/**
 * 电影信息修复job,目前只修复年份，以后需要修复别的信息可在此job里面加
 * 修复一些没有图片或者图片是我们网站默认图片的电影
 * added by xiongjiewu at 2013-07-13
 */
include("jobBase.php");
class FixMovieInfo extends jobBase
{

    private $_id;
    private $_limit = 500;

    function __construct() {
        parent::__construct();
    }
    public function run() {
        $this->_id = 0;
        $i = 0;
        while(true) {
            $movieInfo = $this->_getMoviceInfo($this->_id,$this->_limit,"all");
            if (empty($movieInfo)) {//没有要处理的信息
                var_dump("共处理:" . $i . "部电影！\n");
                exit;
            }
            foreach($movieInfo as $movieVal) {
                $this->_id = $movieVal['id'];
                if (!empty($movieVal['time1'])) {
                    $newNianFen = date("Y",$movieVal['time1']);

                    //年份为空或者不等于上映时间的年份
                    if (empty($movieVal['nianfen']) || ($movieVal['nianfen'] != $newNianFen)) {
                        $i++;
                        $upRes = $this->_updateInfo(array("id" => $movieVal['id']),array("nianfen" => $newNianFen),"tbl_detailInfo");
                        if (!empty($upRes)) {
                            var_dump("电影[{$movieVal['id']}]--[{$movieVal['name']}]年份处理成功!\n");
                        } else {
                            var_dump("电影[{$movieVal['id']}]--[{$movieVal['name']}]年份处理失败!\n");
                        }
                    }
                }
            }
        }
    }
}
$do = new FixMovieInfo();
$do->run();