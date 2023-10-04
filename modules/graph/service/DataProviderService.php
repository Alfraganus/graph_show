<?php

namespace app\modules\graph\service;

use app\modules\graph\interface\ChartInterface;

class DataProviderService implements ChartInterface
{
    public static function transactionTypes(string $data, &$typeBalance) : void
    {
        match ($data) {
            'balance' => $typeBalance['balance'] = ($typeBalance['balance'] ?? 0) + 1,
            'buy' => $typeBalance['buy'] = ($typeBalance['buy'] ?? 0) + 1,
            'buy stop' => $typeBalance['buystop'] = ($typeBalance['buystop'] ?? 0) + 1,
        };
    }

    public function profitStateProvider($profit, &$positive, &$negative) : void
    {
        if ($profit > 0) {
            $positive++;
        } elseif ($profit < 0) {
            $negative++;
        }
    }

    public function chartDataProvider(array $data) : array
    {
        $totalElements = count($data);
        $interval = $totalElements / 20;

        $firstOpenTime = null;
        $lastOpenTime = null;
        for ($i = 0; $i < $totalElements; $i += intval($interval)) {
            if ($i % intval($interval) === 0) {
                $chartData['series']['name'][] = $data[$i]['open_time'];
                $chartData['series']['data'][] = $data[$i]['profit'];

                if ($firstOpenTime === null) {
                    $firstOpenTime = $data[$i]['open_time'];
                }
                $lastOpenTime = $data[$i]['open_time'];
            }
        }
        $chartData['first'] = $firstOpenTime;
        $chartData['last'] = $lastOpenTime;

        return $chartData;
    }
}