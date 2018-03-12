<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.zip.zip_lib');
N2Loader::import('libraries.backup', 'smartslider');

class N2SmartSliderExport
{

    private $uniqueCounter = 1;

    /**
     * @var N2SmartSliderBackup
     */
    private $backup;
    private $sliderId = 0;

    public $images = array(), $visuals = array();

    private $files, $usedNames = array(), $imageTranslation = array();

    public function __construct($sliderId) {
        $this->sliderId = $sliderId;
    }

    public function create($saveAsFile = false) {
        $this->backup = new N2SmartSliderBackup();
        $slidersModel = new N2SmartsliderSlidersModel();
        if ($this->backup->slider = $slidersModel->get($this->sliderId)) {
            $this->backup->slider['params'] = new N2Data($this->backup->slider['params'], true);
            $slidesModel                    = new N2SmartsliderSlidesModel();
            $this->backup->slides           = $slidesModel->getAll($this->backup->slider['id']);

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
                'export'
            ), array(
                $this,
                $this->backup->slider
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
                        'prepareExport'
                    ), array(
                        $this,
                        &$params
                    ));
                } else {
                    unset($enabledWidgets);
                }
            }

            for ($i = 0; $i < count($this->backup->slides); $i++) {
                $slide = $this->backup->slides[$i];
                self::addImage($slide['thumbnail']);
                $slide['params'] = new N2Data($slide['params'], true);

                self::addImage($slide['params']->get('backgroundImage'));
                self::addLightbox($slide['params']->get('link'));


                N2SmartSliderLayer::prepareExport($this, $slide['slide']);

                if (!empty($slide['generator_id'])) {
                    N2Loader::import("models.generator", "smartslider");
                    $generatorModel             = new N2SmartsliderGeneratorModel();
                    $this->backup->generators[] = $generatorModel->get($slide['generator_id']);
                }
            }

            $zip = new N2ZipFile();

            $this->images  = array_unique($this->images);
            $this->visuals = array_unique($this->visuals);

            foreach ($this->images AS $image) {
                $this->backup->NextendImageManager_ImageData[$image] = N2ImageManager::getImageData($image, true);
                if ($this->backup->NextendImageManager_ImageData[$image]) {
                    self::addImage($this->backup->NextendImageManager_ImageData[$image]['tablet']['image']);
                    self::addImage($this->backup->NextendImageManager_ImageData[$image]['mobile']['image']);
                } else {
                    unset($this->backup->NextendImageManager_ImageData[$image]);
                }
            }

            $this->images = array_unique($this->images);

            $usedNames = array();
            foreach ($this->images AS $image) {
                $file = N2ImageHelper::fixed($image, true);
                if (N2Filesystem::fileexists($file)) {
                    $fileName = strtolower(basename($file));
                    while (in_array($fileName, $usedNames)) {
                        $fileName = $this->uniqueCounter . $fileName;
                        $this->uniqueCounter++;
                    }
                    $usedNames[] = $fileName;

                    $this->backup->imageTranslation[$image] = $fileName;
                    $zip->addFile(file_get_contents($file), 'images/' . $fileName);
                }
            }

            foreach ($this->visuals AS $visual) {
                $this->backup->visuals[] = N2StorageSectionAdmin::getById($visual);
            }
            $zip->addFile(serialize($this->backup), 'data');
            if (!$saveAsFile) {
                ob_end_clean();
                header('Content-disposition: attachment; filename=' . preg_replace('/[^a-zA-Z0-9_-]/', '', $this->backup->slider['title']) . '.ss3');
                header('Content-type: application/zip');
                echo $zip->file();
                n2_exit(true);
            } else {
                $file   = preg_replace('/[^a-zA-Z0-9_-]/', '', $this->backup->slider['title']) . '.ss3';
                $folder = N2Platform::getPublicDir();
                $folder .= '/export/';
                if (!N2Filesystem::existsFolder($folder)) {
                    N2Filesystem::createFolder($folder);
                }
                N2Filesystem::createFile($folder . $file, $zip->file());
            }
        }
    }

    public function  createHTML($isZIP = true) {
        $this->files = array();
        ob_end_clean();
        N2AssetsManager::createStack();

        N2AssetsPredefined::frontend(true);

        ob_start();
        N2Base::getApplication("smartslider")
              ->getApplicationType('widget')
              ->render(array(
                  "controller" => 'home',
                  "action"     => N2Platform::getPlatform(),
                  "useRequest" => false
              ), array(
                  $this->sliderId,
                  'Export as HTML'
              ));

        $slidersModel = new N2SmartsliderSlidersModel();
        $slider       = $slidersModel->get($this->sliderId);
        $sliderHTML   = ob_get_clean();
        $headHTML     = '';

        $css = N2AssetsManager::getCSS(true);
        foreach ($css['url'] AS $url) {
            $headHTML .= NHtml::style($url, true, array(
                    'media' => 'screen, print'
                )) . "\n";
        }
        array_unshift($css['files'], N2LIBRARYASSETS . '/normalize.css');
        foreach ($css['files'] AS $file) {
            $path               = 'css/' . basename($file);
            $this->files[$path] = file_get_contents($file);
            $headHTML .= NHtml::style($path, true, array(
                    'media' => 'screen, print'
                )) . "\n";
        }

        if ($css['inline'] != '') {
            $headHTML .= NHtml::style($css['inline']) . "\n";
        }

        $js = N2AssetsManager::getJs(true);

        if ($js['globalInline'] != '') {
            $headHTML .= NHtml::script($js['globalInline']) . "\n";
        }

        foreach ($js['url'] AS $url) {
            $headHTML .= NHtml::script($url, true) . "\n";
        }
        foreach ($js['files'] AS $file) {
            $path               = 'js/' . basename($file);
            $this->files[$path] = file_get_contents($file);
            $headHTML .= NHtml::script($path, true) . "\n";
        }

        if ($js['inline'] != '') {
            $headHTML .= NHtml::script($js['inline']) . "\n";
        }

        $sliderHTML = preg_replace_callback('/(src|data-desktop|data-tablet|data-mobile)=["|\'](.*?)["|\']/i', array(
            $this,
            'replaceHTMLImage'
        ), $sliderHTML);

        $sliderHTML = preg_replace_callback('/url\(\s*([\'"]|(&#039;))?(\S*\.(?:jpe?g|gif|png))([\'"]|(&#039;))?\s*\)[^;}]*?/i', array(
            $this,
            'replaceHTMLBGImage'
        ), $sliderHTML);

        $sliderHTML = preg_replace_callback('/(n2-lightbox-urls)=["|\'](.*?)["|\']/i', array(
            $this,
            'replaceLightboxImages'
        ), $sliderHTML);

        $headHTML = preg_replace_callback('/"([^"]*?\.(jpg|png|gif|jpeg))"/i', array(
            $this,
            'replaceJSON'
        ), $headHTML);

        $this->files['index.html'] = "<!doctype html>\n<html lang=\"en\">\n<head>\n<meta charset=\"utf-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge, chrome=1\">\n<title>" . $slider['title'] . "</title>\n" . $headHTML . "</head>\n<body>\n" . $sliderHTML . "</body>\n</html>";

        if (!$isZIP) {
            return $this->files;
        }

        $zip = new N2ZipFile();
        foreach ($this->files AS $path => $content) {
            $zip->addFile($content, $path);
        }
        ob_end_clean();
        header('Content-disposition: attachment; filename=' . preg_replace('/[^a-zA-Z0-9_-]/', '', $slider['title']) . '.zip');
        header('Content-type: application/zip');
        echo $zip->file();
        n2_exit(true);
    }

    private static function addProtocol($image) {
        if (substr($image, 0, 2) == '//') {
            return 'http:' . $image;
        }
        return $image;
    }

    public function replaceHTMLImage($found) {
        $path = N2Filesystem::absoluteURLToPath(self::addProtocol($found[2]));
        if ($path == $found[2]) {
            return $found[0];
        }
        if (N2Filesystem::fileexists($path)) {
            if (!isset($this->imageTranslation[$path])) {
                $fileName = strtolower(basename($path));
                while (in_array($fileName, $this->usedNames)) {
                    $fileName = $this->uniqueCounter . $fileName;
                    $this->uniqueCounter++;
                }
                $this->usedNames[]                  = $fileName;
                $this->files['images/' . $fileName] = file_get_contents($path);
                $this->imageTranslation[$path]      = $fileName;
            } else {
                $fileName = $this->imageTranslation[$path];
            }
            return str_replace($found[2], 'images/' . $fileName, $found[0]);
        } else {
            return $found[0];
        }
    }

    public function replaceLightboxImages($found) {
        $images = explode(',', $found[2]);
        foreach ($images AS $k => $image) {
            $images[$k] = $this->replaceHTMLImage(array(
                $image,
                '',
                $image
            ));
        }
        return 'n2-lightbox-urls="' . implode(',', $images) . '"';
    }

    public function replaceHTMLBGImage($found) {
        $path = $this->replaceHTMLImage(array(
            $found[3],
            '',
            $found[3]
        ));
        return str_replace($found[3], $path, $found[0]);
    }

    public function replaceJSON($found) {
        $image = str_replace('\\/', '/', $found[1]);
        $path  = $this->replaceHTMLImage(array(
            $image,
            '',
            $image
        ));
        return str_replace($found[1], str_replace('/', '\\/', $path), $found[0]);
    }

    public function addImage($image) {
        if (!empty($image)) {
            $this->images[] = $image;
        }
    }

    public function addLightbox($url) {
        preg_match('/^([a-zA-Z]+)\[(.*)]/', $url, $matches);
        if (!empty($matches)) {
            if ($matches[1] == 'lightbox') {
                $images = explode(',', $matches[2]);
                foreach ($images AS $image) {
                    $this->addImage($image);
                }
            }
        }
    }

    public function addVisual($id) {
        if (is_numeric($id) && $id > 10000) {
            $this->visuals[] = $id;
        }
    }
}