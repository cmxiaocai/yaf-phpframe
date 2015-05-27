<?php
class DemoTest extends PHPUnit_YafTestCase{

    public function setUp(){
        parent::setUp();
    }

    public function testController(){

        $request = new PHPUnit_MockYafRequest("GET", "index", "demo", 'view', array());
        $request->setModuleName('index');
        $request->setControllerName('demo');
        $request->setActionName('view');
        $request->setPost('aid', 101);
        $request->setPost('caption', 'xxxxxxx');
        // $request->setQuery('aid', 101);
        // $request->setQuery('caption', 'xxxxxxx');
        $this->application->getDispatcher()->dispatch($request);

        $title = PHPUnit_MockYafView::getInstance()->get('title');
        $data  = PHPUnit_MockYafView::getInstance()->get('data');
        $post  = PHPUnit_MockYafView::getInstance()->get('post');

        $this->assertEquals('*模板使用示例*', $title);
        $this->assertEquals(1, $data[0]['uid']);
        $this->assertEquals('xiaocai', $data[0]['name']);
        $this->assertEquals(101, $post['aid']);
        $this->assertEquals('xxxxxxx', $post['caption']);
    }

    public function testService(){
        $services = new \services\ucenter\Login();
        $result   = $services->getUserInfo();
        $this->assertEquals(true, is_array($result));
        $this->assertEquals(339, $result['uid']);
    }

    public function testModel(){
        $model  = new \UserModel();
        $result = $model->fetchListByUids(array(1,2));
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('xiaocai', $result[0]['name']);
    }

}