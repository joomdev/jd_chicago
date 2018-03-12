<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderStorage
{


    public static function init() {
        N2Pluggable::addAction('fontStorage', 'N2SmartSliderStorage::fontStorage');
        N2Pluggable::addAction('styleStorage', 'N2SmartSliderStorage::styleStorage');
        N2Pluggable::addAction('animationStorage', 'N2SmartSliderStorage::animationStorage');
        N2Pluggable::addAction('splitTextAnimationStorage', 'N2SmartSliderStorage::splitTextAnimationStorage');
        N2Pluggable::addAction('backgroundAnimationStorage', 'N2SmartSliderStorage::backgroundAnimationStorage');
        N2Pluggable::addAction('postBackgroundAnimationStorage', 'N2SmartSliderStorage::postBackgroundAnimationStorage');
        N2Pluggable::addAction('layoutStorage', 'N2SmartSliderStorage::layoutStorage');
    }

    public static function styleStorage(&$sets, &$styles) {
        N2Base::getApplicationInfo('smartslider')
              ->loadLocale();

        array_push($sets, array(
            'id'           => 1000,
            'referencekey' => '',
            'value'        => n2_('Heading')
        ));

        array_push($styles, array(
            'id'           => 1001,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Blue'),
                'data' => array(
                    array(
                        'backgroundcolor' => '01add3ff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1002,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('White'),
                'data' => array(
                    array(
                        'backgroundcolor' => 'ffffffcc',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1003,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Green'),
                'data' => array(
                    array(
                        'backgroundcolor' => '5cba3cff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1004,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Purple'),
                'data' => array(
                    array(
                        'backgroundcolor' => '8757b2ff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1005,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Grey'),
                'data' => array(
                    array(
                        'backgroundcolor' => '81898dff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1006,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Black'),
                'data' => array(
                    array(
                        'backgroundcolor' => '000000cc',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1007,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Rounded Blue'),
                'data' => array(
                    array(
                        'backgroundcolor' => '01add3ff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'borderradius'    => '3',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1008,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Rounded Green'),
                'data' => array(
                    array(
                        'backgroundcolor' => '5cba3cff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'borderradius'    => '3',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1009,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Rounded Purple'),
                'data' => array(
                    array(
                        'backgroundcolor' => '8757b2ff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'borderradius'    => '3',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1010,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Rounded Grey'),
                'data' => array(
                    array(
                        'backgroundcolor' => '81898dff',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'borderradius'    => '3',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1011,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Rounded White'),
                'data' => array(
                    array(
                        'backgroundcolor' => 'ffffffcc',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'borderradius'    => '3',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1012,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Rounded Black'),
                'data' => array(
                    array(
                        'backgroundcolor' => '000000cc',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'borderradius'    => '3',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1013,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Border White'),
                'data' => array(
                    array(
                        'backgroundcolor' => '00000000',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'border'          => '2|*|solid|*|ffffffff',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1014,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Border Dark'),
                'data' => array(
                    array(
                        'backgroundcolor' => '00000000',
                        'padding'         => '5|*|20|*|5|*|20|*|px',
                        'border'          => '2|*|solid|*|000000cc',
                    ),

                ),
            )
        ));


        array_push($sets, array(
            'id'           => 1100,
            'referencekey' => '',
            'value'        => n2_('Button')
        ));

        array_push($styles, array(
            'id'           => 1101,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rectangle Green'),
                'data' => array(
                    array(
                        'backgroundcolor' => '5cba3cff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1102,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rectangle Blue'),
                'data' => array(
                    array(
                        'backgroundcolor' => '01add3ff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1103,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rectangle Purple'),
                'data' => array(
                    array(
                        'backgroundcolor' => '8757b2ff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1104,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rectangle Grey'),
                'data' => array(
                    array(
                        'backgroundcolor' => '81898dff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1105,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Rectangle Green'),
                'data' => array(
                    array(
                        'backgroundcolor' => '5cba3cff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '3',
                    ),
                    array(
                        'backgroundcolor' => '58ad3bff',
                    ),
                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1106,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Rectangle Blue'),
                'data' => array(
                    array(
                        'backgroundcolor' => '01add3ff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '3',
                    ),
                    array(
                        'backgroundcolor' => '04a0c3ff',
                    ),
                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1107,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Rectangle Purple'),
                'data' => array(
                    array(
                        'backgroundcolor' => '8757b2ff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '3',
                    ),
                    array(
                        'backgroundcolor' => '7b51a1ff',
                    ),
                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1108,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Grey'),
                'data' => array(
                    array(
                        'backgroundcolor' => '81898dff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '3',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1109,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Green'),
                'data' => array(
                    array(
                        'backgroundcolor' => '5cba3cff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '30',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1110,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Blue'),
                'data' => array(
                    array(
                        'backgroundcolor' => '01add3ff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '30',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1111,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Purple'),
                'data' => array(
                    array(
                        'backgroundcolor' => '8757b2ff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '30',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1112,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Rounded Grey'),
                'data' => array(
                    array(
                        'backgroundcolor' => '81898dff',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'borderradius'    => '30',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1113,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Border Dark'),
                'data' => array(
                    array(
                        'backgroundcolor' => '00000000',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'border'          => '2|*|solid|*|000000cc',
                    ),

                ),
            )
        ));

        array_push($styles, array(
            'id'           => 1114,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Border Light'),
                'data' => array(
                    array(
                        'backgroundcolor' => '00000000',
                        'padding'         => '10|*|30|*|10|*|30|*|px',
                        'border'          => '2|*|solid|*|ffffffff',
                    ),

                ),
            )
        ));

        array_push($sets, array(
            'id'           => 1800,
            'referencekey' => '',
            'value'        => n2_('Other')
        ));

        array_push($styles, array(
            'id'           => 1801,
            'referencekey' => 1800,
            'value'        => array(
                'name' => n2_('List'),
                'data' => array(
                    array(
                        'padding' => '10|*|20|*|10|*|20|*|px',
                        'extra'   => 'margin:0;'
                    ),

                ),
            )
        ));

        array_push($sets, array(
            'id'           => 1900,
            'referencekey' => '',
            'value'        => n2_('My styles')
        ));
    }

    public static function fontStorage(&$sets, &$fonts) {
        N2Base::getApplicationInfo('smartslider')
              ->loadLocale();

        array_push($sets, array(
            'id'           => 1000,
            'referencekey' => '',
            'value'        => n2_('Default')
        ));

        array_push($fonts, array(
            'id'           => 1001,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('X-small Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '12||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1002,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('X-small Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '12||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1003,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Small Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '14||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1004,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Small Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '14||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1005,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Medium Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '24||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1006,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Medium Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '24||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1007,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Large Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '30||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1008,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Large Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '30||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1009,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('X-large Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '36||px',
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1010,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('X-large Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '36||px',
                    ),


                ),
            )
        ));

        array_push($sets, array(
            'id'           => 1100,
            'referencekey' => '',
            'value'        => n2_('Center')
        ));

        array_push($fonts, array(
            'id'           => 1101,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('X-small Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '12||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1102,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('X-small Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '12||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1103,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Small Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '14||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1104,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Small Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '14||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1105,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Medium Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '24||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1106,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Medium Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '24||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1107,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Large Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '30||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1108,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('Large Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '30||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1109,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('X-large Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '36||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1110,
            'referencekey' => 1100,
            'value'        => array(
                'name' => n2_('X-large Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '36||px',
                        'align' => 'center'
                    ),


                ),
            )
        ));

        array_push($sets, array(
            'id'           => 1300,
            'referencekey' => '',
            'value'        => n2_('Link')
        ));
        array_push($fonts, array(
            'id'           => 1303,
            'referencekey' => 1300,
            'value'        => array(
                'name' => n2_('Small Light'),
                'data' => array(
                    array(
                        'color' => 'ffffffff',
                        'size'  => '14||px',
                        'align' => 'left'
                    ),
                    array(
                        'color' => '1890d7ff'
                    ),

                ),
            )
        ));
        array_push($fonts, array(
            'id'           => 1304,
            'referencekey' => 1300,
            'value'        => array(
                'name' => n2_('Small Dark'),
                'data' => array(
                    array(
                        'color' => '282828ff',
                        'size'  => '14||px',
                        'align' => 'left'
                    ),
                    array(
                        'color' => '1890d7ff'
                    ),

                ),
            )
        ));

        array_push($sets, array(
            'id'           => 1900,
            'referencekey' => '',
            'value'        => n2_('My fonts')
        ));
    }

    public static function animationStorage(&$sets, &$animations) {
        N2Base::getApplicationInfo('smartslider')
              ->loadLocale();

        array_push($sets, array(
            'id'           => 1000,
            'referencekey' => '',
            'value'        => n2_('Simple')
        ));
    }

    public static function splitTextAnimationStorage(&$sets, &$animations) {
        N2Base::getApplicationInfo('smartslider')
              ->loadLocale();

        array_push($sets, array(
            'id'           => 1000,
            'referencekey' => '',
            'value'        => n2_('Default')
        ));

        array_push($animations, array(
            'id'           => 1001,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Fade'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'opacity' => 0
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1002,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Left'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'opacity' => 0,
                        'x'       => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1003,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Right'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'opacity' => 0,
                        'x'       => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1004,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Top'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'opacity' => 0,
                        'y'       => -80
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1005,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Bottom'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'opacity' => 0,
                        'y'       => 80
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1006,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Scale up'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'opacity' => 0,
                        'scale'   => 0
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1007,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Scale down'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'opacity' => 0,
                        'scale'   => 5
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1008,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Warp'),
                'data' => array(
                    'transformOrigin' => '50|*|50|*|0',
                    'animation'       => array(
                        'ease'      => 'easeInBack',
                        'opacity'   => 0,
                        'x'         => 20,
                        'scale'     => 5,
                        'rotationX' => 90
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1009,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Twirl'),
                'data' => array(
                    'transformOrigin' => '100|*|100|*|0',
                    'animation'       => array(
                        'ease'      => 'easeInOutBack',
                        'opacity'   => 0,
                        'scale'     => 5,
                        'rotationX' => 360,
                        'rotationY' => -360,
                        'rotationZ' => 360
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1010,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Domino'),
                'data' => array(
                    'transformOrigin' => '0|*|0|*|0',
                    'animation'       => array(
                        'ease'      => 'easeInBack',
                        'rotationY' => 90
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1011,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Stand up'),
                'data' => array(
                    'transformOrigin' => '100|*|100|*|0',
                    'animation'       => array(
                        'ease'      => 'easeInOutBack',
                        'opacity'   => 0,
                        'rotationZ' => 90
                    )
                )
            )
        ));

        array_push($animations, array(
            'id'           => 1012,
            'referencekey' => 1000,
            'value'        => array(
                'name' => n2_('Rotate down'),
                'data' => array(
                    'transformOrigin' => '50|*|0|*|0',
                    'animation'       => array(
                        'ease'      => 'easeInBack',
                        'rotationX' => 90
                    )
                )
            )
        ));

        array_push($sets, array(
            'id'           => 1900,
            'referencekey' => '',
            'value'        => n2_('My split text animations')
        ));
    }

    public static function backgroundAnimationStorage(&$sets, &$animations) {
        N2Base::getApplicationInfo('smartslider')
              ->loadLocale();

        array_push($sets, array(
            'id'           => 1000,
            'referencekey' => '',
            'value'        => n2_('Default')
        ));

        array_push($animations, array(
            "id"           => 1402,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Scale to left'),
                'data' => array(
                    'type'   => 'Flat',
                    'tiles'  => array(
                        'crop'     => true,
                        'delay'    => 0,
                        'sequence' => 'ForwardDiagonal'
                    ),
                    'main'   => array(
                        'type'     => 'both',
                        'duration' => 1,
                        'current'  => array(
                            'ease'  => 'easeOutCubic',
                            'scale' => 0.7
                        ),
                        'next'     => array(
                            'ease' => 'easeOutCubic',
                            'left' => "100%"
                        )
                    ),
                    'invert' => array(
                        'zIndex'  => 2,
                        'current' => array(
                            'left'  => "100%",
                            'scale' => 1
                        ),
                        'next'    => array(
                            'scale' => 0.7,
                            'left'  => 0
                        )
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1012,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Zoom'),
                'data' => array(
                    'type'   => 'Flat',
                    'tiles'  => array(
                        'crop'     => false,
                        'delay'    => 0,
                        'sequence' => 'ForwardDiagonal'
                    ),
                    'main'   => array(
                        'type'     => 'both',
                        'duration' => .75,
                        'current'  => array(
                            'ease'    => 'easeOutCubic',
                            'scale'   => 0.5,
                            'opacity' => 0
                        ),
                        'next'     => array(
                            'ease'    => 'easeOutCubic',
                            'opacity' => 0,
                            'scale'   => 1.5
                        )
                    ),
                    'invert' => array(
                        'current' => array(
                            'scale' => 1.5
                        ),
                        'next'    => array(
                            'scale' => 0.5
                        )
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1013,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Fade'),
                'data' => array(
                    'type'  => 'Flat',
                    'tiles' => array(
                        'delay'    => 0,
                        'sequence' => 'ForwardDiagonal'
                    ),
                    'main'  => array(
                        'type'     => 'both',
                        'duration' => 1,
                        'zIndex'   => 2,
                        'current'  => array(
                            'ease'    => 'easeOutCubic',
                            'opacity' => 0
                        )
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1014,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Curtain to left'),
                'data' => array(
                    'type'        => 'Flat',
                    'rows'        => 1,
                    'columns'     => 25,
                    'tiles'       => array(
                        'delay'    => .03,
                        'sequence' => 'BackwardCol'
                    ),
                    'main'        => array(
                        'type'     => 'next',
                        'duration' => .35,
                        'next'     => array(
                            'ease'    => 'easeInOutQuart',
                            'opacity' => "0",
                            'left'    => '-100%'
                        )
                    ),
                    'invert'      => array(
                        'next' => array(
                            'left' => '100%'
                        )
                    ),
                    'invertTiles' => array(
                        'sequence' => 'ForwardCol'
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1024,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Puzzle'),
                'data' => array(
                    'type'    => 'Flat',
                    'rows'    => 5,
                    'columns' => 7,
                    'tiles'   => array(
                        'delay'    => 1,
                        'sequence' => 'Random'
                    ),
                    'main'    => array(
                        'type'     => 'next',
                        'duration' => 0.8,
                        'next'     => array(
                            'ease'    => 'easeInOutQuart',
                            'opacity' => 0
                        )
                    )
                )
            )
        ));

        array_push($sets, array(
            'id'           => 1100,
            'referencekey' => '',
            'value'        => n2_('Vertical')
        ));

        array_push($animations, array(
            "id"           => 1404,
            'referencekey' => 1100,
            "value"        => array(
                'name' => n2_('Scale to top'),
                'data' => array(
                    'type'   => 'Flat',
                    'tiles'  => array(
                        'crop'     => true,
                        'delay'    => 0,
                        'sequence' => 'ForwardDiagonal'
                    ),
                    'main'   => array(
                        'type'     => 'both',
                        'duration' => 1,
                        'current'  => array(
                            'ease'  => 'easeOutCubic',
                            'scale' => 0.7
                        ),
                        'next'     => array(
                            'ease' => 'easeOutCubic',
                            'top'  => "100%"
                        )
                    ),
                    'invert' => array(
                        'zIndex'  => 2,
                        'current' => array(
                            'top'   => "100%",
                            'scale' => 1
                        ),
                        'next'    => array(
                            'scale' => 0.7,
                            'top'   => 0
                        )
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1403,
            'referencekey' => 1100,
            "value"        => array(
                'name' => n2_('Scale to bottom'),
                'data' => array(
                    'type'   => 'Flat',
                    'tiles'  => array(
                        'crop'     => true,
                        'delay'    => 0,
                        'sequence' => 'ForwardDiagonal'
                    ),
                    'main'   => array(
                        'type'     => 'both',
                        'duration' => 1,
                        'current'  => array(
                            'ease'  => 'easeOutCubic',
                            'scale' => 0.7
                        ),
                        'next'     => array(
                            'ease' => 'easeOutCubic',
                            'top'  => "-100%"
                        )
                    ),
                    'invert' => array(
                        'zIndex'  => 2,
                        'current' => array(
                            'top'   => "-100%",
                            'scale' => 1
                        ),
                        'next'    => array(
                            'scale' => 0.7,
                            'top'   => 0
                        )
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1016,
            'referencekey' => 1100,
            "value"        => array(
                'name' => n2_('Curtain to bottom'),
                'data' => array(
                    'type'        => 'Flat',
                    'rows'        => 25,
                    'columns'     => 1,
                    'tiles'       => array(
                        'delay'    => .03,
                        'sequence' => 'ForwardRow'
                    ),
                    'main'        => array(
                        'type'     => 'next',
                        'duration' => .35,
                        'next'     => array(
                            'ease'    => 'easeInOutQuart',
                            'opacity' => "0",
                            'top'     => '100%'
                        )
                    ),
                    'invert'      => array(
                        'next' => array(
                            'top' => '-100%'
                        )
                    ),
                    'invertTiles' => array(
                        'sequence' => 'BackwardRow'
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1017,
            'referencekey' => 1100,
            "value"        => array(
                'name' => n2_('Curtain to top'),
                'data' => array(
                    'type'        => 'Flat',
                    'rows'        => 25,
                    'columns'     => 1,
                    'tiles'       => array(
                        'delay'    => .03,
                        'sequence' => 'BackwardRow'
                    ),
                    'main'        => array(
                        'type'     => 'next',
                        'duration' => .35,
                        'next'     => array(
                            'ease'    => 'easeInOutQuart',
                            'opacity' => "0",
                            'top'     => '-100%'
                        )
                    ),
                    'invert'      => array(
                        'next' => array(
                            'top' => '100%'
                        )
                    ),
                    'invertTiles' => array(
                        'sequence' => 'ForwardRow'
                    )
                )
            )
        ));

        array_push($sets, array(
            'id'           => 1200,
            'referencekey' => '',
            'value'        => 'RTL'
        ));

        array_push($animations, array(
            "id"           => 1401,
            'referencekey' => 1200,
            "value"        => array(
                'name' => n2_('Scale to right'),
                'data' => array(
                    'type'   => 'Flat',
                    'tiles'  => array(
                        'crop'     => true,
                        'delay'    => 0,
                        'sequence' => 'ForwardDiagonal'
                    ),
                    'main'   => array(
                        'type'     => 'both',
                        'duration' => 1,
                        'current'  => array(
                            'ease'  => 'easeOutCubic',
                            'scale' => 0.7
                        ),
                        'next'     => array(
                            'ease' => 'easeOutCubic',
                            'left' => "-100%"
                        )
                    ),
                    'invert' => array(
                        'zIndex'  => 2,
                        'current' => array(
                            'left'  => "-100%",
                            'scale' => 1
                        ),
                        'next'    => array(
                            'scale' => 0.7,
                            'left'  => 0
                        )
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1015,
            'referencekey' => 1200,
            "value"        => array(
                'name' => n2_('Curtain to right'),
                'data' => array(
                    'type'        => 'Flat',
                    'rows'        => 1,
                    'columns'     => 25,
                    'tiles'       => array(
                        'delay'    => .03,
                        'sequence' => 'ForwardCol'
                    ),
                    'main'        => array(
                        'type'     => 'next',
                        'duration' => .35,
                        'next'     => array(
                            'ease'    => 'easeInOutQuart',
                            'opacity' => "0",
                            'left'    => '100%'
                        )
                    ),
                    'invert'      => array(
                        'next' => array(
                            'left' => '-100%'
                        )
                    ),
                    'invertTiles' => array(
                        'sequence' => 'BackwardCol'
                    )
                )
            )
        ));

    }

    public static function postBackgroundAnimationStorage(&$sets, &$animations) {
        N2Base::getApplicationInfo('smartslider')
              ->loadLocale();

        array_push($sets, array(
            'id'           => 1000,
            'referencekey' => '',
            'value'        => n2_('Default')
        ));

        array_push($animations, array(
            "id"           => 1001,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Downscale'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale'),
                    'from'     => array(
                        'scale' => 1.5
                    ),
                    'to'       => array(
                        'scale' => 1.2
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1002,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Downscale left'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','x'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.2,
                        'x'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1003,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Downscale right'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','x'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.2,
                        'x'     => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1004,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Downscale top'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'y'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.2,
                        'y'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1005,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Downscale bottom'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'y'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.2,
                        'y'     => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1006,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Upscale'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale'),
                    'from'     => array(
                        'scale' => 1.2
                    ),
                    'to'       => array(
                        'scale' => 1.5
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1007,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Upscale left'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','x'),
                    'from'     => array(
                        'scale' => 1.2,
                        'x'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1008,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Upscale right'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','x'),
                    'from'     => array(
                        'scale' => 1.2,
                        'x'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1009,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Upscale top'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','y'),
                    'from'     => array(
                        'scale' => 1.2,
                        'y'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'y'     => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1010,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('Upscale bottom'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('scale','y'),
                    'from'     => array(
                        'scale' => 1.2,
                        'y'     => 0

                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'y'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1011,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To left'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('x'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1012,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To right'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('x'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1013,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To top'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'y'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'y'     => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1014,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To bottom'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'y'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'y'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1015,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To bottom left'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('x', 'y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0,
                        'y'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => 100,
                        'y'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1016,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To top right'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('x', 'y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0,
                        'y'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => -100,
                        'y'     => 100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1017,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To bottom left'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('x', 'y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0,
                        'y'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => 100,
                        'y'     => -100
                    )
                )
            )
        ));

        array_push($animations, array(
            "id"           => 1018,
            'referencekey' => 1000,
            "value"        => array(
                'name' => n2_('To bottom right'),
                'data' => array(
                    'duration' => 5,
                    'strength' => array('x', 'y'),
                    'from'     => array(
                        'scale' => 1.5,
                        'x'     => 0,
                        'y'     => 0
                    ),
                    'to'       => array(
                        'scale' => 1.5,
                        'x'     => -100,
                        'y'     => -100
                    )
                )
            )
        ));
    }

    public static function layoutStorage(&$sets, &$layouts) {
        N2Base::getApplicationInfo('smartslider')
              ->loadLocale();

        array_push($sets, array(
            'id'           => 1900,
            'referencekey' => '',
            'value'        => n2_('My layouts')
        ));
    }
}

N2SmartSliderStorage::init();