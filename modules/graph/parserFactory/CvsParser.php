<?php

namespace app\modules\graph\parserFactory;

use app\modules\graph\interface\ParserInterface;
use app\modules\graph\service\CvslParserService;

class CvsParser implements ParserInterface
{
    public function parse(array $data)
    {
        return new CvslParserService();
    }
}