<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/10/1
 * Baidu searcher
 */
namespace core\Rule;

use QL\Contracts\PluginContract;
use QL\QueryList;

class Baidu implements PluginContract
{
    protected $ql;
    protected $keyword;
    protected $pageNumber = 10;
    protected $httpOpt = [];
    //接口地址
    // const API = 'https://www.baidu.com/s';
    const API = 'https://m.baidu.com/s';
    const RULES = [
    //   'title' => ['h3','text'],
      'link' => ['.c-line-clamp1 span:eq(0)','text']
    ];
    const RANGE = '.result .c-result-content';

    public function __construct(QueryList $ql, $pageNumber)
    {
        $this->ql = $ql->rules(self::RULES)->range(self::RANGE);
        $this->pageNumber = $pageNumber;
    }

    public static function install(QueryList $queryList, ...$opt)
    {
        $name = $opt[0] ?? 'baidu';
        $queryList->bind($name,function ($pageNumber = 10){
            return new Baidu($this,$pageNumber);
        });
    }

    public function setHttpOpt(array $httpOpt = [])
    {
        $this->httpOpt = $httpOpt;
        return $this;
    }

    public function search($keyword)
    {
        $this->keyword = $keyword;
        return $this;
    }

    public function page($page = 1,$realURL = false)
    {
        return $this->query($page)->query()->getData(function ($item) use($realURL){
            $preg = "/^https:\\/\\/.+/";
            $preg_cn = "/[\x7f-\xff]/";
            // ||   匹配到中文或者https链接返回为空内容
            if(preg_match($preg,$item['link']) || preg_match($preg_cn,$item['link']))// && !preg_match("/./",$item['link'])
            {
                $item['link'] = '';
            }
            return $item;
        });
    }

    /**
     * 
     */
    public function getCount()
    {
        $count = 0;
        $text =  $this->query(1)->find('.nums')->text();
        // $text =  $this->query(1)->encoding('UTF-8','UTF-8')->find('.nums')->text();
        // $a = iconv('GB2312', 'UTF-8', $text);
        // header("Content-type:text/html;charset=GB2312");
        // print_r($text);die;
        if(preg_match('/[\d,]+/',$text,$arr))
        {
            $count = str_replace(',','',$arr[0]);
        }
        return (int)$count;
    }

    public function getCountPage()
    {
        $count = $this->getCount();
        $countPage = ceil($count / $this->pageNumber);
        return $countPage;
    }

    /**
     * 使用百度API查询
     */
    protected function query($page = 1)
    {
        $this->ql->get(self::API,[
            'ie' => 'utf-8',
            'wd' => $this->keyword,
            'rn' => $this->pageNumber,
            'pn' => $this->pageNumber * ($page-1)
        ],$this->httpOpt);
        return $this->ql;
    }

}