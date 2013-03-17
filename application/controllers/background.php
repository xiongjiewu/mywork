<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Background extends CI_Controller {

    public function index()
    {
        $this->load->helper('url');
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css"));
        $this->load->set_top_index(0);
        $this->set_background_view('background/background');

    }

    public function movielist()
    {
        $page = $this->input->get("p");
        $page = $page ? $page : 1;
        $sort = $this->input->get("sort");
        $sort = $sort ? $sort : 1;
        $this->load->helper('url');
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/latestmovie.css"));
        $this->load->set_js(array("js/background/latestmovie.js"));
        $this->load->set_top_index(1);
        $limit = 20;
        $this->load->model('Backgroundadmin');
        $sortStr = $this->_movieSortType[$sort]['sort'];
        $sortS = false;
        if ($sort == 4 || $sort == 5) {
            $sortS = "and time1 <=" . time();
            $sortStr = $sortS . "  " . $sortStr;
        }
        if ($sort == 6 || $sort == 7) {
            $sortS = "and time1 >" . time();
            $sortStr = $sortS . " " . $sortStr;
        }
        $movieList = $this->Backgroundadmin->getDetailInfoList(($page- 1) * $limit,$limit,0,$sortStr) ;
        $this->set_attr("page",$page);
        $this->set_attr("movieList",$movieList);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("sort",$sort);
        $this->set_attr("movieSortType",$this->_movieSortType);
        $count = $this->Backgroundadmin->getDetailInfoCount(0,$sortS);
        $base_url = get_url("background/movielist?sort={$sort}&p=");
        $fenye = $this->set_page_info($page,$limit,$count,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->set_background_view('background/movielist');
    }

    public function upmovie()
    {
        $this->load->helper('url');
        $error = $this->input->get("error");
        $success = $this->input->get("success");
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/upmovie.css"));
        $this->load->set_js(array("js/background/upmovie.js"));
        $this->load->set_top_index(2);
        $this->set_attr("error",$error);
        $this->set_attr("success",$success);
        $this->set_background_view('background/upmovie');
    }

    public function upmovieaction()
    {
        $dataData = $this->input->post();
        unset($dataData['return']);
        unset($dataData['upload_url']);
        if (empty($dataData['time0'])) {
            unset($dataData['time0']);
        } else {
            $dataData['time0'] = strtotime($dataData['time0']);
        }

        if (empty($dataData['time1'])) {
            unset($dataData['time1']);
        } else {
            $dataData['time1'] = strtotime($dataData['time1']);
        }

        if (empty($dataData['time2'])) {
            unset($dataData['time2']);
        } else {
            $dataData['time2'] = strtotime($dataData['time2']);
        }

        if (empty($dataData['time3'])) {
            unset($dataData['time3']);
        } else {
            $dataData['time3'] = strtotime($dataData['time3']);
        }

        if (!empty($dataData['watchLink'])) {
            $watchLink = $dataData['watchLink'];
        }
        unset($dataData['watchLink']);

        if (!empty($dataData['downloadLink'])) {
            $downloadLink = $dataData['downloadLink'];
        }
        unset($dataData['downloadLink']);

        if (!empty($dataData['bofangqi'])) {
            $boFangQi = $dataData['bofangqi'];
        }
        unset($dataData['bofangqi']);

        if (!empty($dataData['qingxi'])) {
            $qingXi = $dataData['qingxi'];
        }
        unset($dataData['qingxi']);

        if (!empty($dataData['shoufei'])) {
            $shouFei = $dataData['shoufei'];
        }
        unset($dataData['shoufei']);

        if (!empty($dataData['size'])) {
            $size = $dataData['size'];
        }
        unset($dataData['size']);

        if (!empty($dataData['downloadType'])) {
            $downloadType = $dataData['downloadType'];
        }
        unset($dataData['downloadType']);
        $dataData['nianfen'] = date("Y",strtotime($dataData['nianfen']));
        $dataData['image'] = $dataData['image_url'];
        unset($dataData['image_url']);
        unset($dataData['submit']);
        $this->load->model('Backgroundadmin');
        $checkRes = $this->Backgroundadmin->checkDetail($dataData);//检查各个参数
        $this->load->helper('url');
        if (!$checkRes['code']) {//参数有错误
            redirect("/index.php/background/upmovie?error=" . $checkRes['error']);
        } else {
            $id = $this->Backgroundadmin->insertDetailInfo($dataData);
            if (!$id) {
                redirect(get_url("/background/upmovie?error=数据库链接失败，请稍候再试"));
            } else {
                if (!empty($watchLink)) {
                    $watchLinkArr = explode(";",$watchLink);
                    $boFangQiArr = explode(";",$boFangQi);
                    $qingXiArr = explode(";",$qingXi);
                    $shouFeiArr = explode(";",$shouFei);
                    foreach($watchLinkArr as $watchKey => $watchValue) {
                        if (empty($watchValue)) {
                            continue;
                        }
                        $infoArr = array();
                        $infoArr['infoId'] = $id;
                        $infoArr['link'] = $watchValue;
                        $infoArr['player'] = $boFangQiArr[$watchKey];
                        $infoArr['qingxi'] = $qingXiArr[$watchKey];
                        $infoArr['shoufei'] = $shouFeiArr[$watchKey];
                        $this->Backgroundadmin->inserWatchLink($infoArr);
                    }
                }
                if (!empty($downloadLink)) {
                    $downloadLinkArr = explode(";",$downloadLink);
                    $sizeArr = explode(";",$size);
                    $downloadTypeArr = explode(";",$downloadType);
                    foreach($downloadLinkArr as $downloadKey => $downloadvalue) {
                        if (empty($downloadvalue)) {
                            continue;
                        }
                        $infoArr = array();
                        $infoArr['infoId'] = $id;
                        $infoArr['link'] = $downloadvalue;
                        $infoArr['size'] = $sizeArr[$downloadKey];
                        $infoArr['type'] = $downloadTypeArr[$downloadKey];
                        $this->Backgroundadmin->inserDownLink($infoArr);
                    }
                }
                redirect(get_url("/background/upmovie?success={$id}"));
            }
        }
    }

    public function admin()
    {
        $this->load->helper('url');
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css"));
        $this->load->set_top_index(3);
        $this->set_background_view('background/background');
    }

    public function action() {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        $dataData = $this->input->post();
        if (empty($dataData) || empty($dataData['type']) || empty($dataData['id'])) {
            echo json_encode($result);
            exit;
        }
        $type = intval($dataData['type']);
        $id = $dataData['id'];
        $idArr = explode(";",$id);
        $idNew = array();
        foreach($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            $idNew[] = $idVal;
        }
        if (empty($idNew) || $type < 1 || $type > 8) {
            echo json_encode($result);
            exit;
        }
        if ($type == 1) {
            $info = $this->updateTheLastest($idNew,1);
        } elseif ($type == 2) {
            $info = $this->writeTheLastest($idNew,1);
        } elseif ($type == 3) {
            $info = $this->updateTheLastest($idNew,2);
        } elseif ($type == 4) {
            $info = $this->writeTheLastest($idNew,2);
        } elseif ($type == 5) {

        } elseif ($type == 6) {
            $info = $this->upDetailInfo($idNew,array("del" => 1));
        } elseif ($type == 7) {
            $info = $this->upDetailInfo($idNew,array("del" => 0));
        } elseif ($type == 8) {
            $info = $this->delDetailInfo($idNew);
        }
        if ($info) {
            $result['code'] = 2;
            $result['info'] = "操作成功！";
        } else {
            $result['info'] = "数据库链接失败！";
        }
        echo json_encode($result);
        exit;
    }

    public function updateTheLastest($id = array(),$type = 1)
    {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        if (empty($id) || empty($type) || ($type !=1 && $type != 2)) {
            return $result;
        }
        $idStr = implode(";",$id);
        $idStr .= ";";
        $this->load->model('Backgroundadmin');
        $info = $this->Backgroundadmin->getNewestInfo($type);
        $infoArr = array("infoIdStr" =>$idStr);
        if (empty($info)) {
            $infoArr['type'] = $type;
            return $this->Backgroundadmin->insertNewestInfo($infoArr);
        } else {
            return $this->Backgroundadmin->updateNewestInfo($infoArr,$type);
        }
    }

    public function writeTheLastest($id = array(),$type = 1)
    {
        $result = array(
            "code" => 1,
            "infto" => "参数错误",
        );
        if (empty($id) || empty($type) || ($type !=1 && $type != 2)) {
            return $result;
        }
        $idStr = implode(";",$id);
        $idStr .= ";";
        $this->load->model('Backgroundadmin');
        $info = $this->Backgroundadmin->getNewestInfo($type);
        if (!empty($info[0])) {
            $idStr = $info[0]['infoIdStr'] . $idStr;
        }
        $infoArr = array("infoIdStr" =>$idStr);
        if (empty($info)) {
            $infoArr['type'] = $type;
            return $this->Backgroundadmin->insertNewestInfo($infoArr);
        } else {
            return $this->Backgroundadmin->updateNewestInfo($infoArr,$type);
        }
    }

    public function upDetailInfo($id = array(),$data = array())
    {
        if (empty($id) || empty($data)) {
            return false;
        }
        $this->load->model('Backgroundadmin');
        return $this->Backgroundadmin->updateDetailInfoById($id,$data);
    }

    public function delDetailInfo($id)
    {
        if (empty($id)) {
            return false;
        }
        $this->load->model('Backgroundadmin');
        return $this->Backgroundadmin->deleteDetailInfoById($id);
    }

    public function editmovie()
    {
        $this->load->helper('url');
        $id = $this->input->get("id");
        $this->load->model('Backgroundadmin');
        $dyInfo = $this->Backgroundadmin->getDetailInfo($id);
        if (empty($dyInfo)) {
            redirect(get_url("/background/"));
        }
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($id);
        $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($id);

        $error = $this->input->get("error");
        $success = $this->input->get("success");
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/editmovie.css"));
        $this->load->set_js(array("js/background/editmovie.js"));
        $this->load->set_top_index(-1);
        $this->set_attr("id",$id);
        $this->set_attr("error",$error);
        $this->set_attr("success",$success);
        $this->set_attr("dyInfo",$dyInfo);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);
        $this->set_attr("downLoadLinkInfo",$downLoadLinkInfo);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("bofangqiType",$this->_bofangqiType);
        $this->set_attr("downLoadType",$this->_downLoadType);
        $this->set_attr("qingxiType",$this->_qingxiType);
        $this->set_attr("shoufeiType",$this->_shoufeiType);
        $this->set_background_view('background/editmovie');
    }

    public function updatedy()
    {
        $dataData = $this->input->post();
        unset($dataData['return']);
        unset($dataData['upload_url']);
        $id = $dataData['id'];
        unset($dataData['id']);
        if (empty($dataData['time0'])) {
            unset($dataData['time0']);
        } else {
            $dataData['time0'] = strtotime($dataData['time0']);
        }

        if (empty($dataData['time1'])) {
            unset($dataData['time1']);
        } else {
            $dataData['time1'] = strtotime($dataData['time1']);
        }

        if (empty($dataData['time2'])) {
            unset($dataData['time2']);
        } else {
            $dataData['time2'] = strtotime($dataData['time2']);
        }

        if (empty($dataData['time3'])) {
            unset($dataData['time3']);
        } else {
            $dataData['time3'] = strtotime($dataData['time3']);
        }

        if (!empty($dataData['watchLink'])) {
            $watchLink = $dataData['watchLink'];
        }
        unset($dataData['watchLink']);

        if (!empty($dataData['downloadLink'])) {
            $downloadLink = $dataData['downloadLink'];
        }
        unset($dataData['downloadLink']);

        if (!empty($dataData['bofangqi'])) {
            $boFangQi = $dataData['bofangqi'];
        }
        unset($dataData['bofangqi']);

        if (!empty($dataData['qingxi'])) {
            $qingXi = $dataData['qingxi'];
        }
        unset($dataData['qingxi']);

        if (!empty($dataData['shoufei'])) {
            $shouFei = $dataData['shoufei'];
        }
        unset($dataData['shoufei']);

        if (!empty($dataData['size'])) {
            $size = $dataData['size'];
        }
        unset($dataData['size']);

        if (!empty($dataData['downloadType'])) {
            $downloadType = $dataData['downloadType'];
        }
        unset($dataData['downloadType']);

        $dataData['nianfen'] = date("Y",strtotime($dataData['nianfen']));
        $dataData['image'] = $dataData['image_url'];
        unset($dataData['image_url']);
        unset($dataData['submit']);
        $this->load->model('Backgroundadmin');
        $checkRes = $this->Backgroundadmin->checkDetail($dataData);//检查各个参数
        $this->load->helper('url');
        if (!$checkRes['code']) {//参数有错误
            redirect(get_url("/background/editmovie?id={$id}&error=" . $checkRes['error']));
        } else {
            $info= $this->Backgroundadmin->updateDetailInfo($id,$dataData);
            if (!$info) {
                redirect(get_url("/background/editmovie?id={$id}&error=数据库链接失败，请稍候再试"));
            } else {
                $del = $this->Backgroundadmin->deleteWatchLinkInfoByInfoId($id);
                if (!$del) {
                    redirect(get_url("/background/editmovie?id={$id}&error=数据库链接失败，请稍候再试"));
                }
                $del = $this->Backgroundadmin->deleteDownLoadInfoByInfoId($id);
                if (!$del) {
                    redirect(get_url("/background/editmovie?id={$id}&error=数据库链接失败，请稍候再试"));
                }
                if (!empty($watchLink)) {
                    $watchLinkArr = explode(";",$watchLink);
                    $boFangQiArr = explode(";",$boFangQi);
                    $qingXiArr = explode(";",$qingXi);
                    $shouFeiArr = explode(";",$shouFei);
                    foreach($watchLinkArr as $watchKey => $watchValue) {
                        if (empty($watchValue)) {
                            continue;
                        }
                        $infoArr = array();
                        $infoArr['infoId'] = $id;
                        $infoArr['link'] = $watchValue;
                        $infoArr['player'] = $boFangQiArr[$watchKey];
                        $infoArr['qingxi'] = $qingXiArr[$watchKey];
                        $infoArr['shoufei'] = $shouFeiArr[$watchKey];
                        $this->Backgroundadmin->inserWatchLink($infoArr);
                    }
                }
                if (!empty($downloadLink)) {
                    $downloadLinkArr = explode(";",$downloadLink);
                    $sizeArr = explode(";",$size);
                    $downloadTypeArr = explode(";",$downloadType);
                    foreach($downloadLinkArr as $downloadKey => $downloadvalue) {
                        if (empty($downloadvalue)) {
                            continue;
                        }
                        $infoArr = array();
                        $infoArr['infoId'] = $id;
                        $infoArr['link'] = $downloadvalue;
                        $infoArr['size'] = $sizeArr[$downloadKey];
                        $infoArr['type'] = $downloadTypeArr[$downloadKey];
                        $this->Backgroundadmin->inserDownLink($infoArr);
                    }
                }
                redirect(get_url("/background/editmovie?id={$id}&success={$id}"));
            }
        }
    }

    public function recycle()
    {
        $page = $this->input->get("p");
        $page = $page ? $page : 1;
        $this->load->helper('url');
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/recycle.css"));
        $this->load->set_js(array("js/background/recycle.js"));
        $this->load->set_top_index(4);
        $limit = 20;
        $this->load->model('Backgroundadmin');
        $movieList = $this->Backgroundadmin->getDetailInfoList(($page- 1) * $limit,$limit,1) ;
        $this->set_attr("page",$page);
        $this->set_attr("movieList",$movieList);
        $this->set_attr("movieType",$this->_movieType);
        $count = $this->Backgroundadmin->getDetailInfoCount(1);
        $base_url = get_url("/background/recycle?p=");
        $fenye = $this->set_page_info($page,$limit,$count,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->set_background_view('background/recycle');
    }

}