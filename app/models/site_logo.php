<?php
App::import('Model', 'Attachment');
class SiteLogo extends Attachment
{
    public $name = 'SiteLogo';
    var $useTable = 'attachments';
    public $actsAs = array(
        'Inheritable' => array(
            'inheritanceField' => 'class',
            'fieldAlias' => 'SiteLogo'
        )
    );
}
?>