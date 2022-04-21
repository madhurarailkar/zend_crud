<?php
namespace Post\Form;

use Zend\Form\Form;

class PostForm  extends Form{
    function __construct($name=null){
        parent::__construct('post');
        $this->setAttribute('method','POST');

        $this->add([
            'name'=>'id',
            'type'=>'hidden',
        ]);

        $this->add([
            'name'=>'title',
            'type'=>'text',
            'options'=>[
                'label'=>'Title'
            ]
        ]);

        $this->add([
            'name'=>'description',
            'type'=>'textarea',
            'options'=>[
                'label'=>'Description'
            ]
        ]);

        $this->add([
            'name'=>'category',
            'type'=>'text',
            'options'=>[
                'label'=>'Category'
            ]
        ]);
        
        $this->add([
            'name'=>'submit',
            'type'=>'submit',
            'attributes'=>[
                'value'=>'save',
                'id'=>'buttonSave'
            ]
        ]);
    }
}