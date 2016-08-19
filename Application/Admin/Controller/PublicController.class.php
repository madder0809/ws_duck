<?php
namespace Admin\Controller;
use Think\Controller;

class PublicController extends Controller {
    function _initialize(){
        
    }
    function login(){
    	if(IS_POST){
    		if($this->verifyLogin(I('username'), I('password')) > 0){
    			$this->success('登录成功！', U('Index/index'));
    		}
    	}else{
    		if(is_login() > 0 ){
    			$this->redirect('Index/index');
    		}else{
    			$this->display();
    		}
    	}
    }

    private function verifyLogin($username, $password){
    	if(!isset($username)){
    		$this->error('用户名不能为空！');
    	}
    	if(!isset($password)){
    		$this->error('密码不能为空！');
    	}
    	$map = array('username'=>$username);
    	$admin_user = M('admin')->where($map)->find();
    	if(is_array($admin_user)){
    		if(md5($password) == $admin_user['password']){
    			$user=array();
    			$user['uid'] = $admin_user['id'];
    			$user['username'] = $admin_user['username'];
                session('uid', $user['uid'],60*60);
    			S('usernmae', $user['username']);
    			return $admin_user['id'];
    		}
    	}
    	$this->error('用户不存在或密码错误');
    }

    public function logout(){
    	session('[destroy]');
        session_destroy();
    	redirect(U('public/login'));
    }

	function upload(){
		$verifyToken = md5('unique_salt' . $_POST['timestamp']);
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			include_once 'Application/Common/Common/UploadHandler.class.php';
			$uploadHandler = new UploadHandler();
			$fileinfo = $uploadHandler->upload();
			echo json_encode($fileinfo);
			exit;
		}
		echo  "没有文件".empty($_FILES);
	}
}
