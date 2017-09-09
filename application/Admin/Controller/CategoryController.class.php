<?php
namespace Admin\Controller;

class CategoryController extends BaseController
{
    //分类列表页
    public function lst(){
        $model = D('category');
        $data = $model->getTree();
        //var_dump($data);exit;
        //设置页面信息
        $this->assign(array(
            'data' => $data,
            '_page_title' => '分类列表',
            '_page_btn_name' => '添加新分类',
            '_page_btn_link' => U('add'),
        ));
        $this->display();
    }

    //删除分类
    public function delete(){
        $model = D('category');
        if($model->delete(I('get.id')) !== FALSE){
            $this->success('删除成功',U('lst'));
        }else{
            $this->success('删除失败！原因：'.$model->getError());
        }
    }

    //添加分类
    public function add()
    {

        $model = D('category');
        //判断用户是否提交表单
        if (IS_POST) {

            //2.CREATE方法：a.接收数据并保存到模型中 b.根据模型中定义的规则验证表单
            /*
             * 第一个参数：要接收的数据默认是$_POST
             * 第二个参数:表单的类型,当前是添加还是修改的表单,1:添加  2:修改
             * $_POST:表单中原始的数据, I('post.'):过滤之后的$_POST数据,过滤XSS攻击
             */
            if ($model->create(I('post.'), 1)) {
                //echo '<pre>';var_dump($data);exit;
                //插入到数据库中
                if ($model->add()) {
                    //显示成功信息并等待1秒后重定向
                    $this->success('操作成功!', U('lst?p='.I('get.p')));
                    exit;
                }
            }
            //如果走到这 说明上面失败了在这里除了失败请求
            //从模型中取出失败的原因
            //由控制器显示错误信息,并在3秒后调回上一个页面
            $this->error($model->getError());
        }
        //取出所有的分类做下拉框
        $catData = $model->getTree();


        //显示页面
        $this->assign(array(
            'catData' => $catData,
            '_page_title' => '添加分类',
            '_page_btn_name' => '分类列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();
    }

    //修改分类
    public function edit(){
        $id = I('get.id');
        $model = D('Category');
        if(IS_POST){
            if($model->create(I('post.'), 2)){
                if($model->save() !== FALSE){
                    $this->success('修改成功！',U('lst'));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $data = $model->find($id);
        //获取所有分类制作下拉框
        $catData = $model->getTree();
        //取出当前分类的子分类
        $children = $model->getChildren($id);

        //显示页面
        $this->assign(array(
            'data' => $data,
            'catData' => $catData,
            'children' => $children,
            '_page_title' => '修改分类',
            '_page_btn_name' => '分类列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();
    }
}

