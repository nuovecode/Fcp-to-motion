<?php

class HelperData {


    /**
     * Check if an xml node
     * is a clip in timeline.
     *
     * @param $clipList
     * @return array
     */

    function checkClip ($clipList) {
        $clips = array();
        foreach ($clipList as $clip) {
            if (isset($clipList , $clip['name'])
                && !isset($clip->audio, $clip->gap->audio)
                && ($this->checkClipNodeName($clip) == true)) {

                $clips[] = $clip;
            }
        }
        return $clips;
    }

    /**
     * @param $clip
     * @return bool
     */

    function checkClipNodeName($clip) {
        if ($clip->getName() == 'clip'
            || $clip->getName() == 'video') {
            return true;
        }
    }

    /**
     * @param $clip
     * @return bool
     */

    function checkSourceClipName ($clip) {
        if ($clip->getName() == 'asset'
            || $clip->getName() == 'effect'
            || $clip->getName() == 'media') {
            return true;
        }
    }

}