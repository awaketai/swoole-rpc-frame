<?php
/**
 * Created by PhpStorm.
 * User: Asher
 * Date: 2020/1/16
 * Time: 15:15
 */

namespace asher\core;


class Route
{

    private static $route;

    /**
     * 添加路由操作
     * @param $method
     * @param $routeInfo
     */
    public static function addRoute($method,$routeInfo){

        self::$route[$method][] = $routeInfo;
    }

    /**
     * 路由分发
     */
    public static function dispatch($method,$pathInfo){
        $method = strtoupper($method);
        switch ($method){
            case 'GET':
                foreach (self::$route[$method] as $v){
                    // 判断当前的路径是否在注册的路由内
                    if($pathInfo == $v['routePath']){
                        $handle = explode('@',$v['handle']);
                        $class = strtolower($handle[0]);
                        $method = $handle[1];
                        return (new $class)->$method();
                    }
                }
                break;
            case 'POST':
                break;
        }
    }

    public function getRoute(){

        return self::$route;
    }

}