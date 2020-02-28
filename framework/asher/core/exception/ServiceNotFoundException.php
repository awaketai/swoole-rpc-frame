<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/25
 * Time: 5:43 下午
 */

namespace asher\core\exception;


class ServiceNotFoundException extends \Exception
{
    public function getName(){

        return 'Invalid service config,service not found';
    }

}