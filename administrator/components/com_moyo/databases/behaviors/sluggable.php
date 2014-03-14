<?php
/**
 * Com
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Socialhub
 * @subpackage  ...
 * @uses        Com_
 */
 
defined('KOOWA') or die('Protected resource');

class ComMoyoDatabaseBehaviorSluggable extends KDatabaseBehaviorSluggable
{
	/**
	 * @param KCommandContext $context
	 */
	protected function _beforeTableInsert(KCommandContext $context)
	{
		$this->_createSlug();
	}

	/**
	 * Force not to run parent _afterTableInsert()
	 *
	 * @param KCommandContext $context
	 */
	protected function _afterTableInsert(KCommandContext $context)
	{
	}
}