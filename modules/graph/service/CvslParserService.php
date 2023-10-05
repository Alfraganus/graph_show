<?php

namespace app\modules\graph\service;

use DateTime;

class CvslParserService
{
    private DataProviderService $dataProvider;

    public function __construct(DataProviderService $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function execute($filePath,&$positive, &$negative,&$typeBalance) : array
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
                if ( $parsedDate !== false && $profit = $data[13]) {
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