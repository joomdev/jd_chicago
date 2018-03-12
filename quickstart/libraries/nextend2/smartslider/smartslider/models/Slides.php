<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

/**
 * User: David
 * Date: 2014.06.03.
 * Time: 8:32
 */
class N2SmartsliderSlidesModel extends N2Model
{

    public function __construct() {
        parent::__construct("nextend2_smartslider3_slides");
    }

    public function get($id) {
        return $this->db->findByPk($id);
    }

    public function getAll($sliderid = 0, $where = '') {
        return $this->db->queryAll('SELECT * FROM ' . $this->db->tableName . ' WHERE slider = ' . $sliderid . ' ' . $where . ' ORDER BY ordering', false, "assoc", null);
    }

    public function getRowFromPost($sliderId, $slide, $base64 = true) {

        if (!isset($slide['title'])) return false;
        if ($slide['title'] == '') $slide['title'] = n2_('New slide');

        if (isset($slide['publishdates'])) {
            $date = explode('|*|', $slide['publishdates']);
        } else {
            $date[0] = isset($slide['publish_up']) ? $slide['publish_up'] : null;
            $date[1] = isset($slide['publish_down']) ? $slide['publish_down'] : null;
            unset($slide['publish_up']);
            unset($slide['publish_down']);
        }
        $up   = strtotime(isset($date[0]) ? $date[0] : '');
        $down = strtotime(isset($date[1]) ? $date[1] : '');

        $generator_id = isset($slide['generator_id']) ? intval($slide['generator_id']) : 0;

        $params = $slide;
        unset($params['title']);
        unset($params['slide']);
        unset($params['description']);
        unset($params['thumbnail']);
        unset($params['published']);
        unset($params['first']);
        unset($params['publishdates']);

        if (isset($params['generator_id'])) {
            unset($params['generator_id']);
        }

        return array(
            'title'        => $slide['title'],
            'slide'        => ($base64 ? base64_decode($slide['slide']) : $slide['slide']),
            'description'  => $slide['description'],
            'thumbnail'    => $slide['thumbnail'],
            'published'    => (isset($slide['published']) ? $slide['published'] : 0),
            'publish_up'   => date('Y-m-d H:i:s', ($up && $up > 0 ? $up : strtotime('-1 day'))),
            'publish_down' => date('Y-m-d H:i:s', ($down && $down > 0 ? $down : strtotime('+10 years'))),
            'first'        => (isset($slide['first']) ? $slide['first'] : 0),
            'params'       => json_encode($params),
            'slider'       => $sliderId,
            'ordering'     => $this->getMaximalOrderValue($sliderId) + 1,
            'generator_id' => $generator_id
        );
    }

    /**
     * @param      $sliderId
     * @param      $slide
     * @param bool $base64
     *
     * @return bool
     */
    public function create($sliderId, $slide, $base64 = true) {

        $row = $this->getRowFromPost($sliderId, $slide, $base64);

        $slideId = $this->_create($row['title'], $row['slide'], $row['description'], $row['thumbnail'], $row['published'], $row['publish_up'], $row['publish_down'], 0, $row['params'], $row['slider'], $row['ordering'], $row['generator_id']);

        self::markChanged($sliderId);

        return $slideId;
    }

    protected function getMaximalOrderValue($sliderid = 0) {

        $query  = "SELECT MAX(ordering) AS ordering FROM " . $this->db->tableName . " WHERE slider = :id";
        $result = $this->db->queryRow($query, array(
            ":id" => $sliderid
        ));

        if (isset($result['ordering'])) return $result['ordering'] + 1;
        return 0;
    }

    public function renderEditForm($slide) {
        if ($slide) {

            $data = json_decode($slide['params'], true);
            if ($data == null) $data = array();
            $data += $slide;
            $data['sliderid'] = $slide['slider'];
            echo '<input name="slide[generator_id]" value="' . $slide['generator_id'] . '" type="hidden" />';
        } else {
            $data = array(
                'static-slide' => N2Request::getInt('static')
            );
        }

        $data['first'] = isset($slide['first']) ? $slide['first'] : 0;
        $this->editForm($data);
        return new N2Data($data);
    }

