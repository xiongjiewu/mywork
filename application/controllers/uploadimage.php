<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 图片上传类
 * added by xiongjiewu at 2013-3-7
 */
class Uploadimage extends CI_Controller {

    public function index($data = null,$path = "dy",$fileName = "image")
    {
        $result = array(
            "status" => "no",
            "error" => "服务连接失败，请重新尝试!",
        );
        $config['upload_path']         = "./.././img/images/{$path}/";
        $config['allowed_types']     = 'png|gif|jpg';
        $config['max_size']          = '2048';
        $config['max_width']          = '1024';
        $config['max_height']         = '768';
        $config['file_name']         = md5($data . date('YmdHis'));

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload($fileName)) {
            $data['error'] =  $this->upload->display_errors();
        } else {
            $data = array('upload_data' => $this->upload->data());
        }

        if (!empty($data['error'])) {
            $result["error"] = $data['error'];
        } else {
            $result["status"] = "ok";
            $imageFullPath = $data['upload_data']['full_path'];
            $imageFullPathArr = explode("images",$imageFullPath);
            $imageFullPath = "/images" . $imageFullPathArr[1];
            $result["path"] = $imageFullPath;
            $result["fullPath"] = trim(get_config_value("img_base_url"),"/") . $imageFullPath;
            unset($result["error"]);
        }
        $this->load->view('uploadfile/uploadfilereturn',array("data"=>$result));
    }
}