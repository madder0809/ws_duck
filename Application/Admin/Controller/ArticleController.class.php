<?php
namespace Admin\Controller;

class ArticleController extends AdminController {
	private $validate = array(
        array('title', 'require', '标题不能为空'),
        array('content', 'require', '内容不能为空'),
        array('listorder', 'require', '固定级别不能为空'),
        array('publishtime', 'require', '发布时间不能为空')
        );

	public function _initialize(){
		parent::_initialize();
		$type = I("type") ? I("type") : 1;//默认为1
		$this->type = $type;
	}

    public function index(){
    	$type = $this->type;
    	$menu = array(1=>"新闻动态",2=>"行业动态",3=>"常见问题");
    	$list = M("article")->where("type = {$type}")->order("id DESC")->select();
    	$type_name = $menu[$type];
    	$this->assign("type_name",$type_name);
    	$this->assign("list",$list);
    	$this->assign("_menu",$menu);
        $this->display();
    }

    //发布
    public function add(){
        if(IS_POST){
            if(M("article")->validate($this->validate)->create()){
                if(M("article")->add()){
                    $this->success("发布成功",U("index",array("type"=>$this->type)));
                }else{
                    $this->error("发布失败");
                }
            }else{
                $this->error(M("article")->getError());
            }   
        }else{
            $data['type'] = $this->type;
            $data['status'] = $this->status;
            $this->assign($data);
            $this->assign('current_url', U('Article/index', array('type'=>$this->type)));
            $this->display('edit');
        }
    }

    //编辑
    public function edit(){
        $id = I("id");
        if(!$id){
            $this->error("出错了");
        }
        $article = M("article");
        if(IS_POST){
            if($article->validate($this->validate)->create()){
                if($article->save()!==false){
                    $this->success("编辑成功",U("index",array("type"=>$this->type)));
                }
            }else{
                $this->error($article->getError());
            }
        }else{
            $data['id'] = $id;
            $data['type'] = $this->type;
            $data['status'] = $this->status;
            $info = M("article")->find($id);
            $data['info'] = $info;
            $this->assign($data);
            $this->assign('current_url', U('Article/index', array('type'=>$this->type)));
            $this->display("edit");
        }
    }

    //删除
    public function del(){
        $id = I('id');
        if(!$id) $this->error("没找到对应文章");
        if(M('article')->where("id = {$id}")->delete()){
            $this->success('删除成功', U('index',array("type"=>$this->type)));
        }else{
            $this->error('删除失败');
        }
    }
}