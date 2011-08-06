<?php
App::import('Model', 'Attachment');
class UserAvatar extends Attachment
{
    public $name = 'UserAvatar';
    var $useTable = 'attachments';
    public $actsAs = array(
        'Inheritable' => array(
            'inheritanceField' => 'class',
            'fieldAlias' => 'UserAvatar'
        )
    );
}
?>