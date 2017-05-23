<?php

namespace adapter;
/**
 * @see http://medoo.lvtao.net/doc.php
 * @see http://medoo.lvtao.net/doc.where.php
 */
class Mysql{

    static $instance = array();

    /**
     * 获取数据库主库链接
     * @param $isconn 是否立刻链接db
     * @return pdoconnect
     */
    public function getMasterConnect($isconn=false){
        return $this->_getConnect('master', $isconn);
    }

    /**
     * 获取数据库从库链接
     * @param $isconn 是否立刻链接db
     * @return pdoconnect
     */
    public function getSlaveConnect($isconn=false){
        if( isset(self::$instance['_FORCE_']) && self::$instance['_FORCE_'] === true ){
            return $this->_getConnect('master', $isconn);
        }
        return $this->_getConnect('slave', $isconn);
    }

    /**
     * 将接下来的查询强制切换到主库
     * @param $action true:强制切换至主库 false:解除
     * @return null
     */
    public function forceMaster($action=true){
        self::$instance['_FORCE_'] = $action;
    }

    /**
     * 清除mysql单例链接 (避免mysql回收连接出现"mysql server gone away",用于长驻内存的服务)
     * @return null
     */
    public function clearConnect(){
        self::$instance['master'] = null;
        self::$instance['slave'] = null;
    }

    /**
     * 获得查询链接
     * @param $type 主从类型
     * @param $isconn 是否立刻链接db
     * @return pdoconnect
     */
    private function _getConnect($type, $isconn=false){
        if( !isset(self::$instance[$type]) ){
            self::$instance[$type] = new __MethodCALL($type);
        }
        if( $isconn === true ){
            self::$instance[$type]->connectPDO();
        }
        return self::$instance[$type];
    }

    public function __destruct(){}

}

//确保必要时才connect mysql
class __MethodCALL{
    private $_PDO  = null;
    private $_type = null;

    public function __construct($type){
        $this->_type = $type;
    }

    public function __call($function_name, $args){
        if( $this->_PDO === null ){
            $this->connectPDO();
        }
        return call_user_func_array(array($this->_PDO, $function_name), $args);
    }

    public function connectPDO(){
        $conf       = $this->_readDbConfig($this->_type);
        $this->_PDO = new \medoo($conf);
        // if( (new \adapter\Debug())->getStatus() ){
        //     $this->_PDO->pdo = new \DebugBar\DataCollector\PDO\TraceablePDO($this->_PDO->pdo);
        // }
    }

    public function getPDO(){
        return $this->_PDO->pdo;
    }

    private function _readDbConfig($type){
        $config = new \Yaf\Config\Ini(APPLICATION_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR."mysql.ini", APPLICATION_ENVIRONMENT);
        $ips    = explode(',', $config[$type.'_ip']);
        $host   = explode(':', $ips[array_rand($ips)]);
        return array(
            'database_type' => 'mysql',
            'database_name' => $config['database'],
            'server'        => $host[0],
            'port'          => $host[1],
            'charset'       => $config['charset'],
            'username'      => $config['username'],
            'password'      => $config['password']
        );
    }

}
