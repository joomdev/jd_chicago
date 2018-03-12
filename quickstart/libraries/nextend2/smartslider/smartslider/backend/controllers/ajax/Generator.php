<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import("backend.controllers.Generator", 'smartslider');
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2SmartsliderBackendGeneratorControllerAjax extends N2SmartSliderControllerAjax
{

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.generator',
            'models.Sliders',
            'models.Slides'
        ), 'smartslider');
    }

    public function actionRecordsTable() {

        $this->validateToken();

        $this->validatePermission('smartslider_edit');

        $generatorId = N2Request::getInt('generator_id');

        $generatorModel = new N2SmartsliderGeneratorModel();

        if ($generatorId > 0) {
            $generator = $generatorModel->get($generatorId);

            $this->validateDatabase($generator);
        } else {
            $info      = new N2Data(N2Request::getVar('generator'));
            $generator = array(
                'group'  => $info->get('group'),
                'type'   => $info->get('type'),
                'params' => '{}'
            );
        }

        $info = $generatorModel->getGeneratorInfo($generator['group'], $generator['type']);

        $generator['params'] = new N2Data($generator['params'], true);

        $generator['params']->loadArray(N2Request::getVar('generator'));

        require_once($info->path . '/generator.php');
        $class = 'N2Generator' . $generator['group'] . $generator['type'];
        /** @var N2GeneratorAbstract $dataSource */
        $dataSource = new $class($info, $generator['params']);


        $request = new N2Data(N2Request::getVar('generator'));

        $group = max(intval($request->get('record-group', 1)), 1);

        $result = $dataSource->getData(max($request->get('record-slides', 1), 1), max($request->get('record-start', 1), 1), $group);

        if (count($result)) {
            ob_start();

            $headings = array();

            for ($i = 1; $i <= $group; $i++) {
                $headings[] = '#';
                foreach ($result[0][0] AS $k => $v) {
                    $headings[] = '{' . $k . '/' . $i . '}';
                }
            }

            $headingHTML = NHtml::tag('thead', array(), NHtml::tag('tr', array(), '<th>' . implode('</th><th>', $headings) . '</th>'));


            $rows = array();

            $i = 0;
            foreach ($result AS $records) {
                foreach ($records AS $g => $record) {
                    $rows[$i][] = $i + 1;
                    foreach ($record AS $k => $v) {
                        $rows[$i][] = NHtml::tag('div', array(), htmlentities($v));
                    }
                }
                $i++;
            }

            for ($i = 0; $i < count($rows); $i++) {
                $rows[$i] = '<td>' . implode('</td><td>', $rows[$i]) . '</td>';
            }
            $recordHTML = NHtml::tag('tbody', array(), '<tr>' . implode('</tr><tr>', $rows) . '</tr>');


            echo NHtml::tag('div', array('style' => 'width: 100%; height: 100%; overflow: auto;'), NHtml::tag('table', array(
                'class' => 'n2-generator-records n2-table n2-table-fancy',
                'style' => 'margin: 10px; width: auto; table-layout: fixed;'
            ), $headingHTML . $recordHTML));

            $this->response->respond(array(
                'html' => ob_get_clean()
            ));

        } else {
            N2Message::notice('No records found for the filter');
            $this->response->error();
        }
    }

    public function actionGetAuthUrl() {
        $this->validateToken();
        $this->validatePermission('smartslider_config');
        $group = N2Request::getVar('group');
        $type  = N2Request::getVar('type');

        $generatorModel = new N2SmartsliderGeneratorModel();
        $info           = $generatorModel->getGeneratorInfo($group, $type);

        try {
            $configuration = $info->getConfiguration();
            $this->response->respond(array('authUrl' => $configuration->startAuth()));
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
            $this->response->error();
        }
    }

    public function actionGetData() {
        $this->validateToken();
        $this->validatePermission('smartslider_edit');

        $group = N2Request::getVar('group');
        $type  = N2Request::getVar('type');


        $generatorModel = new N2SmartsliderGeneratorModel();
        $info           = $generatorModel->getGeneratorInfo($group, $type);

        $configuration = $info->getConfiguration();
        try {
            $this->response->respond(call_user_func(array(
                $configuration,
                N2Request::getCmd('method')
            )));
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
            $this->response->error();
        }
    }
}