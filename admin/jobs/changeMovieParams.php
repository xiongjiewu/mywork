<?php
/**
 * 改变电影参数job，观看次数+分数+打分总数+下载次数，
 * 此时下载链接整合到电影
 * added by xiongjiewu 2013-08-18
 * Class changeMovieParams
 */
include("jobBase.php");
class changeMovieParams extends jobBase {
    private $_limit = 500;
    private $_id;

    //播放1min+max
    private $_minWatchCount = 10000;
    private $_maxWatchCount = 368982;
    //播放2min+max
    private $_minWatchCount2 = 1983;
    private $_maxWatchCount2 = 12834;

    //摇摇1min+max
    private $_minYaoYaoCount = 1381;
    private $_maxYaoYaoCount = 183408;
    //摇摇2min+max
    private $_minYaoYaoCount2 = 192;
    private $_maxYaoYaoCount2 = 1298;

    //搜索次数1min+max
    private $_minSearchCount = 10329;
    private $_maxSearchCount = 198381;
    //搜索次数1min+max
    private $_minSearchCount2 = 1991;
    private $_maxSearchCount2 = 19328;

    //下载次数1min+max
    private $_minDownCount = 182832;
    private $_maxDownCount = 591893;
    //下载次数2min+max
    private $_minDownCount2 = 1943;
    private $_maxDownCount2 = 19384;

    //打分总数1min+max
    private $_minScoreCount = 593;
    private $_maxScoreCount = 1834;
    //打分总数2min+max
    private $_minScoreCount2 = 194;
    private $_maxScoreCount2 = 592;

    //分隔天数
    private $_splitDayCount = 30;

    private $_nowTime;



    public function __construct()
    {
        parent::__construct();
        $this->_id = 0;
        $this->_nowTime = time();
    }

    public function run() {
        while(true) {
            //处理的电影信息
            $movieInfos = $this->_getMoviceInfo($this->_id,$this->_limit,"all");
            if (empty($movieInfos)) {
                exit;
            }
            foreach($movieInfos as $movieVal) {
                $this->_id = $movieVal['id'];
                $upDataArr = array();
                if ($movieVal['exist_watch'] == 1) {//存在观看地址，更新观看次数+摇到次数+打分
                    if (($this->_nowTime - $movieVal['createtime']) > ($this->_splitDayCount * 86400)) {
                        $upDataArr['playNum'] = $movieVal['playNum'] + rand($this->_minWatchCount,$this->_maxWatchCount);//播放随机一个次数
                        $upDataArr['yaoyaoNum'] = $movieVal['yaoyaoNum'] + rand($this->_minYaoYaoCount,$this->_maxYaoYaoCount);//摇摇随机一个次数
                        $upDataArr['searchNum'] = $movieVal['searchNum'] + rand($this->_minSearchCount,$this->_maxSearchCount);//被搜索随机一个次数
                        //分数
                        $upDataArr['start1Num'] = $movieVal['start1Num'] + rand($this->_minScoreCount,$this->_maxScoreCount);//1星随机一个次数
                        $upDataArr['start2Num'] = $movieVal['start2Num'] + rand($this->_minScoreCount,$this->_maxScoreCount);//2星随机一个次数
                        $upDataArr['start3Num'] = $movieVal['start3Num'] + rand($this->_minScoreCount,$this->_maxScoreCount);//3星随机一个次数
                        $upDataArr['start4Num'] = $movieVal['start4Num'] + rand($this->_minScoreCount,$this->_maxScoreCount);//4星随机一个次数
                        $upDataArr['start5Num'] = $movieVal['start5Num'] + rand($this->_minScoreCount,$this->_maxScoreCount);//5星随机一个次数
                        $upDataArr['totalStartNum'] = $upDataArr['start1Num'] + $upDataArr['start2Num'] + $upDataArr['start3Num'] + $upDataArr['start4Num'] + $upDataArr['start5Num'];//评分总数
                        $upDataArr['score'] = ($upDataArr['start1Num'] * 2 + $upDataArr['start2Num'] * 4 + $upDataArr['start3Num'] * 6 + $upDataArr['start4Num'] * 8 + $upDataArr['start5Num'] * 10) / $upDataArr['totalStartNum'];
                    } else {//入库时间不超过一个月
                        $upDataArr['playNum'] = $movieVal['playNum'] + rand($this->_minWatchCount2,$this->_maxWatchCount2);//播放随机一个次数
                        $upDataArr['yaoyaoNum'] = $movieVal['yaoyaoNum'] + rand($this->_minYaoYaoCount2,$this->_maxYaoYaoCount2);//摇摇随机一个次数
                        $upDataArr['searchNum'] = $movieVal['searchNum'] + rand($this->_minSearchCount2,$this->_maxSearchCount2);//被搜索随机一个次数
                        //分数
                        $upDataArr['start1Num'] = $movieVal['start1Num'] + rand($this->_minScoreCount2,$this->_maxScoreCount2);//1星随机一个次数
                        $upDataArr['start2Num'] = $movieVal['start2Num'] + rand($this->_minScoreCount2,$this->_maxScoreCount2);//2星随机一个次数
                        $upDataArr['start3Num'] = $movieVal['start3Num'] + rand($this->_minScoreCount2,$this->_maxScoreCount2);//3星随机一个次数
                        $upDataArr['start4Num'] = $movieVal['start4Num'] + rand($this->_minScoreCount2,$this->_maxScoreCount2);//4星随机一个次数
                        $upDataArr['start5Num'] = $movieVal['start5Num'] + rand($this->_minScoreCount2,$this->_maxScoreCount2);//5星随机一个次数
                        $upDataArr['totalStartNum'] = $upDataArr['start1Num'] + $upDataArr['start2Num'] + $upDataArr['start3Num'] + $upDataArr['start4Num'] + $upDataArr['start5Num'];//评分总数
                        $upDataArr['score'] = ($upDataArr['start1Num'] * 2 + $upDataArr['start2Num'] * 4 + $upDataArr['start3Num'] * 6 + $upDataArr['start4Num'] * 8 + $upDataArr['start5Num'] * 10) / $upDataArr['totalStartNum'];
                    }
                }

                if ($movieVal['exist_down'] == 1) {//存在下载地址，更新下载次数
                    if (($this->_nowTime - $movieVal['createtime']) > ($this->_splitDayCount * 86400)) {
                        $upDataArr['downNum'] = $movieVal['downNum'] + rand($this->_minDownCount,$this->_maxDownCount);//下载随机一个次数
                    } else {//入库时间不超过一个月
                        $upDataArr['downNum'] = $movieVal['downNum'] + rand($this->_minDownCount2,$this->_maxDownCount2);//下载随机一个次数
                    }
                }

                //更新操作
                if (!empty($upDataArr)) {
                    $upRes = $this->_updateDetailInfo($movieVal['id'],$upDataArr);
                    if (empty($upRes)) {
                        var_dump("电影[{$movieVal['id']}]处理失败!\n");
                    } else {
                        var_dump("电影[{$movieVal['id']}]处理成功!\n");
                    }
                }
            }
        }
    }
}
$do = new changeMovieParams();
$do->run();