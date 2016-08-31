<?php
namespace Admin\Controller;

class IndexController extends AdminController {

    
    public function index(){
        /*$article = M("article");
        //通知
        $data['notice']=$article->where("type = 1 AND status = 1")->order('id DESC')->limit(3)->select();
        $this->assign($data);*/
        $this->display();
    }
}