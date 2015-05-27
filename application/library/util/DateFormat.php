<?php

class Util_DateFormat{

    public static function agotime($time) {
        $diff =  time() - $time;
        if ($diff<60)
            return $diff . "秒前";
        $diff = round($diff/60);
        if ($diff<60)
            return $diff . "分钟前";
        $diff = round($diff/60);
        if ($diff<24)
            return $diff . "小时前";
        $diff = round($diff/24);
        if ($diff<7)
            return $diff . "天前";
        $diff = round($diff/7);
        if ($diff<4)
            return $diff . "周前";
        $diff = round($diff/4);
        if($diff < 12) 
            return $diff . '月前';
        return date("Y-m-d H:i:s", $time);
    }

}