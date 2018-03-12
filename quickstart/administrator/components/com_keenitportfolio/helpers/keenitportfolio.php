<?php

/**
 * @version     2.0.0
 * @package     com_keenitportfolio
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abdur Rashid <rashid.cse.05@gmail.com> - http://www.keenitsolution.com
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Keenitportfolio helper.
 */
class KeenitportfolioHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        		JHtmlSidebar::addEntry(
			JText::_('COM_KEENITPORTFOLIO_TITLE_PORTFOLIOS'),
			'index.php?option=com_keenitportfolio&view=portfolios',
			$vName == 'portfolios'
		);
		JHtmlSidebar::addEntry(
			JText::_('JCATEGORIES') . ' (' . JText::_('COM_KEENITPORTFOLIO_TITLE_PORTFOLIOS') . ')',
			"index.php?option=com_categories&extension=com_keenitportfolio",
			$vName == 'categories'
		);
		if ($vName=='categories') {
			JToolBarHelper::title('Keen IT Portfolio: JCATEGORIES (COM_KEENITPORTFOLIO_TITLE_PORTFOLIOS)');
		}

    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_keenitportfolio';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }


}
