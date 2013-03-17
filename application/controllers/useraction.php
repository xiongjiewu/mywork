<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站用户行为controller,需登录
 * added by xiongjiewu at 2013-3-3
 */
class Useraction extends CI_Controller
{
    public function index()
    {


    }

    public function post()
    {
        $data = $this->input->post();
        if (empty($data['dyId'])) {
            echo "参数错误";
            exit;
        } elseif (!isset($data['content'])) {
            echo "请输入内容！";
            exit;
        }
        $info['infoId'] = $data['dyId'];
        $info['userName'] = "xiongjiewu";
        $info['userId'] = 1;
        $info['content'] = trim($data['content']);
        $info['time'] = time();
        $this->load->model('Yingping');
        $res = $this->Yingping->insertYingpingInfo($info);
        if (empty($res)) {

        } else {
            $this->jump_to("/detail/index/{$data['dyId']}#createpost");
        }
    }

    public function ding()
    {
        $data = $this->input->post();
        if (empty($data['pid'])) {
            return false;
        }
        $this->load->model('Yingping');
        $res = $this->Yingping->updateYingpingInfoById($data['pid'],array("ding" => "ding + 1"));
        return $res;
    }

}
