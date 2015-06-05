<?php

namespace adapter;

class Mysql{

    static $instance = array();

    public function getMasterConnect(){
        if( isset(self::$instance['master']) ){
            return self::$instance['master'];
        }
        self::$instance['master'] = new \medoo($this->_readConfig('master_ip'));
        return self::$instance['master'];
    }

    public function getSlaveConnect(){
        if( isset(self::$instance['slave']) ){
            return self::$instance['slave'];
        }
        self::$instance['slave'] = new \medoo($this->_readConfig('slave_ip'));
        return self::$instance['slave'];
    }

    private function _readConfig($type){
        $config = new \Yaf\Config\Ini(APPLICATION_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR."mysql.ini", APPLICATION_ENVIRONMENT);
        $ips    = explode(',', $config[$type]);
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

    public function __destruct(){}

}