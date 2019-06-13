<?php
namespace web\controller;
use core\View;

use QL\QueryList;
use core\Rule\Baidu;
use core\Rule\Sogo;

class Index{
    protected $view;
    public function __construct(){
        $this->view = new View();
    }
    public function version(){
        return $this->view->with("version","当前版本1.0")->make("index");
    }
    public function index(){
        /*
        $ql = QueryList::getInstance();
        $ql->use(Baidu::class);
        //or Custom function name
        $ql->use(Baidu::class,'baidu');

        $baidu = $ql->baidu(10);
        $searcher = $baidu->search('QueryList');
        $data = $searcher->page(1);
        dd($data->all());*/
        
        $ql = QueryList::getInstance();
        $ql->use(Sogo::class);
        $sogo = $ql->sogo();
        $searcher = $sogo->search('QueryList');
        $data = $searcher->page(1);
        dd($data->all());
    }
}