<?php
include_once __SITE_PATH . '/Model/'. 'GetData.php';
include_once __SITE_PATH . '/Model/Helpers/'. 'Xml.php';


class Project {

    public function __construct() {

        $this->uploaded   = new getData();
        $this->operation  = new Operation();
        $this->insertxml  = new ManageXml();
        $this->settings  = $this->uploaded->getSequenceData();
        $this->duration  = $this->uploaded->getSequenceDuration();
        $this->ntsc      = $this->uploaded->isNtsc();
    }

    /**
     * Scene settings
     *
     * @param  SimpleXMLElement object
     * @return SimpleXMLElement
     **/

    function setSequenceData($project) {

        $framerate    = $this->settings['frameDuration'];
        $duration = $this->operation->solveFraction($this->uploaded->getSequenceDuration())
            /$this->operation->solveFraction($framerate);

        $project->scene->sceneSettings->duration  = $duration;
        $project->scene->sceneSettings->width     = $this->settings['width'];
        $project->scene->sceneSettings->height    = $this->settings['height'];
        $project->scene->sceneSettings->frameRate = substr($framerate, strpos($framerate, "/") +1 , 2);

        if ($this->ntsc == '120000') {
            $project->scene->sceneSettings->NTSC = '1';
        }
    }

    /**
     * TimeRange and playRange
     * @param  SimpleXMLElement object
     *
     * @return SimpleXMLElement
     **/

    function setSequenceRange($project) {

        $n1 = $this->ntsc * $this->operation->solveFraction($this->duration);

        $project->scene->timeRange->attributes()->duration = $n1.' '.$this->ntsc.' 1 0';
        $project->scene->playRange->attributes()->duration = $n1.' '.$this->ntsc.' 1 0';
    }


    /**
     * Numbers of audio tracks
     * @param $project
     * @return SimpleXMLElement
     */

    function setAudioTracksNumber($project) {
        $number = count($this->uploaded->getAudioClipData());
        $project->scene->audioTracks = $number;
    }

    /**
     * @param $project
     * @return SimpleXMLElement
     */

    function setTimingOut($project) {

        $frameduration = $this->operation->solveFraction($this->settings['frameDuration']);
        $out = - $this->ntsc * $frameduration;

        $project->scene->scenenode->scenenode->timing->attributes()->out = $out .' '.$this->ntsc. ' 1 0';
        $project->scene->scenenode->timing->attributes()->out = $out .' '.$this->ntsc. ' 1 0';

        $project->scene->footage->timing->attributes()->out = $out .' '.$this->ntsc. ' 1 0';
    }

    /**
     * Layer timing
     * Highest value from Clips timing out
     *
     * @param $project
     * @return SimpleXMLElement
     */

    function setLayerTiming($project){



        $outvalues = array();
        foreach($this->uploaded->getClipData() as $clip) {

            $outvalues [] = $this->ntsc * ($this->uploaded->getTcStart()
                + $this->operation->solveFraction($clip['duration']));
        }
        $out = ((string)max($outvalues));

        $project->scene->layer->timing->attributes()->in = '0 '.$this->ntsc.' 1 0';
        $project->scene->layer->timing->attributes()->out = round($out).' '.$this->ntsc.' 1 0';

    }

    /**
     * Layer dimensions
     * @param $project
     */

    function setLayerDimensions($project) {

        $object = $this->insertxml->query_attribute($project->scene->layer->parameter, "name", "Object");
        foreach($object->parameter as $param) {
            switch($param['name']) {
                case 'Fixed Width':
                case 'Aperture Width':
                  $param->attributes()->default  = $this->settings['width'];
                  $param->attributes()->value  = $this->settings['width'];
                break;
                case 'Fixed Height':
                case 'Aperture Height':
                  $param->attributes()->default  = $this->settings['height'];
                  $param->attributes()->value  = $this->settings['height'];
                break;
            }
        }
    }
}