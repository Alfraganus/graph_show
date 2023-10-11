<?php

namespace app\modules\graph\service;

use app\modules\graph\interface\ParserInterface;
use DateTime;

class HtmlParserService/* implements ParserInterface*/
{

    private DataProviderService $dataProvider;

    const PARSE_TYPE_ANY = 'any';
    const PARSE_TYPE_POSITIVE = 'positive';
    const PARSE_TYPE_PRICE = 'price';
    const PARSE_TYPE_POSTIVE_TRANSFORMATION = 'positive_transformation';

    public function __construct(DataProviderService $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function parse($filePath, &$positive, &$negative, &$typeBalance)
    {
        $html = file_get_contents($filePath);

        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $data = [];

        // getting all <tr> elements
        $trElements = $doc->getElementsByTagName('tr');

        foreach ($trElements as $trElement) {
            $rowData = [];
            $tdElements = $trElement->getElementsByTagName('td');

            foreach ($tdElements as $tdElement) {
                $rowData[] = $tdElement->textContent;
            }
            $data[] = $rowData;
        }
        $chart = [];
        $balance = 0;
        foreach ($data as $dataSingle) {
            if (isset($dataSingle[1])) {
                $parsedDate = DateTime::createFromFormat("Y.m.d H:i:s", $dataSingle[1]);
                if ($parsedDate !== false && floatval(end($dataSingle))) {
                    $this->dataProvider->transactionTypes($dataSingle[2], $typeBalance);
                    $this->dataProvider->profitStateProvider(end($dataSingle), $positive, $negative);
                    $balance += floatval(str_replace([' ', ','], '', end($dataSingle)));
                    $chart[] = [
                        'open_time' => $parsedDate->format('Y-m-d H:i'),
                        'profit' => $balance,
                    ];
                }
            }
        }
        return $chart;
    }

}