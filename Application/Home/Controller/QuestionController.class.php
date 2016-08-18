<?php
namespace Home\Controller;
use Think\Controller;
class QuestionController extends Controller {
	//常见问题
    public function index(){
        $this->display();
    }
    //投资预算
    public function budget(){
        $this->display();
    }
    //加盟条件
    public function condition(){
        $this->display();
    }
    //加盟流程
    public function process(){
        $this->display();
    }
    //加盟费用
    public function cost(){
        $this->display();
    }
}