    public function simpleEditForm($data = array()) {
        $configurationXmlFile = dirname(__FILE__) . '/forms/slide.xml';
        N2Loader::import('libraries.form.form');
        $form = new N2Form(N2Base::getApplication('smartslider')
                                 ->getApplicationType('backend'));

        $data['publishdates'] = isset($data['publishdates']) ? $data['publishdates'] : ((isset($data['publish_up']) ? $data['publish_up'] : '') . '|*|' . (isset($data['publish_down']) ? $data['publish_down'] : ''));

        if (isset($data['slide'])) {
            $data['slide'] = base64_encode($data['slide']);
        }

        $form->loadArray($data);

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('slide');
    }

    /**
     * @param array $data
     */
    private function editForm($data = array()) {
        $this->simpleEditForm($data);

        $slidersModel = new N2SmartsliderSlidersModel();
        $slider       = $slidersModel->get(N2Request::getInt('sliderid', 0));

        $slidersSliderXml = call_user_func(array(
                'N2SSPluginType' . $slider['type'],
                "getPath"
            )) . '/slide.xml';
        if ((!isset($data['static-slide']) || $data['static-slide'] != 1) && N2Filesystem::existsFile($slidersSliderXml)) {
            $form = new N2Form();

            $form->loadArray($data);

            $form->loadXMLFile($slidersSliderXml);

            echo $form->render('slide');
        }

        if (isset($data['generator_id']) && $data['generator_id'] > 0) {
            $form = new N2Form();
            $form->loadArray($data);

            $form->loadXMLFile(dirname(__FILE__) . '/forms/slide_generator.xml');
            echo $form->render('slide');
        }

        N2JS::addFirstCode("new NextendForm('smartslider-form','', {});");
    }

    /**
     * @param int  $id
     * @param      $slide
     * @param bool $base64
     *
     * @return bool
     */
    public function save($id, $slide, $base64 = true) {
        if (!isset($slide['title']) || $id <= 0) return false;
        if ($slide['title'] == '') $slide['title'] = n2_('New slide');


        if (isset($slide['publishdates'])) {
            $date = explode('|*|', $slide['publishdates']);
        } else {
            $date[0] = $slide['publish_up'];
            $date[1] = $slide['publish_down'];
            unset($slide['publish_up']);
            unset($slide['publish_down']);
        }
        $up   = strtotime(isset($date[0]) ? $date[0] : '');
        $down = strtotime(isset($date[1]) ? $date[1] : '');

        $tmpslide = $slide;
        unset($tmpslide['title']);
        unset($tmpslide['slide']);
        unset($tmpslide['description']);
        unset($tmpslide['thumbnail']);
        unset($tmpslide['published']);
        unset($tmpslide['publishdates']);

        $this->db->update(array(
            'title'        => $slide['title'],
            'slide'        => ($base64 ? base64_decode($slide['slide']) : $slide['slide']),
            'description'  => $slide['description'],
            'thumbnail'    => $slide['thumbnail'],
            'published'    => (isset($slide['published']) ? $slide['published'] : 0),
            'publish_up'   => date('Y-m-d H:i:s', ($up && $up > 0 ? $up : strtotime('-1 day'))),
            'publish_down' => date('Y-m-d H:i:s', ($down && $down > 0 ? $down : strtotime('+10 years'))),
            'params'       => json_encode($tmpslide)
        ), array('id' => $id));

        self::markChanged(N2Request::getInt('sliderid'));

        return $id;
    }

    public function updateParams($id, $params) {

        $this->db->update(array(
            'params' => json_encode($params)
        ), array('id' => $id));

        return $id;
    }

    public function delete($id) {

        $slide = $this->get($id);

        if ($slide['generator_id'] > 0) {
            $slidesWithSameGenerator = $this->getAll($slide['slider'], 'AND generator_id = ' . intval($slide['generator_id']));
            if (count($slidesWithSameGenerator) == 1) {
                $generatorModel = new N2SmartsliderGeneratorModel();
                $generatorModel->delete($slide['generator_id']);
            }
        }

        $this->db->deleteByAttributes(array(
            "id" => intval($id)
        ));

        self::markChanged($slide['slider']);

    }

    public function createQuickImage($image, $sliderId) {
        $publish_up   = date('Y-m-d H:i:s', strtotime('-1 day'));
        $publish_down = date('Y-m-d H:i:s', strtotime('+10 years'));

        $parameters = array(
            'backgroundImage' => $image['image']
        );

        return $this->_create($image['title'], json_encode(array()), $image['description'], $image['image'], 1, $publish_up, $publish_down, 0, json_encode($parameters), $sliderId, $this->getMaximalOrderValue($sliderId), '');
    }

