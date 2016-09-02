<?php
include_once __SITE_PATH . '/Model/'. 'GetData.php';
include_once __SITE_PATH . '/Model/Helpers/'. 'Xml.php';

abstract class MotionElements {

    const ID_START_SOURCE = 10020;
    const ID_START_CLIP   = 20000;
    const ID_START_AUDIO  = 30000;

    public function __construct() {

        $this->uploaded       = new getData();
        $this->operation      = new Operation();
        $this->insertxml      = new ManageXml();
        $this->ntsc           = $this->uploaded->isNtsc();
        $this->frameduration  = $this->uploaded->getFrameDuration();
    }

    abstract protected function insertClip($project);

    /**
     * Get Timing out value of
     * clip video and audio
     *
     * @param $data
     * @return string
     */

    function getClipTimingOut($data) {

        $out = $this->ntsc * ($this->uploaded->getOffset($data)
                + $this->operation->solveFraction($data['duration']));
        return $out.' '.$this->ntsc.' 1 0';
    }
}