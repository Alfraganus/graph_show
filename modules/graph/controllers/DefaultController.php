<?php

namespace app\modules\graph\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\web\Controller;
use yii\web\UploadedFile;
use DateTime;
use app\modules\graph\service\DataProviderService;

/**
 * Default controller for the `graph` module
 */
class DefaultController extends Controller
{

    private DataProviderService $dataProvider;

    public function __construct($id, $module, DataProviderService $dataProvider, $config = [])
    {
        $this->dataProvider = $dataProvider;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $model = new DynamicModel(['csvFile']);
        $model->addRule(['csvFile'], 'file', ['extensions' => 'csv']);

        if (Yii::$app->request->post()) {
            $chart = [];
            $typeBalance = [];
            $negative = 0;
            $positive = 0;
            $upload = UploadedFile::getInstance($model, 'csvFile');
            if ($upload) {
                $fileContent = file_get_contents($upload->tempName);
                $lines = explode("\n", $fileContent);
                $headerSkipped = false;
                /*skipping first row of CVS*/
                foreach ($lines as $line) {
                    if (!$headerSkipped) { $headerSkipped = true; continue;}
                    if (empty(trim($line))) { continue; }
                    $data = str_getcsv($line);
                    DataProviderService::transactionTypes($data[2], $typeBalance);   /* getting types of transactions*/

                    if ($profit = $data[13]) {
                        $this->dataProvider->profitStateProvider($profit, $positive, $negative);
                        if ($data[2] != 'balance') {
                            $chart[] = [
                                'open_time'=>DateTime::createFromFormat("Y.m.d H:i:s", $data[1])->format('Y-m-d H:i'),
                                'profit' => floatval($profit),
                            ];
                        }
                    }
                }
                Yii::$app->session->setFlash('success', 'Data has been imported successfully.');

                return $this->render('index', [
                    'model' => $model,
                    'chartData' => $this->dataProvider->chartDataProvider($chart),
                    'balanceType' => $typeBalance,
                    'profitState' => [
                        'positiveAmount' => $positive,
                        'negativeAmount' => $negative,
                    ],
                ]);
            } else {
                Yii::$app->session->setFlash('error', 'Please upload a valid CSV file.');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
