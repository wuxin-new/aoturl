<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/10/1
 * Sogo searcher
 */
namespace core\Rule;

use QL\Contracts\PluginContract;
use QL\QueryList;

class Sogo implements PluginContract
{
    protected $ql;
    protected $keyword;
    protected $pageNumber = 10;
    protected $httpOpt = [];
    protected $result;
    //接口地址
    const API = 'https://m.sogou.com/web/searchList.jsp';
    const RULES = [
    //   'title' => ['h3','text'],
      'link' => ['.citeurl','text']
    ];
    const RANGE = '.result';

    public function __construct(QueryList $ql)
    {
        $this->ql = $ql->rules(self::RULES)->range(self::RANGE);
    }

    public static function install(QueryList $queryList, ...$opt)
    {
        $name = $opt[0] ?? 'sogo';
        $queryList->bind($name,function (){
            return new Sogo($this);
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

    public function page($page = 1)
    {
        return $this->query($page)->query()->getData(function ($item) {
            $preg_cn = "/[\x7f-\xff]/"; // 匹配到带有中文的链接返回为空内容
            if(preg_match($preg_cn,$item['link']))
            {
                $item['link'] = '';
            }
            return $item;
        });
    }

    /**
     * 使用百度API查询
     * tsn 0-4 对应无限制、一天、一周、一月、一年
     */
    protected function query($page = 1)
    {
        $this->ql->get(self::API,[
            'ie' => 'utf-8',//utf-8
            'query' => $this->keyword,
            'p' => $page,
            'tsn' => 0
        ],$this->httpOpt);
        return $this->ql;
    }

}