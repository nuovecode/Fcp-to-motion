<?php
include_once __SITE_PATH . '/Model/'. 'Export.php';

class FormValidation {


    function validateFile() {

        $name = $_FILES['xml']['name'];
        $ext= pathinfo($name, PATHINFO_EXTENSION);

        if($ext != "fcpxml") {

            $error = "<strong>Error.</strong> You must upload a .fcpxml file";

            echo "<div class='alert-danger'><ul class='container'>";
            echo "<li>" . $error . "</li>";
            echo "</ul></div>";

        } else {
            $this->export  = new ExportFile();
            $this->export->exportXML($name);
        }

    }


}