<?php
namespace Home\Controller;
use Think\Controller;
class ProductsController extends Controller {
    public function index(){
    	$products = M("products");
    	$count = $products->count();
    	$page = new \Think\Page($count,6);
    	$list = $products->limit($page->firstRow.','.$page->listRows)->select();
    	$page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
    	$this->assign('_page', $page->show());
        $this->assign('list',$list);
        $this->display();
    }
    public function view(){
    	$id = I("id");
        if(!$id) $this->error("出错，未找到产品");
    	$products = M("products");
        $data['current'] = $products->find($id);
        $data['prev'] = $products->where("id < {$id}")->order("id DESC")->find();
        $data['next'] = $products->where("id > {$id}")->order("id ASC")->find();
        $data['random'] = $products ->where("id <> {$id}")->order("RAND()")->limit(6)->select();
        $this->assign($data);
    	$this->display();
    }
}