<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/22
 * Time: 11:03 上午
 */

namespace asher\core;


class Reload
{
    public $watch; // 监控的文件夹
    public $md5Flag; // 上次计算的md5的值

    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance(){
        if(!self::$instance instanceof self){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function reload(){
        // 判断当前文件的MD5值跟上一次有没有区别
        $md5 = $this->getMd5();
        if($md5 != $this->md5Flag){
            $this->md5Flag = $md5;
            return true;
        }
        return false;
    }

    public function getMd5(){
        $md5 = '';
        foreach ($this->watch as $dir){
            $md5 .= self::md5file($dir);
        }
        return md5($md5);
    }

    /**
     * 遍历目录，得到所有文件的md5散列值
     * @param $dir
     * @return string
     * @Author:
     * @Date:2020/1/22
     * @Time:11:17 上午
     */
    public function md5file($dir){
        if(!is_dir($dir)){
            return '';
        }
        $md5File = [];
        $d = dir($dir); // 返回文件目录实例
        while(false !== ($entry = $d->read())){
            if($entry == '.' || $entry == '..'){
                continue;
            }
            if(is_dir($dir . '/' . $entry)){
                $md5File[] = self::md5file($dir.'/'.$entry);
            }elseif (substr($entry,-4) === '.php'){
                $md5File[] = md5_file($dir.'/'.$entry);
            }
            $md5File[] = $entry;
        }
        $d->close();
        return md5(implode('',$md5File));
    }
}