<?php

class Doctrine_Template_Listener_QueryLint extends Doctrine_Record_Listener
{
    public function preDqlSelect( Doctrine_Event $event )
    {
        $invoker = $event->getInvoker();
        $query = $event->getQuery();

        $this->verifyJoinAlias($query);
        $this->verifyQueryClass($query);
    }

    protected function verifyJoinAlias($query) {

        $parts = $query->getDqlPart('from');

        foreach ($parts as $part) {

            //Check that is not chaining more that one relation at a time
            if (preg_match('/(\w+\.\w+\.[^ ]+)/i', $part, $matches)) {
                 throw new Exception("Each Doctrine Query table reference need to have it own statement, please separate {$matches[0]} in \"$part\"");
            }

            //Check that all the relations has alias
            if (preg_match('/^.*JOIN\s+(\S+)(?:\s+)?((?:(?!WITH|ON)\S+))?.*?$/i', $part, $matches)) {
                if (!$matches[2]) {
                    throw new Exception("Each Doctrine Query join need to have an alias, please add one to {$matches[1]} in \"$part\"");
                }
            }
        }
    }

    protected function verifyQueryClass($query) {

        $className = Doctrine_Manager::getInstance()->getAttribute(Doctrine::ATTR_QUERY_CLASS);

        if (!$query instanceof $className) {
            throw new Exception("The query object need to be a instance of $className");
        }
    }
}
