<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends Controller {
    function _initialize(){
        if(is_login() < 1 ){
            $this->redirect('Public/login');
        }
    }
}