<?php
/**
 * @package   angi4j
 * @copyright Copyright (C) 2009-2016 Nicholas K. Dionysopoulos. All rights reserved.
 * @author    Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 */

defined('_AKEEBA') or die();

class AngieModelJoomlaConfiguration extends AngieModelBaseConfiguration
{
	public function __construct($config = array(), AContainer $container = null)
	{
		// Call the parent constructor
		parent::__construct($config, $container);

		// Get the Joomla! version from the configuration or the session
		if (array_key_exists('jversion', $config))
		{
			$jVersion = $config['jversion'];
		}
		else
		{
			$jVersion = $this->container->session->get('jversion', '2.5.0');
		}

		// Load the configuration variables from the session or the default configuration shipped with ANGIE
		$this->configvars = $this->container->session->get('configuration.variables');

		if (empty($this->configvars))
		{
			// Get default configuration based on the Joomla! version
			if (version_compare($jVersion, '2.5.0', 'ge') && version_compare($jVersion, '3.0.0', 'lt'))
			{
				$v = '25';
			}
			else
			{
				$v = '30';
			}
			$className = 'J' . $v . 'Config';
			$filename = APATH_INSTALLATION . '/platform/models/jconfig/j' . $v . '.php';
			$this->configvars = $this->loadFromFile($filename, $className);

			if (!empty($this->configvars))
			{
				$this->saveToSession();
			}
		}
	}

	/**
	 * Loads the configuration information from a PHP file
	 *
	 * @param   string $file      The full path to the file
	 * @param   string $className The name of the configuration class
     *
     * @return  array
	 */
	public function loadFromFile($file, $className = 'JConfig')
	{
		$ret = array();

		include_once $file;

		if (class_exists($className))
		{
			foreach (get_class_vars($className) as $key => $value)
			{
				$ret[$key] = $value;
			}
		}

		return $ret;
	}

	/**
	 * Get the contents of the configuration.php file
	 *
	 * @param   string $className The name of the configuration class, by default it's JConfig
	 *
	 * @return  string  The contents of the configuration.php file
	 */
	public function getFileContents($className = 'JConfig')
	{
		$out = "<?php\nclass $className {\n";
		foreach ($this->configvars as $name => $value)
		{
			if (is_array($value))
			{
				$pieces = array();

				foreach ($value as $key => $data)
				{
					$data = addcslashes($data, '\'\\');
					$pieces[] = "'" . $key . "' => '" . $data . "'";
				}

				$value = "array (\n" . implode(",\n", $pieces) . "\n)";
			}
			else
			{
				// Log and temp paths in Windows systems will be forward-slash encoded
				if ((($name == 'tmp_path') || ($name == 'log_path')))
				{
					$value = $this->TranslateWinPath($value);
				}
				$value = "'" . addcslashes($value, '\'\\') . "'";
			}
			$out .= "\tpublic $" . $name . " = " . $value . ";\n";
		}

		$out .= '}' . "\n";

		return $out;
	}
}