<?php

namespace app\modules\graph\service;

class HtmlParserService
{

    public function load()
    {
        $doc = new DOMDocument();
        $doc->loadHTML("<html><body>Test<br></body></html>");
        echo $doc->saveHTML();
    }
}