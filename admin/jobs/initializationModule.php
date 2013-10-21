<?php
/**
 * 每天初始化各个模块job，例如首页的最新上映、经典电影、即将上映，根据各个参数选择最佳影片展示出来
 * added by xiongjiewu 2013-08-18
 * Class initializationModule
 */
include("jobBase.php");
class initializationModule extends jobBase {

    private $_nowTime;

    public function __construct()
    {
        parent::__construct();
        $this->_nowTime = time();
    }
    public function run() {
        //最新上映电影
        $newMovieInfo = $this->_getNewMovieInfo();
        if (!empty($newMovieInfo)) {
            $newMovieIdArr = array();
            foreach($newMovieInfo as $newMovie) {
                $newMovieIdArr[] = $newMovie['id'];
            }
            $newMovieIdStr = implode(";",$newMovieIdArr);
            $this->_updateInfo(array("type" => 1),array("infoIdStr" => $newMovieIdStr),"tbl_newest");
        }

        //即将上映电影信息
        $commingMovieInfo = $this->_getCommingMovieInfo();
        if (!empty($commingMovieInfo)) {
            $commingMovieIdArr = array();
            foreach($commingMovieInfo as $commingMovie) {
                $commingMovieIdArr[] = $commingMovie['id'];
            }
            $commingMovieIdStr = implode(";",$commingMovieIdArr);
            $this->_updateInfo(array("type" => 2),array("infoIdStr" => $commingMovieIdStr),"tbl_newest");
        }


        //经典电影电影信息
        $classMovieInfo = $this->_getClassMovieInfo();
        if (!empty($classMovieInfo)) {
            $classMovieIdArr = array();
            foreach($classMovieInfo as $classMovie) {
                $classMovieIdArr[] = $classMovie['id'];
            }
            $classMovieIdStr = implode(";",$classMovieIdArr);
            $this->_updateInfo(array("type" => 3),array("infoIdStr" => $classMovieIdStr),"tbl_newest");
        }
    }

    /**
     * 获取最新电影信息
     * @param int $limit
     * @return array
     */
    private function _getNewMovieInfo($limit = 50) {
        $sql = "select * from `tbl_detailInfo` where del = 0 and exist_watch = 1 and time1 <= " . $this->_nowTime;
        $sql .= " order by time1 desc,score desc limit " . $limit;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        return $stmt->fetchAll ();
    }

    /**
     * 获取即将电影信息
     * @param int $limit
     * @return array
     */
    private function _getCommingMovieInfo($limit = 50) {
        $sql = "select * from `tbl_detailInfo` where del = 0 and time1 > " . $this->_nowTime;
        $sql .= " order by time1 asc,score desc limit " . $limit;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        return $stmt->fetchAll ();
    }

    /**
     * 获取电影电影信息
     * @param int $limit
     * @return array
     */
    private function _getClassMovieInfo($limit = 50) {
        $sql = "select * from `tbl_detailInfo` where del = 0 and exist_watch = 1 and topType > 0";
        $sql .= " order by score desc limit " . $limit;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        return $stmt->fetchAll ();
    }
}
$do = new initializationModule();
$do->run();