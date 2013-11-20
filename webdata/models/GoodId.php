<?php

class GoodId extends Pix_Table
{
    public function init()
    {
        $this->_name = 'good_id';
        $this->_primary = 'id';

        $this->_columns['id'] = array('type' => 'bigint', 'unsigned' => true);
        $this->_columns['parent_id'] = array('type' => 'int');
        $this->_columns['name'] = array('type' => 'text');
        $this->_columns['ename'] = array('type' => 'text');

        $this->addIndex('parent_id', array('parent_id'));
    }
}
