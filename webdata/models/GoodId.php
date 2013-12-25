<?php

class GoodIdRow extends Pix_Table_Row
{
    public function getParents()
    {
        $parents = array();
        $parents[] = $this;
        $p = $this;
        while ($p->parent_id) {
            $p = GoodId::find($p->parent_id);
            $parents[] = $p;
        }
        return $parents;
    }

    public function id()
    {
        $id = $this->id;
        if ($id < 100) {
            return sprintf('%02d', $this->id);
        } elseif ($id < 10000) {
            return sprintf('%04d', $this->id);
        } elseif ($id < 1000000) {
            return sprintf('%06d', $this->id);
        } elseif ($id < 100000000) {
            return sprintf('%08d', $this->id);
        } else {
            return sprintf('%010d', $this->id);
        }
    }
}

class GoodId extends Pix_Table
{
    public function init()
    {
        $this->_name = 'good_id';
        $this->_primary = 'id';
        $this->_rowClass = 'GoodIdRow';

        $this->_columns['id'] = array('type' => 'bigint', 'unsigned' => true);
        $this->_columns['parent_id'] = array('type' => 'int');
        $this->_columns['name'] = array('type' => 'text');
        $this->_columns['ename'] = array('type' => 'text');

        $this->addIndex('parent_id', array('parent_id'));
    }

    public function getLatestTime()
    {
        return GoodIn2Code::search(array('good_id' => 2))->max('time')->time;
    }
}
