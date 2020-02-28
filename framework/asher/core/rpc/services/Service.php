<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/26
 * Time: 5:16 下午
 */

namespace asher\core\rpc\services;


interface Service
{
    /**
     * 服务端需要处理数据的方法
     * @return mixed
     */
    public function info();

}