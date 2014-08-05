<?php

class ComMoyoControllerBehaviorCopyable extends KControllerBehaviorAbstract
{
    /**
     * @param KCommandContext $context
     */
    public function _actionCopy(KCommandContext $context)
    {
        /**
         * Hmm, this doesn't work.
         * We need to keep a place where the original data is stored, also the translatable behavior needs to return which one is original.
         * This is needed because the original one has to be saved and all the others need an update.
         *
         * So first we will get all the articles for every language and save them in one variable.
         */
        foreach(KRequest::get('get.id', 'raw') as $articleId)
        {
            $this->count = 1;
            $articles = array();
            $languages = JLanguageHelper::getLanguages();

            foreach($languages as $language) {
                JFactory::getLanguage()->setLanguage($language->lang_code);
                $article = $this->getService('com://admin/articles.model.articles')->id($articleId)->getItem();

                if($article->original)
                {
                    $this->original_language = $language->lang_code;
                    $original = $article;
                }
                else
                {
                    $articles[$language->lang_code] = $article;
                }
            }

            JFactory::getLanguage()->setLanguage($this->original_language);
            $id = $this->_copyOriginal($original);
            $this->_updateLanguages($id, $articles);
        }
    }

    public function _copyOriginal($original)
    {
        while(true) {
            $row = $this->getService('com://admin/articles.database.row.article');
            
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
                unset($data['taxonomy_taxonomy_id']);

                $row->setData($data);
                $row->title = $title;
                $row->slug = $slug;
                $row->enabled = 0;
                $row->translated = 0;

                // Bind all the taxonomies.
                if($original->isRelationable())
                {
                    foreach($original->getRelations()->ancestors as $key => $ancestor) {
                        $ancestor_part = $original->getAncestors(array(
                            'filter' => array(
                                'type' => KInflector::singularize($key)
                            )
                        ))->getColumn('taxonomy_taxonomy_id');

                        if(count($ancestor_part) > 0) {
                            $row->setData(array(
                                $key => KInflector::isSingular($key) ? end($ancestor_part) : array_keys($ancestor_part)
                            ));
                        }
                    }

                    foreach($original->getRelations()->descendants as $key => $ancestor) {
                        $ancestor_part = $original->getDescendants(array(
                            'filter' => array(
                                'type' => KInflector::singularize($key)
                            )
                        ))->getColumn('taxonomy_taxonomy_id');

                        if(count($ancestor_part) > 0) {
                            $row->setData(array(
                                $key => KInflector::isSingular($key) ? end($ancestor_part) : array_keys($ancestor_part)
                            ));
                        }
                    }
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

    public function _updateLanguages($id, $articles)
    {
        foreach($articles as $language => $article)
        {
            // Original Data
            $data = $article->getData();
            unset($data['id']);
            unset($data['taxonomy_taxonomy_id']);

            $this->_checkName($article, false);

            $title = $article->title . ' (' . $this->count . ')';
            $slug = $article->slug . '-' . $this->count;

            JFactory::getLanguage()->setLanguage($language);
            $row = $this->getService('com://admin/articles.model.articles')->id($id)->getItem();

            $row->setData($data);
            $row->title = $title;
            $row->enabled = 0;
            $row->slug = $slug;
            $row->translated = 0;

            $row->save();
        }
    }
    
    protected function _checkName(&$item, $setCount = true)
    {
        preg_match('/.*(\((.*)\)).*/', $item->title, $matches);
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