<?php

namespace Codeception\Module;

use Codeception\Module;
use Codeception\Lib\ModuleContainer;
use Elasticsearch\ClientBuilder;

class Elasticsearch extends Module
{
    public $elasticsearch;

    public function _initialize()
    {
        $clientBuilder = ClientBuilder::create();
        $clientBuilder->setHosts($this->_getConfig('hosts'));
        $this->client = $clientBuilder->build();
    }

    public function _before(\Codeception\TestInterface $test)
    {
        $this->cleanup();
    }

    protected function cleanup()
    {
        if ($this->_getConfig('cleanup') && !empty($this->_getConfig('indexes'))) {
            $indexes = explode(',', $this->_getConfig('indexes'));
            foreach ($indexes as $index)
            {
                if ($this->client->indices()->exists(['index' => $index])) {
                    $this->client->indices()->delete(['index' => $index]);
                    codecept_debug('clear index: '.$index);
                }
            }
        }
    }

    public function grabFromElasticsearch($index = null, $type = null, $queryString = '*')
    {
        $result = $this->client->search(
            [
                'index' => $index,
                'type' => $type,
                'q' => $queryString,
                'size' => 1
            ]
        );
        return !empty($result['hits']['hits'])
            ? $result['hits']['hits'][0]['_source']
            : array();
    }
    public function seeInElasticsearch($index, $type, $fieldsOrValue)
    {
        return $this->assertTrue($this->count($index, $type, $fieldsOrValue) > 0, 'item exists');
    }
    public function dontSeeInElasticsearch($index, $type, $fieldsOrValue)
    {
        return $this->assertTrue($this->count($index, $type, $fieldsOrValue) === 0,
            'item does not exist');
    }
    protected function count($index, $type, $fieldsOrValue)
    {
        $query = [];
        if (is_array($fieldsOrValue)) {
            $query['bool']['filter'] = array_map(function ($value, $key) {
                return ['match' => [$key => $value]];
            }, $fieldsOrValue, array_keys($fieldsOrValue));
        }
        else {
            $query['multi_match'] = [
                'query' => $fieldsOrValue,
                'fields' => '_all',
            ];
        }
        $params = [
            'index' => $index,
            'type' => $type,
            'size' => 1,
            'body' => ['query' => $query],
        ];
        $this->client->indices()->refresh();
        $result = $this->client->search($params);
        return (int) $result['hits']['total'];
    }
    public function haveInElasticsearch($document)
    {
        $result = $this->client->index($document);
        $this->client->indices()->refresh();
        return $result;
    }
}
