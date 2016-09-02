<?php
include_once __SITE_PATH . '/Model/Helpers/' . 'Data.php';

class ImportedData {

    public function __construct() {

        $this->uploaded    = new SimpleXMLElement($_FILES['xml']['tmp_name'], NULL, TRUE);
        $this->cliplist    = $this->uploaded->library->event->project->sequence->spine->children();
        $this->helperData  = new HelperData();
        $this->operation   = new Operation();

    }


    /**
     * Add fake data to retrieve offset
     * of children clips and secondary
     * storyline clips
     */


    function addChildrenClipOffset() {

        foreach($this->cliplist as $clip) {

            $mainStart = $this->operation->solveFraction($clip['start']);
            $mainOffset = $this->operation->solveFraction($clip['offset']);
            $parentOffset = $mainStart - $mainOffset;

            foreach($clip as $child) {

                $childOffset = $this->operation->solveFraction($child['offset']) - $parentOffset;

                if ($child->getName() == "spine") {

                    foreach($child->children() as $clip2) {
                        $spineOffset = $this->operation->solveFraction($clip2['offset']) + $childOffset;
                        $clip2->addAttribute('child-offset', $spineOffset );
                    }

                } else {

                    $child->addAttribute('child-offset', $childOffset);
                }
            }
        }
    }



    /**
     * Add fake lane for secondary storyline clips
     * based on corresponding secondary storyline
     */

    function addSecondaryStorylineLane() {

        foreach($this->cliplist as $firstLevel) {
            foreach($firstLevel as $spine) {
                if ($spine->getName() == "spine") {

                    $lane = $spine['lane'];

                    foreach($spine->children() as $clip2) {

                        $clip2->addAttribute('lane', $lane );

                    }
                }
            }
        }
    }


    /**
     * Add fake id to clip and
     * corresponding audio track
     *
     * @param $xml
     */

    public function NO_addClipAudioFakeLinkId($xml) {

        //TODO: Rivedere con l'audio

        $clips = 'qualcosa';
        $i = 0;
        foreach($clips as $clip) {
            $n = $i++;
            if(isset($clip->video->audio)) {
                $clip->addAttribute('fakelink','fakelink'.$n );
                $clip->video->audio->addAttribute('fakelink','fakelink'.$n);
            }
        }
    }

    /**
     * Get manipulated file
     * @return SimpleXMLElement
     */

    public function getFile() {

        $xml = $this->uploaded;

        $class = get_class_methods($this);
        foreach($class as $method){
            if("add" == substr($method,0,3)){
                echo $this->{$method}($xml);
            }
        }
        return $xml;
    }
}

