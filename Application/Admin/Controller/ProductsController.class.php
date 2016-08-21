<?php
namespace Admin\Controller;

class ProductsController extends AdminController {
    private $validate = array(
        array('title', 'require', '标题不能为空'),
        array('url', 'require', '图片不能为空'),
        );

    public function index(){
        $list = M("products")->order('id DESC')->select();
        $this->assign("list",$list);
        $this->display();
    }

     #添加
    public function add(){
        if(IS_POST){
            $products = M("products");
            if($products->validate($this->validate)->create()){
                if($products->add()){
                    $this->success("添加成功",U("index"));
                }else{
                    $this->error("添加失败");
                }
            }else{
                $this->error($products->getError());
            }
        }else{
            $this->ajaxReturn($this->fetch('edit'));
        }
    }

    #编辑
    public function edit(){
        $id = I("id");
        if(!$id) $this->error("出错了");
        $products = M("products");
        if(IS_POST){
            if($products->validate($this->validate)->create()){
                if($products->save()!==false){
                    $this->success("编辑成功",U("index"));
                }else{
                    $this->success("编辑失败",U("index"));
                }
            }else{
                $this->error($products->getError());
            }
        }else{
            $data['id'] = $id;
            $data['products'] = $products->find($id);
            $this->assign($data);
            $this->ajaxReturn($this->fetch('edit'));
        }
    }

    #删除
    public function del(){
        $id = I('id');
        if(empty($id)){
            $this->error('出错了');
        }
        $path = M("products")->where("id = {$id}")->getField("url");
        if(M("products")->delete($id)){
            if(file_exists($path)){
                unlink($path);
            }
            $this->success('删除成功', U('index'));
        }else{
            $this->error('删除失败');
        }
    }
}