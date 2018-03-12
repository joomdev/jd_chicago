<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.slider.generator.N2SmartSliderGeneratorAbstract', 'smartslider');
require_once(JPATH_SITE . '/components/com_content/helpers/route.php');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorJoomlaContentArticle extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        N2Loader::import('nextend.database.database');
        $db = JFactory::getDbo();

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        $tags       = array_map('intval', explode('||', $this->data->get('sourcetags', '0')));

        $query = 'SELECT ';
        $query .= 'con.id, ';
        $query .= 'con.title, ';
        $query .= 'con.alias, ';
        $query .= 'con.introtext, ';
        $query .= 'con.fulltext, ';
        $query .= 'con.catid, ';
        $query .= 'cat.title AS cat_title, ';
        $query .= 'cat.alias AS cat_alias, ';
        $query .= 'con.created_by, con.state, ';
        $query .= 'usr.name AS created_by_alias, ';
        $query .= 'con.images ';

        $query .= 'FROM #__content AS con ';

        $query .= 'LEFT JOIN #__users AS usr ON usr.id = con.created_by ';

        $query .= 'LEFT JOIN #__categories AS cat ON cat.id = con.catid ';

        $jNow  = JFactory::getDate();
        $now   = $jNow->toSql();
        $where = array(
            'con.state = 1 ',
            "(con.publish_up = '0000-00-00 00:00:00' OR con.publish_up < '" . $now . "') AND (con.publish_down = '0000-00-00 00:00:00' OR con.publish_down > '" . $now . "') "
        );

        if (!in_array(0, $categories)) {
            $where[] = 'con.catid IN (' . implode(',', $categories) . ') ';
        }

        if (!in_array(0, $tags)) {
            $where[] = 'con.id IN (SELECT content_item_id FROM #__contentitem_tag_map WHERE type_alias = \'com_content.article\' AND tag_id IN (' . implode(',', $tags) . ')) ';
        }

        $sourceUserID = intval($this->data->get('sourceuserid', ''));
        if ($sourceUserID) {
            $where[] = 'con.created_by = ' . $sourceUserID . ' ';
        }

        switch ($this->data->get('sourcefeatured', 0)) {
            case 1:
                $where[] = 'con.featured = 1 ';
                break;
            case -1:
                $where[] = 'con.featured = 0 ';
                break;
        }
        $language = $this->data->get('sourcelanguage', '*');
        if ($language) {
            $where[] = 'con.language = ' . $db->quote($language) . ' ';
        }

        if (count($where) > 0) {
            $query .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $order = N2Parse::parse($this->data->get('joomlaorder', 'con.title|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $db->setQuery($query);
        $result = $db->loadAssocList();

        $dispatcher = JDispatcher::getInstance();
        JPluginHelper::importPlugin('content');
        $uri = N2Uri::getBaseUri();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = Array(
                'title' => $result[$i]['title']
            );

            $article       = new stdClass();
            $article->text = N2SmartSlider::removeShortcode($result[$i]['introtext']);
            $_p            = array();
            $dispatcher->trigger('onContentPrepare', array(
                'com_smartslider3',
                &$article,
                &$_p,
                0
            ));
            if (!empty($article->text)) {
                $r['description'] = $article->text;
            }

            $article->text = $result[$i]['fulltext'];
            $_p            = array();
            $dispatcher->trigger('onContentPrepare', array(
                'com_smartslider3',
                &$article,
                &$_p,
                0
            ));
            if (!empty($article->text)) {
                $result[$i]['fulltext'] = $article->text;
                if (!isset($r['description'])) {
                    $r['description'] = $result[$i]['fulltext'];
                } else {
                    $r['fulltext'] = $result[$i]['fulltext'];
                }
            }

            $images = (array)json_decode($result[$i]['images'], true);

            $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback($uri . "/", array(
                @$images['image_intro'],
                @$images['image_fulltext']
            ), array(
                @$r['description']
            ));

            $r += array(
                'url'               => ContentHelperRoute::getArticleRoute($result[$i]['id'] . ':' . $result[$i]['alias'], $result[$i]['catid'] . ':' . $result[$i]['cat_alias']),
                'url_label'         => sprintf(n2_('View %s'), n2_('article')),
                'category_list_url' => 'index.php?option=com_content&view=category&id=' . $result[$i]['catid'],
                'category_blog_url' => 'index.php?option=com_content&view=category&layout=blog&id=' . $result[$i]['catid'],
                'fulltext_image'    => !empty($images['image_fulltext']) ? N2ImageHelper::dynamic($uri . "/" . $images['image_fulltext']) : '',
                'category_title'    => $result[$i]['cat_title'],
                'created_by'        => $result[$i]['created_by_alias'],
                'id'                => $result[$i]['id']
            );

            $data[] = $r;
        }
        return $data;
    }

}