<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ImageForm extends Form
{
    public function __construct()
    {
        // Define form name.
        parent::__construct('image-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->addElements();        
        $this->addInputFilter();
    }
    
    protected function addElements() 
    {
        $this->add([
            'type'  => 'file',
            'name' => 'file',
            'attributes' => [                
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Image file',
            ],
        ]);        
          
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Upload',
                'id' => 'submitbutton',
            ],
        ]);               
    }

    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();   
        $this->setInputFilter($inputFilter);
      
        $inputFilter->add([
                'type'     => 'Zend\InputFilter\FileInput',
                'name'     => 'file',
                'required' => true,   
                'validators' => [
                    ['name'    => 'FileUploadFile'],
                    [
                        'name'    => 'FileMimeType',                        
                        'options' => [                            
                            'mimeType'  => ['image/jpeg', 'image/png']
                        ]
                    ],
                    ['name'    => 'FileIsImage'],
                    [
                        'name'    => 'FileImageSize',
                        'options' => [
                            'minWidth'  => 128,
                            'minHeight' => 128,
                            'maxWidth'  => 4096,
                            'maxHeight' => 4096
                        ]
                    ],
                ],
                'filters'  => [                    
                    [
                        'name' => 'FileRenameUpload',
                        'options' => [  
                            'target' => './data/upload',
                            'useUploadName' => true,
                            'useUploadExtension' => true,
                            'overwrite' => true,
                            'randomize' => false
                        ]
                    ]
                ],   
            ]);         
    }
}
