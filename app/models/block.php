<?php

class Block extends AppModel {
    public $name = 'Block';

    public $validate = array(
        'title' => array(
            'rule' => 'notEmpty'
        ),
        'body' => array(
            'rule' => 'notEmpty'
        ),
        'region' => array(
            'rule' => 'notEmpty'
        ),
    );
}

?>
