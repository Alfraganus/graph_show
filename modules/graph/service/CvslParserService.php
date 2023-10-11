<?php

namespace app\modules\graph\service;

use app\modules\graph\interface\ParserInterface;
use DateTime;

class CvslParserService implements ParserInterface
{
    private DataProviderService $dataProvider;

    public function __construct(DataProviderService $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function parse($filePath,&$positive, &$negative,&$typeBalance, $parsingType) : array
    {
        $fileContent = file_get_contents($filePath);
        $lines = explode("\n", $fileContent);
        $headerSkipped = false;
        $chart = [];
        /*skipping first row of CVS*/
        foreach ($lines as $line) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }
            if (empty(trim($line))) {
                continue;
            }
            $data = str_getcsv($line);
            if (isset($data[1])) {
                $parsedDate = DateTime::createFromFormat("Y.m.d H:i:s", $data[1]);
                if ( $parsedDate !== false && $profit = $data[5]) {
                    $this->dataProvider->transactionTypes($data[2],$typeBalance);
                    $this->dataProvider->profitStateProvider($profit, $positive, $negative);
                    $chart[] = [
                        'open_time' =>$parsedDate->format('Y-m-d H:i'),
                        'profit' => floatval($profit),
                    ];
                }
            }
        }
        return $chart;
    }
}