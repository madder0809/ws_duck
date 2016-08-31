<?php
namespace Admin\Controller;

class InfoController extends AdminController {
	private $validate = array(
        array('title', 'require', '标题不能为空'),
        array('content', 'require', '内容不能为空'),
        );

	public function _initialize(){
		parent::_initialize();
		$type = I("type") ? I("type") : 1;//默认为1
		$this->type = $type;
        $this->type_name = array(1=>"公司简介",2=>"公司文化",3=>"公司团队",4=>"投资预算",5=>"加盟条件",6=>"加盟流程",7=>"加盟费用",8=>"联系我们");
	}

    public function index(){
    	//$type = $this->type;
    	$data['list'] = M("web_info")->order("id ASC")->select();
        $data['type_name'] = $this->type_name;
    	$this->assign($data);
        $this->display();
    }


    //编辑
    public function edit(){
        $id = I("id");
        if(!$id){
            $this->error("出错了");
        }
        $web_info = M("web_info");
        if(IS_POST){
            if($web_info->validate($this->validate)->create()){
                if($web_info->save()!==false){
                    $this->success("编辑成功",U("index",array("type"=>$this->type)));
                }
            }else{
                $this->error($web_info->getError());
            }
        }else{
            $data['id'] = $id;
            $data['type'] = $this->type;
            $data['status'] = $this->status;
            $info = $web_info->find($id);
            $data['info'] = $info;
            $this->assign($data);
            $this->display("edit");
        }
    }

    //删除
    public function del(){
        $id = I('id');
        if(!$id) $this->error("没找到对应文章");
        if(M('web_info')->where("id = {$id}")->delete()){
            $this->success('删除成功', U('index',array("type"=>$this->type)));
        }else{
            $this->error('删除失败');
        }
    }
}