<?php

namespace app\modules\graph\parserFactory;

use app\modules\graph\interface\ParserInterface;
use app\modules\graph\service\HtmlParserService;

class HtmlParser implements ParserInterface
{
    public function parse(array $data)
    {
        return new HtmlParserService();
    }
}