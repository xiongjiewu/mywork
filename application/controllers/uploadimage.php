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
        $config['upload_path']         = "./.././img/images/{$path}/";//保存地址
        $config['allowed_types']     = 'png|gif|jpg|PNG|GIF|JPG';//图片类型
        $config['max_size']          = '2048';//限制大小/M
        $config['max_width']          = '1024';//限制最大宽度
        $config['max_height']         = '768';//限制最大高度
        $config['file_name']         = md5($data . date('YmdHis'));

        $this->load->library('upload', $config);

        $data = array();
        if(!$this->upload->do_upload($fileName)) {
            $data['error'] =  $this->upload->display_errors();
        } else {
            $data = array('upload_data' => $this->upload->data());
        }
        if (!empty($data['error'])) {//上传失败
            $result["error"] = $data['error'];
        } else {
            //图片全路径
            $imageFullPath = $data['upload_data']['full_path'];

            //图片剪裁
            $fileNameArr = explode(".",$imageFullPath);
            $fileType = $fileNameArr[count($fileNameArr) - 1];//图片类型
            $this->load->model('Resizeimage');
            $resizeImageInfo = $data['upload_data']['full_path'];
            $simage = $this->_getImage($resizeImageInfo);
            if (!empty($simage)) {
                $resizeImageInfo = $this->Resizeimage->resizeImageDo($simage,$width,$height,$config['upload_path'],$config['file_name'] . "_{$width}x{$height}.",$fileType);
            }

            //获取图片地址
            $imageFullPathArr = explode("images",$resizeImageInfo);
            $imageFullPath = "/images" . $imageFullPathArr[1];

            $result["status"] = "ok";
            $result["path"] = $imageFullPath;
            $result["fullPath"] = trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $imageFullPath;
            unset($result["error"]);
        }
        $this->load->view('uploadfile/uploadfilereturn',array("data"=>$result));
    }

    private function _getImage($resizeImageInfo) {
        $imageInfo = getimagesize($resizeImageInfo);
        switch ($imageInfo[2]) {
            case 1:
                $simage = imagecreatefromgif($resizeImageInfo);
                break;
            case 2:
                $simage = imagecreatefromjpeg($resizeImageInfo);
                break;
            case 3:
                $simage = imagecreatefrompng($resizeImageInfo);
                break;
            case 6:
                $simage = imagecreatefromwbmp($resizeImageInfo);
                break;
            default:
                $simage = false;
        }
        return $simage;
    }
}