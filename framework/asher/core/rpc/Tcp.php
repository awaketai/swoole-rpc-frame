<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/24
 * Time: 11:15 上午
 */

namespace asher\core\rpc;


use asher\core\bean\BeanFactory;
use asher\core\Http;

class Tcp extends Http
{
    public $server;

    public function run(){
        BeanFactory::get('Config')->load();
        $config = BeanFactory::get('Config')->get('tcp');
        $this->server = new \Swoole\Server($config['host'],$config['port']);
        $this->server->on('start',[$this,'onStart']);
        $this->server->on('workerStart',[$this,'onWorkerStart']);
        $this->server->on('receive',[$this,'onReceive']);
        $this->server->start();
    }

    public function onReceive($serv, $fd, $from_id, $data){
        // 接收到客户端的消息
        $this->infoDeal($data);
        $serv->send($fd,'接收到的消息为：'.$data);
    }

    public function onStart(){
        // 对于每个进程需要使用到的代码在worker进程中加载 onworkerstart
        // 对于公共的文件，不经常发生变化的文件可以在start中加载
        // 在主进程启动后进行定时监控文件变化，以便平滑重启
        $config = BeanFactory::get('Config')->get('tcp');
        $this->reloadTimer();
        echo PHP_EOL.'********************************************'.PHP_EOL;
        echo sprintf("TCP    | Listen:%s:%d, type:TCP,worker:%d",$config['host'],$config['port'],$config['setting']['worker_num']);
    }

    /**
     * 如果要HTTP和TCP一起启动
     * 如果http服务多端口监听，tcp服务和http服务是共享进程的，不会为tcp服务再单独设置worker
     * @param object $server \Swoole\Http\Server
     */
    public function listen($server){
        $config = BeanFactory::get('Config')->get('tcp');
        $server->addListener($config['host'],$config['port'],SWOOLE_SOCK_TCP);
        $server->set($config['setting']);
        $server->on('receive',[$this,'onReceive']);
    }

    public function onWorkerStart($server, int $worker_id)
    {
        parent::onWorkerStart($server, $worker_id); // TODO: Change the autogenerated stub
    }

    /**
     * 服务端数据处理
     * @param string $data {"jsonrpc":"2.0","method":"1.0::ListServices::info","params":{"id":1}}
     * @return \stdClass
     */
    public function infoDeal($data){
        if(empty($data)){
            return new \stdClass();
        }

        $data = json_decode($data,true);
        if(!isset($data['method'])){
            return json_encode(['code' => 2001,'msg' => 'Invalid method']);
        }
        $method = explode('::',$data['method']);
        if(!isset($method[0])){
            return json_encode(['code' => 2002,'msg' => 'Blank param version']);
        }
        if(!isset($method[1])){
            return json_encode(['code' => 2003,'msg' => 'Blank param revoke class']);
        }
        if(!isset($method[2])){
            return json_encode(['code' => 2004,'msg' => 'Blank param revoke method']);
        }
        $service = 'asher\core\rpc\services\\'.'v'.(int)$method[0] .'\\'.$method[1];
        if(!class_exists($service)){
            return json_encode(['code' => 2005,'msg' => 'Service not found','data' => ['service' => $service]]);
        }
        $action = $method[2];
        $ret = (new $service)->$action(isset($data['params']) ? $data['params'] : []);
        return json_encode(['code' => 200,'msg' => 'success','data' => $ret]);
    }

}