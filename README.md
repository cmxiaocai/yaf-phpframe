# 在实际使用中对YAF做了哪些扩展

yaf是高度灵活可扩展的框架，自身不会提供业务功能。往往在业务开发中需要根据情况对yaf进行一定的补充扩展以满足业务实现。以下是在业务使用中总结出的扩展方式可供yaf使用者学习参考。



## 引入Composer

> composer刚好可以弥补yaf不足之处，只需要在`$application->bootstrap()`前引用`vendor/autoload.php`即可~

*示例代码:*

```php
require_once "../vendor/autoload.php";
$application = new Yaf\Application( APPLICATION_PATH."/config/application.ini", APPLICATION_ENVIRONMENT);
$application->bootstrap()->run();
```

*composer.json:*

```json
{
    "config": {
        "secure-http": false
    },
    "require": {
        "phpunit/phpunit": "4.6.6",
        "catfan/medoo": "dev-master"
    },
    "autoload": {
        "psr-0": {
            "adapter" : "application/",
            "services": "application/"
        }
    },
    "minimum-stability": "stable",
    "repositories": [
        {"type": "composer", "url": "http://packagist.phpcomposer.com"},
        {"packagist": false}
    ]
}
```

*执行composer:*

```shell
composer update         #更新包
composer install        #首次安装
composer update nothing #不更新库
```



## Service业务逻辑层	

> 目前市面上的php框架均采用MVC概念，但严格来说Model-View-Controller并非"三层架构"。我们经常会有这样的困扰：这段登陆逻辑的代码应该放在Controller还是封装到Model里？下面以一段用户登陆业务示例演示service层是如何使用。
> 引入service层后的代码结构如下:

*controller*:

> controller的定位是`接收参数->调用业务层->输出呈现`，因此controller本身不负责处理业务逻辑只负责参数的传递。

```php
class AdminController extends Yaf\Controller_Abstract{
    public function loginAction(){
        $account = [
            'account'  => $this->getRequest()->getPost('account'),
            'password' => $this->getRequest()->getPost('password'),
        ]
        $service = new \services\user\login($account);
        if( $service->check() ){
            echo "success";
        }else{
            echo "error";
        }
    }
}
```

*services:*

> services的定位是`业务逻辑处理`，services不会直接对数据进行访问也不能直接获取外部参数(如$_GET,$_POST)，需要借助model才能操作数据。

```php
amespace services\user;
class Login{
    private $_account;

    public function __construct($account){
        $this->_account = $account;
    }

    public function check(){
        $Model  = new UserModel();
        if( !$Model->hasAccount($this->_account) ){
            $this->_writeErrorLog();
            return false;
        }
        return true;
    }

    private function _writeErrorLog(){
        $Model  = new UserLogModel();
        $Model->insert($this->_account);
    }
}
```

*model：*

> model的定位是`数据访问`，model自身不参与任何业务逻辑。( 部分同学会用采用新建model文件的方式解决业务代码和数据访问代码耦合问题。但是只要文件名叫做`model`就会存在被误解的可能，因此独立出一个services目录用来存放业务代码结构会更干净些 )

```php
class UserModel {
    public function hasAccount($account){ /*...*/ }
}
class UserLogModel {
    public function insert($log){ /*...*/ }
}
```



## phpunit单元测试

> 想要保证每个人提交的产品质量，单元测试是必不可少的~yaf高度灵活的特性也使phpunit的引入十分容易。

*如何运行单元测试*

```bash
php wwwroot/vendor/phpunit/phpunit/phpunit --bootstrap wwwroot/test/bootstrap.php /data/wwwroot/test/
```

*mock了哪些功能?*

*PHPUnit_MockYafRequest:*

> 针对controller层测试可以伪造请求

```php
$request = new PHPUnit_MockYafRequest("GET", "index", "demo", 'view', array());
$request->setModuleName('index');
$request->setControllerName('demo');
$request->setActionName('view');
$request->setPost('aid', 101);
$request->setPost('caption', 'xxxxxxx');
$this->application->getDispatcher()->dispatch($request);
```



*PHPUnit_MockYafView:*

> 可以对模板的assign参进行测试

```php
$title = PHPUnit_MockYafView::getInstance()->get('title');
$this->assertEquals('*模板使用示例*', $title);
```



*示例代码:*

```php
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
        $this->application->getDispatcher()->dispatch($request);
      
        $title = PHPUnit_MockYafView::getInstance()->get('title');
        $this->assertEquals('*模板使用示例*', $title);
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
```



## 异常捕获机制

> 是否经常看到大量的if..else..来处理异常逻辑？当异常发生时为何不直接throw出来，让需要的地方捕获到它？

*先看看错误示范:*

> admin和api控制器均要查询用户信息，他们呈现数据的方式也不一样。但底层mysql查询出现异常时需要一层一层的将异常往上抛，因此会出现大量的if...else..判断逻辑。

