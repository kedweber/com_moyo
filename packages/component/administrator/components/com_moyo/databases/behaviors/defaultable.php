<?php
/**
 * ComCloudinary
 *
 * @author      Jasper van Rijbroek <jasper@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Cloudinary
 */

defined('KOOWA') or die('Restricted Access');

class ComMoyoDatabaseBehaviorDefaultable extends KDatabaseBehaviorAbstract
{
	protected function _beforeTableInsert(KCommandContext $context)
	{
		//$this->_makeDefault($context);
	}

	protected function _beforeTableUpdate(KCommandContext $context)
	{
		$this->_makeDefault($context);
	}

	public function _buildQueryWhere(KDatabaseQuery $query)
	{

	}

	protected function _makeDefault(KCommandContext $context)
	{
		$modified = $context->data->getModified();

		if(in_array('default', $modified)) {
			$table = $this->getTable();
			$db    = $table->getDatabase();
			$query = $db->getQuery();

			$this->_buildQueryWhere($query);

			$update =  'UPDATE `'.$db->getTableNeedle().$table->getBase().'` ';
			$update .= 'SET `default` = 0 ';
			$update .= (string) $query;
			$db->execute($update);
		}
	}
}