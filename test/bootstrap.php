<?php
date_default_timezone_set("Asia/Shanghai");
define('ROOTDIRECTORY_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', ROOTDIRECTORY_PATH.'/application');
define('APPLICATION_ENVIRONMENT', 'local');
require_once ROOTDIRECTORY_PATH."/vendor/autoload.php";

class PHPUnit_YafTestCase extends PHPUnit_Framework_TestCase{

    public $application;
    public function setUp(){
        $this->application = Yaf\Registry::get('Application');
        if( $this->application ){
            return;
        }
        $this->application = new Yaf\Application( APPLICATION_PATH."/config/application.ini", APPLICATION_ENVIRONMENT);
        $this->application->bootstrap();
        Yaf\Registry::set('Application', $this->application);
        Yaf\Dispatcher::getInstance()->setView( PHPUnit_MockYafView::getInstance() );
    }

}

Final Class PHPUnit_MockYafView implements Yaf\View_Interface {

    private $_tpl_vars = array();

    private static $_instance = NULL;
    public  static function getInstance(){
        if( ! ( self::$_instance instanceof self) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function setScriptPath($view_directory) {
        $this->_view_directory = $view_directory;
    }

    public function getScriptPath() {
        return $this->_view_directory;
    }

    public function get($key=NULL) {
        if ( is_null($key) ) 
            return $this->_tpl_vars;
        return isset($this->_tpl_vars[$key]) ? $this->_tpl_vars[$key] : NULL;
    }

    public function assign($spec, $value = null) {
        if ( ! is_array($spec) ) {
            $this->_tpl_vars[$spec] = $value;
            return;
        }
        foreach ($spec as $key => $value) {
            $this->_tpl_vars[$key] = $value;
        }
    }

    public function render( $view_path, $tpl_vars = NULL) {
        return;
    }

    public function display( $view_path, $tpl_vars = NULL) {
        return;
    }

    public function clear() {
        $this->_tpl_vars = array();
    }

    public function __get($key) {
        return $this->get($key);
    }
}

Final Class PHPUnit_MockYafRequest extends Yaf\Request_Abstract {

    public function setModuleName($name){
        $this->module = $name;
    }

    public function getModuleName(){
        return $this->module ? $this->module : 'index';
    }

    public function setControllerName($name){
        $this->controller = $name;
    }

    public function setActionName($name){
        $this->action = $name;
    }

    public function setMethod($name){
        $this->method = $name;
    }

    public function setPost($name, $value) {
        $_POST[$name] = $value;
    }

    public function getPost($name = NULL) {
        if ( is_null($name) ) 
            return $_POST;
        return isset( $_POST[$name]) ? $_POST[$name] : NULL;
    }

    public function setQuery($name, $value) {
        $_GET[$name] = $value;
    }

    public function getQuery($name = NULL) {
        if ( is_null($name) ) 
            return $_GET;
        return isset( $_GET[$name]) ? $_GET[$name] : NULL;
    }

    public function setCookie($name, $value) {
        $_COOKIE[$name] = $value;
    }

    public function getCookie($name = NULL) {
        if ( is_null($name) ) 
            return $_COOKIE;
        return isset( $_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
    }

    public function setSession($name, $value) {
        $_SESSION[$name] = $value;
    }

}