<?php
include_once __SITE_PATH . '/Model/'. 'Scene.php';
include_once __SITE_PATH . '/Model/mtn_Clips/'. 'Clips.php';
//include_once __SITE_PATH . '/Model/mtn_Audio/'. 'Audio.php';
include_once __SITE_PATH . '/Model/mtn_SourceClips/'. 'SourceClips.php';


class ExportFile {

    const FILENAME = 'exported.motn';

    public function __construct() {

        $this->project = new Project();
        $this->source  = new Source_Clip();
        //$this->audio   = new Audio_Clip();
        $this->clip    = new Clip();
    }

    /**
     * @return bool
     */

    function download() {
        return true;
    }

    /**
     * Export Project width attributes
     *
     * @return object
     */

    function exportXML($name) {

        $project = new SimpleXMLElement( __SITE_PATH . '/src/' . 'project.xml', NULL, TRUE);

        $this->source->insertClip($project);
        $this->clip->insertClip($project);
        //$this->audio->insertClip($project);


        $class = get_class_methods('Project');
        foreach($class as $method){
            if("set" == substr($method,0,3)){
                echo $this->project->{$method}($project);
            }
        }

        if ($this->download() == true) {

            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename=' . pathinfo($name, PATHINFO_FILENAME) . '.motn');

            echo $project->asXML();
            exit();
        }

    }

}