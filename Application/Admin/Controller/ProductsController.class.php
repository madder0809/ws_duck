<?php
namespace Admin\Controller;

class ProductsController extends AdminController {

    public function index(){
        $this->display();
    }

     #添加
    public function add(){
        if(IS_POST){
            $laboratory = M("laboratory");
            if($laboratory->validate($this->validate)->create()){
                if($laboratory->add()){
                    $this->success("添加成功",U("index"));
                }else{
                    $this->error("添加失败");
                }
            }else{
                $this->error($laboratory->getError());
            }
        }else{
            $data['admin_user'] = $this->admin_user_list;
            $this->assign($data);
            $this->ajaxReturn($this->fetch('edit'));
        }
    }
}