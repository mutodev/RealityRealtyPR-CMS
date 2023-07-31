<?php

class Doctrine_Connection_MasterSlave extends Doctrine_Connection implements Doctrine_EventListener_Interface
{
    protected $master = null;
    protected $slave  = null;

    static protected $lastInsertTime = 0;
    static protected $replicaLagWait = 5000; //5 seconds in milliseconds

    public function __construct(PDO $master, PDO $slave)
    {
        $this->master = $master;
        $this->slave  = $slave;
    }

    public function preQuery(Doctrine_Event $event)
    {
        $this->changeConnection($event->getInvoker(), $this->getQueryType($event->getQuery()));
    }

    public function prePrepare(Doctrine_Event $event)
    {
        $this->changeConnection($event->getInvoker(), $this->getQueryType($event->getQuery()));
    }

    public function preExec(Doctrine_Event $event)
    {
        $this->changeConnection($event->getInvoker(), 'WRITE');
    }

    // protected
    protected function changeConnection($conn, $type = 'READ') {

        //GET CONNECTION TYPE
        if ($type == 'READ') {

            $connType = 'SLAVE';

            //Wait for previous write to propagate to read replicas
            if (round(microtime(true) * 1000) <= (self::$lastInsertTime + self::$replicaLagWait)) {
                $connType = 'MASTER';
            }
        }
        else {

            self::$lastInsertTime = round(microtime(true) * 1000);

            $connType = 'MASTER';
        }

        //SET CURRENT CONNECTION
        $this->forceDbh($conn, $connType);
    }

    protected function getQueryType($query) {

        $query = trim($query);

        if (preg_match('/^SELECT.*FOR UPDATE$/i', $query)) {
            return 'WRITE';
        }

        if (preg_match('/^SELECT/i', $query)) {
            return 'READ';
        }

        return 'WRITE';
    }

    protected function forceDbh($conn, $type)
    {
        $type = strtolower($type);

        if ($this->$type !== $conn->dbh) {
            $conn->dbh = $this->$type;
        }
    }

    public function preTransactionBegin(Doctrine_Event $event)
    {
        $this->changeConnection($event->getInvoker()->getConnection(), 'WRITE');
    }
    public function preTransactionCommit(Doctrine_Event $event)
    {
        $this->changeConnection($event->getInvoker()->getConnection(), 'WRITE');
    }
    public function preTransactionRollback(Doctrine_Event $event)
    {
        $this->changeConnection($event->getInvoker()->getConnection(), 'WRITE');
    }

    // the remaining methods required by Doctrine_EventListener_Interface
    public function postTransactionCommit(Doctrine_Event $event) { }
    public function postTransactionRollback(Doctrine_Event $event) { }
    public function postTransactionBegin(Doctrine_Event $event) { }
    public function postConnect(Doctrine_Event $event) { }
    public function preConnect(Doctrine_Event $event) { }
    public function postPrepare(Doctrine_Event $event) { }
    public function postExec(Doctrine_Event $event) { }
    public function postQuery(Doctrine_Event $event) { }
    public function preStmtExecute(Doctrine_Event $event) { }
    public function postStmtExecute(Doctrine_Event $event) { }
    public function preError(Doctrine_Event $event) { }
    public function postError(Doctrine_Event $event) { }
    public function preFetch(Doctrine_Event $event) { }
    public function postFetch(Doctrine_Event $event) { }
    public function preFetchAll(Doctrine_Event $event) { }
    public function postFetchAll(Doctrine_Event $event) { }
}