    public function createQuickVideo($video, $sliderId) {
        $publish_up   = date('Y-m-d H:i:s', strtotime('-1 day'));
        $publish_down = date('Y-m-d H:i:s', strtotime('+10 years'));

        $parameters = array();

        $slide = new N2SmartSliderSlideHelper();

        switch ($video['type']) {
            case 'youtube':
                new N2SmartSliderItemHelper($slide, 'youtube', array(
                    'desktopportraitwidth'  => '100%',
                    'desktopportraitheight' => '100%',
                    'desktopportraitalign'  => 'left',
                    'desktopportraitvalign' => 'top'
                ), array(
                    "code"       => $video['video'],
                    "youtubeurl" => $video['video'],
                    "image"      => $video['image']
                ));
                break;
            case 'vimeo':
                new N2SmartSliderItemHelper($slide, 'vimeo', array(
                    'desktopportraitwidth'  => '100%',
                    'desktopportraitheight' => '100%',
                    'desktopportraitalign'  => 'left',
                    'desktopportraitvalign' => 'top'
                ), array(
                    "vimeourl" => $video['video'],
                    "image"    => ''
                ));
                break;
            default:
                return false;
        }
        $layers = $slide->data['slide'];

        return $this->_create($video['title'], json_encode($layers), $video['description'], $video['image'], 1, $publish_up, $publish_down, 0, json_encode($parameters), $sliderId, $this->getMaximalOrderValue($sliderId), '');
    }

    public function createQuickPost($post, $sliderId) {
        $publish_up   = date('Y-m-d H:i:s', strtotime('-1 day'));
        $publish_down = date('Y-m-d H:i:s', strtotime('+10 years'));

        $data = new N2Data($post);

        $parameters = array(
            'backgroundImage' => $data->get('image'),
            'link'            => $data->get('link') . '|*|_self'
        );

        $title       = $data->get('title');
        $description = $data->get('description');

        return $this->_create($title, json_encode($this->getSlideLayers($title, $description)), $description, $data->get('image'), 1, $publish_up, $publish_down, 0, json_encode($parameters), $sliderId, $this->getMaximalOrderValue($sliderId), '');
    }

    private function getSlideLayers($hasTitle = false, $hasDescription = false) {
        $slide = new N2SmartSliderSlideHelper();
        if ($hasTitle && $hasDescription) {
            new N2SmartSliderItemHelper($slide, 'heading', array(
                'desktopportraitleft'   => 30,
                'desktopportraittop'    => 12,
                'desktopportraitalign'  => 'left',
                'desktopportraitvalign' => 'top'
            ), array(
                'heading' => '{name/slide}'
            ));
            new N2SmartSliderItemHelper($slide, 'text', array(
                'desktopportraitleft'   => 30,
                'desktopportraittop'    => 70,
                'desktopportraitalign'  => 'left',
                'desktopportraitvalign' => 'top'
            ), array(
                'content' => '{description/slide}'
            ));
            return $slide->data['slide'];
        } else if ($hasTitle) {

            new N2SmartSliderItemHelper($slide, 'heading', array(
                'desktopportraitleft'   => 30,
                'desktopportraittop'    => -12,
                'desktopportraitalign'  => 'left',
                'desktopportraitvalign' => 'bottom'
            ), array(
                'heading' => '{name/slide}'
            ));
            return $slide->data['slide'];
        }
        return array();
    }

    public function import($slide, $sliderId) {
        return $this->_create($slide['title'], $slide['slide'], $slide['description'], $slide['thumbnail'], $slide['published'], $slide['publish_up'], $slide['publish_down'], $slide['first'], $slide['params']->toJson(), $sliderId, $slide['ordering'], $slide['generator_id']);
    }

    private function _create($title, $slide, $description, $thumbnail, $published, $publish_up, $publish_down, $first, $params, $slider, $ordering, $generator_id) {
        $this->db->insert(array(
            'title'        => $title,
            'slide'        => $slide,
            'description'  => $description,
            'thumbnail'    => $thumbnail,
            'published'    => $published,
            'publish_up'   => $publish_up,
            'publish_down' => $publish_down,
            'first'        => $first,
            'params'       => $params,
            'slider'       => $slider,
            'ordering'     => $ordering,
            'generator_id' => $generator_id
        ));

        return $this->db->insertId();
    }

