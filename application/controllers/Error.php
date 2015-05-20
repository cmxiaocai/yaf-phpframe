<?php
/**
 * 错误控制器, 在发生未捕获的异常时刻被调用
 * @author xiaocai
 * @since  2014-3-3
 */

#define YAF_MAX_BUILDIN_EXCEPTION	10

#define YAF_ERR_BASE 				512
#define YAF_UERR_BASE				1024
#define YAF_ERR_MASK				127

#define YAF_ERR_STARTUP_FAILED 		512
#define YAF_ERR_ROUTE_FAILED 		513
#define YAF_ERR_DISPATCH_FAILED 	514
#define YAF_ERR_NOTFOUND_MODULE 	515
#define YAF_ERR_NOTFOUND_CONTROLLER 516
#define YAF_ERR_NOTFOUND_ACTION 	517
#define YAF_ERR_NOTFOUND_VIEW 		518
#define YAF_ERR_CALL_FAILED			519
#define YAF_ERR_AUTOLOAD_FAILED 	520
#define YAF_ERR_TYPE_ERROR			521

class ErrorController extends Yaf\Controller_Abstract {

	public function errorAction() {
		$exception = $this->getRequest()->getException();
		var_dump($exception);die;
		switch ( $exception->getCode() ) {
			case Yaf\ERR\NOTFOUND\ACTION:
			case Yaf\ERR\NOTFOUND\CONTROLLER:
			case Yaf\ERR\NOTFOUND\MODULE:
			case Yaf\ERR\AUTOLOAD_FAILED:
				if( APPLICATION_ENVIRONMENT != 'com' ){
					echo $exception->getMessage();
				}else{
					header("Status: 404 Not Found");
				}
				break;
			case Yaf\ERR\NOTFOUND\VIEW:
				break;
			default:
				$this->errorCustom($exception);
				break;
		}
		return false;
	}

	public function errorCustom($exception) {

		$module     = method_exists($exception,'getModuleName') ? $exception->getModuleName() : null;
		$action     = method_exists($exception,'getActionName') ? $exception->getActionName() : null;
		$controller = method_exists($exception,'getControllerName') ? $exception->getControllerName() : null;
		
		$this->getView()->assign("module",     $module);
		$this->getView()->assign("controller", $controller);
		$this->getView()->assign("action",     $action);
		$this->getView()->assign("message",    $exception->getMessage());
		$this->getView()->assign("code",       $exception->getCode());
		$this->getView()->assign("line",       $exception->getLine());
		$this->getView()->assign("file",       $exception->getFile());

		$callback = $this->getRequest()->getQuery("callback");
		if( $callback ){
			$callback = htmlentities($_GET['callback'], ENT_QUOTES, "UTF-8");
			$callback = Util_Xss::RemoveXSS($callback);
		}
		$this->getView()->assign("callback", $callback);

		if( YafDebug::isOpen() ){
			YafDebug::$debugbar['exceptions']->addException($exception);
		}
			
		if($module){
			$error_file = APPLICATION_VIEWS . 'error/error_' . strtolower($module) . '.html';
		}else{
			$error_file = APPLICATION_VIEWS . 'error/error.html';
		}
		
		$this->getView()->display($error_file);
	}
}
