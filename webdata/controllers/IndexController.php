<?php

class IndexController extends Pix_Controller
{
    public function indexAction()
    {
    }

    public function goodidAction()
    {
        list(, /*index*/, /*goodid*/, $goodid) = explode('/', $this->getURI());
        if (!$goodid_obj = GoodId::find(intval($goodid))) {
            return $this->redirect('/');
        }

        $this->view->time = GoodId::getLatestTime();
        $this->view->country = '合計';
        $this->view->goodid = $goodid_obj;
    }
}
