<?php

namespace app\modules\graph\interface;

interface ParserInterface
{
    public function parse($filePath,&$positive, &$negative,&$typeBalance) : array;
}