<?php

namespace adapter;

/**
 * redis适配
 */
class Redis{

    static $instance = array();

    /**
     * 返回redis链接实例
     * @return connect
     */
    public function getMasterConnect(){
        if( isset(self::$instance['master']) ){
            return self::$instance['master'];
        }
        $config = new \Yaf\Config\Ini(APPLICATION_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR."redis.ini", APPLICATION_ENVIRONMENT);
        $redis  = new \redis();
        if( APPLICATION_ENVIRONMENT == 'product' ){
            // $redis->auth("passwd");
        }
        $redis->pconnect($config->master->host, $config->master->port, 15);
        $redis->select(6);
        self::$instance['master'] = $redis;
        return self::$instance['master'];
    }

    public function getSlaveConnect(){

    }

    /**
     * 清除redis单例链接
     * @return null
     */
    public function clearConnect(){
        self::$instance['master'] = null;
    }

}
