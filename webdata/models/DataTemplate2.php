<?php

class DataTemplate2 extends Pix_Table
{
    public function init()
    {
        $this->_primary = array('good_id', 'time', 'country_id');

        $this->_columns['good_id'] = array('type' => 'int');
        $this->_columns['time'] = array('type' => 'int');
        $this->_columns['country_id'] = array('type' => 'int');
        $this->_columns['num_unit_id'] = array('type' => 'int');
        $this->_columns['num_value'] = array('type' => 'int');
        $this->_columns['weight_unit_id'] = array('type' => 'int');
        $this->_columns['weight_value'] = array('type' => 'int');
        $this->_columns['value'] = array('type' => 'int');
    }
}
