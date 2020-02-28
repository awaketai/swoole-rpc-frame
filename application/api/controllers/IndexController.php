<?php
/**
 * Created by PhpStorm.
 * User: Asher
 * Date: 2020/1/16
 * Time: 14:46
 */

namespace app\api\controllers;


/**
 * @Controller(prefix="index")
 * Class IndexController
 * @package app\api\controllers
 */
class IndexController
{
    /**
     * @RequestMapping(prefix="index")
     */
    public function index(){

        return 'index - index';
    }

}