<?php
namespace Admin\Controller;

class ArticleController extends AdminController {
	public function _initialize(){
		parent::_initialize();
		$type = I("get.type") ? I("get.type") : 1;//默认为1
		$this->type = $type;
	}

    public function index(){
    	$type = $this->type;
    	$menu = array(1=>"新闻动态",2=>"行业动态",3=>"常见问题");
        $this->display();
    }
    
}