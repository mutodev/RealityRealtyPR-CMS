<?php

$sent = false;

if (isset($_POST['email'])) {
    $Lead = new Lead();
    $Lead->first_name = $_POST['name'];
    $Lead->email = $_POST['email'];
    $Lead->type = 'JOB';
    $Lead->save();
    $sent = true;
}

$q = new Doctrine_Query();
$q->from('Account a');
$q->leftJoin('a.Branch b');
$q->leftJoin('a.Properties p');
//$q->andWhere('a.show_in_list = 1');
//$q->andWhere('a.s3_photo IS NOT NULL');
//$q->andWhere('a.type = ?', 'Manager');
$q->andWhereIn('a.id', [10010, 3776, 30168, 32696, 32725, 32812]);
$q->orderBy('a.created_at');
$Brokers = $q->execute();

$newOrder = [];
$newOrder[] = $Brokers[0];
$newOrder[] = $Brokers[4];
$newOrder[] = $Brokers[1];
$newOrder[] = $Brokers[3];
$newOrder[] = $Brokers[5];
$newOrder[] = $Brokers[2];

$Brokers = $newOrder;

Action::set(compact('Brokers', 'sent'));
