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

    protected static $_pool = null;

    public static function getCode($name)
    {
        if (is_null(self::$_pool)) {
            self::$_pool = array();
            foreach (CountryGroup::search(1) as $group) {
                self::$_pool[$group->name] = $group->id;
            }
        }

        if (!array_key_exists($name, self::$_pool)) {
            $group = CountryGroup::insert(array(
                'name' => $name,
            ));
            self::$_pool[$name] = $group->id;
        }

        return self::$_pool[$name];
    }
}