    public function duplicate($id) {
        $slide = $this->get($id);

        // Shift the afterwards slides ++
        $this->db->query("UPDATE {$this->db->tableName} SET ordering = ordering + 1 WHERE slider = :sliderid AND ordering > :ordering", array(
            ":sliderid" => intval($slide['slider']),
            ":ordering" => intval($slide['ordering'])
        ), '');

        if (!empty($slide['generator_id'])) {
            $generatorModel        = new N2SmartsliderGeneratorModel();
            $slide['generator_id'] = $generatorModel->duplicate($slide['generator_id']);
        }

        $slide['slide'] = N2Data::json_encode(N2SmartSliderLayer::translateIds(json_decode($slide['slide'], true)));

        $slideId = $this->_create($slide['title'] . ' - copy', $slide['slide'], $slide['description'], $slide['thumbnail'], $slide['published'], $slide['publish_up'], $slide['publish_down'], 0, $slide['params'], $slide['slider'], $slide['ordering'] + 1, $slide['generator_id']);

        self::markChanged($slide['slider']);
        return $slideId;
    }

    public function copy($id, $targetSliderId) {
        $id    = intval($id);
        $slide = $this->get($id);
        if ($slide['generator_id'] > 0) {
            $generatorModel        = new N2SmartSliderGeneratorModel();
            $slide['generator_id'] = $generatorModel->duplicate($slide['generator_id'], $targetSliderId);
        }

        $slide['slide'] = N2Data::json_encode(N2SmartSliderLayer::translateIds(json_decode($slide['slide'], true)));

        $slideId = $this->_create($slide['title'] . ' - copy', $slide['slide'], $slide['description'], $slide['thumbnail'], $slide['published'], $slide['publish_up'], $slide['publish_down'], 0, $slide['params'], $targetSliderId, $slide['ordering'], $slide['generator_id']);
        self::markChanged($slide['slider']);
        return $slideId;
    }

    public function first($id) {
        $slide = $this->get($id);

        $this->db->update(array("first" => 0), array(
            "slider" => $slide['slider']
        ));

        $this->db->update(array(
            "first" => 1
        ), array(
            "id" => $id
        ));

        self::markChanged($slide['slider']);
    }

    public function publish($id) {

        self::markChanged(N2Request::getInt('sliderid'));

        return $this->db->update(array(
            "published" => 1
        ), array("id" => intval($id)));
    }

    public function unPublish($id) {
        $this->db->update(array(
            "published" => 0
        ), array(
            "id" => intval($id)
        ));

        self::markChanged(N2Request::getInt('sliderid'));

    }

    public function deleteBySlider($sliderid) {

        $slides = $this->getAll($sliderid);
        foreach ($slides as $slide) {
            $this->delete($slide['id']);
        }
        self::markChanged($sliderid);
    }

    /**
     * @param $sliderid
     * @param $ids
     *
     * @return bool|int
     */
    public function order($sliderid, $ids) {
        if (is_array($ids) && count($ids) > 0) {
            $i = 0;
            foreach ($ids AS $id) {
                $id = intval($id);
                if ($id > 0) {
                    $update = $this->db->update(array(
                        'ordering' => $i,
                    ), array(
                        "id"     => $id,
                        "slider" => $sliderid
                    ));

                    $i++;
                }
            }

            self::markChanged($sliderid);

            return $i;
        }
        return false;
    }

    public function markChanged($sliderid) {
        N2SmartSliderHelper::getInstance()
                           ->setSliderChanged($sliderid, 1);
    }

    public function makeStatic($slideId) {
        $slideData = $this->get($slideId);
        if ($slideData['generator_id'] > 0) {
            $sliderObj = new N2SmartSlider($slideData['slider'], array());
            $rootSlide = new N2SmartSliderSlide($sliderObj, $slideData);
            $rootSlide->initGenerator(array());
            $slides = $rootSlide->expandSlide();

            // Shift the afterwards slides with the slides count
            $this->db->query("UPDATE {$this->db->tableName} SET ordering = ordering + " . count($slides) . " WHERE slider = :sliderid AND ordering > :ordering", array(
                ":sliderid" => intval($slideData['slider']),
                ":ordering" => intval($slideData['ordering'])
            ), '');

            $firstUsed = false;
            $i         = 1;
            foreach ($slides AS $slide) {
                $row = $slide->getRow();
                // set the proper ordering
                $row['ordering'] += $i;
                if ($row['first']) {
                    // Make sure to mark only one slide as start slide
                    if ($firstUsed) {
                        $row['first'] = 0;
                    } else {
                        $firstUsed = true;
                    }
                }
                $this->db->insert($row);
                $i++;
            }

            $this->db->query("UPDATE {$this->db->tableName} SET published = 0, first = 0 WHERE id = :id", array(
                ":id" => $slideData['id']
            ), '');

            return count($slides);
        } else {
            return false;
        }
    }

