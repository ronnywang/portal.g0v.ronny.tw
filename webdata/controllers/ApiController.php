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
            $value['Weight'] = intval($row->weight_value);
            $value['WeightUnit'] = UnitGroup::getName($row->weight_unit_id);
            $value['Value'] = intval($row->value);

            if ($code == 11) {
                $value['Number'] = intval($row->num_value);
                $value['NumberUnit'] = UnitGroup::getName($row->num_unit_id);
            }

            $values[] = $value;
        }

        return $this->jsonp($values);
    }
}
