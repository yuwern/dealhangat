<?php
App::import('Model', 'Attachment');
class PageLogo extends Attachment
{
    public $name = 'PageLogo';
    var $useTable = 'attachments';
    public $actsAs = array(
        'Inheritable' => array(
            'inheritanceField' => 'class',
            'fieldAlias' => 'PageLogo'
        )
    );
}
?>