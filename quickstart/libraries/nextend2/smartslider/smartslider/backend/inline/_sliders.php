<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><div class="n2-sidebar-inner">
    <?php
    N2Loader::import('libraries.browse.browse');

    $app          = N2Base::getApplication('smartslider');
    $accessEdit   = N2Acl::canDo('smartslider_edit', $app->info);
    $accessDelete = N2Acl::canDo('smartslider_delete', $app->info);

    $sliderid    = N2Request::getInt('sliderid', 0);
    $generatorId = N2Request::getInt('generator_id', 0);
    $controller  = N2Request::getCmd('nextendcontroller');

    $slidersModel = new N2SmartsliderSlidersModel();

    $showSlideManager = false;
    $dl               = array();

    if (!$sliderid) {

        $orderBy          = N2SmartSliderSettings::get('slidersOrder', 'time');
        $orderByDirection = N2SmartSliderSettings::get('slidersOrderDirection', 'DESC');

        $actions = NHtml::tag('a', array(
            "class" => 'n2-button n2-button-grey n2-button-medium' . ($orderBy == 'title' ? ' n2-active' : ''),
            "href"  => $this->appType->router->createUrl(array(
                'sliders/orderby',
                array(
                    'title' => ($orderBy == 'title' ? ($orderByDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC')
                ) + N2Form::tokenizeUrl()
            ))
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-16' . ($orderBy == 'title' ? ($orderByDirection == 'ASC' ? ' n2-i-sortalphabetic1' : ' n2-i-sortalphabetic2') : ' n2-i-sortalphabetic1')), ''));

        $actions .= NHtml::tag('a', array(
            "class" => 'n2-button n2-button-grey n2-button-medium' . ($orderBy == 'time' ? ' n2-active' : ''),
            "href"  => $this->appType->router->createUrl(array(
                'sliders/orderby',
                array(
                    'time' => ($orderBy == 'time' ? ($orderByDirection == 'ASC' ? 'DESC' : 'ASC') : 'DESC')
                ) + N2Form::tokenizeUrl()
            ))
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-16' . ($orderBy == 'time' ? ($orderByDirection == 'ASC' ? ' n2-i-sortdate1' : ' n2-i-sortdate2') : ' n2-i-sortdate2')), ''));

        $dl[]    = array(
            'title'   => '<span>' . n2_('SLIDER LIST') . '</span>',
            'class'   => 'n2-ss-slider-ordering n2-ss-slide2-list',
            'tooltip' => null,
            'actions' => $actions
        );
        $sliders = $slidersModel->getAll($orderBy, $orderByDirection);
    } else {
        // Show only one sliders's sidebar in this context
        $sliders = array($slidersModel->get($sliderid));
    }

    if ($sliders) {
        foreach ($sliders AS $slider) {
            $active = $sliderid == $slider['id'];
            $preUl  = '';
            if ($active) {
                $showSlideManager = true;
                ob_start();
                $this->widget->init("buttonmenu", array(
                    "content" => NHtml::tag('div', array(
                        'class' => 'n2-button-menu'
                    ), NHtml::tag('div', array(
                        'class' => 'n2-button-menu-inner n2-border-radius'
                    ), NHtml::link(n2_('Add images'), '#', array(
                            'class' => 'n2-add-quick-image n2-h4'
                        )) . NHtml::link(n2_('Add video'), '#', array(
                            'class' => 'n2-add-quick-video n2-h4' . (N2Platform::$hasPosts ? '' : ' n2-separator')
                        )) . (N2Platform::$hasPosts ? NHtml::link(n2_('Add post'), '#', array(
                            'class' => 'n2-add-quick-post n2-h4'
                        )) : '') . NHtml::link(n2_('Create empty slide'), $app->router->createUrl(array(
                            "slides/create",
                            array(
                                "sliderid" => N2Request::getInt('sliderid'),
                                "static"   => 0
                            )
                        )), array(
                            'class' => 'n2-h4 n2-separator'
                        )). NHtml::link(n2_('Create static slide'), $app->router->createUrl(array(
                            "slides/create",
                            array(
                                "sliderid" => N2Request::getInt('sliderid'),
                                "static"   => 1
                            )
                        )), array(
                            'class' => 'n2-h4'
                        ))  . NHtml::link(n2_('Create dynamic slides'), $app->router->createUrl(array(
                            "generator/create",
                            array(
                                "sliderid" => N2Request::getInt('sliderid')
                            )
                        )), array(
                            'class' => 'n2-h4'
                        ))))
                ));
                $buttonMenu = ob_get_clean();

                ob_start();
                $this->widget->init("buttonmenu", array(
                    "content" => NHtml::tag('div', array(
                        'class' => 'n2-button-menu'
                    ), NHtml::tag('div', array(
                        'class' => 'n2-button-menu-inner n2-border-radius'
                    ), NHtml::link(n2_('Select all'), '#', array(
                            'class' => 'n2-h4'
                        )) . NHtml::link(n2_('Select none'), '#', array(
                            'class' => 'n2-h4'
                        )) . NHtml::link(n2_('Select published'), '#', array(
                            'class' => 'n2-h4'
                        )) . NHtml::link(n2_('Select unpublished'), '#', array(
                            'class' => 'n2-h4'
                        ))))
                ));
                $selectButtonMenu = ob_get_clean();

                ob_start();
                $this->widget->init("buttonmenu", array(
                    "content" => NHtml::tag('div', array(
                        'class' => 'n2-button-menu'
                    ), NHtml::tag('div', array(
                        'class' => 'n2-button-menu-inner n2-border-radius'
                    ), NHtml::link(n2_('Duplicate'), '#', array(
                            'class' => 'n2-h4'
                        )) . NHtml::link(n2_('Publish'), '#', array(
                            'class' => 'n2-h4'
                        )) . NHtml::link(n2_('Unpublish'), '#', array(
                            'class' => 'n2-h4'
                        ))))
                ));
                $actionButtonMenu = ob_get_clean();

                ob_start();
                ?>
                <div class="n2-sidebar-list-bg n2-ss-slides-control">
                    <?php
                    echo NHtml::tag('div', array(
                        'class' => 'n2-button n2-button-big n2-button-grey n2-slides-bulk'
                    ), NHtml::tag('i', array('class' => 'n2-i n2-i-bulk n2-it'), ''));
                    echo NHtml::tag('div', array('class' => 'n2-button n2-button-with-menu n2-button-big n2-button-green n2-slides-add'), NHtml::link(n2_('Add image slide'), '#', array(
                            'class' => 'n2-button-inner n2-add-quick-image n2-uc n2-h3'
                        )) . $buttonMenu);

                    echo NHtml::tag('div', array('class' => 'n2-button n2-button-with-menu n2-button-big n2-button-grey n2-bulk-select'), NHtml::link(n2_('Select'), '#', array(
                            'class' => 'n2-button-inner n2-uc n2-h4'
                        )) . $selectButtonMenu);
                    echo NHtml::tag('div', array('class' => 'n2-button n2-button-with-menu n2-button-big n2-button-grey n2-bulk-action'), NHtml::link(n2_('Delete'), '#', array(
                            'class' => 'n2-button-inner n2-uc n2-h4'
                        )) . $actionButtonMenu);

                    echo NHtml::tag('div', array(
                        'class' => 'n2-button n2-button-big n2-button-red n2-bulk-cancel n2-uc n2-h4'
                    ), n2_('Cancel'));
                    ?>
                </div>
                <?php
                $preUl = ob_get_clean();
            }

            $actions = '';
            if ($accessEdit) {
                $actions .= NHtml::tag('a', array(
                    "href" => $this->appType->router->createUrl(array(
                        'slider/duplicate',
                        array(
                            'sliderid' => $slider["id"]
                        ) + N2Form::tokenizeUrl()
                    ))
                ), NHtml::tag('i', array('class' => 'n2-i n2-i-16 n2-i-duplicate n2-i-grey-opacity'), ''));
            }

            if ($accessDelete) {
                $actions .= NHtml::tag('a', array(
                    "onclick" => "return NextendDeleteModalLink(this, 'slider-delete', " . json_encode($slider['title']) . ");",
                    "href"    => $this->appType->router->createUrl(array(
                        'slider/delete',
                        array(
                            'sliderid' => $slider["id"]
                        ) + N2Form::tokenizeUrl()
                    ))
                ), NHtml::tag('i', array('class' => 'n2-i n2-i-16 n2-i-delete n2-i-grey-opacity'), ''));
            }

            $dl[] = array(
                'title'   => '<i class="n2-i n2-i-slider"></i><span>' . $slider['title'] . '</span><span class="n2-id n2-h5">#' . $slider['id'] . '</span>',
                'link'    => $this->appType->router->createUrl(array(
                    'slider/edit',
                    array(
                        'sliderid' => $slider["id"]
                    )
                )),
                'class'   => 'n2-ss-slide2-list ' . ($active ? 'n2-open ' : '') . ($active && $controller == 'sliders' ? 'active ' : ''),
                'preUl'   => $preUl,
                'tooltip' => null
                /*,
                                'actions' => $actions*/,
                'actions' => $actions
            );
        }
    }
    /**
     * @see Definitionlist
     */
    $this->widget->init("definitionlist", array(
        "dl" => $dl
    ));

    if ($showSlideManager) {
        $this->renderInline("slidemanager", array(
            'slider' => $slider
        ));
    }
    ?>
</div>