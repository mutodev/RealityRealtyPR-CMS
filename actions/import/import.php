<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

$MODEL = get('model');

include('features.php');
include('truko.php');

$fields = array();
$handle = @fopen("$MODEL.csv", "r");
if ($handle) {
    while (($row = fgetcsv($handle, 4096)) !== false) {
        if (empty($fields)) {
            $fields = $row;
            continue;
        }

        if ($MODEL == 'PropertyConditionRelation' || $MODEL == 'PropertyConditionRelation1') {
            $row = array_combine($fields, $row);

            foreach($idMapping as $field => $id){
                if ($row[$field]) {
                    try{
                        $m = new PropertyConditionRelation();
                        $m->property_id  = $row['id_propiedad'];
                        $m->condition_id = $id;
                        $m->save();
                    }catch(Exception $e){}
                }
            }
        }elseif ($MODEL == 'PropertyPhoto') {


            for($i = 1; $i < 26;$i++) {
                $img = $row[$i];

                if ($img) {
                    $photo = array();
                    $photo['original'] = $img;
                    $photo['large'] = $img;
                    $photo['medium'] = $img;
                    $photo['small'] = $img;
                    $photo['property_id'] = $row[0];

                    try{
                        $Object = new $MODEL;
                        $Object->syncAndSave($photo);
                    }catch(Exception $e){}
                }
            }
        }
        else {
            $values = array();
            foreach ($row as $k=>$value) {
                if (!$value || $value == 'NULL') {
                    continue;
                }

                if ($MODEL == 'Property' && $fields[$k] == 'end_at') {

                    if (strlen($value) == 8) {
                        $value = substr($value, 0, 4).'-'.substr($value, 4, 2).'-'.substr($value, 6, 2).' 00:00:00';
                    }
                    else {
                        $value = null;
                    }
                }

                if ($MODEL == 'Property' && $fields[$k] == 'maintenance') {
                    if (!is_null($value)) {
                        $value = $value == 'Y' ? 1 : 0;
                    }
                }

                $values[$fields[$k]] = utf8_encode($value);
            }

            try{
                if ($MODEL == 'Contract') {
                    $row = array_combine($fields, $row);
                    $Property = Doctrine::getTable('Property')->find($row['property_id']);

                    if ($Property) {
                        if ($Property->contract_id) {
                            $Contract = $Property->Contract;
                            $Contract->syncAndSave(array_merge($Contract->toArray(), $values));
                        } else {
                            $Object = new $MODEL;
                            $Object->syncAndSave($values);

                            $Property->contract_id = $Object->id;
                            $Property->save();
                        }
                    }
                }
                else {
                    $Object = new $MODEL;
                    if ($MODEL == 'Account') {
                        $row = array_combine($fields, $row);
                        $RC4 = new truko;

                        $password = $RC4->jhonson($row['password'],"de");

                        pr($row['id'], $row['email'], $password);

                        $Object = Doctrine::getTable('Account')->find($row['id']);
                        $Object->setPassword($password);
                        $Object->save();

//                        $Role = Doctrine::getTable('Role')->find($row['administrator'] ? 'company.manager' : 'company.agent');
//                        Auth::addAccountToRole( $Object , $Role );

                        pr($row);
                    } else {
                        $Object->syncAndSave($values);
                    }
                }

            }catch(Exception $e){
                pr($e->getMessage());
                pr($values);
            }
        }
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

prd('The End');
