<?php

namespace asher\core\rpc;


use asher\core\bean\BeanFactory;
use asher\core\exception\ServiceNotFoundException;

class TcpClient
{
    public $version = '1.0';

    /**
     * @param string $name revoke arguments name
     * @param array $arguments revoke function arguments
     * @return $this
     * @throws ServiceNotFoundException
     */
    public function __call($name, $arguments)
    {
        // 链式调用
        // TODO: Implement __call() method.
        if($name == 'service'){
            $this->serviceName = $arguments[0];
            return $this;
        }
        if($name == 'version'){
            $this->version = $arguments[0];
            return $this;
        }
        $req = [
            'jsonrpc' => '2.0',
            'method' => sprintf("%s::%s::%s",$this->version,$this->serviceName,$name),
            'params' => $arguments[0], // 链式调用最后一个方法的参数
        ];
        // 请求指定服务器获取数据
        $serviceName = ucfirst($this->serviceName . '_'.$this->version);
        // 获取指定服务器配置
        $config = BeanFactory::get('Config')->get($serviceName);
        if(empty($config)){
            throw new ServiceNotFoundException('Invalid service config');
        }

        $this->tcpClient($config['host'],$config['port'],json_encode($req));
    }

    public function tcpClient($host,$port,$data){
        // tcp服务端ip，端口获取：
        // 1.多个服务端向指定的注册中心注册自己的ip,port，客户端根据服务端的名称获取相应的ip,port，此时可以引入consul
        // 2.在配置文件中指定相应服务的ip,port
        $client = new \Swoole\Client(SWOOLE_SOCK_TCP);

        //连接到服务器
        if (!$client->connect($host, $port, 0.5))
        {
            die("connect failed.");
        }
        //向服务器发送数据
        if (!$client->send($data))
        {
            die("send failed.");
        }
        //从服务器接收数据
        $data = $client->recv();
        if (!$data)
        {
            die("recv failed.");
        }
//        echo $data;
        //关闭连接
        $client->close();
    }

}