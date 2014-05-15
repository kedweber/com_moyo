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

class ComMoyoDatabaseBehaviorCreatable extends KDatabaseBehaviorCreatable
{
    protected function _beforeTableSelect(KCommandContext $context)
    {
        $query = $context->query;

        if($query) {
            $query->select('created.name AS created_by_name');
            $query->join('left', '#__users AS created', 'tbl.created_by = created.id');
        }
    }
}