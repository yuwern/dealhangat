<?php
App::import('Model', 'Deal');
class SubDeal extends Deal
{
    public $name = 'SubDeal';
    var $useTable = 'deals';
    /*public $actsAs = array(
    'Inheritable' => array(
    'inheritanceField' => 'class',
    'fieldAlias' => 'UserAvatar'
    )
    );*/
    public $belongsTo = array(
        'Deal' => array(
            'className' => 'Deal',
            'foreignKey' => 'parent_id',
            'conditions' => array(
                'Deal.id' => 'SubDeal.parent_id',
            ) ,
            'fields' => '',
            'order' => '',
        ) ,
    );
}
?>