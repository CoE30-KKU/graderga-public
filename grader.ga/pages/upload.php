<?php

    function generateRandom($length = 5) {
        $characters = md5(time());
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    if ($_FILES['file']['name']) {
        if (!$_FILES['file']['error']) {

            //Clear cache folder
            /*
                $files = glob('../file/cache/*'); // get all file names
                foreach($files as $file){ // iterate files
                    if(is_file($file)) {
                        unlink($file); // delete file
                    }
                }
            */
            $name = generateRandom(5);
            $ext = explode('.', $_FILES['file']['name']);
            $filename = str_replace("." . $ext[sizeof($ext) - 1], "", $_FILES['file']['name']) . "_$name." . $ext[sizeof($ext) - 1];

            if (!file_exists('../file/cache/')) {
                mkdir('../file/cache/');
            }

            $destination = '../file/cache/' . $filename; //change this directory
            $location = $_FILES["file"]["tmp_name"];
            move_uploaded_file($location, $destination);
            echo '../file/cache/' . $filename;//change this URL
        } else {
            echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
        }
    }

?>