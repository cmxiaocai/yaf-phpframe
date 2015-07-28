<?php
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用
 * @author xiaocai
 * @since  2014-3-3
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 */
class Bootstrap extends Yaf\Bootstrap_Abstract{

	private $__config = array();

	// 初始化配置信息
	public function _initConfig() { 
		$this->__config = Yaf\Application::app()->getConfig();

		//注册本地类前缀
		$namespace = $this->__config['application']['library']['localnamespace'];
		Yaf\Loader::getInstance()->registerLocalNamespace( explode(',', $namespace) );
	}
	
	// 初始化路由配置
	public function _initRoute(Yaf\Dispatcher $dispatcher) {
		$router = $dispatcher->getRouter();
		$config = new Yaf\Config\Ini( APPLICATION_PATH . "/config/routes.ini" );
		if ( ! empty($config->routes) )
			$router->addConfig($config->routes);
	}
	
	// 初始化模板路径
	public function _initView(Yaf\Dispatcher $dispatcher){
		define('APPLICATION_VIEWS', APPLICATION_PATH . '/views/');
	}
	
	//捕获异常
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