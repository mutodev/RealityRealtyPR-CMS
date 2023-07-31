<?php

class Doctrine_Template_Listener_SemiInheritance extends Doctrine_Record_Listener
{

    public function __construct(array $options = array())
    {
        $this->_options = $options;
    }

    public function preInsert(Doctrine_Event $event)
    {
        $invoker = $event->getInvoker();
        $model   = $this->_options['parent'];
        $parent  = new $model;

        if ($invoker->hasReference($model)) {
            $parent->sync( $invoker[$model]->toArray() );
        }

        //Sync parent with invoker values
        $parent->sync( array_filter($invoker->toArray(), 'is_scalar'));

        if (Doctrine::getTable($model)->hasColumn('type')) {
            $parent->type = $invoker->getTable()->name;
        }

        //Save parent to get ID
        if ($parent->getTable()->getOption('treeImpl') == 'NestedSet') {

            //Get tree root
            $q = Doctrine_Query::create();
            $q->from($model);
            $q->where('level = 0 AND lft = 1');
            $Root = $q->fetchOne();

            $parent->getNode()->insertAsLastChildOf($Root);
        }
        else {
            $parent->save();
        }

        $invoker['id']   = $parent->id;
        $invoker[$model] = $parent;
    }

    public function preUpdate(Doctrine_Event $event) {
        $invoker = $event->getInvoker();
        $model   = $this->_options['parent'];

        $parent = $invoker[$model];
        $parent->sync( array_filter($invoker->toArray(), 'is_scalar'));
    }
}
