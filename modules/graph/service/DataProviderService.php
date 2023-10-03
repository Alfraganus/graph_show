<?php

namespace app\modules\graph\service;

class DataProviderService
{
    public static function transactionTypes(string $data)
    {
        match ($data) {
            'balance'   => $result['balance'] = ($result['balance'] ?? 0) + 1,
            'buy'       => $result['buy']     = ($result['buy'] ?? 0) + 1,
            'buy stop'  => $result['buystop'] = ($result['buystop'] ?? 0) + 1,
        };
        return $result;
    }

    public function numberReformatter($data)
    {
        $result = number_format($data, 2);
        $result = str_replace(',', '', $data);;

        return $result;
    }
}