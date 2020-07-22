<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\ImageForm;

class ImageController extends AbstractActionController
{
    private $imageManager;

    public function __construct($imageManager)
    {
        // start work with pictures
        $this->imageManager = $imageManager;
    }
    
    public function uploadAction() 
    {
        $form = new ImageForm();
        
        if($this->getRequest()->isPost()) {
            
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            
            $form->setData($data);
                
            if($form->isValid()) {
                    
                $data = $form->getData();
                    
                // Redirect the user to "Image Gallery" page.
                return $this->redirect()->toRoute('images');
            }                        
        } 
        
        // Render the page.
        return new ViewModel([
                     'form' => $form
                 ]);
    }

    public function indexAction()
    {
        // Get the list of already saved files.
        $files = $this->imageManager->getSavedFiles();
        
        // Render the view template.
        return new ViewModel([
            'files'=>$files
        ]);
    }

    public function fileAction() 
    {
        // Get the file name from GET variable.
        $fileName = $this->params()->fromQuery('name', '');

        // Check whether the user needs a thumbnail or a full-size image.
        $isThumbnail = (bool)$this->params()->fromQuery('thumbnail', false);
    
        // Get path to image file.
        $fileName = $this->imageManager->getImagePathByName($fileName);
        
                
        // Get image file info (size and MIME type).
        $fileInfo = $this->imageManager->getImageFileInfo($fileName);        
        if ($fileInfo===false) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);            
            return;
        }
                
        // Write HTTP headers.
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine("Content-type: " . $fileInfo['type']);        
        $headers->addHeaderLine("Content-length: " . $fileInfo['size']);
            
        // Write file content.
        $fileContent = $this->imageManager->getImageFileContent($fileName);
        if($fileContent!==false) {                
            $response->setContent($fileContent);
        } else {        
            // Set 500 Server Error status code.
            $this->getResponse()->setStatusCode(500);
            return;
        }
        
        if($isThumbnail) {
            // Remove temporary thumbnail image file.
            unlink($fileName);
        }
        
        // Return Response to avoid default view rendering.
        return $this->getResponse();
    }    
}
