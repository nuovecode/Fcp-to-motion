<?php
include_once __SITE_PATH . '/Model/' . 'mtnElements.php';

class Source_Clip extends MotionElements {

    /**
     * Set Source clip attributes
     * @param SimpleXMLElement object $clip
     * @param SimpleXMLElement object $data
     *
     * @return SimpleXMLElement
     **/

    function setClipAttributes($clip, $data) {

        $clip->attributes()->name = $data['name'];
        $clip->pathURL = $data['src'];
        $clip->relativeURL = implode('/', array_slice (explode('/' , $data['src']), -2,2,true));

        if ($data['duration'] != '0s') {

            $missingDuration = $this->operation->solveFraction($data['duration']);
            $clip->missingDuration  =  $missingDuration;
            $clip->creationDuration =  $missingDuration / $this->operation->solveFraction($this->frameduration);

            $framerate = substr($this->frameduration, strpos($this->frameduration, "/") +1 , 2);
            $timingout = $this->ntsc / $framerate * ($clip->creationDuration -1);

            $clip->timing->attributes()->out = $timingout.' '. $this->ntsc.' 1 0';


        } else {

            $clip->missingDuration  =  $this->operation->solveFraction($this->frameduration);
            $clip->creationDuration =  '1';
            $clip->timing->attributes()->out = '0'.' '. $this->ntsc.' 1 0';
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

        $sourceId = self::ID_START_SOURCE;
        $id_table  = $this->uploaded->getClipSourceLinkId($sourceId);
        $ref = ((string)$data["id"]);

        if (isset($id_table[$ref])) {
            $sourceLink = $id_table[$ref];
            $clip->attributes()->id = $sourceLink;
        }
    }

    /**
     * Insert Source Clip Dimensions
     * @param $clip
     * @param $data
     */

    function setDimensions($clip, $data) {


        $dimension = $this->uploaded->getDimensions($data);
        $project = $this->uploaded->getSequenceData();

        $duration = $this->operation->solveFraction($this->uploaded->getSequenceDuration())
            /$this->operation->solveFraction($project['frameDuration']);

        $format = ((string)$data["format"]);

        foreach($this->uploaded->getFormat() as $node) {
            switch($node['id']) {
                case $format:
                    $clip->missingWidth = ((string)$node["width"]);
                    $clip->missingHeight = ((string)$node["height"]);
                    $object = $this->insertxml->query_attribute($clip->parameter, "name", "Object");
                    foreach($object->parameter as $param) {
                        switch($param['name']) {
                            case 'Fixed Width':

                                $param->attributes()->default = $dimension['width'];
                                $param->attributes()->value = $dimension['width'];

                                break;

                            case 'Fixed Height':

                                $param->attributes()->default = $dimension['height'];
                                $param->attributes()->value = $dimension['height'];

                                break;

                            case 'Frame Rate':

                                if ($data['duration'] == '0s') {
                                    $param->attributes()->value = $duration;
                                } else {
                                    
                                    $frameDuration = 1 / $this->operation->solveFraction($node['frameDuration']);
                                    $param->attributes()->value = $frameDuration;
                                }

                                break;
                        }
                    }
            }
        }

    }


    /**
     * Create a list of clip width attributes
     * @param  SimpleXMLElement object $project
     *
     * @return SimpleXMLElement
     **/

    function insertClip($project){

        $clip  = new SimpleXMLElement( __SITE_PATH . '/src/' . 'sourceclip.xml', NULL, TRUE);
        $attributes = $this->uploaded->getSourceClipData();

        foreach ($attributes as $data) {

            $class = get_class_methods($this);
            foreach($class as $method){
                if("set" == substr($method,0,3)){
                    echo $this->{$method}($clip, $data);
                }
            }

            $this->insertxml->simplexml_import_simplexml($project->scene->footage, $clip);
        }

    }

}