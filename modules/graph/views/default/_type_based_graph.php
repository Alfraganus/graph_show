<?php

use miloschuman\highcharts\Highcharts;

if ($balanceType) {
  echo  Highcharts::widget([
        'options' => [
            'title' => ['text' => 'Типы сделок'],
            'chart' => [
                'type' => 'pie',
            ],
            'series' => [
                [
                    'name' => 'Категории',
                    'data' => array_map(function ($title, $count) {
                        return ['name' => $title, 'y' => $count];
                    }, [
                        'balance',
                        'buy',
                        'buy stop'
                    ], [
                        $balanceType['balance'],
                        $balanceType['buy'],
                        $balanceType['buystop'],
                    ]),
                ]
            ]
        ]
    ]);
}
