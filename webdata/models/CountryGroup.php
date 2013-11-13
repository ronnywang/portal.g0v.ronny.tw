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
    protected static $_name_pool = null;

    protected static function initPool()
    {
        if (is_null(self::$_pool)) {
            self::$_pool = array();
            foreach (CountryGroup::search(1) as $group) {
                self::$_pool[$group->name] = $group->id;
                self::$_name_pool[$group->id] = $group->name;
            }
        }
    }

    public static function getCode($name, $auto_create = true)
    {
        self::initPool();

        if ($auto_create and !array_key_exists($name, self::$_pool)) {
            $group = CountryGroup::insert(array(
                'name' => $name,
            ));
            self::$_pool[$name] = $group->id;
            self::$_name_pool[$group->id] = $name;
        }

        return self::$_pool[$name];
    }

    public static function getName($id)
    {
        self::initPool();
        return self::$_name_pool[$id];
    }
}
