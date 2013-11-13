<?php

class ApiController extends Pix_Controller
{
    protected function error($message)
    {
        return $this->jsonp(array(
            'error' => true,
            'message' => $message,
        ), strval($_GET['callback']));
    }

    public function searchgoodidcountryAction()
    {
        list(,,,$goodid, $country) = explode('/', $this->getURI());

        $code = strlen($goodid);
        if (!in_array($code, array(2,4,6,8,11))) {
            return $this->error("不正確的貨品代碼，貨品代碼必需要 2, 4, 6, 8, 11 位數的整數");
        }
        if (!$country) {
            return $this->error("本 API 格式為 /api/searchgoodidcountry/{GoodId}/{CountryName}");
        }
        $country = urldecode($country);
        if (!$country_id = CountryGroup::getCode($country, false)) {
            return $this->error("找不到 {$country} 這個國家");
        }
        $table = Pix_Table::getTable('GoodIn' . $code . 'code');
        $values = array();
        foreach ($table->search(array('country_id' => intval($country_id), 'good_id' => intval($goodid)))->order('time ASC') as $row) {
            $value = array();
            $value['Time'] = $row->time;
            $value['Weight'] = $row->weight_value;
            $value['WeightUnit'] = UnitGroup::getName($row->weight_unit_id);
            $value['Value'] = $row->value;

            if ($code == 11) {
                $value['Number'] = $row->num_value;;
                $value['NumberUnit'] = UnitGroup::getName($row->num_unit_id);
            }

            $values[] = $value;
        }

        return $this->jsonp($values, strval($_GET['callback']));
    }

    public function searchgoodidtimeAction()
    {
        list(,,,$goodid, $time) = explode('/', $this->getURI());

        $code = strlen($goodid);
        if (!in_array($code, array(2,4,6,8,11))) {
            return $this->error("不正確的貨品代碼，貨品代碼必需要 2, 4, 6, 8, 11 位數的整數");
        }
        if (!$time) {
            return $this->error("本 API 格式為 /api/searchgoodidtime/{GoodId}/{YearMonth}");
        }
        $table = Pix_Table::getTable('GoodIn' . $code . 'code');
        $values = array();
        foreach ($table->search(array('good_id' => intval($goodid), 'time' => intval($time))) as $row) {
            $value = array();
            $value['Country'] = CountryGroup::getName($row->country_id);
            $value['Weight'] = $row->weight_value;
            $value['WeightUnit'] = UnitGroup::getName($row->weight_unit_id);
            $value['Value'] = $row->value;

            if ($code == 11) {
                $value['Number'] = $row->num_value;;
                $value['NumberUnit'] = UnitGroup::getName($row->num_unit_id);
            }

            $values[] = $value;
        }

        return $this->jsonp($values);
    }
}
