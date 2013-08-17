<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 图片上传类
 * added by xiongjiewu at 2013-3-7
 */
class Uploadimage extends CI_Controller {

    public function index($data = null,$path = "dy",$fileName = "image",$width = 100,$height = 100)
    {
        $result = array(
            "status" => "no",
            "error" => "服务连接失败，请重新尝试!",
        );

        $imgUpInfo = APF::get_instance()->get_config_value($path,"imgcollocation");
        if (empty($imgUpInfo)) {
            $result['error'] = "指定文件夹不存在";
            return $this->load->view('uploadfile/uploadfilereturn',array("data"=>$result));
        }

        if (empty($_FILES[$fileName])) {
            $result['error'] = "图片不存在";
            return $this->load->view('uploadfile/uploadfilereturn',array("data"=>$result));
        }

        //文件类型
        $tp = APF::get_instance()->get_config_value("img_type","imgcollocation");
        if (!in_array($_FILES[$fileName]["type"],$tp)) {
            $result['error'] = "图片类型不正确";
            return $this->load->view('uploadfile/uploadfilereturn',array("data"=>$result));
        }

        //图片大小限制
        $img_max_size = APF::get_instance()->get_config_value("img_max_size","imgcollocation");

        if ($img_max_size < $_FILES[$fileName]['size']) {        //判断文件的大小
            $result['error'] = "图片大小超出";
            return $this->load->view('uploadfile/uploadfilereturn',array("data" => $result));
        }

        //图片信息
        $fileNameArr = explode(".",$_FILES[$fileName]['name']);
        $fileType = $fileNameArr[count($fileNameArr) - 1];//图片类型
        $file_name =  $imgUpInfo["root"] . "/" . md5($data . date('YmdHis')) . "." . $fileType;

        //开始上传
        $upClass = new UpYun($imgUpInfo['bucket'],$imgUpInfo['user'],$imgUpInfo['password']);
        $upRes = $upClass->putImg($_FILES[$fileName]["tmp_name"],$file_name,true);
        if (empty($upRes)) {
            $result['error'] = "上传失败，请稍后再试";
            return $this->load->view('uploadfile/uploadfilereturn',array("data" => $result));
        } else {
            $result["status"] = "ok";
            if (!empty($imgUpInfo["size"][$width])) {
                $upRes .= $imgUpInfo["size"][$width];
            }
            $result["path"] = $upRes;
            $result["fullPath"] = trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $upRes;
            unset($result["error"]);
            return $this->load->view('uploadfile/uploadfilereturn',array("data" => $result));
        }
    }
}