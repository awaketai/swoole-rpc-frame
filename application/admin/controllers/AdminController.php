<?php
/**
 * Created by PhpStorm.
 * User: Asher
 * Date: 2020/1/17
 * Time: 16:47
 */

namespace app\admin\controllers;


use asher\core\rpc\TcpClient;

/**
 * @Controller(prefix="admin")
 * Class AdminController
 * @package app\admin\controllers
 */
class AdminController
{
    /**
     * @RequestMapping(prefix="index")
     */
    public function index(){

        $client = new TcpClient();
        $client->service('ListService')->version('1.0')->info(['id' => 1]);
        $client->service('InfoService')->version('1.0')->info(['test']);
        return 'admin - index';
    }

    public function a(){
        echo '1';
    }
}