<?php
namespace Home\Controller;
use Think\Controller;
class ProductsController extends Controller {
    public function index(){
        $this->display();
    }
    public function view(){
    	$this->display();
    }
}