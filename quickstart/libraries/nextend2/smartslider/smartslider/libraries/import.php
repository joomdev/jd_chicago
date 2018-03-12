<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.zip.zip_read');
N2Loader::import('libraries.backup', 'smartslider');

class N2SmartSliderImport
{

    /**
     * @var N2SmartSliderBackup
     */
    private $backup;
    private $imageTranslation = array();
    private $sectionTranslation = array();

    private $sliderId = 0;

    private $restore = false;

    public function enableRestore() {
        $this->restore = true;
    }

    public function import($filePathOrData, $imageImportMode = 'clone', $linkedVisuals = 1, $isFilePath = true) {
        $zip        = new N2ZipRead();
        $importData = $zip->read_zip($filePathOrData, $isFilePath);
        if (!isset($importData['data'])) {
            return false;
        }
        $this->backup = unserialize($importData['data']);

        $this->sectionTranslation = array();
        $this->importVisuals($this->backup->visuals, $linkedVisuals);


        $sliderModel = new N2SmartsliderSlidersModel();
        if ($this->restore) {
            $this->sliderId = $sliderModel->restore($this->backup->slider);
        } else {
            $this->sliderId = $sliderModel->import($this->backup->slider);
        }
        if (!$this->sliderId) {
            return false;
        }
        switch ($imageImportMode) {
            case 'clone':
                $images     = $importData['images'];
                $imageStore = new N2StoreImage('slider' . $this->sliderId, true);
                foreach ($images AS $file => $content) {
                    $localImage = $imageStore->makeCache($file, $content);
                    if ($localImage) {
                        $this->imageTranslation[$file] = N2ImageHelper::dynamic(N2Uri::pathToUri($localImage));
                    } else {
                        $this->imageTranslation[$file] = $file;
                    }
                    if (!$this->imageTranslation[$file]) {
                        $this->imageTranslation[$file] = array_search($file, $this->backup->imageTranslation);
                    }
                }
                break;
            case 'update':
                $keys   = array_keys($this->backup->NextendImageHelper_Export);
                $values = array_values($this->backup->NextendImageHelper_Export);
                foreach ($this->backup->imageTranslation AS $image => $value) {
                    $this->imageTranslation[$value] = str_replace($keys, $values, $image);
                }
                break;
            default:
                break;
        }

        foreach ($this->backup->NextendImageManager_ImageData AS $image => $data) {
            $data['tablet']['image'] = $this->fixImage($data['tablet']['image']);
            $data['mobile']['image'] = $this->fixImage($data['mobile']['image']);
            N2ImageManager::addImageData($this->fixImage($image), $data);
        }

        unset($importData);

        if (empty($this->backup->slider['type'])) {
            $this->backup->slider['type'] = 'simple';
        }

        $class = 'N2SSPluginType' . $this->backup->slider['type'];
        N2Loader::importPath(call_user_func(array(
                $class,
                "getPath"
            )) . NDS . 'backup');

        $class = 'N2SmartSliderBackup' . $this->backup->slider['type'];
        call_user_func_array(array(
            $class,
            'import'
        ), array(
            $this,
            &$this->backup->slider
        ));


        $enabledWidgets = array();
        $plugins        = array();
        N2Plugin::callPlugin('sswidget', 'onWidgetList', array(&$plugins));

        $params = $this->backup->slider['params'];
        foreach ($plugins AS $k => $v) {
            $widget = $params->get('widget' . $k);
            if ($widget && $widget != 'disabled') {
                $enabledWidgets[$k] = $widget;
            }
        }

        foreach ($enabledWidgets AS $k => $v) {
            $class = 'N2SSPluginWidget' . $k . $v;
            if (class_exists($class, false)) {
                $params->fillDefault(call_user_func(array(
                    $class,
                    'getDefaults'
                )));

                call_user_func_array(array(
                    $class,
                    'prepareImport'
                ), array(
                    $this,
                    $params
                ));
            } else {
                unset($enabledWidgets);
            }
        }

        $sliderModel->importUpdate($this->sliderId, $params);

        $generatorTranslation = array();
        N2Loader::import("models.generator", "smartslider");
        $generatorModel = new N2SmartsliderGeneratorModel();
        foreach ($this->backup->generators as $generator) {
            $generatorTranslation[$generator['id']] = $generatorModel->import($generator);
        }


        $slidesModel = new N2SmartsliderSlidesModel();
        for ($i = 0; $i < count($this->backup->slides); $i++) {
            $slide              = $this->backup->slides[$i];
            $slide['params']    = new N2Data($slide['params'], true);
            $slide['thumbnail'] = $this->fixImage($slide['thumbnail']);
            $slide['params']->set('backgroundImage', $this->fixImage($slide['params']->get('backgroundImage')));
            $slide['params']->set('link', $this->fixLightbox($slide['params']->get('link')));

            $slide['slide'] = N2SmartSliderLayer::prepareImport($this, $slide['slide']);

            if (isset($generatorTranslation[$slide['generator_id']])) {
                $slide['generator_id'] = $generatorTranslation[$slide['generator_id']];
            }
            $slidesModel->import($slide, $this->sliderId);
        }
        return $this->sliderId;
    }

    public function fixImage($image) {
        if (isset($this->backup->imageTranslation[$image]) && isset($this->imageTranslation[$this->backup->imageTranslation[$image]])) {
            return $this->imageTranslation[$this->backup->imageTranslation[$image]];
        }
        return $image;
    }

    public function fixSection($idOrRaw) {
        if (isset($this->sectionTranslation[$idOrRaw])) {
            return $this->sectionTranslation[$idOrRaw];
        }
        return $idOrRaw;
    }

    public function fixLightbox($url) {
        preg_match('/^([a-zA-Z]+)\[(.*)](.*)/', $url, $matches);
        if (!empty($matches) && $matches[1] == 'lightbox') {
            $images    = explode(',', $matches[2]);
            $newImages = array();
            foreach ($images AS $image) {
                $newImages[] = $this->fixImage($image);
            }
            $url = 'lightbox[' . implode(',', $newImages) . ']' . $matches[3];
        }
        return $url;
    }

    private function importVisuals($records, $linkedVisuals) {
        if (count($records)) {
            if (!$linkedVisuals) {
                foreach ($records AS $record) {
                    $this->sectionTranslation[$record['id']] = $record['value'];
                }
            } else {
                $sets = array();
                foreach ($records AS $record) {
                    $storage = N2Base::getApplication($record['application'])->storage;
                    if (!isset($sets[$record['application'] . '_' . $record['section']])) {
                        $sets[$record['application'] . '_' . $record['section']] = $storage->add($record['section'] . 'set', null, $this->backup->slider['title']);
                    }
                    $this->sectionTranslation[$record['id']] = $storage->add($record['section'], $sets[$record['application'] . '_' . $record['section']], $record['value']);
                }
            }
        }
    }
}