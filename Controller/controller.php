<?php

include_once __SITE_PATH . '/Model/' . 'FormValidation.php';

class Controller {

    public function __construct()  {

        if(isset($_POST['submit'])) {

            $this->validate  = new FormValidation();
            $this->validate->validateFile();
        }
    }

    public function invoke() {

        include __SITE_PATH . '/view/'. 'index.php';
    }




}