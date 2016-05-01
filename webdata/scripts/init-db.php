<?php

include(__DIR__ . '/../init.inc.php');
$models = 'CountryGroup GoodId GoodIn11code GoodIn2code GoodIn4code GoodIn6code GoodIn8code GoodOut11code GoodOut2code GoodOut4code GoodOut6code GoodOut8code GoodRein11code GoodRein2code GoodRein4code GoodRein6code GoodRein8code GoodReout11code GoodReout2code GoodReout4code GoodReout6code GoodReout8code UnitGroup';
foreach (explode(' ', $models) as $table) {
    Pix_Table::getTable($table)->createTable();
}
