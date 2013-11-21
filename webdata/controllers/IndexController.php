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

        $this->view->goodid = $goodid_obj;
    }
}
