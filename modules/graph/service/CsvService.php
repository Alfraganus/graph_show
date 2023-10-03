<?php

namespace app\modules\graph\controllers;


class CsvService
{
    public static function transactionTypes(string $data) : array
    {
        match ($data) {
            'balance'   => $result['balance'] = ($result['balance'] ?? 0) + 1,
            'buy'       => $result['buy']     = ($result['buy'] ?? 0) + 1,
            'buy stop'  => $result['buystop'] = ($result['buystop'] ?? 0) + 1,
        };
        return $result;
    }
}