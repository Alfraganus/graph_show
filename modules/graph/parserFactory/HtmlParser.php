<?php

namespace app\modules\graph\parserFactory;

use app\modules\graph\interface\ParserInterface;
use app\modules\graph\service\HtmlParserService;

class HtmlParser
{
    public function parseHTML(array $data)
    {
        return new HtmlParserService();
    }
}