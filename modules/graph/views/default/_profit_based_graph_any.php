<?php

use miloschuman\highcharts\Highcharts;

if ($chartData) {
    echo Highcharts::widget([
        'options' => [
            'title' => [
                'text' => sprintf('%s - %s',
                    $chartData['first'],
                    $chartData['last'])
            ],
            'xAxis' => [
                'categories' => $chartData['series']['name'],
                'title' => ['text' => 'Дни']
            ],
            'yAxis' => [
                'title' => ['text' => 'Количество']
            ],
            'series' => [
                [
                    'name' => 'сделки', 'data' => $chartData['series']['data']
                ],
            ]
        ]
    ]);
}

?>