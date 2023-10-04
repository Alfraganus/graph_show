<?php

namespace app\modules\graph\interface;

interface ChartInterface
{
    public function chartDataProvider(array $data);
}