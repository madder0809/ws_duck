<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends Controller {
	/*public function _initialize(){
		$type = I("get.type");
		if(!$type) $this->error("error");
		$this->type = $type;
	}*/

    public function view(){
    	//$type = I("get.type");
    	$id = I("get.id");
		if(!$id) $this->error("error");
		$where['type'] = $type;
		/*$count = M('article')->where($where)->count();
	    $page = new \Think\Page($count,5);
	    $this->assign('_page', $page->show());*/
	    $info = M('article')->find($id);
	    $this->assign($info);
	    $this->display();
    }
}