<?php

use miloschuman\highcharts\Highcharts;

if($profitState) {
    echo Highcharts::widget([
        'options' => [
            'title' => ['text' => 'Контракты с прибылью или убытком'],
            'chart' => [
                'type' => 'pie',
            ],
            'series' => [
                [
                    'name' => 'Категории',
                    'data' => array_map(function ($title, $count) {
                        return ['name' => $title, 'y' => $count];
                    }, [
                        'Выгодные контракты',
                        'Поврежденные контракты'
                    ], [
                        $profitState['positiveAmount'],
                        $profitState['negativeAmount'],
                    ]),
                ]
            ]
        ]
    ]);
}

?>
