<?php

namespace asher\core;


class Config
{
    protected static $configMap = []; // config file map

    /**
     * 加载配置文件
     */
    public function load(){
        $files = glob(CONFIG_PATH . '/*.php');
        if($files){
            foreach ($files as $dir => $file) {
                self::$configMap += include $file;
            }
        }
    }

    public function get($key){
        if(isset(self::$configMap[$key])){
            return self::$configMap[$key];
        }
        return null;
    }
}