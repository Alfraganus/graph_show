<?php

namespace app\modules\graph\controllers;

use app\components\HtmlDomComponent;
use app\modules\graph\service\HtmlParserService;
use DOMDocument;
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

    private function parseHtmlFile2($filePath)
    {
        $html = file_get_contents($filePath);

        // Use a DOM parser to extract data from <tr> elements
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $data = [];

        // Find all <tr> elements
        $trElements = $doc->getElementsByTagName('tr');

        // Initialize an array to store column names
        $columnNames = [];

        // Process the first row (assumed to be column headers)
        $headerRow = $trElements->item(0); // Assuming the header row is the first one
        if ($headerRow) {
            $tdElements = $headerRow->getElementsByTagName('td');
            foreach ($tdElements as $tdElement) {
                $columnNames[] = $tdElement->textContent;
            }
        }

        // Start processing data rows from the second row
        for ($i = 1; $i < $trElements->length; $i++) {
            $trElement = $trElements->item($i);
            $rowData = [];
            $tdElements = $trElement->getElementsByTagName('td');

            // Associate column names with column values
            foreach ($tdElements as $index => $tdElement) {
                if (isset($columnNames[$index])) {
                    $columnName = $columnNames[$index];
                    $rowData[$columnName] = $tdElement->textContent;
                } else {
                    // Handle the case where there's no matching column name
                    $columnName = 'UnnamedColumn' . $index;
                    $rowData[$columnName] = $tdElement->textContent;
                }
            }

            $data[] = $rowData;
        }

        return $data;
    }
    private function parseHtmlFile($filePath)
    {
        // Load the HTML file
        $html = file_get_contents($filePath);

        // Use a DOM parser to extract data from <tr> elements
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $data = [];

        // Find all <tr> elements
        $trElements = $doc->getElementsByTagName('tr');

        foreach ($trElements as $trElement) {
            $rowData = [];
            $tdElements = $trElement->getElementsByTagName('td');

            foreach ($tdElements as $tdElement) {
                $rowData[] = $tdElement->textContent;
            }

            $data[] = $rowData;
        }

        // Calculate the total number of elements in the array
        $totalElements = count($data);

        // Check if there are more than 25 elements
        if ($totalElements > 25) {
            // Remove the last 25 elements from the array
            $data = array_slice($data, 0, $totalElements - 29);
        }

        return $data;
    }

    public function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new DynamicModel(['csvFile']);
        $model->addRule(['csvFile'], 'file', ['extensions' => ['csv', 'html']]);

        if (Yii::$app->request->post()) {
            $chart = [];
            $typeBalance = [];
            $negative = 0;
            $positive = 0;
            $upload = UploadedFile::getInstance($model, 'csvFile');
            if ($upload) {
                $fileExtension = $upload->getExtension();
                if ($fileExtension == 'html')  {
                    return  $this->parseHtmlFile($upload->tempName);
                }
                $fileContent = file_get_contents($upload->tempName);
                $lines = explode("\n", $fileContent);
                $headerSkipped = false;
                /*skipping first row of CVS*/
                foreach ($lines as $line) {
                    if (!$headerSkipped) {
                        $headerSkipped = true;
                        continue;
                    }
                    if (empty(trim($line))) {
                        continue;
                    }
                    $data = str_getcsv($line);
                    DataProviderService::transactionTypes($data[2], $typeBalance);   /* getting types of transactions*/

                    if ($profit = $data[13]) {
                        $this->dataProvider->profitStateProvider($profit, $positive, $negative);
                        if ($data[2] != 'balance') {
                            $chart[] = [
                                'open_time' => DateTime::createFromFormat("Y.m.d H:i:s", $data[1])->format('Y-m-d H:i'),
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
