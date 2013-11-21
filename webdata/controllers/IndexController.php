<?php

class IndexController extends Pix_Controller
{
    public function indexAction()
    {
    }

    public function goodidAction()
    {
        list(, /*index*/, /*goodid*/, $goodid, $country, $time) = explode('/', $this->getURI());
        if (!$goodid_obj = GoodId::find(intval($goodid))) {
            return $this->redirect('/');
        }

        if (intval($time) == 0) {
            $this->view->time = GoodId::getLatestTime();
        } else {
            $this->view->time = $time;
        }
        $country = urldecode($country);
        if ($country == '') {
            $this->view->country = '合計';
        } elseif (!CountryGroup::getCode($country, false)) {
            return $this->redirect('/');
        } else {
            $this->view->country = $country;
        }
        $this->view->goodid = $goodid_obj;
    }
}
