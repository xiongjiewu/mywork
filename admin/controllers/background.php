<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Background extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        if (empty($this->userId) || empty($this->userName) || empty($this->adminId)) {
            redirect(get_url("/login/"), true); //未登录
            exit;
        }
        $this->load->model('Backgroundadmin');
    }

    public function index()
    {
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css"));
        $this->load->set_top_index(0);
        $this->set_view('background/background',"base");

    }

    /**
     * 电影列表
     */
    public function movielist()
    {
        $page = $this->input->get("p");
        $page = $page ? $page : 1;
        $sort = $this->input->get("sort");
        $sort = $sort ? $sort : 1;
        $this->load->set_top_index(1);
        $limit = 20;
        $sortStr = $this->_movieSortType[$sort]['sort'];
        $sortS = false;
        $dyname = trim($this->input->get("dyname"));
        if (!empty($dyname)) {
            $movieList = $this->Backgroundadmin->getDetailInfoBySearchW($dyname,50);
            $count = count($movieList);
            if (empty($movieList)) {
                $idStr = intval($dyname);
                $movieList = $this->Backgroundadmin->getDetailInfo($idStr,0,true);
                $count = count($movieList);
            }
            $this->set_attr("dyname", $dyname);
        } else {
            if ($sort == 8) {
                $sortS = "and time1 <=" . time() . " and exist_watch = 0";
                $sortStr = $sortS . " " . $sortStr;
                $movieList = $this->Backgroundadmin->getDetailInfoList(($page - 1) * $limit, $limit, 0, $sortStr);
                $count = $this->Backgroundadmin->getDetailInfoCount(0, $sortS);
            } else {
                if ($sort == 4 || $sort == 5) {
                    $sortS = "and time1 <=" . time();
                    $sortStr = $sortS . "  " . $sortStr;
                } elseif ($sort == 6 || $sort == 7) {
                    $sortS = "and time1 >" . time();
                    $sortStr = $sortS . " " . $sortStr;
                } elseif ($sort <= 11 && $sort >= 9) {
                    $sortStr = " and " . $sortStr;
                } elseif ($sort <= 13 && $sort >= 12) {
                    $sortStr = " and " . $sortStr;
                }
                $movieList = $this->Backgroundadmin->getDetailInfoList(($page - 1) * $limit, $limit, 0, $sortStr);
                $count = $this->Backgroundadmin->getDetailInfoCount(0, $sortS);
            }
        }
        $this->set_attr("page", $page);
        $this->set_attr("movieList", $movieList);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("sort", $sort);
        $this->set_attr("movieSortType", $this->_movieSortType);

        //专题信息
        $this->load->model('Movietopic');
        $topicInfo = $this->Movietopic->getTopicInfoList();
        $this->set_attr("topicInfo", $topicInfo);
        //系列信息
        $xilieInfo = $this->Movietopic->getTopicInfoList(2);
        $this->set_attr("xilieInfo", $xilieInfo);

        $topicType = $this->input->get("topicType");
        $topicId = $this->input->get("topicId");
        $this->set_attr("topicType", $topicType);
        $this->set_attr("topicId", $topicId);

        $base_url = get_url("/background/movielist?sort={$sort}&p=");
        $fenye = $this->set_page_info($page, $limit, $count, $base_url);
        $this->set_attr("fenye", $fenye);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/movielist.css"));
        $this->load->set_js(array("js/background/movielist.js"));
        $this->set_view('background/movielist',"base");
    }

    /**
     * 上传电影
     */
    public function upmovie()
    {
        $error = $this->input->get("error");
        $success = $this->input->get("success");
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/upmovie.css"));
        $this->load->set_js(array("js/background/upmovie.js", "js/My97DatePicker/WdatePicker.js"));
        $this->load->set_top_index(2);
        $this->set_attr("bofangqiType", $this->_bofangqiType);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("moviePlace", $this->_moviePlace);
        $this->set_attr("error", $error);
        $this->set_attr("success", $success);
        $this->set_view('background/upmovie',"base");
    }

    /**
     * 上传电影数据库操作
     */
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
        $dataData['nianfen'] = substr($dataData['nianfen'], 0, 4);
        $dataData['image'] = $dataData['image_url'];
        unset($dataData['image_url']);
        unset($dataData['submit']);
        
        $checkRes = $this->Backgroundadmin->checkDetail($dataData); //检查各个参数
        if (!$checkRes['code']) { //参数有错误
            redirect("/background/upmovie?error=" . $checkRes['error']);
        } else {
            $dataData['name'] = trim($dataData['name']);
            if (!empty($watchLink)) {//是否存在观看链接标志位
                $watch = str_replace(";","",$watchLink);
                if (!empty($watch)) {
                    $dataData['exist_watch'] = 1;
                } else {
                    $dataData['exist_watch'] = 0;
                }
            } else {
                $dataData['exist_watch'] = 0;
            }

            if (!empty($downloadLink)) {//是否存在下载链接标志位
                $down = str_replace(";","",$downloadLink);
                if (!empty($down)) {
                    $dataData['exist_down'] = 1;
                } else {
                    $dataData['exist_down'] = 0;
                }
            } else {
                $dataData['exist_down'] = 0;
            }

            $id = $this->Backgroundadmin->insertDetailInfo($dataData);
            if (!$id) {
                redirect(get_url("/background/upmovie?error=数据库链接失败，请稍候再试"));
            } else {
                if (!empty($watchLink)) {
                    $watchLinkArr = explode(";", $watchLink);
                    $boFangQiArr = explode(";", $boFangQi);
                    $qingXiArr = explode(";", $qingXi);
                    $shouFeiArr = explode(";", $shouFei);
                    foreach ($watchLinkArr as $watchKey => $watchValue) {
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
                    $downloadLinkArr = explode(";", $downloadLink);
                    $sizeArr = explode(";", $size);
                    $downloadTypeArr = explode(";", $downloadType);
                    foreach ($downloadLinkArr as $downloadKey => $downloadvalue) {
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

    public function user($page = 1)
    {
        $username = trim($this->input->get("username"));
        $this->load->model('User');
        if (empty($username)) {
            $page = empty($page) || $page <= 0 ? 1 : $page;
            $count = $this->User->getUserCount();
            $limit = 10;
            $page = ($count > 0 && ($page > ceil($count / $limit))) ? ceil($count / $limit) : $page;
            $userInfoList = $this->User->getUserList(($page - 1) * $limit, $limit);
            $this->set_attr("userInfoList", $userInfoList);
        } else {
            $this->set_attr("username", $username);
            $userInfoList = $this->User->getUserInfoBySearchW($username);
            $this->set_attr("userInfoList", $userInfoList);
            $page = 1;
            $count = 0;
            $limit = 10;
        }
        $userIds = array();
        foreach ($userInfoList as $userVal) {
            $userIds[] = $userVal['id'];
        }
        if (empty($this->Admin)) {
            $this->load->model('Admin');
        }
        $userAdminInfo = $this->Admin->getUserInfosByIds($userIds);
        $userAdminInfo = $this->initArrById($userAdminInfo, "userId");
        $this->set_attr("userAdminInfo", $userAdminInfo);
        $loginAdminInfo = $this->Admin->getAdminInfoByUserId($this->userId);
        $this->set_attr("loginAdminInfo", $loginAdminInfo);
        $baseUrl = get_url("/background/user/");
        $fenye = $this->set_page_info($page, $limit, $count, $baseUrl);
        $this->set_attr("fenye", $fenye);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/user.css"));
        $this->load->set_js(array("js/background/user.js"));
        $this->load->set_top_index(3);
        $this->set_view('background/user',"base");
    }

    public function action()
    {
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
        $idArr = explode(";", $id);
        $idNew = array();
        foreach ($idArr as $idVal) {
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
            $info = $this->updateTheLastest($idNew, 1);
        } elseif ($type == 2) {
            $info = $this->writeTheLastest($idNew, 1);
        } elseif ($type == 3) {
            $info = $this->updateTheLastest($idNew, 2);
        } elseif ($type == 4) {
            $info = $this->writeTheLastest($idNew, 2);
        } elseif ($type == 5) {
            $info = $this->updateTheLastest($idNew, 3);
        } elseif ($type == 6) {
            $info = $this->writeTheLastest($idNew, 3);
        } elseif ($type == 7) {
            $info = $this->upDetailInfo($idNew, array("del" => 1));
        } elseif ($type == 8) {
            $info = $this->upDetailInfo($idNew, array("del" => 0));
        } elseif ($type == 9) {
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

    public function updateTheLastest($id = array(), $type = 1)
    {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        if (empty($id) || empty($type) || ($type != 1 && $type != 2 && $type != 3)) {
            return $result;
        }
        $idStr = implode(";", $id);
        $idStr .= ";";
        
        $info = $this->Backgroundadmin->getNewestInfo($type);
        $infoArr = array("infoIdStr" => $idStr);
        if (empty($info)) {
            $infoArr['type'] = $type;
            return $this->Backgroundadmin->insertNewestInfo($infoArr);
        } else {
            return $this->Backgroundadmin->updateNewestInfo($infoArr, $type);
        }
    }

    public function writeTheLastest($id = array(), $type = 1)
    {
        $result = array(
            "code" => 1,
            "infto" => "参数错误",
        );
        if (empty($id) || empty($type) || ($type != 1 && $type != 2 && $type != 3)) {
            return $result;
        }
        $idStr = implode(";", $id);
        $idStr .= ";";
        
        $info = $this->Backgroundadmin->getNewestInfo($type);
        if (!empty($info[0])) {
            $idStr = $info[0]['infoIdStr'] . $idStr;
        }
        $infoArr = array("infoIdStr" => $idStr);
        if (empty($info)) {
            $infoArr['type'] = $type;
            return $this->Backgroundadmin->insertNewestInfo($infoArr);
        } else {
            return $this->Backgroundadmin->updateNewestInfo($infoArr, $type);
        }
    }

    public function upDetailInfo($id = array(), $data = array())
    {
        if (empty($id) || empty($data)) {
            return false;
        }
        
        return $this->Backgroundadmin->updateDetailInfoById($id, $data);
    }

    public function delDetailInfo($id)
    {
        if (empty($id)) {
            return false;
        }
        
        return $this->Backgroundadmin->deleteDetailInfoById($id);
    }

    /**
     * 编辑电影信息
     */
    public function editmovie()
    {
        $id = $this->input->get("id");
        $status = $this->input->get("status");
        
        $dyInfo = $this->Backgroundadmin->getDetailInfo($id);
        if (empty($dyInfo)) {
            redirect(get_url("/background/"));
        }
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($id);
        $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($id);

        $error = $this->input->get("error");
        $success = $this->input->get("success");
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/editmovie.css"));
        $this->load->set_js(array("js/background/editmovie.js", "js/My97DatePicker/WdatePicker.js"));
        $this->load->set_top_index(-1);
        $this->set_attr("id", $id);
        $this->set_attr("status", ($status == "base") ? true : false);
        $this->set_attr("error", $error);
        $this->set_attr("success", $success);
        $this->set_attr("dyInfo", $dyInfo);
        $this->set_attr("watchLinkInfo", $watchLinkInfo);
        $this->set_attr("downLoadLinkInfo", $downLoadLinkInfo);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("moviePlace", $this->_moviePlace);
        $this->set_attr("bofangqiType", $this->_bofangqiType);
        $this->set_attr("downLoadType", $this->_downLoadType);
        $this->set_attr("qingxiType", $this->_qingxiType);
        $this->set_attr("shoufeiType", $this->_shoufeiType);
        $this->set_view('background/editmovie',"base");
    }

    /**
     * 编辑电影信息数据库操作
     */
    public function updatedy()
    {
        $dataData = $this->input->post();
        unset($dataData['return']);
        unset($dataData['upload_url']);
        $id = $dataData['id'];
        unset($dataData['id']);
        $status = $dataData['status'];
        unset($dataData['status']);
        if (empty($dataData['time0'])) {
            $dataData['time0'] = 0;
        } else {
            $dataData['time0'] = strtotime($dataData['time0']);
        }

        if (empty($dataData['time1'])) {
            $dataData['time1'] = 0;
        } else {
            $dataData['time1'] = strtotime($dataData['time1']);
        }

        if (empty($dataData['time2'])) {
            $dataData['time2'] = 0;
        } else {
            $dataData['time2'] = strtotime($dataData['time2']);
        }

        if (empty($dataData['time3'])) {
            $dataData['time3'] = 0;
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

        $dataData['nianfen'] = substr($dataData['nianfen'], 0, 4);
        $dataData['image'] = $dataData['image_url'];
        unset($dataData['image_url']);
        unset($dataData['submit']);
        
        $checkRes = $this->Backgroundadmin->checkDetail($dataData); //检查各个参数
        if (!$checkRes['code']) { //参数有错误
            redirect(get_url("/background/editmovie?id={$id}&error=" . $checkRes['error']));
        } else {
            $dataData['name'] = trim($dataData['name']);
            if (!empty($watchLink)) {
                $watch = str_replace(";","",$watchLink);
                if (!empty($watch)) {
                    $dataData['exist_watch'] = 1;
                } else {
                    $dataData['exist_watch'] = 0;
                }
            } else {
                $dataData['exist_watch'] = 0;
            }

            if (!empty($downloadLink)) {
                $down = str_replace(";","",$downloadLink);
                if (!empty($down)) {
                    $dataData['exist_down'] = 1;
                } else {
                    $dataData['exist_down'] = 0;
                }
            } else {
                $dataData['exist_down'] = 0;
            }

            $info = $this->Backgroundadmin->updateDetailInfo($id, $dataData);
            if (!$info) {
                redirect(get_url("/background/editmovie?id={$id}&error=数据库链接失败，请稍候再试"));
            } elseif ($status == 0 ) {
                $del = $this->Backgroundadmin->deleteWatchLinkInfoByInfoId($id);
                if (!$del) {
                    redirect(get_url("/background/editmovie?id={$id}&error=数据库链接失败，请稍候再试"));
                }
                $del = $this->Backgroundadmin->deleteDownLoadInfoByInfoId($id);
                if (!$del) {
                    redirect(get_url("/background/editmovie?id={$id}&error=数据库链接失败，请稍候再试"));
                }
                if (!empty($watchLink)) {
                    $watchLinkArr = explode(";", $watchLink);
                    $boFangQiArr = explode(";", $boFangQi);
                    $qingXiArr = explode(";", $qingXi);
                    $shouFeiArr = explode(";", $shouFei);
                    foreach ($watchLinkArr as $watchKey => $watchValue) {
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
                    $downloadLinkArr = explode(";", $downloadLink);
                    $sizeArr = explode(";", $size);
                    $downloadTypeArr = explode(";", $downloadType);
                    foreach ($downloadLinkArr as $downloadKey => $downloadvalue) {
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
            }
            if ($status == 0) {
                redirect(get_url("/background/editmovie?id={$id}&success={$id}"));
            } else {
                redirect(get_url("/background/editmovie?id={$id}&status=base&success={$id}"));
            }
        }
    }

    public function recycle()
    {
        $dyname = trim($this->input->get("dyname"));
        if (!empty($dyname)) {
            $page = 1;
            $movieList = $this->Backgroundadmin->getDetailInfoBySearchW($dyname,20,1);
            $count = $limit = count($movieList);
            $this->set_attr("dyname", $dyname);
        } else {
            $page = $this->input->get("p");
            $page = $page ? $page : 1;
            $this->load->set_top_index(5);
            $limit = 20;

            $movieList = $this->Backgroundadmin->getDetailInfoList(($page - 1) * $limit, $limit, 1);
            $this->set_attr("page", $page);
            $count = $this->Backgroundadmin->getDetailInfoCount(1);
        }

        $this->load->helper('url');
        $this->load->set_title("电影吧，收回站");
        $this->load->set_css(array("css/background/background.css", "css/background/recycle.css"));
        $this->load->set_js(array("js/background/recycle.js"));
        $this->set_attr("movieList", $movieList);
        $this->set_attr("movieType", $this->_movieType);
        $base_url = get_url("/background/recycle?p=");
        $fenye = $this->set_page_info($page, $limit, $count, $base_url);
        $this->set_attr("fenye", $fenye);
        $this->set_view('background/recycle',"base");
    }

    public function useraction()
    {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        $dataData = $this->input->post();
        if (empty($dataData) || !isset($dataData['type']) || empty($dataData['id'])) {
            echo json_encode($result);
            exit;
        }
        $type = intval($dataData['type']);
        if ($type !== 0 && $type !== 1) {
            echo json_encode($result);
            exit;
        }
        $this->load->model("Admin");
        $adminInfo = $this->Admin->getAdminInfoByUserId($this->userId);
        if (empty($adminInfo)) {
            $result['code'] = 1;
            $result['info'] = "不是管理员，禁止操作！";
            echo json_encode($result);
            exit;
        }
        $id = $dataData['id'];
        $idArr = explode(";", $id);
        $this->load->model("User");
        foreach ($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            $this->User->updateUserInfo(array("status" => $type), array("id" => $idVal));
        }
        $result['code'] = 2;
        $result['info'] = "操作成功！";
        echo json_encode($result);
        exit;
    }

    public function feedback($type = 1, $page = 1)
    {
        $page = (empty($page) || ($page <= 0)) ? 1 : $page;
        $typeS = $type;
        if ($type == 1) {
            $typeS = $reply = null;
        } elseif ($type == 2) {
            $reply = 0;
        } elseif ($type == 3) {
            $typeS = 2;
            $reply = 1;
        } elseif ($type == 4) {
            $typeS = 1;
            $reply = 0;
        } elseif ($type == 5) {
            $typeS = 1;
            $reply = 1;
        }
        $limit = 10;
        $this->load->model("Feedback");
        $count = $this->Feedback->getFeedbackInfoCount($typeS, $reply);
        $feedbackInfoList = $this->Feedback->getFeedbackInfoList(($page - 1) * $limit, $limit, $typeS, $reply);
        $this->set_attr("feedbackInfoList", $feedbackInfoList);
        $baseUrl = get_url("/background/feedback/{$type}/");
        $fenye = $this->set_page_info($page, $limit, $count, $baseUrl);
        $this->set_attr("fenye", $fenye);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/feedback.css"));
        $this->load->set_js(array("js/background/feedback.js"));
        $this->load->set_top_index(4);
        $this->set_view('background/feedback',"base");
    }

    public function delfeedback()
    {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        $dataData = $this->input->post();
        if (empty($dataData) || empty($dataData['id'])) {
            echo json_encode($result);
            exit;
        }
        $id = $dataData['id'];
        $idArr = explode(";", $id);
        $this->load->model("Feedback");
        foreach ($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            $this->Feedback->updateFeedbackInfo(array("del" => 1), array("id" => $idVal));
        }
        $result['code'] = 2;
        $result['info'] = "操作成功！";
        echo json_encode($result);
        exit;
    }

    public function editfeedback($id = null)
    {
        if (empty($id)) {
            $this->jump_to("/");
            exit;
        }
        $this->load->model("Feedback");
        $info = $this->Feedback->getFeedBackInfosByIds($id, false);
        if (empty($info[0])) {
            $this->jump_to("/");
            exit;
        }
        $content = trim($this->input->post("content"));
        if ($content !== "") {
            $this->load->model("Message");
            $data = array(
                "userId" => $info[0]['userId'],
                "time" => time(),
                "content" => $content,
            );
            $mId = $this->Message->insertMessageInfo($data);
            $this->Feedback->updateFeedbackInfo(array("reply" => $mId), array("id" => $id));
            $this->set_attr("mId", $mId);
        }
        $info[0]['content'] = $this->ubb2Html($info[0]['content']);
        $this->set_attr("info", $info[0]);
        $this->load->set_css(array("css/background/background.css", "css/background/editfeedback.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js", "js/xheditor-1.2.1/xheditor_lang/zh-cn.js", "js/background/editfeedback.js"));
        $this->load->set_top_index(4);
        $this->set_view('background/editfeedback',"base");
    }

    public function adminaction()
    {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        $dataData = $this->input->post();
        if (empty($dataData) || !isset($dataData['type']) || empty($dataData['id'])) {
            echo json_encode($result);
            exit;
        }
        $type = intval($dataData['type']);
        if ($type !== 0 && $type !== 1) {
            echo json_encode($result);
            exit;
        }
        $id = $dataData['id'];
        if (empty($this->Admin)) {
            $this->load->model("Admin");
        }
        $adminInfo = $this->Admin->getAdminInfoByUserId($this->userId);
        if (empty($adminInfo) || ($adminInfo['type'] == 0)) {
            $result['code'] = 1;
            $result['info'] = "不是超级管理员，禁止操作！";
            echo json_encode($result);
            exit;
        } else {
            if ($id == $this->userId) {
                $result['code'] = 1;
                $result['info'] = "不能对自己进行操作";
                echo json_encode($result);
                exit;
            } else {
                if ($type === 0) {
                    $this->Admin->deleteAdminByUserId($id);
                } else {
                    $this->Admin->insertAdminInfo(array("userId" => $id, "time" => time(), "type" => 0));
                }
                $result['code'] = 2;
                $result['info'] = "操作成功！";
                echo json_encode($result);
                exit;
            }
        }
    }

    public function sendemail($id = null)
    {
        if (empty($id)) {
            $this->jump_to("/background/user/");
            exit;
        }
        $this->load->model("User");
        $userInfo = $this->User->getUserInfoByFiled(array("id" => $id));
        if (empty($userInfo)) {
            $this->jump_to("/background/user/");
            exit;
        }
        $postData = $this->input->post();
        if (!empty($postData) && ($postData['title'] != '') && ($postData['content'] != '')) {
            $title = trim($postData['title']);
            $content = trim($postData['content']);
            $this->load->model("Email");
            $this->Email->insertEmailInfo(array("title" => $title, "content" => $content, "time" => time(), "email" => $userInfo['email'], "userName" => $userInfo['userName']));
            $this->set_attr("success", true);
        }
        $this->load->set_top_index(3);
        $this->load->set_css(array("css/background/background.css", "css/background/sendemail.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js", "js/xheditor-1.2.1/xheditor_lang/zh-cn.js", "js/background/sendemail.js"));
        $this->set_view('background/sendemail',"base");
    }

    public function help()
    {
        $page = $this->input->get("p");
        $page = $page ? $page : 1;
        $this->load->helper('url');
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/help.css"));
        $this->load->set_js(array("js/background/help.js"));
        $this->load->set_top_index(6);
        $limit = 20;
        $this->load->model('Help');
        $helpList = $this->Help->getHelpInfoList(($page - 1) * $limit, $limit);
        $this->set_attr("page", $page);
        $this->set_attr("helpList", $helpList);
        $count = $this->Help->getHelpInfoCount();
        $base_url = get_url("/background/help/?p=");
        $fenye = $this->set_page_info($page, $limit, $count, $base_url);
        $this->set_attr("fenye", $fenye);
        $this->set_view('background/help',"base");
    }

    public function createhelp()
    {
        $title = trim($this->input->post("title"));
        $content = trim($this->input->post("content"));
        if ($title != "" && $content !== "") {
            $this->load->model("Help");
            $data = array(
                "title" => $title,
                "content" => $content,
            );
            $this->Help->insertHelpInfo($data);
            $this->set_attr("success", true);
        }
        $this->load->set_css(array("css/background/background.css", "css/background/createhelp.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js", "js/xheditor-1.2.1/xheditor_lang/zh-cn.js", "js/background/createhelp.js"));
        $this->load->set_top_index(6);
        $this->set_view('background/createhelp',"base");
    }

    public function edithelp($id = null)
    {
        if (empty($id)) {
            $this->jump_to("/");
            exit;
        }
        $this->load->model("Help");
        $info = $this->Help->getHelpInfoByFiled(array("id" => $id));
        if (empty($info)) {
            $this->jump_to("/");
            exit;
        }
        $title = trim($this->input->post("title"));
        $content = trim($this->input->post("content"));
        if ($title != "" && $content !== "") {
            $this->load->model("Help");
            $data = array(
                "title" => $title,
                "content" => $content,
            );
            $this->Help->updateHelpkInfo($data, array("id" => $id));
            $this->set_attr("success", true);
        }
        $this->set_attr("info", $info);
        $this->load->set_css(array("css/background/background.css", "css/background/edithelp.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js", "js/xheditor-1.2.1/xheditor_lang/zh-cn.js", "js/background/edithelp.js"));
        $this->load->set_top_index(4);
        $this->set_view('background/edithelp',"base");
    }

    public function useradd($page = 1)
    {
        $page = empty($page) ? $page : 1;
        $this->load->helper('url');
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/useradd.css"));
        $this->load->set_js(array("js/background/useradd.js"));
        $this->load->set_top_index(7);
        $limit = 20;
        $this->load->model("Usergive");
        $userGiveList = $this->Usergive->getUserGiveListByFiled(array("del" => 0), ($page - 1) * $limit, $limit);
        if (!empty($userGiveList)) {
            $infoIds = array();
            foreach ($userGiveList as $info) {
                $infoIds[] = $info['infoId'];
            }
            $this->set_attr("userGiveList", $userGiveList);
            $this->load->model("Backgroundadmin");
            $infoArr = $this->Backgroundadmin->getDetailInfo($infoIds, null, true);
            $infoArr = $this->initArrById($infoArr, "id");
            $this->set_attr("infoArr", $infoArr);
        }
        $count = $this->Usergive->getUserGiveCountByFiled(array("del" => 0));
        $this->set_attr("page", $page);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("movieSortType", $this->_movieSortType);
        $base_url = get_url("/background/useradd/");
        $fenye = $this->set_page_info($page, $limit, $count, $base_url);
        $this->set_attr("fenye", $fenye);
        $this->set_view('background/useradd',"base");
    }

    public function delusergive()
    {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        $dataData = $this->input->post();
        if (empty($dataData) || empty($dataData['id'])) {
            echo json_encode($result);
            exit;
        }
        $id = $dataData['id'];
        $id = trim($id, ";");
        $idArr = explode(";", $id);
        $this->load->model("Usergive");
        $this->Usergive->updateUserGiveInfoById($idArr, array("del" => 1));
        $result['code'] = 2;
        $result['info'] = "操作成功！";
        echo json_encode($result);
        exit;
    }

    public function edituseradd($id = null)
    {
        if (empty($id)) {
            $this->jump_to("/background/useradd/");
            exit;
        }
        $this->load->model("Usergive");
        $info = $this->Usergive->getUserGiveInfoByFiled(array("id" => $id, "del" => 0));
        if (empty($info)) {
            if (empty($id)) {
                $this->jump_to("/background/useradd/");
                exit;
            }
        }
        $postData = $this->input->post();
        if (!empty($postData)) {
            $data['infoId'] = $info['infoId'];
            $data['link'] = mysql_real_escape_string($postData['link']);
            $this->load->model("Backgroundadmin");
            if ($info['type'] == 1) {
                $data['player'] = $postData['watchType'];
                $data['qingxi'] = $postData['qingxi'];
                $data['shoufei'] = $postData['shoufei'];
                $res = $this->Backgroundadmin->inserWatchLink($data);
            } else {
                $data['size'] = $postData['size'];
                $data['type'] = $postData['downloadType'];
                $res = $this->Backgroundadmin->inserDownLink($data);
            }
            if (!empty($res)) {
                $this->load->model("Usergive");
                $this->Usergive->updateUserGiveInfoById(array("id" => $id), array("del" => 1));
                $this->jump_to("/background/useradd/");
                exit;
            } else {
                $this->set_attr("error", "参数有误");
            }
        }
        $this->set_attr("info", $info);
        $this->set_attr("bofangqiType", $this->_bofangqiType);
        $this->set_attr("qingxiType", $this->_qingxiType);
        $this->set_attr("shoufeiType", $this->_shoufeiType);
        $this->set_attr("downLoadType", $this->_downLoadType);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/edituseradd.css"));
        $this->load->set_js(array("js/background/edituseradd.js"));
        $this->set_view('background/edituseradd',"base");
    }

    /**
     * 抓取列表
     */
    public function grablist() {
        $this->load->model('GrabMovice');
        $page = $this->input->get("p");
        $page = $page ? $page : 1;
        $sort = $this->input->get("sort");
        $sort = empty($sort) ? "all" : $sort;
        $this->load->set_top_index(1);
        $limit = 20;
        $dyname = trim($this->input->get("dyname"));

        $webInfo = APF::get_instance()->get_config_value("zhuaqu_web_info","watchlink");//抓取网站信息
        $this->set_attr("webInfo",$webInfo);

        if (!empty($dyname)) {
            $movieList = $this->GrabMovice->getGrabMoviceInfoBySearchW($dyname);
            $count = count($movieList);
            $this->set_attr("dyname", $dyname);
        } else {
            $sqlF = "";
            if ($sort == "top") {//top电影
                $sqlF = "topType = 1";
            } elseif ($sort != "all") {
                $sortArr = explode("_",$sort);
                if (empty($sortArr)) {
                    $this->jump_to("/background/grablist/");
                    exit;
                }
                $sqlParam = array();
                if (empty($webInfo[$sortArr[0]]['type'])) {
                    $this->jump_to("/background/grablist/");
                    exit;
                }
                $sqlParam[] = "webType = " . $webInfo[$sortArr[0]]['type'];
                if ($sortArr[1] == "later") {
                    $sqlParam[] = "time1 <=" . time();
                } elseif ($sortArr[1] == "comming") {
                    $sqlParam[] = "time1 >" . time();
                } elseif($sortArr[1] == "top") {
                    $sqlParam[] = "topType = 1";
                }
                $sqlF = implode(" and ",$sqlParam);
            }
            $movieList = $this->GrabMovice->getGrabMoviceInfoList(($page - 1) * $limit,$limit,$sqlF);
            $count = $this->GrabMovice->getGrabMoviceInfoCount($sqlF);
        }
        foreach($movieList as $moviceKey => $moviceVal) {
            $checkRes = $this->GrabMovice->checkGrabMovice($moviceVal);//检查该有信息是否有
            $movieList[$moviceKey]['dataCheck'] = $checkRes['code'];
        }
        $this->set_attr("page", $page);
        $this->set_attr("movieList", $movieList);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("sort", $sort);
        $this->set_attr("movieSortType", $this->_movieSortType);

        //抓取配置文件名
        $grabFileName = APF::get_instance()->get_config_value("zhuaqu_movice_id_file_path","watchlink");
        $grabInfo = file_get_contents($grabFileName);
        //抓取信息数组
        $grabInfoArr = json_decode($grabInfo,true);
        $this->set_attr("grabInfoArr", $grabInfoArr);

        $base_url = get_url("/background/grablist?sort={$sort}&p=");
        $fenye = $this->set_page_info($page, $limit, $count, $base_url);
        $this->set_attr("fenye", $fenye);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/grablist.css"));
        $this->load->set_js(array("js/background/grablist.js"));
        $this->set_view('background/grablist',"base");
    }

    /**
     * 编辑抓取电影信息
     */
    public function editgrabmovice() {
        $id = $this->input->get("id");
        $this->load->model('GrabMovice');
        $dyInfo = $this->GrabMovice->getGrabMoviceInfo($id);
        if (empty($dyInfo)) {
            redirect(get_url("/background/grablist/"));
        }

        $error = $this->input->get("error");
        $success = $this->input->get("success");
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/editgrabmovice.css"));
        $this->load->set_js(array("js/background/editgrabmovice.js", "js/My97DatePicker/WdatePicker.js"));
        $this->load->set_top_index(-1);
        $this->set_attr("id", $id);
        $this->set_attr("error", $error);
        $this->set_attr("success", $success);
        $this->set_attr("dyInfo", $dyInfo);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("moviePlace", $this->_moviePlace);
        $this->set_attr("bofangqiType", $this->_bofangqiType);
        $this->set_attr("downLoadType", $this->_downLoadType);
        $this->set_attr("qingxiType", $this->_qingxiType);
        $this->set_attr("shoufeiType", $this->_shoufeiType);
        $this->set_view('background/editgrabmovice',"base");
    }

    /**
     * 执行抓取动作，向文件中插入执行抓取动作信息
     */
    public function grabdo() {
        $grabVal = trim($this->input->post("grabVal"));
        if (empty($grabVal)) {
            echo json_encode(array("code"=>"error","info"=>"参数错误"));
            exit;
        }
        $grabArr = explode("_",$grabVal);
        $webInfo = APF::get_instance()->get_config_value("zhuaqu_web_info","watchlink");//抓取网站信息
        if (empty($webInfo[$grabArr[0]])) {
            echo json_encode(array("code"=>"error","info"=>"参数错误"));
            exit;
        }
        if (empty($webInfo[$grabArr[0]][$grabArr[1]])) {
            echo json_encode(array("code"=>"error","info"=>"目前暂不能抓取该网站相关信息"));
            exit;
        }
        $grabFileName = APF::get_instance()->get_config_value("zhuaqu_movice_id_file_path","watchlink");

        //向文件写入抓取信息
        file_put_contents($grabFileName,json_encode(array("name"=>$grabArr[0],"urlType"=>$grabArr[1])));
        echo json_encode(array("code"=>"success","info"=>"抓取中..."));
        exit;
    }

    /**
     *  把抓取的电影信息inType更新为1，以备放入电影列表
     */
    public function upgradmovicebyid() {
        $id = trim($this->input->post("id"));
        $id = trim($id,";");
        if (empty($id)) {
            echo json_encode(array("code"=>"error","info"=>"参数错误"));
            exit;
        }
        $idArr = explode(";",$id);
        $this->load->model('GrabMovice');
        foreach($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            $this->GrabMovice->updateGrabMoviceInfo($idVal,array("inType"=>1));
        }
        echo json_encode(array("code"=>"success","info"=>"操作成功！"));
        exit;
    }

    /**
     *  根据id字符串（“；”分割的id字符串）删除抓取的电影信息
     */
    public function deletegradmovicebyid() {
        $id = trim($this->input->post("id"));
        $id = trim($id,";");
        if (empty($id)) {
            echo json_encode(array("code"=>"error","info"=>"参数错误"));
            exit;
        }
        $idArr = explode(";",$id);
        $this->load->model('GrabMovice');
        foreach($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            $this->GrabMovice->updateGrabMoviceInfo($idVal,array("del"=>1));
        }
        echo json_encode(array("code"=>"success","info"=>"操作成功！"));
        exit;
    }

    /**
     * 更新抓取的电影信息
     */
    public function updategrabmoviceinfo()
    {
        $dataData = $this->input->post();
        unset($dataData['return']);
        unset($dataData['upload_url']);
        unset($dataData['submit']);
        unset($dataData['in_list']);
        $id = $dataData['id'];
        unset($dataData['id']);
        if (empty($dataData['time0'])) {
            $dataData['time0'] = 0;
        } else {
            $dataData['time0'] = strtotime($dataData['time0']);
        }

        if (empty($dataData['time1'])) {
            $dataData['time1'] = 0;
        } else {
            $dataData['time1'] = strtotime($dataData['time1']);
        }

        if (empty($dataData['time2'])) {
            $dataData['time2'] = 0;
        } else {
            $dataData['time2'] = strtotime($dataData['time2']);
        }

        if (empty($dataData['time3'])) {
            $dataData['time3'] = 0;
        } else {
            $dataData['time3'] = strtotime($dataData['time3']);
        }
        $dataData['nianfen'] = substr($dataData['nianfen'], 0, 4);
        $dataData['image'] = $dataData['image_url'];
        unset($dataData['image_url']);
        $this->load->model('GrabMovice');
        $checkRes = $this->GrabMovice->checkGrabMovice($dataData); //检查各个参数
        if (!$checkRes['code']) { //参数有错误
            $this->jump_to("/background/editgrabmovice?id={$id}&error=" . $checkRes['error']);
            exit;
        } else {
            $dataData['name'] = trim($dataData['name']);
            $info = $this->GrabMovice->updateGrabMoviceInfo($id, $dataData);
            if (!$info) {
                $this->jump_to("/background/editgrabmovice?id={$id}&error=数据库链接失败，请稍候再试");
                exit;
            } else {
                $this->jump_to("/background/editgrabmovice?id={$id}&success={$id}");
                exit;
            }
        }
    }
    /**
     * 最新上映列表
     */
    public function lastlist($type = 1) {
        $type = intval($type);
        $type = in_array($type,array(1,2,3)) ? $type : 1;
        $this->set_attr("type", $type);

        $lastIds = $this->Backgroundadmin->getNewestInfo($type);
        if (!empty($lastIds[0])) {
            $idStr = trim($lastIds[0]['infoIdStr'],";");
            $ids = explode(";",$idStr);
            if (!empty($ids)) {
                $movieList = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                $this->set_attr("moviecount",count($movieList));

                $dyname = trim($this->input->get("dyname"));
                if (!empty($dyname)) {
                    foreach($movieList as $movieKey => $movieVal) {
                        if ($movieVal['name'] == $dyname) {
                            $movieList[$movieKey]['disabled'] = false;
                        } else {
                            $movieList[$movieKey]['disabled'] = true;
                        }
                    }
                    $this->set_attr("dyname", $dyname);
                }
                $this->set_attr("movieList", $movieList);
            }
        }

        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("movieSortType", $this->_movieSortType);

        if ($type == 1) {
            $title = "最新上映";
        } elseif ($type == 2) {
            $title = "即将上映";
        } elseif ($type == 3) {
            $title = "重温经典";
        } else {
            $title = "";
        }
        $this->load->set_title("电影吧，{$title}");
        $this->load->set_css(array("css/background/background.css", "css/background/movielist.css"));
        $this->load->set_js(array("js/background/lastlist.js"));
        $this->set_view('background/lastlist',"base");
    }

    /**
     * 即将上映列表
     */
    public function comminglist() {
        $this->lastlist(2);
    }

    /**
     * 重温经典列表
     */
    public function classlist() {
        $this->lastlist(3);
    }

    /**
     * 删除缓存
     */
    public function removecach() {
        $day = date("Ymd");
        system("rm /home/webapp/www/dianying8/application/cache/home_total_dy_info_" . $day);
        echo json_encode(array("info"=> "删除成功！"));
    }
}
