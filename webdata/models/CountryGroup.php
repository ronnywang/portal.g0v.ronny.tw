<?php

class CountryGroup extends Pix_Table
{
    public function init()
    {
        $this->_name = 'country_group';
        $this->_primary = 'id';

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['name'] = array('type' => 'varchar', 'size' => 32);
    }

    public static function getCode($name, $auto_create = true)
    {
        $group = CountryGroup::find_by_name($name);
        if ($auto_create and !$group) {
            $group = CountryGroup::insert(array(
                'name' => $name,
            ));
        }

        return $group->id;
    }

    public static function getName($id)
    {
        return CountryGroup::find($id)->name;
    }
}
