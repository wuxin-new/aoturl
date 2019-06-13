<?php
namespace core;
class bootstarp {
    public static function run(){
        self::parstUrl();
    }
    public static function parstUrl(){
        // dd($_SERVER);
        if(isset($_GET['s'])){
            $info = explode("/",$_GET['s']);
            $class ="\web\controller\\".ucfirst($info[0]);
            $action = $info[1];
        }else{
            $class ="\web\controller\Index";
            $action = "version";
        }
        echo (new $class)->$action();
    }
}