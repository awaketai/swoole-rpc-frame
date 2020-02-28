<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/19
 * Time: 5:28 下午
 */

namespace asher\core\bean;


class BeanFactory
{
    private static $container = [];

    /**
     * class instance single
     * @var array
     */
    private static $instance = [];
    /**
     * 设置对象
     * @param string $name
     * @param callable $func
     */
    public static function set(string $name,callable $func){

        self::$container[$name] = $func;
    }

    /**
     * get class object
     * @param string $name
     * @param bool $new new object
     * @return mixed|null
     */
    public static function get(string $name,$new = false){

        if(isset(self::$container[$name])){
            if(!$new){
                if(isset(self::$instance[$name])){
                    return self::$instance[$name];
                }
            }

            // invoke anonymous function
            $class = (self::$container[$name])();
            if(is_string($class)){
                $class = str_replace('/','\\',$class);
                self::$instance[$name] = new $class;
            }elseif(is_object($class)){
                self::$instance[$name] = $class;
            }
            // the other way given
            return self::$instance[$name];
        }
        return null;
    }
}