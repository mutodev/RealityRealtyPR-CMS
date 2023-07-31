<?php

class Doctrine_Template_SharedProperties extends Doctrine_Template
{
    protected $_options = array();

    public function __construct(array $options = array())
    {
        $this->_options = $options;
    }

    public function setTableDefinition()
    {
        foreach ($this->_options['relations'] as $relation) {
            $columnName = Doctrine_Inflector::tableize($relation) . '_id';
            if (!$this->_table->hasColumn($columnName)) {
                $this->hasColumn($columnName, 'integer');
            }
        }

        $this->_table->unshiftFilter(new Doctrine_Record_Filter_SharedProperties($this->_options));
        $this->addListener(new Doctrine_Template_Listener_SharedProperties($this->_options));
    }

    public function setUp()
    {
        foreach ($this->_options['relations'] as $model) {
            $table = $this->_table;
            $local = Doctrine_Inflector::tableize($model) . '_id';
            $foreign = Doctrine::getTable($model)->getIdentifier();
            $this->_makeRelation($table, $model, $local, $foreign, true);
        }

        foreach ($this->_options['relations'] as $model) {
            $table = Doctrine::getTable($model);
            $local = $table->getIdentifier();
            $foreign = Doctrine_Inflector::tableize($model) . '_id';
            $this->_makeRelation($table, $this->_table->getOption('name'), $table->getIdentifier(), $foreign);
        }
    }

    protected function _makeRelation(Doctrine_Table $table, $model, $local, $foreign, $cascade = false)
    {
        if (!$table->hasRelation($model)) {

            $options = array('local' => $local, 'foreign' => $foreign);

            if ($cascade) {
                $options['onDelete'] = 'CASCADE';
            }

            $table->bind(array($model, $options), Doctrine_Relation::ONE);
        }
    }

    public function __call($method, $arguments)
    {
        $invoker = $this->getInvoker();
        foreach ($this->_options['relations'] as $model) {
            try {
                return call_user_func_array(array($invoker->$model, $method), $arguments);
            } catch (Exception $e) {
                continue;
            }
        }
    }
}

class Doctrine_Record_Filter_SharedProperties extends Doctrine_Record_Filter
{
    protected $_options = array();

    public function __construct($options)
    {
        $this->_options = $options;
    }

    public function init()
    {
        foreach ($this->_options['relations'] as $model) {
            $this->_table->getRelation($model);
        }
    }

    public function filterSet(Doctrine_Record $record, $name, $value)
    {
        foreach ($this->_options['relations'] as $model) {
            try {
                $record->$model->$name = $value;
                return $record;
            } catch (Exception $e) {}
        }
        throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
    }

    public function filterGet(Doctrine_Record $record, $name)
    {
        foreach ($this->_options['relations'] as $model) {
            try {
                return $record->$model->$name;
            } catch (Exception $e) {}
        }
        throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
    }
}
