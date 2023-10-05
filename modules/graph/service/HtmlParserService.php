<?php

namespace app\modules\graph\service;

use DateTime;

class HtmlParserService
{

    private DataProviderService $dataProvider;

    public function __construct(DataProviderService $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function execute($filePath,&$positive, &$negative,&$typeBalance) : array
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
        foreach ($data as $dataSingle) {

            if (isset($dataSingle[1])) {
                $parsedDate = DateTime::createFromFormat("Y.m.d H:i:s", $dataSingle[1]);
                if ($parsedDate !== false && floatval(end($dataSingle))) {
                    $this->dataProvider->transactionTypes($dataSingle[2],$typeBalance);
                    $this->dataProvider->profitStateProvider(end($dataSingle), $positive, $negative);
                    $chart[] = [
                        'open_time' => $parsedDate->format('Y-m-d H:i'),
                        'profit' => floatval(end($dataSingle)),
                    ];
                }

            }
        }

        return $chart;
    }
}