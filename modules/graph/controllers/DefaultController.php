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
                  if ($data[13] && $data[2] != 'balance') {
                          $chart[] = [
                              'open_time'=>DateTime::createFromFormat("Y.m.d H:i:s", $data[1])->format('Y-m-d H:i'),
                              'profit'=>floatval($data[13]),
                          ];
                  }
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
        $totalElements = count($data);
        $interval = $totalElements / 20;
        $chartData = [
            'title' => 'profit vs. Time Chart',
            'xAxis' => ['type' => 'datetime'],
            'series' => [],
        ];
        $nameArray = [];
        $profit = [];
        for ($i = 0; $i < $totalElements; $i++) {
            if ($i % $interval === 0) {
                $nameArray[] = $data[$i]['open_time'];
                $profit[] = $data[$i]['profit'];
                $chartData['series'][] = [
                    'name' => $data[$i]['open_time'],
                    'data' => [$data[$i]['profit']],
                ];
            }
        }
        $chartData['series'] = [
            'name' => $nameArray,
            'data' => $profit,
        ];
return $chartData;
        echo "<pre>";
        return var_dump(print_r($profit));
    }



}
