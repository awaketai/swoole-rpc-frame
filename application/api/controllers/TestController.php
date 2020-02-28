<?php
/**
 * Created by PhpStorm.
 * User: Asher
 * Date: 2020/1/16
 * Time: 14:47
 */

namespace app\api\controllers;

use asher\core\Route;

/**
 * Class TestController
 * @Controller(prefix="test")
 * @package app\api\controllers
 */
class TestController
{
    /**
     * @RequestMapping(route="test")
     */
    public function test(){

        return 'test - test';
    }

    /**
     * @RequestMapping(route="index")
     */
    public function index(){


        return 'test - index';
    }

    public function test2(){

        var_dump((new Route())->getRoute());
    }


}