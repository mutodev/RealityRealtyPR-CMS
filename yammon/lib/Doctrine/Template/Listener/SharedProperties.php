<?php

class Doctrine_Template_Listener_SharedProperties extends Doctrine_Record_Listener
{

    public function __construct(array $options = array())
    {
        $this->_options = $options;
    }
/*
    public function preDqlSelect(Doctrine_Event $event)
    {
        $invoker = $event->getInvoker();
        $params  = $event->getParams();
        $table   = $params['component']['table'];
        $alias   = $params['alias'];
        $query   = $event->getQuery();

        //Always join the share properties tables if not exist in the JOINS sections
        foreach ($this->_options['relations'] as $relation) {

            //INNER JOIN already added
            if ($query->hasRelation($relation, $alias)){
                continue;
            }

            //Relation already joined in the other direction
            if (@$params['component']['relation']['localTable']->name == $relation) {
                continue;
            }

            //Join relation
            $query->innerJoin("$alias.$relation lgg".rand());
        }
    }
*/
    public function preInsert(Doctrine_Event $event)
    {
        $invoker = $event->getInvoker();

        foreach ($this->_options['relations'] as $model) {

            if (!$invoker->hasReference($model)) {
                $invoker[$model] = new $model;
            }

            if (Doctrine::getTable($model)->hasColumn('type')) {
                $invoker[$model]->type = $invoker->getTable()->name;
            }

            $invoker[$model]->save();
        }
    }
}
