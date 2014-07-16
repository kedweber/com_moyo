<?php
 
defined('KOOWA') or die('Protected resource');

class ComMoyoDatabaseTableDefault extends KDatabaseTableDefault
{
	/**
	 * @param KConfig $config
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append($this->getConfig());

		parent::_initialize($config);
	}

	/**
	 * @return mixed
	 */
	public function getConfig()
	{
		$identifier = clone $this->getIdentifier();

		$config = json_decode(file_get_contents(JPATH_BASE . '/config' . '/' . 'com_' . $identifier->package . '/' . implode('/', $identifier->path) . '/' . $identifier->name . '.json'), true);

		return $config;
	}

	/**
	 * Register one or more behaviors to the table
	 *
	 * @param   array   Array of one or more behaviors to add.
	 * @return  KDatabaseTableAbstract
	 */
	public function addBehavior($behaviors)
	{
		$behaviors = (array) KConfig::unbox($behaviors);

		foreach($behaviors as $key => $behavior)
		{
			if (!($behavior instanceof KDatabaseBehaviorInterface)) {

				if(is_array($behavior)) {
					$behavior   = $this->getBehavior($key, $behavior);
				} else {
					$behavior   = $this->getBehavior($behavior);
				}
			}

			//Add the behavior
			$this->getSchema()->behaviors[$behavior->getIdentifier()->name] = $behavior;
			$this->getCommandChain()->enqueue($behavior);
		}

		return $this;
	}
}