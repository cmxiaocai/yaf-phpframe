<?php

class UserModel {

    private $_table = 'cms_user';

    public function __construct(){
        $this->_master = (new Mysql())->getMasterConnect();
        $this->_slave  = (new Mysql())->getSlaveConnect();
    }

    public function findByIds($ids, $limit, $order='time_by_create DESC'){
        return $this->_slave->select($this->_table, ['uid','name'], [
            'AND'   => ['unique'=>$ids],
            'LIMIT' => $limit,
            'ORDER' => $order
        ]);
    }

}