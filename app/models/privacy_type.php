<?php
class PrivacyType extends AppModel
{
    public $name = 'PrivacyType';
    public $displayField = 'name';
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }
}
?>