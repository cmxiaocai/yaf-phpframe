<?php

namespace services;

class BaseEntity{

    protected $_rule_valid = [];
    private $_data;
    private $_only;

    public function __construct($data=array()){
        if(empty($data)){
            return;
        }
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    public function only(){
        $this->_only = true;
    }

    public function has($key){
        return isset($this->_data[$key]);
    }

    public function isEmpty($key){
        return empty($this->_data[$key]);
    }

    public function remove($key){
        if( array_key_exists($key, $this->_data) ){
            unset($this->_data[$key]);
        }
    }

    public function getData($key=null){
        if( $key === null ){
            return $this->_data;
        }else{
            return $this->_data[$key];
        }
    }

    public function append($key, $arr){
        if(isset($this->_data[$key])){
            $this->_data[$key] = array_merge($this->_data[$key], $arr);
        }else{
            $this->_data[$key] = $arr;
        }
    }

    private function Validator($key, $value){
        if(!isset($this->_rule_valid[$key])){
            return;
        }
        $validator = \Util_Validator::make( $value, $this->_rule_valid[$key]);
    }

    public function __set($key, $value){
        if( $this->_only===true && isset($this->_data[$key])){
            throw new \Exception("Already exists", 500);
        }
        $this->Validator($key, $value);
        $this->_data[$key] = $value;
    }

    public function __get($key){
        if(!isset( $this->_data[$key])){
            return null;
        }
        return $this->_data[$key];
    }

}
