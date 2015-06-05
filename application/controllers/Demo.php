<?php
/**
 * YAF框架示例代码
 * @author xiaocai
 * @since  2014-3-3
 */
class DemoController extends Yaf\Controller_Abstract{

    public function IndexAction(){
        echo 'yaf~';
        return false;
    }

    //路由器示例
    //http://xiaocai.loc/article-2014-3-1/13124.html
    public function routeAction($date,$arid){
        var_dump($date);
        var_dump($arid);
        return false;
    }

    //模板使用示例
    public function viewAction(){
        $data = array(
            array('uid'=>1, 'name'=>'xiaocai'),
            array('uid'=>2, 'name'=>'xiaomao'),
            array('uid'=>3, 'name'=>'xiaowei'),
        );
        $this->getView()->assign('data',$data);
        $this->getView()->assign('title','*模板使用示例*');
        $this->getView()->assign('post', $this->getRequest()->getPost());
        //$this->getView()->display('xxxx.html');
        //$this->getView()->render();
    }
    
    public function modelAction(){
        $model  = new \UserModel();
        $result = $model->fetchListByUids(array(1,2));
        var_dump($result);
        return false;
    }

    public function servicesAction(){
        $services = new \services\ucenter\Login();
        $result   = $services->getUserInfo();
        var_dump($result);
        return false;
    }

    public function adapterAction(){
        $connect = (new \adapter\Mysql())->getMasterConnect();
        $result  = $connect->select('article', '*');
        var_dump($result);
        return false;
    }

    public function libraryAction(){
        var_dump( Util_DateFormat::agotime( time()-85 ) );
        return false;
    }

 }