<?php
/**
 * Created by PhpStorm.
 * User: Asher
 * Date: 2020/1/15
 * Time: 20:40
 */

namespace asher;

use asher\core\Route;
use asher\core\bean\BeanFactory;
use app\api\controllers\TestController;

class App
{
    public const beanFile = 'bean.php';

    public function run($argv){
        try{
            $this->init();
            // start swoole worker
            if(isset($argv[1])){
                switch (trim($argv[1])){
                    case 'start:http':
                        BeanFactory::get('Http')->run();
                        break;
                    case 'start:tcp' :
                        BeanFactory::get('Tcp')->run();
                        break;
                    default:

                }
            }else{
                // 默认同时启动http和tcp服务
                BeanFactory::get('Http')->run();
//                $this->msg(__FILE__,__LINE__,"Invalid start method");
//                die();
            }

            // 1.app服务器优化
            // 2.代码、配置热重启
            // 3.swoole的生命周期
        }catch (\Exception $e){
            $this->msg($e->getFile(),$e->getLine(),$e->getMessage());
        }catch (\Throwable $t){
            $this->msg($t->getFile(),$t->getLine(),$t->getMessage());
        }
    }

    public function init(){
        define('ROOT_PATH',dirname(dirname(dirname(__FILE__)))); // 根目录
        define('APP_PATH',ROOT_PATH . '/application'); // 应用目录
        define('CORE_PATH',ROOT_PATH . '/framework/asher/core');
        define('CONFIG_PATH',ROOT_PATH .'/config');
        // load the bean class
        $beanClass = require_once APP_PATH . '/' . self::beanFile;
        if($beanClass){
            foreach ($beanClass as $name => $obj) {
                BeanFactory::set($name,$obj);
            }
        }
    }

    public function msg($file,$line,$msg){

        echo 'Error Info - File : '.$file.' Line : '.$line.' Message :'.$msg.PHP_EOL;

    }
}