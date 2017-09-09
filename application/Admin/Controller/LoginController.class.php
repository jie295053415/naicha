<?php
/**
 * Created by PhpStorm.
 * User: KiTLoYuan
 * Date: 2017/8/31
 * Time: 19:45
 */

namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller
{
    protected $insertField = array('username', 'password', 'cpassword', 'chkcode');
    protected $updateField = array('id', 'username', 'password', 'cpassword');

    //登录
    public function login()
    {
        if(IS_POST){
            $model = D('Admin');
            //接收表单并且验证表单
            if($model->validate($model->_login_validate)->create()){
                if($model->login()){
                    $this->success('登录成功！',U('Index/index'));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $this->display();
    }

    public function logout()
    {
        $model = D('Admin');
        $model->logout();
        redirect('login');
    }

    //验证码
    public function chkcode()
    {
        $Verify = new \Think\Verify(array(
            'fontSize' => 30,     //验证码字体大小
            'length' => 2,        //验证码位数
            'useNoise' => TRUE,   //关闭验证码杂点
            'codeSet' => '1234567890',
        ));
        $Verify->entry();
    }

}