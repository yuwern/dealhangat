<?php
class UserPreferenceCategory extends AppModel
{
    public $name = 'UserPreferenceCategory';
    public $displayField = 'name';
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }
}
?>