<?php
include_once __SITE_PATH . '/Model/' . 'mtnElements.php';

class Audio_Clip extends MotionElements {

    /**
     * Insert SourceClip Id
     *
     * @param $clip
     * @param $data
     *
     */

    function setId($clip, $data) {

        $sourceId = self::ID_START_AUDIO;
        $id_table  = $this->uploaded->getClipAudioLinkId($sourceId);
        $ref = ((string)$data['fakelink']);

        if (isset($id_table[$ref])) {
            $clip->attributes()->id = $id_table[$ref];
        }
    }

    /**
     * Set timing out
     * @param $clip
     * @param $data
     */

    function setTiming($clip, $data) {
        $clip->timing->attributes()->out = $this->getClipTimingOut($data);
    }

    /**
     * Insert clip in timeline linked object
     *
     * @param $clip
     * @param $data
     */

    function setTimelineClipLinkId($clip, $data) {

        $sourceId = self::ID_START_CLIP;
        $id_table = $this->uploaded->getClipAudioLinkId($sourceId);
        $ref = ((string)$data['fakelink']);

        if ( isset($id_table[$ref]) && isset($data['fakelink'])) {
            $clip->linkedobjects = $id_table[$ref];
        } else {
            unset($clip->linkedobjects);
        }
    }

    /**
     * Insert Source Clip link
     *
     * @param $clip
     * @param $data
     */

    function setSourceClipLinkId($clip, $data) {

        $sourceId = self::ID_START_SOURCE;
        $id_table = $this->uploaded->getClipSourceLinkId($sourceId);
        $ref = $this->uploaded->getRefId($data,'ref');

        if (isset($id_table[$ref])) {
            $sourcelink = $id_table[$ref];
            $this->insertxml->query_attribute($clip->parameter->parameter, "name", "Media")
                ->attributes()->value  = $sourcelink;
        }
    }

    /**
     * Create a list of audio clip width attributes
     * @param  SimpleXMLElement object $project
     *
     * @return SimpleXMLElement
     **/

    function insertClip($project){

        $clip  = new SimpleXMLElement( __SITE_PATH . '/src/' . 'audio.xml', NULL, TRUE);
        $attributes = $this->uploaded->getAudioClipData();

        foreach ($attributes as $data) {
            $clip->attributes()->name = $data['name'];

            $class = get_class_methods($this);
            foreach($class as $method){
                if("set" == substr($method,0,3)){
                    echo $this->{$method}($clip, $data);
                }
            }
            $this->insertxml->simplexml_import_simplexml($project->scene->audio, $clip);
        }
    }

}