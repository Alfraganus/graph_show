<?php

namespace app\modules\graph\controllers;

use app\modules\graph\service\DataProviderService;
use Yii;
use yii\base\DynamicModel;
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
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new DynamicModel(['csvFile']);
        $model->addRule(['csvFile'], 'file');
        $overall = 0;
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

                }
                 return [
                     'result'=>$type,
                     'overall'=>$this->dataProvider->numberReformatter($overall)
                 ];
                Yii::$app->session->setFlash('success', 'Data has been imported successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Please upload a valid CSV file.');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('index',[
            'model'=>$model
        ]);
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
