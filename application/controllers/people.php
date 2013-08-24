<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站人物详细信息页面
 * added by xiongjiewu at 2013-07-03
 */
class People extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Character');
        $this->load->model('Actinfo');
        $this->load->model('Directorinfo');
        $this->load->model('Backgroundadmin');
        $this->load->model('Characterimg');
        $this->load->set_top_index(-1);
    }
    public function index($id = null)
    {
        $this->set_attr("endcodeId",$id);
        //将id字符串解密，转换成数字id
        $id = intval(APF::get_instance()->decodeId($id));
        if (empty($id)) {
            redirect("/" );
            exit;
        }

        $conditionSt = "id = " . $id . " and del = 0 limit 1;";
        //人物信息
        $characterInfo = $this->Character->getCharacterInfoByCon($conditionSt);
        if (empty($characterInfo[0])) {
            $this->jump_to("/error/index/1/");
            exit;
        }
        //人物信息
        $characterInfo = $characterInfo[0];
        $this->set_attr("characterInfo",$characterInfo);
        //更新人物查看次数
        $this->Character->updateInfoByFiled(array("clickNum" => $characterInfo['clickNum'] + 1),array("id" => $characterInfo['id']));

        //星座信息
        $xingzuoInfo = APF::get_instance()->get_config_value("constellatoryInfo");
        $this->set_attr("xingzuoInfo",$xingzuoInfo);

        //人物电影信息
        $infoIds1 = $infoIds2 = array();
        $movieInfo1 = $this->Actinfo->getActinfoByActinName($characterInfo['name']);
        if (!empty($movieInfo1)) {
            $this->initArrById($movieInfo1,"infoId",$infoIds1);
        }
        $movieInfo2 = $this->Directorinfo->getDirectorinfoByDirectorName($characterInfo['name']);
        if (!empty($movieInfo2)) {
            $this->initArrById($movieInfo2,"infoId",$infoIds2);
        }
        $infoIds = array_merge($infoIds1,$infoIds2);
        if (!empty($infoIds)) {
            $infoIds = array_unique($infoIds);
            $movieTotalInfos = $this->Backgroundadmin->getDetailInfo($infoIds,null,true);
            $this->set_attr("movieTotalInfos",$movieTotalInfos);
        }

        //人物图片
        $imgConditionStr = "characterId = " . $characterInfo['id'] . " and del = 0";
        $peopleImgInfo = $this->Characterimg->getCharacterImgInfoByCon($imgConditionStr);
        $this->set_attr("peopleImgInfo",$peopleImgInfo);

        //是否显示图片
        $type = $this->input->get("type");
        $showImg = ($type == "img" && !empty($peopleImgInfo)) ? true : false;
        $this->set_attr("showImg",$showImg);

        $this->load->set_title("{$characterInfo['name']} - " . $this->base_title .  " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/people.css"));
        $this->load->set_js(array("js/dianying/people.js"));
        $this->set_view('dianying/people','base3');
    }
}