<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderBackendSlidersController extends N2SmartSliderController
{

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.Sliders',
            'models.Slides',
            'models.generator'
        ), 'smartslider');
    }

    public function actionIndex() {
        N2Loader::import(array(
            'models.Layouts',
            'models.SliderItems'
        ), 'smartslider');

        $this->addView(null);
        $this->render();
    }

    public function actionOrderBy() {
        $time = N2Request::getCmd('time', null);
        if ($time == 'DESC' || $time == 'ASC') {
            N2SmartSliderSettings::set('slidersOrder', 'time');
            N2SmartSliderSettings::set('slidersOrderDirection', $time);
        }
        $title = N2Request::getCmd('title', null);
        if ($title == 'DESC' || $title == 'ASC') {
            N2SmartSliderSettings::set('slidersOrder', 'title');
            N2SmartSliderSettings::set('slidersOrderDirection', $title);
        }
        $this->redirectToSliders();
    }

    public function actionExportAll() {
        N2Loader::import('libraries.export', 'smartslider');
        $slidersModel = new N2SmartsliderSlidersModel();
        $sliders      = $slidersModel->getAll();
        foreach ($sliders AS $slider) {
            $export = new N2SmartSliderExport($slider['id']);
            $export->create(true);
        }

        $folder = N2Platform::getPublicDir();
        $folder .= '/export/';
        $zip = new N2ZipFile();

        foreach (N2Filesystem::files($folder) AS $file) {
            $zip->addFile(file_get_contents($folder . $file), $file);
        }
        ob_end_clean();
        header('Content-disposition: attachment; filename=sliders_unzip_to_import.zip');
        header('Content-type: application/zip');
        echo $zip->file();
        n2_exit(true);
    }

    public function actionRestoreByUpload() {
        $this->actionImportByUpload(true);
    }

    public function actionImportByUpload($restore = false) {
        if ($this->validatePermission('smartslider_edit')) {
            if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                N2Message::error(sprintf(n2_('You were not allowed to upload this file to the server (upload limit %s). Please you this alternative method!'), @ini_get('post_max_size')));

                $this->redirect(array(
                    "sliders/importFromServer"
                ));
            } else if (N2Request::getInt('save')) {
                if ($this->validateToken() && isset($_FILES['slider']) && isset($_FILES['slider']['tmp_name']['import-file'])) {

                    switch ($_FILES['slider']['error']['import-file']) {
                        case UPLOAD_ERR_OK:
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            throw new RuntimeException('No file sent.');
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            throw new RuntimeException('Exceeded filesize limit.');
                        default:
                            throw new RuntimeException('Unknown errors.');
                    }

                    if (N2Filesystem::fileexists($_FILES['slider']['tmp_name']['import-file'])) {

                        $data = new N2Data(N2Request::getVar('slider'));

                        N2Loader::import('libraries.import', 'smartslider');
                        $import   = new N2SmartSliderImport();
                        if($restore){
                            $import->enableRestore();
                        }
                        $sliderId = $import->import($_FILES['slider']['tmp_name']['import-file'], $data->get('image-mode', 'clone'), $data->get('linked-visuals', 0));

                        if ($sliderId !== false) {
                            N2Message::success(n2_('Slider imported.'));
                            $this->redirect(array(
                                "slider/edit",
                                array("sliderid" => $sliderId)
                            ));
                        } else {
                            N2Message::error(n2_('Import error!'));
                            $this->refresh();
                        }
                    } else {
                        N2Message::error(n2_('The imported file is not readable!'));
                        $this->refresh();
                    }


                } else {

                }
            }

            if ($restore) {
                $this->addView('restoreByUpload');
            } else {
                $this->addView('importByUpload');
            }
            $this->render();
        }
    }

    public function actionImportFromServer() {
        if ($this->validatePermission('smartslider_edit')) {


            if (N2Request::getInt('save')) {

                if ($this->validateToken()) {
                    $data = new N2Data(N2Request::getVar('slider'));
                    $file = $data->get('import-file');
                    if (empty($file)) {
                        N2Message::error(n2_('Please select a file!'));
                        $this->refresh();
                    } else {
                        $dir = N2Platform::getPublicDir();
                        if (N2Filesystem::fileexists($dir . '/' . $file)) {
                            N2Loader::import('libraries.import', 'smartslider');
                            $import   = new N2SmartSliderImport();
                            $sliderId = $import->import($dir . '/' . $file, $data->get('image-mode', 'clone'), $data->get('linked-visuals', 0));

                            if ($sliderId !== false) {

                                if ($data->get('delete')) {
                                    @unlink($dir . '/' . $file);
                                }

                                N2Message::success(n2_('Slider imported.'));
                                $this->redirect(array(
                                    "slider/edit",
                                    array("sliderid" => $sliderId)
                                ));
                            } else {
                                N2Message::error(n2_('Import error!'));
                                $this->refresh();
                            }
                        } else {
                            N2Message::error(n2_('The chosen file is missing!'));
                            $this->refresh();
                        }
                    }
                } else {
                    $this->refresh();
                }
            }

            $this->addView('importFromServer');
            $this->render();
        }
    }
}