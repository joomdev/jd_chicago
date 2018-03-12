<?php
/**
 * @package angi4j
 * @copyright Copyright (C) 2009-2016 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 */

defined('_AKEEBA') or die();

class AngieViewDatabase extends AView
{
	public function onBeforeMain()
	{
		/** @var AngieModelSteps $stepsModel */
		$stepsModel = AModel::getAnInstance('Steps', 'AngieModel', array(), $this->container);
		/** @var AngieModelDatabase $dbModel */
		$dbModel = AModel::getAnInstance('Database', 'AngieModel', array(), $this->container);

		$this->substep = $stepsModel->getActiveSubstep();
		$this->number_of_substeps = $stepsModel->getNumberOfSubsteps();
		$this->db = $dbModel->getDatabaseInfo($this->substep);

		return true;
	}
}