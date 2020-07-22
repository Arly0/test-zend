<?php
namespace Application\Service;

class ImageManager 
{
    // The directory where we save image files.
    private $saveToDir = './data/upload/';
        
    public function getSaveToDir() 
    {
        return $this->saveToDir;
    }  

    public function getSavedFiles() 
    {
        // The directory where we plan to save uploaded files.
        
        // Check whether the directory already exists, and if not,
        // create the directory.
        if(!is_dir($this->saveToDir)) {
            if(!mkdir($this->saveToDir)) {
                throw new \Exception('Could not create directory for uploads: ' . 
                             error_get_last());
            }
        }
        
        // Scan the directory and create the list of uploaded files.
        $files = [];        
        $handle  = opendir($this->saveToDir);
        while (false !== ($entry = readdir($handle))) {
            
            if($entry=='.' || $entry=='..')
                continue; // Skip current dir and parent dir.
            
            $files[] = $entry;
        }
        closedir($handle);
        
        // Return the list of uploaded files.
        return $files;
    }  

    public function deleteImageByName($fileName)
    {
        $handle  = opendir($this->saveToDir);
        while (false !== ($entry = readdir($handle))) {
            
            if($entry=='.' || $entry=='..')
                continue; // Skip current dir and parent dir.
            if($entry === $fileName){
                unlink($this->saveToDir . $entry);
            }
        }
        closedir($handle);

        return true;
    }

     // Returns the path to the saved image file.
     public function getImagePathByName($fileName) 
     {
         // Take some precautions to make file name secure.
         $fileName = str_replace("/", "", $fileName);  // Remove slashes.
         $fileName = str_replace("\\", "", $fileName); // Remove back-slashes.
                 
         // Return concatenated directory name and file name.
         return $this->saveToDir . $fileName;                
     }
   
     // Returns the image file content. On error, returns boolean false. 
     public function getImageFileContent($filePath) 
     {
         return file_get_contents($filePath);
     }
     
     // Retrieves the file information (size, MIME type) by image path.
     public function getImageFileInfo($filePath) 
     {
         // Try to open file        
         if (!is_readable($filePath)) {            
             return false;
         }
             
         // Get file size in bytes.
         $fileSize = filesize($filePath);
 
         // Get MIME type of the file.
         $finfo = finfo_open(FILEINFO_MIME);
         $mimeType = finfo_file($finfo, $filePath);
         if($mimeType===false)
             $mimeType = 'application/octet-stream';
     
         return [
             'size' => $fileSize,
             'type' => $mimeType 
         ];
     }  
}