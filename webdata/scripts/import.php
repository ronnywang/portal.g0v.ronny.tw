<?php

include(__DIR__ . '/../init.inc.php');

$columns = array(
    'good_id',
    'time',
    'country_id',
    'weight_value',
    'weight_unit_id',
    'value',
);

$columns_11 = array(
    'good_id',
    'time',
    'country_id',
    'num_value',
    'num_unit_id',
    'weight_value',
    'weight_unit_id',
    'value',
);

foreach (array(2,4,6,8,11) as $code) {
    $insert_datas = array();
    foreach (glob($_SERVER['argv'][1] . '/*-' . $code . 'code.csv') as $file) {
        if (!preg_match('#/([0-9]*)-([0-9]*)-[0-9]*code.csv$#', $file, $matches)) {
            throw new Exception('檔名不對');
        }
        $fp = fopen($file, 'r');
        $rows = fgetcsv($fp);

        while ($rows = fgetcsv($fp)) {
            $insert_data = array();
            $insert_data[] = substr($rows[0], 0, 10); // 貨品代號, 最多取十碼
            $insert_data[] = $matches[1] * 100 + $matches[2];
            $insert_data[] = CountryGroup::getCode($rows[1]); // 國家代號
            if ($code == 11) {
                $insert_data[] = $rows[2]; // 數量
                $insert_data[] = UnitGroup::getCode($rows[3]); // 數量單位
                $insert_data[] = $rows[4]; // 重量
                $insert_data[] = UnitGroup::getCode($rows[5]); // 重量單位
                $insert_data[] = $rows[6]; // 價值
            } else {
                $insert_data[] = $rows[2]; // 重量
                $insert_data[] = UnitGroup::getCode($rows[3]); // 重量單位
                $insert_data[] = $rows[4]; // 價值
            }

            $insert_datas[] = $insert_data;

            if (count($insert_datas) > 50000) {
                $table = Pix_Table::getTable('GoodIn' . $code . 'code');
                $table->bulkInsert($code == 11 ? $columns_11 : $columns, $insert_datas);
                $insert_datas = array();
            }
        }
    }
    if (count($insert_datas)) {
        $table = Pix_Table::getTable('GoodIn' . $code . 'code');
        $table->bulkInsert($code == 11 ? $columns_11 : $columns, $insert_datas);
    }
}
