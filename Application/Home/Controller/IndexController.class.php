<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$data['news'] = M("article")->limit(5)->order("id DESC")->select();
    	$data['products'] = M("products")->limit(10)->order("id DESC")->select();
    	$data['about'] = M("web_info")->where("type = 1")->find();
    	$this->assign($data);
        $this->display();
    }
}