<?php
include_once ('attachment.php');
class Image extends Attachment
{
    public $name = 'Image';
    var $useTable = 'attachments';
    public $actsAs = array(
        //		'WhoDunnit',
        /*		'Slug' => array (
        'label' =>'description',
        'overwrite' => true,
        'unique' => false
        ),
        */
        'ImageUpload'
    );
}
?>