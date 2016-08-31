<?php
namespace Home\Controller;
use Think\Controller;
class ShowController extends Controller {
    //公司简介
    public function intruduce(){
        $info = M("web_info")->where("type = 1")->find();
        $this->assign("info",$info);
        $this->display();
    }

    //公司文化
    public function cultural(){
        $info = M("web_info")->where("type = 2")->find();
        $this->assign("info",$info);
        $this->display();
    }

    //公司团队
    public function team(){
        $info = M("web_info")->where("type = 3")->find();
        $this->assign("info",$info);
        $this->display();
    }

    //公司新闻
    public function news(){
        $article = M('article');
        $count = $article->where("type = 1")->count();
        $page = new \Think\Page($count,5);
        $list = $article->where("type = 1")->limit($page->firstRow.','.$page->listRows)->order("listorder DESC,id ASC")->select();
        $this->assign('_page', $page->show());
        $this->assign('list',$list);
        $this->display();
    }

    //行业动态
    public function dynamics(){
   	$article = M('article');
        $count = $article->where("type = 2")->count();
        $page = new \Think\Page($count,5);
        $list = $article->where("type = 2")->limit($page->firstRow.','.$page->listRows)->order("listorder DESC,id ASC")->select();
        $this->assign('_page', $page->show());
        $this->assign('list',$list);
        $this->display();
    }

    //联系我们
    public function contact(){
        $info = M("web_info")->where("type = 8")->find();
        $this->assign("info",$info);
        $this->display();
    }

    public function view(){
        $id = I("id");
        if(!$id) $this->error("出错，未找到文章");
        $article = M("article");
        $data['current'] = $article->find($id);
        $data['prev'] = $article->where("id < {$id} AND type = {$data['current']['type']}")->order("id DESC")->find();
        $data['next'] = $article->where("id > {$id} AND type = {$data['current']['type']}")->order("id ASC")->find();
        //随机抽取六篇同类文章
        $data['random'] = $article ->where("id <> {$id} AND type = {$data['current']['type']}")->order("RAND()")->limit(6)->select();
        $this->assign($data);
        $this->display();
    }
}
