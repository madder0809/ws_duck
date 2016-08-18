<?php
namespace Home\Controller;
use Think\Controller;
class ShowController extends Controller {
    public function intruduce(){
        $this->display();
    }
    public function cultural(){
        $this->display();
    }
    public function team(){
        $this->display();
    }
    public function news(){
        $article = M('article');
        $count = $article->where("type = 1")->count();
        $page = new \Think\Page($count,5);
        $list = $article->where("type = 1")->limit($page->firstRow.','.$page->listRows)->order("listorder DESC,id ASC")->select();
        $this->assign('_page', $page->show());
        $this->assign('list',$list);
        $this->display();
    }
    public function dynamics(){
        $this->display();
    }
    public function contact(){
        $this->display();
    }
}