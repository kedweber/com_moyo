<?php

class ComMoyoControllerBehaviorCopyable extends KControllerBehaviorAbstract
{
    /**
     * @param KCommandContext $context
     */
    public function _actionCopy(KCommandContext $context)
    {
        foreach(KRequest::get('get.id', 'raw') as $item_id)
        {
            $this->count = 1;
            $items = array();
            $languages = JLanguageHelper::getLanguages();

            $model_identifier = clone $context->caller->getIdentifier();
            $model_identifier->path = array('model');
            $model_identifier->name = KInflector::pluralize($model_identifier->name);

            $isTranslatable = KService::get($model_identifier)->getTable()->hasBehavior('translatable');

            if($isTranslatable) {
                foreach($languages as $language) {
                    JFactory::getLanguage()->setLanguage($language->lang_code);
                    $item = $this->getService($model_identifier)->id($item_id)->getItem();

                    if($item->original)
                    {
                        $this->original_language = $language->lang_code;
                        $original = $item;
                    }
                    else
                    {
                        $items[$language->lang_code] = $item;
                    }
                }
            }

            JFactory::getLanguage()->setLanguage($this->original_language);
            $id = $this->_copyOriginal($original);

            if($isTranslatable) {
                $this->_updateLanguages($id, $items);
            }
        }
    }

    public function _copyOriginal($original)
    {
        while(true) {
            $row_identifier = clone $original->getIdentifier();
            $row_identifier->path = array('database', 'row');

            $row = $this->getService($row_identifier);

            // We will first check the name of the article/ item.
            $this->_checkName($original);

            $title = $original->title . ' (' . $this->count . ')';
            $slug = $original->slug . '-' . $this->count;

            $row->title = $title;
            $row->slug = $slug;

            if(!$row->load())
            {
                $data = $original->getData();
                unset($data['id']);
                if(isset($data['featured'])) {
                    unset($data['featured']);
                }

                $row->setData($data);
                $row->title = $title;
                $row->slug = $slug;
                $row->enabled = 0;
                $row->translated = 0;

                // Bind all the taxonomies.
                if($original->isRelationable()) {
                    $row->setData(json_decode($original->ancestors));
                    $row->setData(json_decode($original->descendants));
                }

                $row->save();
                return $row->id;
            }
            else
            {
                $this->count++;
            }
        }
    }

    public function _updateLanguages($id, $items)
    {
        foreach($items as $language => $item)
        {
            $model_identifier = clone $item->getIdentifier();
            $model_identifier->path = array('model');
            $model_identifier->name = KInflector::pluralize($model_identifier->name);

            // Original Data
            $data = $item->getData();
            unset($data['id']);
            if(isset($data['featured'])) {
                unset($data['featured']);
            }

            $this->_checkName($item, false);

            $title = $item->title . ' (' . $this->count . ')';
            $slug = $item->slug . '-' . $this->count;

            JFactory::getLanguage()->setLanguage($language);
            $row = $this->getService($model_identifier)->id($id)->getItem();

            $row->setData($data);
            $row->title = $title;
            $row->enabled = 0;
            $row->slug = $slug;
            $row->translated = 0;

            if($row->isRelationable()) {
                $row->setData(json_decode($row->ancestors));
                $row->setData(json_decode($row->descendants));
            }

            $row->save();
        }
    }

    protected function _checkName(&$item, $setCount = true)
    {
        preg_match('/.*(\((.*)\))/', $item->title, $matches);

        if($matches[2]) {
            if($setCount) {
                $this->count = $matches[2];
                $this->count++;
            }
            $item->title = trim(str_replace($matches[1], '', $item->title));

            if($item->slug) {
                $item->slug = str_replace('-' . $matches[2], '', $item->slug);
            }
        }
    }
}