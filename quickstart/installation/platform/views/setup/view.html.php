<?php
/**
 * @package angi4j
 * @copyright Copyright (C) 2009-2016 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 */

defined('_AKEEBA') or die();

class AngieViewSetup extends AView
{
	public function onBeforeMain()
	{
		/** @var AngieModelJoomlaSetup $model */
		$model           = $this->getModel();
		$this->stateVars = $model->getStateVariables();
		$this->hasFTP    = function_exists('ftp_connect');

		return true;
	}
}