基于swoole的简单RPC框架
----

## 安装

```
composer update

```

## 启动

```
启动

php bin/bin start:http 启动http服务
php bin/bin start:tcp 启动tcp服务
默认同时启动http和tcp服务
```

## 使用

### 配置

```
config/default.php ip和端口配置
config/services.php RPC 服务调用配置

```
### 普通http请求

> 注：注解不可重复



```

控制器注解方式：
@Controller(prefix="admin")

方法注解方式：
@RequestMapping(prefix="index")

http://127.0.0.1:9050/index/index

解析IndexController::index()方法
```

### RPC请求

```
控制器方法中调用相应的请求服务

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
}

会根据配置的服务进行调用
```

