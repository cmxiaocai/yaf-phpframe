<?php
/**
 * YAF框架示例代码
 * @author xiaocai
 * @since  2014-3-3
 */

class Controller_Demo extends Yaf\Controller_Abstract{

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
		//$this->getView()->display('xxxx.html');
		//$this->getView()->render();
		return true;
	}
	
	//YAF常用操作示例
	public function testAction($page=1){
		
		var_dump($page);

		//$_GET $_POST
		var_dump( $this->getRequest()->getPost("page") );
		var_dump( $this->getRequest()->getQuery("page") );

		//要重定向到的URL
		//$this->getResponse()->setRedirect("http://domain.com/");

		//重定向请求到新的路径
		//$this->redirect("/weibo/mes/ajax/");

		//调用其它控制器方法
		/*
		$this->forward("demo");
		$this->forward("user","demo");
		$this->forward("index","user","demo");
		*/

		//获取当前控制器所属的模块名
		var_dump( $this->getModuleName() );
		//获取当前的请求实例
		var_dump( $this->getRequest() );
		//获取当前的响应实例
		var_dump( $this->getResponse() );

		//获取当前请求中的所有路由参数
		var_dump( $this->getRequest()->getParams() );

		//获取当前请求的类型, 可能的返回值为GET,POST,HEAD,PUT,CLI等.
		var_dump( $this->getRequest()->getMethod() );

		//获取当前请求是否为GET请求,类似的还有isCli isHead isPost isPut isRouted isXmlHttpRequest
		var_dump( $this->getRequest()->isGet() );

		return false;
	}
	
 }