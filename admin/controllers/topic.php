<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台专题页面
 * added by xiongjiewu at 2013-07-14
 */
class Topic extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        if (empty($this->userId) || empty($this->userName) || empty($this->adminId)) {
            redirect(get_url("/login/"), true); //未登录
            exit;
        }
        $this->load->model('Movietopic');
        $this->load->model('Movietopicmovie');
        $this->load->model('Backgroundadmin');
        $this->load->model('Movietopicimg');
        $this->load->model('Backgroundadmin');
    }

    public function index()
    {
        $this->topiclist();
    }

    /**
     * 专题列表
     */
    public function topiclist() {
        $type = $this->input->get("topicType");
        $type = empty($type) ? 1 : $type;
        $this->set_attr("type",$type);

        $p = $this->input->get("p");
        $p = empty($p) ? 1 : $p;
        $limit = 10;
        $dyname = trim($this->input->get("dyname"));
        if (!empty($dyname)) {
            $topicListInfo = $this->Movietopic->getTopicInfoBySearchW($dyname,$type);
            $topicCount = count($topicListInfo);
            $this->set_attr("dyname", $dyname);
        } else {
            $topicListInfo = $this->Movietopic->getTopicInfoListLimit($type,($p - 1) * $limit,$limit);
            $topicCount = $this->Movietopic->getTopicInfoListCount($type);
        }
        $this->set_attr("topicListInfo",$topicListInfo);
        $this->set_attr("topicCount",$topicCount);

        $base_url = get_url("/topic/topiclist?topicType={$type}&p=");
        $fenye = $this->set_page_info($p, $limit, $topicCount, $base_url);
        $this->set_attr("fenye", $fenye);

        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/topiclist.css"));
        $this->load->set_top_index(4);
        $this->load->set_js(array("js/background/topiclist.js"));
        $this->set_view('background/topiclist',"base");
    }

    /**
     * 创建专题
     */
    public function create() {
        $data = $this->input->get();
        if (!empty($data['error'])) {
            $this->set_attr("error",$data['error']);
        }
        if (!empty($data['success'])) {
            $this->set_attr("success",$data['success']);
        }

        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("zhuantiType",APF::get_instance()->get_config_value("zhuantiType"));

        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/topiccreate.css"));
        $this->load->set_js(array("js/background/topiccreate.js"));
        $this->load->set_top_index(4);
        $this->set_view('background/topiccreate',"base");
    }

    /**
     * 上传电影数据库操作
     */
    public function create_topic_do() {
        $data = $this->input->post();
        if (empty($data)) {
            redirect(get_url("/topic/create?error=参数错误"), true);
            exit;
        }

        $name = trim($data['name']);
        if (empty($name)) {
            redirect(get_url("/topic/create?error=名称不能为空"), true);
            exit;
        }

        $sTitle = trim($data['sTitle']);
        if (empty($sTitle)) {
            redirect(get_url("/topic/create?error=小标题不能为空"), true);
            exit;
        }

        $bTitle = trim($data['bTitle']);
        if (empty($bTitle)) {
            redirect(get_url("/topic/create?error=大标题不能为空"), true);
            exit;
        }

        $sImg = trim($data['sImg']);
        if (empty($sImg)) {
            redirect(get_url("/topic/create?error=小图片不能为空"), true);
            exit;
        }

        $mImg = trim($data['mImg']);
        if (empty($mImg)) {
            redirect(get_url("/topic/create?error=中图片不能为空"), true);
            exit;
        }

        $bImg = trim($data['bImg']);
        if (empty($bImg)) {
            redirect(get_url("/topic/create?error=背景图片不能为空"), true);
            exit;
        }

        $data['createTime'] = time();
        $lastId = $this->Movietopic->insertTopicInfo($data);
        if (!empty($lastId)) {
            redirect(get_url("/topic/create?success=" . $lastId), true);
            exit;
        } else {
            redirect(get_url("/topic/create?error=网络链接失败"), true);
            exit;
        }
    }

    /**
     * 将电影加入专题ajax
     */
    public function addtotopic() {
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        $dataData = $this->input->post();
        if (empty($dataData) || empty($dataData['id']) || empty($dataData['topic']) || empty($dataData['tt'])) {
            echo json_encode($result);
            exit;
        }
        $id = $dataData['id'];
        $tt = $dataData['tt'];
        $idArr = explode(";", $id);
        $topicId = $dataData['topic'];
        $data = array();
        $data['topicId'] = $topicId;
        $data['movieType'] = $tt;
        foreach ($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            //查询是否存在
            $info = $this->Movietopicmovie->getTopicMovieInfo($idVal,$topicId,$tt);
            if (empty($info)) {//不存在则插入
                $data['infoId'] = $idVal;
                $data['createTime'] = time();
                $this->Movietopicmovie->insertTopicMovieInfo($data);
            }
        }
        $result['code'] = 2;
        $result['info'] = "操作成功！";
        echo json_encode($result);
        exit;
    }

    private $_topicMovieLimit = 20;

    /**
     * 专题电影列表
     */
    public function topicmovie() {
        $params = $this->input->get();
        $params['id'] = $id = empty($params['id']) ? 0 : intval($params['id']);
        $params["p"] = $page = empty($params["p"]) ? 1 : intval($params["p"]);
        $params["status"] = $status = !isset($params["status"]) ? -1 : $params["status"];
        $params["movieType"] = $movieType = empty($params["movieType"]) ? 1 : $params["movieType"];
        $this->set_attr("mtype", $movieType);
        $this->set_attr("params", $params);

        $limit = $this->_topicMovieLimit;
        if (empty($id)) {//全部专题电影
            $topicMovieInfo = $this->Movietopicmovie->getTopicMovieList(($page - 1) * $limit,$limit,$movieType,$status);
            $count = $this->Movietopicmovie->getTopicMovieListCount($movieType,$status);
            $topicMovieInfo = $this->initArrById($topicMovieInfo,"infoId",$movieTopicIds);
            $movieList = $this->Backgroundadmin->getDetailInfo($movieTopicIds,0,true);
        } else {//读取某个专题电影
            $topicMovieInfo = $this->Movietopicmovie->getTopicMovieListByTopicId($id,($page - 1) * $limit,$limit,$movieType,$status);
            $count = $this->Movietopicmovie->getTopicMovieListCountByTopicId($id,$movieType,$status);
            $topicMovieInfo = $this->initArrById($topicMovieInfo,"infoId",$movieTopicIds);
            $movieList = $this->Backgroundadmin->getDetailInfo($movieTopicIds,0,true);
        }

        $this->set_attr("page", $page);
        $this->set_attr("count", $count);
        $this->set_attr("topicMovieInfo", $topicMovieInfo);
        $this->set_attr("movieList", $movieList);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_attr("movieSortType", $this->_movieSortType);

        //专题信息(系列信息)
        $this->load->model('Movietopic');
        $topicInfo = $this->Movietopic->getTopicInfoList($movieType);
        $this->set_attr("topicInfo", $topicInfo);

        unset($params['p']);
        $base_url = get_url("/topic/topicmovie?" . http_build_query($params) . "&p=");
        $fenye = $this->set_page_info($page, $limit, $count, $base_url);
        $this->set_attr("fenye", $fenye);
        $this->load->set_top_index(4);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css", "css/background/topicmovie.css"));
        $this->load->set_js(array("js/background/topicmovie.js"));
        $this->set_view('background/topicmovie',"base");
    }

    /**
     * 编辑专题电影信息
     */
    public function editmovie() {
        $data = $this->input->get();
        if (!empty($data['error'])) {
            $this->set_attr("error",$data['error']);
        }
        if (!empty($data['success'])) {
            $this->set_attr("success",$data['success']);
        }

        $id = $data["id"];
        $id = intval($id);
        if (empty($id)) {
            redirect(get_url("/topic/topicmovie"), true);
            exit;
        }

        //查询电影信息
        $movieInfo = $this->Movietopicmovie->getOneTopicMovieInfo($id);
        if (empty($movieInfo)) {//不存在跳转致专题电影列表页
            redirect(get_url("/topic/topicmovie"), true);
            exit;
        }

        //电影详细信息
        if (empty($movieInfo['name'])) {
            $movieDetail = $this->Backgroundadmin->getDetailInfo($movieInfo['infoId']);
            $movieInfo['name'] = $movieDetail['name'];
        }
        $movieInfo['bTitle'] = trim($movieInfo['bTitle']);
        if (empty($movieInfo['bTitle']) && !empty($movieDetail)) {
            $movieInfo['bTitle'] = $movieDetail['jieshao'];
        }
        $this->set_attr("id",$id);
        $this->set_attr("movieInfo",$movieInfo);

        //相关剧照信息
        $movieImgInfo = $this->Movietopicimg->getTopicMovieImgByRelatedId($id,2);
        $this->set_attr("movieImgInfo",$movieImgInfo);

        $this->load->set_title("编辑专题电影--电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/edittopicmovie.css"));
        $this->load->set_js(array("js/background/edittopicmovie.js"));
        $this->load->set_top_index(4);
        $this->set_view('background/edittopicmovie',"base");
    }

    /**
     * 编辑专题电影信息数据库操作
     */
    public function edit_topicmovie_do() {
        $params = $this->input->post();

        //验证表单信息，id+
        if (empty($params['id']) || empty($params['name']) || empty($params['image']) || empty($params['sTitle']) || empty($params['bTitle'])) {
            redirect(get_url("/topic/topicmovie"), true);
            exit;
        }

        $id = intval($params['id']);
        $data['name'] = trim($params['name']);
        $data['image'] = trim($params['image']);
        $data['sTitle'] = trim($params['sTitle']);
        $data['bTitle'] = trim($params['bTitle']);
        $data['status'] = 1;//编辑过后默认电影为激活状态

        //更新操作
        $upRes = $this->Movietopicmovie->updateTopicMovieInfo($id,$data);
        if (empty($upRes)) {
            redirect(get_url("/topic/editmovie?id={$id}&error=网络链接失败"), true);
            exit;
        }

        //处理增加的剧照
        if (!empty($params['image_add']) && !empty($params['image_len'])) {
            $addImageArr = explode(";",$params['image_add']);

            //先删除之前的剧照
            $this->Movietopicimg->updateTopicMovieImgInfo($id,2,array("del" => 1));

            foreach($addImageArr as $imageStr) {
                $imageStr = trim($imageStr);
                if (empty($imageStr)) {
                    continue;
                }
                $imageArr = explode(":",$imageStr);
                if (empty($imageArr[0]) || strpos($imageArr[0],"file") === false) {
                    continue;
                }
                $textStr = str_replace("file","text",$imageArr[0]);
                $title = trim($params[$textStr]);
                if (!empty($title) && !empty($imageArr[1])) {//标题和图片不为空，则插入剧照
                    $imgData = array();
                    $imgData['relatedId'] = $id;
                    $imgData['title'] = $title;
                    $imgData['image'] = trim($imageArr[1]);
                    $imgData['type'] = 2;
                    $imgData['createTime'] = time();
                    $this->Movietopicimg->insertTopicMovieImgInfo($imgData);
                }
            }
        }
        redirect(get_url("/topic/editmovie?id={$id}&success=" . urlencode("编辑成功")), true);
        exit;
    }

    /**
     * 专题相关更新操作
     */
    public function updateaction() {
        $id = $this->input->post("id");
        $type = $this->input->post("type");
        $result = array(
            "code" => 1,
            "info" => "参数错误",
        );
        if (empty($id) || empty($type)) {
            echo json_encode($result);
            exit;
        }

        $idArr = explode(";",$id);
        foreach($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            if ($type == 1) {//激活专题/系列
                $this->Movietopic->updateTopicInfo($idVal,array("status" => 1));
            } elseif ($type == 2) {//删除专题\系列电影
                $this->Movietopicmovie->updateTopicMovieInfo($idVal,array("del" => 1));
            } elseif ($type == 3) {//删除专题/系列
                $this->Movietopic->updateTopicInfo($idVal,array("del" => 1));
            }
        }
        $result['code'] = 2;
        $result['info'] = "操作成功！";
        echo json_encode($result);
        exit;
    }

    /**
     * 专题编辑页面
     */
    public function edittopic() {
        $data = $this->input->get();
        if (!empty($data['error'])) {
            $this->set_attr("error",$data['error']);
        }
        if (!empty($data['success'])) {
            $this->set_attr("success",$data['success']);
        }

        $id = $data['id'];
        $id = intval($id);
        if (empty($id)) {
            redirect(get_url("/topic/"), true);
            exit;
        }

        //查询专题信息
        $topicInfo = $this->Movietopic->getTopicMovieInfo($id);
        if (empty($topicInfo)) {
            redirect(get_url("/topic/"), true);
            exit;
        }
        $this->set_attr("topicInfo",$topicInfo);

        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("zhuantiType",APF::get_instance()->get_config_value("zhuantiType"));

        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/background/background.css","css/background/topicedit.css"));
        $this->load->set_js(array("js/background/topicedit.js"));
        $this->load->set_top_index(4);
        $this->set_view('background/topicedit',"base");
    }

    /**
     * 编辑数据库操作
     */
    public function edit_topic_do() {
        $id = $this->input->post("id");
        $id = intval($id);
        if (empty($id)) {
            redirect(get_url("/topic/"), true);
            exit;
        }

        //查询专题信息
        $topicInfo = $this->Movietopic->getTopicMovieInfo($id);
        if (empty($topicInfo)) {
            redirect(get_url("/topic/"), true);
            exit;
        }

        //编辑信息
        $data = $this->input->post();
        unset($data['id']);
        $upRes = $this->Movietopic->updateTopicInfo($id,$data);
        if (empty($upRes)) {
            redirect(get_url("/topic/edittopic?id={$id}&success=" . urlencode("无任何更新")), true);
            exit;
        } else {
            redirect(get_url("/topic/edittopic?id={$id}&success=" . urlencode("编辑成功")), true);
            exit;
        }
    }
}