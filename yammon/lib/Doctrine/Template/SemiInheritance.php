<?php

class Doctrine_Template_SemiInheritance extends Doctrine_Template
{
    protected $_options = array();

    public function __construct($options = array())
    {
        $this->_options = (array) $options;
    }

    public function setTableDefinition()
    {
        $this->_table->unshiftFilter(new Doctrine_Record_Filter_SemiInheritance($this->_options));
        $this->addListener(new Doctrine_Template_Listener_SemiInheritance($this->_options));
    }

    public function setUp()
    {
        $model = $this->_options['parent'];

        $table = $this->_table;
        $local = 'id';
        $foreign = Doctrine::getTable($model)->getIdentifier();
        $this->_makeRelation($table, $model, $local, $foreign, true);

        $table = Doctrine::getTable($model);
        $local = $table->getIdentifier();
        $foreign = 'id';
        $this->_makeRelation($table, $this->_table->getOption('name'), $local, $foreign);
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
        $model = $this->_options['parent'];

        try {
            return call_user_func_array(array($invoker->$model, $method), $arguments);
        } catch (Exception $e) { }
    }
}

class Doctrine_Record_Filter_SemiInheritance extends Doctrine_Record_Filter
{
    protected $_options = array();

    public function __construct($options)
    {
        $this->_options = $options;
    }

    public function init()
    {
        $model = $this->_options['parent'];
        $this->_table->getRelation($model);
    }

    public function filterSet(Doctrine_Record $record, $name, $value)
    {
        $model = $this->_options['parent'];

        try {
            $record->$model->$name = $value;
            return $record;
        } catch (Exception $e) {}

        throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
    }

    public function filterGet(Doctrine_Record $record, $name)
    {
        $model = $this->_options['parent'];
        try {
            return $record->$model->$name;
        } catch (Exception $e) {}

        throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
    }
}