    /**
     * @param $slide  N2SmartSliderSlide
     * @param $slider N2SmartSliderAbstract
     * @param $widget
     * @param $appType
     *
     * @throws Exception
     */
    public static function box($slide, $slider, $widget, $appType) {

        $lt = array();

        if ($slide->isStatic()) {
            $lt[] = NHtml::tag('div', array(
                'class' => 'n2-button-tag n2-button n2-button-x-small n2-sidebar-list-bg n2-uc n2-h5',
            ), n2_('Static slide'));
        } else {

            $lt[] = NHtml::tag('div', array(
                'class' => 'n2-button-tag n2-button n2-button-x-small n2-button-green n2-uc n2-h5 n2-slide-is-first',
            ), n2_('First'));

            $lt[] = NHtml::tag('a', array(
                'class' => 'n2-button n2-button-x-small n2-sidebar-list-bg n2-uc n2-h5 n2-slide-first',
                'href'  => $appType->router->createUrl(array(
                    'slides/first',
                    array(
                        'sliderid' => $slider->sliderId,
                        'slideid'  => $slide->id
                    ) + N2Form::tokenizeUrl()
                ))
            ), n2_('Set First'));
        }

        $rt = array();

        $rt[] = NHtml::tag('a', array(
            'class' => 'n2-button n2-button-small n2-sidebar-list-bg n2-sidebar-list-bg n2-slide-duplicate',
            'href'  => $appType->router->createUrl(array(
                'slides/duplicate',
                array(
                    'sliderid' => $slider->sliderId,
                    'slideid'  => $slide->id
                ) + N2Form::tokenizeUrl()
            ))
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-duplicate'), ''));

        $rt[] = NHtml::tag('a', array(
            'class' => 'n2-button n2-button-small n2-sidebar-list-bg n2-slide-delete',
            'href'  => $appType->router->createUrl(array(
                'slides/delete',
                array(
                    'sliderid' => $slider->sliderId,
                    'slideid'  => $slide->id
                ) + N2Form::tokenizeUrl()
            ))
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-delete'), ''));

        $rt[] = NHtml::tag('div', array(
            'class' => 'n2-button n2-button-small n2-button-blue n2-slide-selected',
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-tick'), ''));

        $rb = array();

        if ($slide->hasGenerator()) {
            $rb[] = NHtml::tag('a', array(
                'class' => 'n2-button n2-button-x-small n2-sidebar-list-bg n2-uc n2-h5 n2-slide-generator' . (N2Request::getVar('generator_id') == $slide->generator_id ? ' n2-button-blue' : ''),
                'href'  => $appType->router->createUrl(array(
                    'generator/edit',
                    array(
                        'generator_id' => $slide->generator_id
                    )
                ))
            ), 'Edit generator');
        }

        $image = $slide->getThumbnail();
        if (empty($image)) {
            $image = '$system$/images/placeholder/image.png';
        }

        $editUrl = $appType->router->createUrl(array(
            'slides/edit',
            array(
                'sliderid' => $slider->sliderId,
                'slideid'  => $slide->id
            )
        ));

        $widget->init("box", array(
            'attributes'         => array(
                'class'        => 'n2-box-slide n2-box-overflow' . ($slide->isFirst() ? ' n2-first-slide' : '') . ($slide->isCurrentlyEdited() ? ' n2-ss-slide-active' : ''),
                'data-slideid' => $slide->id,
                'data-editUrl' => $editUrl
            ),
            'image'              => N2ImageHelper::fixed($image),
            'firstCol'           => Nhtml::link($slide->getTitle() . ($slide->hasGenerator() ? ' [' . $slide->getSlideStat() . ']' : ''), $editUrl, array('class' => 'n2-h4')),
            'lt'                 => implode('', $lt),
            'rt'                 => implode('', $rt),
            'rtAttributes'       => array('class' => 'n2-on-hover'),
            'rb'                 => implode('', $rb),
            'placeholderContent' => NHtml::tag('a', array(
                'class' => 'n2-slide-published' . ($slide->published ? ' n2-active' : ''),
                'href'  => $appType->router->createUrl(array(
                    'slides/publish',
                    array(
                        'sliderid' => $slider->sliderId,
                        'slideid'  => $slide->id
                    ) + N2Form::tokenizeUrl()
                ))
            ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-unpublished'), ''))
        ));
    }
} 