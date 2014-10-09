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

    public function init()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
    }

    public function goodidAction()
    {
        list(,/*api*/,/*goodid*/, $goodid) = explode('/', $this->getURI());
        if (!is_numeric($goodid)) {
            return $this->error("not number");
        }

        $goodid_obj = GoodId::find(intval($goodid));

        $ret = new StdClass;
        $ret->error = 0;
        if ($goodid_obj) {
            $ret->good_id = $goodid_obj->id();
            $parents = array();
            foreach ($goodid_obj->getParents() as $parent_obj) {
                $parent = new StdClass;
                $parent->id = $parent_obj->id();
                $parent->name = $parent_obj->name;
                $parent->ename = $parent_obj->ename;
                $parent->api_link = 'http://' . $_SERVER['HTTP_HOST'] . '/api/goodid/' . $parent_obj->id();
                if ($parent->id == $goodid) {
                    $ret->data = $parent;
                } else {
                    $parents[] = $parent;
                }
            }
            $ret->parents = $parents;
        }

        $ret->children = array();
        foreach (GoodId::search(array('parent_id' => intval($goodid))) as $child_obj) {
            $d = new StdClass;
            $d->id = $child_obj->id();
            $d->name = $child_obj->name;
            $d->ename = $child_obj->ename;
            $d->api_link = 'http://' . $_SERVER['HTTP_HOST'] . '/api/goodid/' . $child_obj->id();
            $ret->children[] = $d;
        }

        return $this->json($ret);
    }

    public function searchgoodidcountryAction()
    {
        list(,,,$inout, $goodid, $country) = explode('/', $this->getURI());

        $code = strlen($goodid);
        if ($code == 10) {
            $code = 11;
        }
        if (!in_array($code, array(2,4,6,8,11))) {
            return $this->error("不正確的貨品代碼，貨品代碼必需要 2, 4, 6, 8, 11 位數的整數");
        }
        if (!$good_data = GoodId::find($goodid)) {
            return $this->error("找不到這個商品代號");
        }
        if (!$country) {
            return $this->error("本 API 格式為 /api/searchgoodidcountry/{GoodId}/{CountryName}");
        }
        $country = urldecode($country);
        if (!$country_id = CountryGroup::getCode($country, false)) {
            return $this->error("找不到 {$country} 這個國家");
        }
        $ret = new StdClass;
        $ret->error = 0;
        $ret->goodid = $goodid;
        $ret->country = $country;

        $data = new StdClass;
        $data->id = $good_data->id();
        $data->name = $good_data->name;
        $data->ename = $good_data->ename;
        $data->api_link = 'http://' . $_SERVER['HTTP_HOST'] . '/api/goodid/' . $good_data->id();

        $ret->good_data = $data;

        if (in_array($inout, array('in', 'out', 'rein', 'reout'))) {
            $table = Pix_Table::getTable('Good' . ucfirst($inout) . $code . 'code');
        } else {
            return $this->error("只能是 in, out, rein, reout");
        }
        $values = array();
        foreach ($table->search(array('country_id' => intval($country_id), 'good_id' => intval($goodid)))->order('time ASC') as $row) {
            $value = array();
            $value['Time'] = intval($row->time);
            $value['Weight'] = intval($row->weight_value);
            $value['WeightUnit'] = UnitGroup::getName($row->weight_unit_id);
            $value['Value'] = intval($row->value);

            if ($code == 11) {
                $value['Number'] = intval($row->num_value);
                $value['NumberUnit'] = UnitGroup::getName($row->num_unit_id);
            }

            $values[] = $value;
        }
        $ret->data = $values;

        return $this->json($ret);
    }

    public function searchgoodidtimeAction()
    {
        list(,/*api*/,/*searchgoodidtime*/,$inout, $goodid, $time) = explode('/', $this->getURI());

        $code = strlen($goodid);
        if ($code == 10) {
            $code = 11;
        }
        if (!in_array($code, array(2,4,6,8,11))) {
            return $this->error("不正確的貨品代碼，貨品代碼必需要 2, 4, 6, 8, 11 位數的整數");
        }
        if (!$time) {
            return $this->error("本 API 格式為 /api/searchgoodidtime/{GoodId}/{YearMonth}");
        }
        if (!$good_data = GoodId::find($goodid)) {
            return $this->error("找不到這個商品代號");
        }

        $ret = new StdClass;
        $ret->error = 0;
        $ret->goodid = $goodid;
        $ret->time = $time;

        $data = new StdClass;
        $data->id = $good_data->id();
        $data->name = $good_data->name;
        $data->ename = $good_data->ename;
        $data->api_link = 'http://' . $_SERVER['HTTP_HOST'] . '/api/goodid/' . $good_data->id();

        $ret->good_data = $data;

        if (in_array($inout, array('in', 'out', 'rein', 'reout'))) {
            $table = Pix_Table::getTable('Good' . ucfirst($inout) . $code . 'code');
        } else {
            return $this->error("只能是 in, out, rein, reout");
        }
        $values = array();
        foreach ($table->search(array('good_id' => intval($goodid), 'time' => intval($time))) as $row) {
            $value = array();
            $value['Country'] = CountryGroup::getName($row->country_id);
            $value['Weight'] = intval($row->weight_value);
            $value['WeightUnit'] = UnitGroup::getName($row->weight_unit_id);
            $value['Value'] = intval($row->value);

            if ($code == 11) {
                $value['Number'] = intval($row->num_value);
                $value['NumberUnit'] = UnitGroup::getName($row->num_unit_id);
            }

            $values[] = $value;
        }

        $ret->data = $values;
        return $this->json($ret);
    }

    public function searchgoodidAction()
    {
        list(,/*api*/,/*searchgoodid*/,$inout, $goodid) = explode('/', $this->getURI());

        $code = strlen($goodid);
        if ($code == 10) {
            $code = 11;
        }
        if (!in_array($code, array(2,4,6,8,11))) {
            return $this->error("不正確的貨品代碼，貨品代碼必需要 2, 4, 6, 8, 11 位數的整數");
        }
        if (!$good_data = GoodId::find($goodid)) {
            return $this->error("找不到這個商品代號");
        }

        $ret = new StdClass;
        $ret->error = 0;
        $ret->goodid = $goodid;

        $data = new StdClass;
        $data->id = $good_data->id();
        $data->name = $good_data->name;
        $data->ename = $good_data->ename;
        $data->api_link = 'http://' . $_SERVER['HTTP_HOST'] . '/api/goodid/' . $good_data->id();

        $ret->good_data = $data;

        if (in_array($inout, array('in', 'out', 'rein', 'reout'))) {
            $table = Pix_Table::getTable('Good' . ucfirst($inout) . $code . 'code');
        } else {
            return $this->error("只能是 in, out, rein, reout");
        }
        $time_values = array();
        $sum_values = array();
        $countries = array();
        $units = array();
        foreach ($table->search(array('good_id' => intval($goodid)))->order('time ASC') as $row) {
            if (!array_key_exists($row->time, $time_values)) {
                $time_values[$row->time] = new StdClass;
                $time_values[$row->time]->time = intval($row->time);
                $time_values[$row->time]->records = array();
            }
            $sum_key = $row->country_id . '-' . $row->weight_unit_id;
            if (!array_key_exists($sum_key, $sum_values)) {
                $sum_values[$sum_key] = new StdClass;
                $sum_values[$sum_key]->Country = CountryGroup::getName($row->country_id);
                $sum_values[$sum_key]->WeightUnit = UnitGroup::getName($row->weight_unit_id);
            }
            $value = array();
            $value['Country'] = CountryGroup::getName($row->country_id);
            $value['Weight'] = intval($row->weight_value);
            $value['WeightUnit'] = UnitGroup::getName($row->weight_unit_id);
            $value['Value'] = intval($row->value);
            $countries[$value['Country']] = true;
            $units[$value['WeightUnit']] = true;
            $sum_values[$sum_key]->Weight += $value['Weight'];
            $sum_values[$sum_key]->Value += $value['Value'];

            if ($code == 11) {
                $value['Number'] = intval($row->num_value);
                $sum_values[$sum_key]->Number += $value['Number'];
                $value['NumberUnit'] = UnitGroup::getName($row->num_unit_id);
            }

            $time_values[$row->time]->records[] = $value;
        }

        $ret->data = array_values($time_values);
        $ret->sum_values = array_values($sum_values);
        $ret->countries = array_keys($countries);
        $ret->units = array_keys($units);
        return $this->json($ret);

    }
}
