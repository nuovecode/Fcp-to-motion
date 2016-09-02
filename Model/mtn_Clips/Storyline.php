<?php

class Storyline {

    public function __construct() {
        $this->helperData  = new HelperData();
    }

    /**
     * Get Main Storyline
     *
     * @param $spineChildren
     * @return array
     */

    public function getMainStoryline($spineChildren) {
        return $this->helperData->checkClip($spineChildren);
    }

    /**
     * Get Main Storyline Level II
     *
     * @param $spineChildren
     * @return array
     */

    public function getMainStorylineChild($spineChildren) {

        $clips = array();
        foreach ($spineChildren as $clip) {
            foreach ($clip->children() as $clip2) {
                $clips[] = $clip2;
            }
        }
        return $this->helperData->checkClip($clips);

    }

    /**
     * @param $spineChildren
     * @return array
     */

    public function getSecondaryStoryline($spineChildren) {

        $secondaryStoryline = array();

        foreach ($spineChildren as $clip) {
            foreach ($clip->children() as $spine) {
                if ($spine->getName() == 'spine') {
                    foreach ($spine->children() as $clip2) {
                        $secondaryStoryline[] = $clip2;
                    }
                }
            }
        }
        return $this->helperData->checkClip($secondaryStoryline);
    }

    /**
     * Get clip from main storyline, main storyline
     * children and secondary storyline and merge
     * them in the array of the clips in timeline.
     *
     * @param $xml
     * @return array
     */

    public function mergeStoryline($xml) {

        $spineChildren = $xml->library->event->project->sequence->spine->children();

        $mainStoryline = $this->getMainStoryline($spineChildren);

        $start1 = count($mainStoryline) + 1;
        $mainStorylineChild = $this->countClip(($this->getMainStorylineChild($spineChildren)), $start1);

        $start2 = $start1 + count($mainStorylineChild) + 1;
        $secondaryStoryline = $this->countClip(($this->getSecondaryStoryline($spineChildren)), $start2);

        $clips = array_merge(
            $mainStoryline +
            $mainStorylineChild +
            $secondaryStoryline);

        return $clips;
    }


    /**
     * Change array index to
     * avoid duplicate
     *
     * @param $clipList
     * @param $startFrom
     * @return array
     */

    function countClip ($clipList, $startFrom) {
        $clips = array(); $index = 0;
        foreach ($clipList as $clip) {
            $index ++;
            $clips[$index + $startFrom] = $clip;
        }
        return $clips;
    }

}