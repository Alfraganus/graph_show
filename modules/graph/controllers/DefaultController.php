<?php

namespace app\modules\graph\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\modules\graph\service\DataProviderService;
use app\modules\graph\service\CvslParserService;
use app\modules\graph\service\HtmlParserService;

/**
 * Default controller for the `graph` module
 */
class DefaultController extends Controller
{
    private DataProviderService $dataProvider;
    private HtmlParserService $htmlParserService;
    private CvslParserService $cvslParserService;

    const FORMAT_HTML = 'html';
    const FORMAT_CSV = 'csv';

    public function __construct(
        $id,
        $module,
        DataProviderService $dataProvider,
        HtmlParserService $htmlParserService,
        CvslParserService $cvslParserService,
        $config = []
    )
    {
        $this->dataProvider = $dataProvider;
        $this->htmlParserService = $htmlParserService;
        $this->cvslParserService = $cvslParserService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {
        $model = new DynamicModel(['file', 'show_interval']);
        $model->addRule(['file'], 'file', ['extensions' => ['html']]);
        $model->addRule(['show_interval'], 'boolean');

        if ($model->load(Yii::$app->request->post())) {
            $typeBalance = [];
            $negative = 0;
            $positive = 0;
            $upload = UploadedFile::getInstance($model, 'file');
            if ($upload) {
                $fileExtension = $upload->getExtension();
              /*  $chart = match ($fileExtension) {
                    HtmlParserService::PARSE_TYPE_ANY => $this->htmlParserService->parse($upload->tempName, $positive, $negative, $typeBalance, HtmlParserService::PARSE_TYPE_ANY),
                    self::FORMAT_CSV => $this->cvslParserService->parse($upload->tempName, $positive, $negative, $typeBalance),
                    default => null
                };*/

                $charts = [
                    'any' => $this->dataProvider->chartDataProvider(
                        $this->htmlParserService->parse($upload->tempName, $positive, $negative, $typeBalance, HtmlParserService::PARSE_TYPE_ANY),
                        $model->show_interval
                    ),
                    'positive' => $this->dataProvider->chartDataProvider(
                        $this->htmlParserService->parse($upload->tempName, $positive, $negative, $typeBalance, HtmlParserService::PARSE_TYPE_POSITIVE),
                        $model->show_interval
                    ),
                    'transform' => $this->dataProvider->chartDataProvider(
                        $this->htmlParserService->parse($upload->tempName, $positive, $negative, $typeBalance, HtmlParserService::PARSE_TYPE_POSTIVE_TRANSFORMATION),
                        $model->show_interval
                    ),
                ];
                Yii::$app->session->setFlash('success', 'Data has been imported successfully.');
                return $this->render('index', [
                    'model' => $model,
                    'chartData' => $charts,
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
