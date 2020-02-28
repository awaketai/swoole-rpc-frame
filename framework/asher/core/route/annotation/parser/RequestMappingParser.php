<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/19
 * Time: 3:49 下午
 */

namespace asher\core\route\annotation\parser;


use asher\core\bean\BeanFactory;
use asher\core\Route;

class RequestMappingParser
{
    public function parse($annotation): void {
        if($annotation->getRoute() && $annotation->getHandle()){
            $routeInfo = [
                'routePath' => $annotation->getRoute(),
                'handle' => $annotation->getHandle(),
            ];
//            Route::addRoute('GET',$routeInfo);
            $route = BeanFactory::get('Route');
            $route::addRoute('GET',$routeInfo);
        }
    }
}