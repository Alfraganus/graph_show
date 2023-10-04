<?php

namespace app\modules\graph\controllers;

use app\modules\graph\service\DataProviderService;
use DateTime;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;
/**
 * Default controller for the `graph` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */

    private $dataProvider;

    public function __construct($id, $module, DataProviderService $dataProvider, $config = [])
    {
        $this->dataProvider = $dataProvider;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
//        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new DynamicModel(['csvFile']);
        $model->addRule(['csvFile'], 'file');
        $overall = 0;
        $chart = [];
        if (\Yii::$app->request->post()) {
            $result = [];
            $upload = UploadedFile::getInstance($model,'csvFile');
             if ($upload) {
                $fileContent = file_get_contents($upload->tempName);
                $lines = explode("\n", $fileContent);
                $headerSkipped = false;
                foreach ($lines as $line) {
                    if (!$headerSkipped) {
                        $headerSkipped = true;
                        continue;
                    }
                    if (empty(trim($line))) {
                        continue;
                    }
                    $data = str_getcsv($line, ',');
                   $overall+= doubleval($data[13]);
                  $type = DataProviderService::transactionTypes($data[2]);
                    $chart[] = [
                      'open_time'=>DateTime::createFromFormat("Y.m.d H:i:s", $data[1])->format('Y-m-d H:i'),
                      'profit'=>$data[13],
                  ];
                }
                $dataChart =  $this->chartData($chart);
                 return $this->render('index',[
                     'model'=>$model,
                     'chartData'=>$dataChart
                 ]);
                /* return [
                     'result'=>$type,
                     'overall'=>$this->dataProvider->numberReformatter($overall)
                 ];*/
                Yii::$app->session->setFlash('success', 'Data has been imported successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Please upload a valid CSV file.');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('index',[
            'model'=>$model,
            'chartData'=>[]
        ]);
    }

    public function chartData($data)
    {
//        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      /*  $data = [
            // Your data here...
        ];*/
        $dates = [];
        $profits = [];
        foreach ($data as $row) {
            if (!empty($row['open_time']) && !empty($row['profit'])) {
                $dates[] = strtotime($row['open_time']);
                $profits[] = (float) $row['profit'];
            }
        }

        // Divide the time range into equal parts (e.g., 20 parts)
        $totalParts = 20;
        $partSize = count($dates) / $totalParts;

        $slicedDates = [];
        $slicedProfits = [];

        for ($i = 0; $i < $totalParts; $i++) {
            $start = (int) ($i * $partSize);
            $end = (int) (($i + 1) * $partSize);
            $slicedDates[] = array_slice($dates, $start, $end - $start);
            $slicedProfits[] = array_slice($profits, $start, $end - $start);
        }

        $chartData = [
            'title' => 'Profit vs. Time Chart',
            'xAxis' => ['type' => 'datetime'],
            'series' => [],
        ];

        for ($i = 0; $i < $totalParts; $i++) {
            $chartData['series'][] = [
                'name' => 'Profit Part ' . ($i + 1),
                'data' => array_map(function ($date, $profit) {
                    return [date('d-m-Y H:i',$date), $profit];
                }, $slicedDates[$i], $slicedProfits[$i]),
            ];
        }
     /*   echo "<pre>";
        return var_dump($chartData);*/

        return $chartData;
    }


  private  function redoit(string $item): array
    {
        $result = [];
            match ($item) {
                'balance' => $result['balance'] = ($result['balance'] ?? 0) + 1,
                'buy' => $result['buy'] = ($result['buy'] ?? 0) + 1,
                'buy stop' => $result['buystop'] = ($result['buystop'] ?? 0) + 1,
            };

        return $result;
    }
}
