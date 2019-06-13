<?php
namespace core;
class View{
    protected $file;
    protected $vars = [];

    public function make($file){
        $this->file = "view/".$file.".php";//__DIR__.
        return $this;
    }

    public function with($name, $value){
        $this->vars[$name] = $value;
        return $this;
    }

    public function __toString(){
        extract($this->vars);
        include $this->file;
        return '';
    }
}