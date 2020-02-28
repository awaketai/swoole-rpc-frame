<?php

return [
    'Route' => function(){
        return \asher\core\Route::class;
    },
    // the other class need load
    'Config' => function(){
        return \asher\core\Config::class;
    },
    'Http' => function(){
        return \asher\core\Http::class;
    },
    'Tcp' => function(){
        return \asher\core\rpc\Tcp::class;
    }
];