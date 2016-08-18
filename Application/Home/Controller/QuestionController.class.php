<?php
namespace Home\Controller;
use Think\Controller;
class QuestionController extends Controller {
	//常见问题
    public function index(){
        $article = M('article');
        $count = $article->where("type = 3")->count();
        $page = new \Think\Page($count,5);
        $list = $article->where("type = 3")->limit($page->firstRow.','.$page->listRows)->order("listorder DESC,id ASC")->select();
        $this->assign('_page', $page->show());
        $this->assign('list',$list);
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