```php
class AdminController extends Yaf\Controller_Abstract{
    public function seeUserAction(){
        $service = new \services\user\info();
        $info    = $service->query();
        if( !is_array($info) ){
            echo "<div>system error.</div>";
        }else{
            echo "<div>name:{$info['name']}</div>";
        }
    }
}
class ApiController extends Yaf\Controller_Abstract{
    public function userAction(){
        $service = new \services\user\info();
        $info    = $service->query();
        if( !is_array($info) ){
            echo json_encode(['code'=>-1, 'message'=>'system error'])
        }else{
            echo json_encode(['code'=>1, 'data'=>$info])
        }
    }
}

namespace services\user;
class info{
    public function query(){
        $Model = new UserModel();
        $info  = $Model->findAccount();
        if( !is_array($info) ){
            return 'account find error.';
        }
        return $info;
    }
}

class UserModel {
    public function findAccount(){ 
        $result = $db->select(...);
        if( $result === false){
            return 'mysql error.';
        }
        return $result;
    }
}
```

*采用异常捕获之后:*

> 在底层任意地方抛出的异常均能被控制器中的`ExceptionHandler`进行捕获(也可以其他地方进行捕获)，捕获之后根据业务情况作出不同的处理，并且避免了使用if...else...的方式传递异常。这样代码结构会更加简洁易懂。

```php
class AdminController extends Yaf\Controller_Abstract{
    public function seeUserAction(){
        $service = new \services\user\info();
        $info    = $service->query();
        echo "<div>name:{$info['name']}</div>";
    }

    public function defaultExceptionHandler($exception, $view) {
        echo "<div>system error.</div>";
    }
}

class ApiController extends Yaf\Controller_Abstract{
    public function userAction(){
        $service = new \services\user\info();
        $info    = $service->query();
        echo json_encode(['code'=>1, 'data'=>$info])
    }

    public function defaultExceptionHandler($exception, $view) {
        echo json_encode(['code'=>-1, 'message' => $exception->getMessage()]);
    }
}

namespace services\user;
class info{
    public function query(){
        $Model = new UserModel();
        return $Model->findAccount();
    }
}

class UserModel {
    public function findAccount(){ 
        $result = $db->select(...);
        if( $result === false){
            throw new Exception('mysql error.');
        }
        return $result;
    }
}
```

*异常捕获是如何实现的:*

> 在Bootstrap中注册set_exception_handler捕获异常，`ExceptionHandler`异常处理类会根据trace从下往上寻找已定义`defaultExceptionHandler`方法，当上层有定义`defaultExceptionHandler`就执行该函数。

```php
class Bootstrap extends Yaf\Bootstrap_Abstract{
    public function _initException(Yaf\Dispatcher $dispatcher) {
        Yaf\Dispatcher::getInstance()->throwException(true);
        Yaf\Dispatcher::getInstance()->catchException(false);
        set_exception_handler( array(new Yaf_ExceptionHandler(), 'handler') );
    }
}
class Yaf_ExceptionHandler {

    public function handler( $exception ) {
        foreach ( $exception->getTrace() as $trace ) {
            if ( ! method_exists($trace['class'], 'defaultExceptionHandler' ) ) 
                continue;
            call_user_func_array(
                array( $trace['class'], 'defaultExceptionHandler' ), 
                array( $exception, $this->getView() )
            );
            exit();
        }
        $this->defaultExceptionHandler( $exception );
    }

    public function defaultExceptionHandler( Exception $exception ) {
        $this->getView()->setScriptPath(APPLICATION_VIEWS);
        $this->getView()->assign("exception", $exception);
        $this->getView()->display('error/error.html');die;
    }

    public function getView() {
        return Yaf\Dispatcher::getInstance()->initView(APPLICATION_VIEWS);
    }
}
```





## adapter适配

> yaf自身不会提供mysql、redis..操作，针对connect的操作统一存放在adapter目录下。model下的数据访问从adapter中进行调用connect。

*使用方法:*

```php
(new \adapter\Debug())->messages('test~');

$master = (new \adapter\Mysql())->getMasterConnect();
$master->select(...);

$redis = (new \adapter\Redis())->getMasterConnect();
$redis->get('xxx')
```

*示例代码:*

```php
<?php

namespace adapter;

/**
 * redis适配
 */
class Redis{
    static $instance = array();
    public function getMasterConnect(){
        if( isset(self::$instance['master']) ){
            return self::$instance['master'];
        }
        $config = new \Yaf\Config\Ini("redis.ini", APPLICATION_ENVIRONMENT);
        $redis  = new \redis();
        $redis->pconnect($config->host, $config->port, 5);
        self::$instance['master'] = $redis;
        return self::$instance['master'];
    }
}
```

