<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/19
 * Time: 3:52 下午
 */

namespace asher\core\route\annotation\mapping;


use asher\core\Route;

class RequestMapping
{
    public const GET     = 'GET';
    public const POST    = 'POST';
    public const PUT     = 'PUT';
    public const PATCH   = 'PATCH';
    public const DELETE  = 'DELETE';
    public const OPTIONS = 'OPTIONS';
    public const HEAD    = 'HEAD';

    /**
     * Action routing path
     * @var string
     */
    private $route = '';

    /**
     * Route name
     * @var string
     */
    private $name = '';

    /**
     * The route handle
     * @var string
     */
    private $handle = '';
    /**
     * Routing supported HTTP method set
     * @var array
     */
    private $method = [self::GET,self::POST];

    /**
     * Routing path params binding
     * @var array
     */
    private $params = [];

    public function __construct($method,$classPrefix,$className)
    {
        $methodDoc = $method->getDocComment();
        if($methodDoc){
            // 如果存在方法注解
            preg_match('/@RequestMapping\((.*)\)/',$methodDoc,$suffix);
            if($suffix && isset($suffix[1])){
                if(false !== stripos($suffix[1],',')){
                    // 如果注解中存在method

                }else{
                    $methodSuffixArr = explode('=',$suffix[1]);
                }
                $methodSuffix = str_replace('"','',$methodSuffixArr[1]); // 去除 " 号
                $classPrefix = stripos($classPrefix,'/') === 0 ? $classPrefix : '/' . $classPrefix;
                $routePath = $classPrefix . '/' . $methodSuffix;
                $handle = $className . '@' .$method->getName();
                if($routePath && $handle){
                    $this->route = $routePath;
                    $this->handle = $handle;
                }
            }
        }
    }

    public function getRoute(): string {

        return $this->route;
    }

    public function getMethod() : array {

        return $this->method;
    }

    public function getParams() : array {

        return $this->params;
    }

    public function getName() : string {

        return $this->name;
    }

    public function getHandle(){

        return $this->handle;
    }
}