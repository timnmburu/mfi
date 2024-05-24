<?php
    require_once __DIR__ .'/../vendor/autoload.php';
    //require_once __DIR__ .'/../templates/crypt.php';

    function uploadDocument($documentName, $locality) {
        if(empty(basename($_FILES[$documentName]["name"]))){
            return 'empty';
        } else {
            $target_dir = __DIR__ . "/../fileStore/$locality/";
            $target_file = $target_dir . basename($_FILES[$documentName]["name"]);
            
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check if file is a valid type
            $validExtensions = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx");
            if (!in_array($imageFileType, $validExtensions)) {
                $uploadOk = 0;
            }
            
            // Check if file already exists
            if (file_exists($target_file)) {
                //$uploadOk = 0;
            }
            
            // Check file size 500kb
            if ($_FILES[$documentName]["size"] > 500000) {
                //$uploadOk = 0;
            }
            
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                return null;
            } else {
                if (move_uploaded_file($_FILES[$documentName]["tmp_name"], $target_file)) {
                    $target_dir = "/fileStore/$locality/";
                    $target_file1 = $target_dir . basename($_FILES[$documentName]["name"]);
                    
                    return $target_file1;
                } else {
                    return null;
                    exit;
                }
            }
        }
    }
    
    function uploadDocs($documentName, $locality, $newPrefix = null) {
        if(empty(basename($_FILES[$documentName]["name"]))){
            return 'empty';
        } else {
            $target_dir = __DIR__ . "/../fileStore/$locality/";
            $originalFileName = basename($_FILES[$documentName]["name"]);
            $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    
            // Check if newPrefix is provided, otherwise use the original filename without extension  
            if ($newPrefix !== null) {
                $newFileName = $newPrefix . '.' . $extension;
            } else {
                $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '.' . $extension; 
            }
    
            $target_file = $target_dir . $newFileName;
    
            $uploadOk = 1;
        
            // Check if file is a valid type
            $validExtensions = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx");
            if (!in_array($extension, $validExtensions)) {
                $uploadOk = 0;
            }
        
            // Check if file already exists
            if (file_exists($target_file)) {
                //$uploadOk = 0;
            }
        
            // Check file size 500kb
            if ($_FILES[$documentName]["size"] > 500000) {
                //$uploadOk = 0;
            }
        
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                return null;
            } else {
                if (move_uploaded_file($_FILES[$documentName]["tmp_name"], $target_file)) {
                    $target_dir = "/fileStore/$locality/";
                    $target_file1 = $target_dir . $newFileName;
                    
                    return $target_file1;
                } else {
                    return null;
                    exit;
                }
            }
        }
    }






































?>