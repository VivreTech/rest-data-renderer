<?php

namespace vivretech\tests\unit\rest\renderer;


use vivretech\rest\renderer\DataRenderer;
use yii\db\Query;


final class DataRendererTest extends TestCase
{

    /* @var string In memory table name. */
    private $tableName = 'product';

    /* @var array In memory table columns. */
    private $tableColumns = [
        'id' => 'pk',
        'name' => 'string',
        'price' => 'decimal',
        'created_at' => 'datetime'
    ];


    /**
     * Setup tables for test ActiveRecord
     */
    protected function setupTestDbData()
    {
        $db = \Yii::$app->getDb();


        /* Create table with columns. */
        $db->createCommand()->createTable($this->tableName, $this->tableColumns)->execute();


        /* Populate table with rows. */
        $db->createCommand()->batchInsert(
            $this->tableName,
            array_diff(array_keys($this->tableColumns), ['id']),
            [
                ['Product 1', 100, date('Y-m-d H:i:s')],
                ['Product 2', 150, date('Y-m-d H:i:s')],
                ['Product 3', 200, date('Y-m-d H:i:s')],
            ]
        )->execute();
    }


    /**
     * @return array a list of table records.
     */
    protected function getRecords()
    {
        $query = new Query();
        $query
            ->select('*')
            ->from($this->tableName);

        return $query->all();
    }


    /**
     * @return DataRenderer
     */
    protected function createRenderer()
    {
        return new class('ArrayModelRenderer') extends DataRenderer {

            /**
             * @param array $params
             * @return mixed
             */
            public function renderMain($params = [])
            {
                return [];
            }


            public function renderSummary($model)
            {
                return [
                    'name' => $model['name'],
                    'price' => $model['price'],
                ];
            }


            public function renderDetailed($model)
            {
                return [
                    'id' => $model['id'],
                    'name' => $model['name'],
                    'price' => $model['price'],
                    'created_at' => $model['created_at'],
                ];
            }
        };
    }


    /* --------------------------------- */
    /*           Running Tests.          */
    /* --------------------------------- */
    public function testMain()
    {
        $this->setupTestDbData();

        $records = $this->getRecords();
        $renderer = $this->createRenderer();

        $firstRecord = reset($records);

        $summaryData = $renderer->run('summary', [$firstRecord]);
        $detailedData = $renderer->run('detailed', [$firstRecord]);

        $this->assertCount(3, $records);
        $this->assertInstanceOf(DataRenderer::class, $renderer);

        /* test summary. */
        $this->assertArrayHasKey('name', $summaryData);
        $this->assertArrayHasKey('price', $summaryData);

        /* test detailed. */
        $this->assertArrayHasKey('id', $detailedData);
        $this->assertArrayHasKey('name', $detailedData);
        $this->assertArrayHasKey('price', $detailedData);
        $this->assertArrayHasKey('created_at', $detailedData);
    }

}
