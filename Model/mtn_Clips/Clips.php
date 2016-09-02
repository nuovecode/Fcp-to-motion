<?php
include_once __SITE_PATH . '/Model/' . 'mtnElements.php';
include_once __SITE_PATH . '/Model/' . 'mtn_Clips/Parameters.php';

class Clip extends MotionElements {

    /**
     * Set time of in and out for clips in timeline
     * @param SimpleXMLElement object $clip
     * @param SimpleXMLElement object $data
     *
     * @return SimpleXMLElement
     **/

    function setTiming($clip, $data){

        $inpoint = $this->uploaded->getStartPoint($data);

        $in =  $this->ntsc * $this->uploaded->getOffset($data);

        $offset = round($in - $this->ntsc * $inpoint);

        $clip->timing->attributes()->out     = $this->getClipTimingOut($data);
        $clip->timing->attributes()->in      = $in.' '.$this->ntsc.' 1 0';
        $clip->timing->attributes()->offset  = $offset.' '.$this->ntsc.' 1 0';

    }

    /**
     * Set Keypoint related to the source clip
     * @param SimpleXMLElement object $clip
     * @param SimpleXMLElement object $data
     *
     * @return SimpleXMLElement
     **/


    function setKeypoint($clip, $data) {

        if (!$this->uploaded->isImage($data)){ /**if it's a clip video**/

            $frameduration = $this->operation->solveFraction($this->frameduration);
            $clipduration  = $this->operation->solveFraction($data['duration']);

            $inValue  = ($this->uploaded->getStartPoint($data) / $frameduration ) + 1 ;
            $outValue = ($this->uploaded->getStartPoint($data) + $clipduration ) / $frameduration + 1 ;
            $inTime   = $this->ntsc * $this->uploaded->getStartPoint($data);
            $outTime  = $this->ntsc * ($this->uploaded->getStartPoint($data) + $clipduration);

            foreach($clip->parameter->parameter as $attr) {
                switch($attr['name']) {
                    case 'Retime Value':

                        $i = 0;
                        foreach($attr->curve->keypoint as $set) {
                            $i++;
                            if ($i == 1) {
                                $set->time  = round($inTime) . ' ' .$this->ntsc . ' 1 0';
                                $set->value = $inValue;
                            } elseif ($i == 2){
                                $set->time  =  round($outTime) . ' ' .$this->ntsc . ' 1 0';
                                $set->value =  $outValue;
                            }
                        }
                        break;
                    case 'Retime Value Cache':

                        $i = 0;
                        foreach($attr->curve->keypoint as $set) {
                            $i++;
                            if ($i == 1) {
                                $set->time  = '0 1 1 0';
                                $set->value = '1';
                            } elseif ($i == 2){
                                $set->time  =  round($outTime) . ' ' .$this->ntsc . ' 1 0';
                                $set->value =  $outValue;
                            }
                        }
                        break;
                    case 'Duration Cache':
                        $attr->attributes()->value = $outValue - 1;
                }
            }

        } else { /** Else if it's an image */

            foreach($clip->parameter->parameter as $param) {
                switch($param['name']) {
                    case ('Retime Value'):
                    case ('Retime Value Cache'):
                        unset($param->curve);
                        $param->addAttribute('default', '1');
                        $param->addAttribute('value', '1');
                        break;
                    case ('Duration Cache'):
                        $param->attributes()->value = 1;
                }
            }
        }
    }

    /**
     * Insert Source Clip Dimensions
     * @param $clip
     * @param $data
     */

    function setDimensions($clip, $data) {

        $source = $this->uploaded->getCorrespondingSourceAsset($data);
        $format = ((string)$source['format']);

        foreach($this->uploaded->getFormat() as $dimensions) {
            switch((string)$dimensions['id']) {
                case $format:
                    $object = $this->insertxml->query_attribute($clip->parameter, "name", "Object");
                    $this->insertxml->query_attribute($object->parameter, "name", "Width")
                        ->attributes()->value = ((string)$dimensions['width']);
                    $this->insertxml->query_attribute($object->parameter, "name", "Height")
                        ->attributes()->value = ((string)$dimensions['height']);
            }
        }

    }

    /**
     * Insert SourceClip Id
     *
     * @param $clip
     * @param $data
     *
     */

    function setId($clip, $data) {

        $sourceId = self::ID_START_CLIP;
        $id_table  = $this->uploaded->getClipAudioLinkId($sourceId);
        $ref = $this->uploaded->getRefId($data,'fakelink');

        if (isset($id_table[$ref])) {
            $clip->attributes()->id = $id_table[$ref];
        }
    }

    /**
     * Insert Audio Clip link
     *
     * @param $clip
     * @param $data
     */

    function setAudioClipLinkId($clip, $data) {

        $sourceId = self::ID_START_AUDIO;
        $id_table = $this->uploaded->getClipAudioLinkId($sourceId);
        $ref = $this->uploaded->getRefId($data,'fakelink');
        
        if (isset($id_table[$ref]) && isset($data['fakelink']) ) {
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
            $sourceLink = $id_table[$ref];
            $nodeMedia = $this->insertxml->query_attribute($clip->parameter[0]->parameter, "name", "Media");
            $nodeMedia->parameter->attributes()->default  = $sourceLink;
            $nodeMedia->parameter->attributes()->value  =  $sourceLink;
        }
    }

    /**
     * Create an array of clips
     * sorted by lane number
     *
     * @return array
     */

    function sortedClip() {

        $fcpClip = $this->uploaded->getClipData();

        foreach ($fcpClip as $key => $row) {
            $mid[$key]  = $row['lane'];
        }
        array_multisort($mid, SORT_NUMERIC, SORT_DESC, $fcpClip);

        return $fcpClip;

    }

    /**
     * Create a list of clip width attributes
     * @param  SimpleXMLElement object $project
     *
     * @return SimpleXMLElement
     **/


    function insertClip($project){


        foreach ($this->sortedClip() as $data) {

            $clip        = new SimpleXMLElement( __SITE_PATH . '/src/' . 'clip.xml', NULL, TRUE);
            $parameters  = new Clip_parameter();

            $clip->attributes()->name = $data['name'];

            $class = get_class_methods($this);
            foreach($class as $method){
                if("set" == substr($method,0,3)){
                    echo $this->{$method}($clip, $data);
                }
            }


            /** Add parameters **/
            $parameters->insertParameters($clip , $data);

            /** Insert to clip **/
            $this->insertxml->simplexml_import_simplexml($project->scene->layer, $clip);

        }
    }
}