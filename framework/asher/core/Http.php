<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/20
 * Time: 3:30 下午
 */

namespace asher\core;


use asher\core\bean\BeanFactory;
use asher\core\route\annotation\mapping\RequestMapping;
use asher\core\route\annotation\parser\RequestMappingParser;
use asher\core\rpc\Tcp;

class Http
{
    const APPLICATION_NAME = 'app';
    const APPLICATION_DIR = 'application';

    public $server;


    // 平滑重启：定时器监控指定目录下的文件
    public function run(){
        BeanFactory::get('Config')->load();
        $config = BeanFactory::get('Config')->get('http');

        $this->server = new \Swoole\Http\Server($config['host'], $config['port']);

        $this->server->on('start',[$this,'onStart']);
        $this->server->on('request',[$this,'onRequest']);
        $this->server->on('workerStart',[$this,'onWorkerStart']);
        // 启动tcp服务
        if(isset($config['tcpEnable']) && $config['tcpEnable'] == 1){
            (new Tcp())->listen($this->server);
        }
        $this->server->start();
    }

    public function onStart(){
        // 对于每个进程需要使用到的代码在worker进程中加载 onworkerstart
        // 对于公共的文件，不经常发生变化的文件可以在start中加载
        // 在主进程启动后进行定时监控文件变化，以便平滑重启
        $httpConfig = BeanFactory::get('Config')->get('http');
        $tcpConfig = BeanFactory::get('Config')->get('tcp');
        $this->reloadTimer();

        echo '********************************************'.PHP_EOL;
        echo sprintf("HTTP    | Listen:%s:%d, type:HTTP,worker:%d",$httpConfig['host'],$httpConfig['port'],$httpConfig['setting']['worker_num']);
        // 是否开启tcp服务
        if(isset($httpConfig['tcpEnable']) && $httpConfig['tcpEnable'] == 1){

            echo PHP_EOL. '********************************************'.PHP_EOL;
            echo sprintf("TCP    | Listen:%s:%d, type:TCP,worker:%d",$tcpConfig['host'],$tcpConfig['port'],$tcpConfig['setting']['worker_num']);
        }
    }

    public function onRequest($request, $response){
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            return $response->end();
        }
        $pathInfo = $request->server['path_info'];
        $method = $request->server['request_method'];
        // 请求分发
        $ret = BeanFactory::get('Route')::dispatch($method,$pathInfo);
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end($ret);
    }

    /**
     * worker start deal
     * @param $server
     * @param int $worker_id
     */
    public function onWorkerStart($server, int $worker_id){
        // 载入注解
        $this->loadAnnotations();
        // 加载配置文件
        BeanFactory::get('Config')->load();
    }

    public function loadAnnotations(){
        $dirs = $this->tree(APP_PATH,'controller');
        if($dirs){
            foreach ($dirs as $dir) {
                $class = $this->getClassName($dir);
                $classNameSpace = self::APPLICATION_NAME.'\\'.$class;
                if(!class_exists($classNameSpace)){
                    continue;
                }
                // 获取注解
                $this->getAnnotations($classNameSpace);
            }
        }
    }

    public function getAnnotations($class){
        $reflect = new \ReflectionClass($class);
        $classDoc = $reflect->getDocComment();
        // 获取类注解
        preg_match('/@Controller\((.*)\)/',$classDoc,$prefix);
        // 如果存在类注解
        if($prefix && isset($prefix[1])){
            $classPrefixArr = explode('=',$prefix[1]);
            $classPrefix = str_replace('"','',$classPrefixArr[1]); // 去除 " 号
            $className = $reflect->getName();
            // 获取方法注解
            array_map(function($method) use ($classPrefix,$className){

                $requestMappingObj = new RequestMapping($method,$classPrefix,strtolower($className));
                // 添加路由
                (new RequestMappingParser())->parse($requestMappingObj);
            },$reflect->getMethods());
        }
    }

    public function tree($file,$filter = 'controller'){
        if(empty($file)){
            return false;
        }
        $dirArr = [];
        $dirs = glob($file . '/*');
        foreach ($dirs as $dir) {
            if(is_dir($dir)){
                $ret = $this->tree($dir);
                if(is_array($ret)){
                    array_walk($ret,function ($vo) use (&$dirArr){
                        $dirArr[] = $vo;
                    });
                }
            }elseif(is_file($dir)){
                // 指定目录的文件
                if(stristr($dir,$filter)){
                    $dirArr[] = $dir;
                }
            }
        }
        return $dirArr;
    }

    public function getClassName($filePath){
        // 获取类名称 eg . TestController
        // 这里采用了截取文件路径的方式，也可以获取文件首部的内容，从namespace中进行匹配命名空间
        $fileName = strrchr($filePath,self::APPLICATION_DIR.'/');
        $pos = strrpos($fileName,'.');
        $file = substr($fileName,0,$pos);
        return str_replace('/','\\',$file);
    }

    public function reloadTimer(){
        $reloadObj = $this->reload();
        \Swoole\Timer::tick(5000,function() use ($reloadObj){
            // 如果文件发生变化，则重启worker进程
            if($reloadObj->reload()){
                $this->server->reload();
            }
        });
    }

    public function reload(){
        $reload = Reload::getInstance();
        // 指定变化后要重新加载的文件
        $reload->watch = [
            ROOT_PATH,
        ];
        return $reload;
    }
}