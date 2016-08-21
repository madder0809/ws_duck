<?php
namespace Admin\Controller;
use Think\Controller;
class UploadHandler extends Controller{
    public function upload() {
        import('Org.Net.UploadFile');
        $upload = new \UploadFile();
        $upload->maxSize  = 2*1024*1000 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg','png','gif');// 设置附件上传类型
        $upload->savePath =  $this->getSaveDir();
        if(!$upload->upload()) {
            return array("errorMsg"=>$upload->getErrorMsg(),"status"=>0);
        }else{
            $uploadFileInfo = $upload->getUploadFileInfo();
            $uploadFileInfo = $uploadFileInfo[0];
            $path = $uploadFileInfo['savepath'].$uploadFileInfo['savename'];
            //filename带文件路径，name原文件名
            return array('path'=>$path, 'filename'=>$uploadFileInfo['name'],'status'=>1);
        }
        return false;
    }
    
    // 建立目录   /Uploads/Excel/
    function getSaveDir(){
        $dir = "/Uploads";
        if(!is_dir($dir)) {
            if(!mkdir($dir, '0777')){
                $this->error("上传目录 ".$dir." 不存在！");
            }
        }
        $dir .= '/photo/';
        if(!is_dir($dir)) {
            if(!mkdir($dir, '0777')){
                $this->error("上传目录 ".$dir." 不存在！");
            }
        }
        return $dir;
    }
    
}