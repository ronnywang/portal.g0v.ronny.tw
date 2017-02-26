<?php

include(__DIR__ . '/../init.inc.php');
$fp = fopen($_SERVER['argv'][1], 'r');
fgetcsv($fp);
$columns = array(
    'id',
    'parent_id',
    'name',
    'ename',
);

GoodId::getDb()->query("BEGIN");
GoodId::getDb()->query("DELETE FROM good_id");

$values = array();
while ($row = fgetcsv($fp)) {
    $value = array(
        $row[0],
        floor($row[0] / 100),
        iconv('UTF-8', 'UTF-8', $row[1]),
        $row[2],
    );
    $values[] = $value;
    if (count($values) > 1000) {
        GoodId::bulkInsert($columns, $values);
        $values = array();
    }
}
if (count($values)) {
    GoodId::bulkInsert($columns, $values);
    $values = array();
}

GoodId::getDb()->query("COMMIT");
