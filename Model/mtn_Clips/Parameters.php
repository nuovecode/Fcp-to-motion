<?php

class Clip_parameter {

    public function __construct() {

        $this->uploaded       = new getData();
        $this->operation      = new Operation();
        $this->insertxml      = new ManageXml();

        $this->sequenceDuration = $this->uploaded->getSequenceDuration();
    }

    /**
     * @param $name
     * @return SimpleXMLElement
     */

    function getFileName ($name) {
        return new SimpleXMLElement( __SITE_PATH . '/src/clip_parameters/' . $name , NULL, TRUE);
    }


    function setPosition ($wrap, $parameters, $data) {

        if ($parameters->getName() == 'adjust-transform'){

            $xml = $this->getFileName('trasform.xml');

            $position = $data->{'adjust-transform'}['position'];
            $rotation = $data->{'adjust-transform'}['rotation'];
            $scale = $data->{'adjust-transform'}['scale'];
            $anchor = $data->{'adjust-transform'}['anchor'];

            $position1 = strstr($position, ' ', true);
            $positionX = round($position1*'10.799999136',1);
            $position2 = substr($position, strpos($position, " ") + 1);
            $positionY = round($position2*'10.799999136',1);

            $rotationZ = $rotation/'57.2957795131';

            $scaleX = strstr($scale, ' ', true);
            $scaleY = substr($scale, strpos($scale, " ") + 1);

            $anchor1 = strstr($anchor, ' ', true);
            $anchorX = round($anchor1*'10.799999136',1);
            $anchor2 = substr($anchor, strpos($anchor, " ") + 1);
            $anchorY = round($anchor2*'10.799999136',1);

            $motionposition = $this->insertxml->query_attribute($xml->parameter, "name", "Position");
            $motionpositionX = $this->insertxml->query_attribute($motionposition->parameter, "name", "X");
            $motionpositionY = $this->insertxml->query_attribute($motionposition->parameter, "name", "Y");
            $motionpositionX->attributes()->value = $positionX;
            $motionpositionY->attributes()->value = $positionY;
            $motionrotation = $this->insertxml->query_attribute($xml->parameter, "name", "Rotation");
            $motionrotation->parameter->attributes()->value = $rotationZ;

            /** @var $motionscale ADJUST TRANSFORM: SCALE */

            $motionscale = $this->insertxml->query_attribute($xml->parameter, "name", "Scale");
            $motionscaleX = $this->insertxml->query_attribute($motionscale->parameter, "name", "X");
            $motionscaleY = $this->insertxml->query_attribute($motionscale->parameter, "name", "Y");

            if(isset($data->{'adjust-transform'}['scale'] )) {
                $motionscaleX->attributes()->value = $scaleX;
                $motionscaleY->attributes()->value = $scaleY;
            }

            $motionanchor = $this->insertxml->query_attribute($xml->parameter, "name", "Anchor Point");
            $motionanchorX = $this->insertxml->query_attribute($motionanchor->parameter, "name", "X");
            $motionanchorY = $this->insertxml->query_attribute($motionanchor->parameter, "name", "Y");
            $motionanchorX->attributes()->value = $anchorX;
            $motionanchorY->attributes()->value = $anchorY;


            $this->insertxml->simplexml_import_simplexml($wrap, $xml);
        }
    }


    function setCrop ($wrap, $parameters, $data) {

        if ($parameters->getName() == 'adjust-crop'){

            $xml = $this->getFileName ('crop.xml');

            $left = $data->{'adjust-crop'}->{'trim-rect'}['left'];
            $right = $data->{'adjust-crop'}->{'trim-rect'}['right'];
            $top = $data->{'adjust-crop'}->{'trim-rect'}['top'];
            $bottom = $data->{'adjust-crop'}->{'trim-rect'}['bottom'];

            $format = $this->uploaded->getCorrespondingSourceFormat($data);
            $variabilecrop = $format['height']/'50';

            $cropleft = round($left*$variabilecrop,1);
            $cropright = round($right*$variabilecrop,1);
            $croptop = round($top*$variabilecrop,1);
            $cropbottom = round($bottom*$variabilecrop,1);

            $Mcropleft = $this->insertxml->query_attribute($xml->parameter, "name", "Left");
            $Mcropleft->attributes()->value = $cropleft;
            $Mcropright = $this->insertxml->query_attribute($xml->parameter, "name", "Right");
            $Mcropright->attributes()->value = $cropright;
            $Mcroptop = $this->insertxml->query_attribute($xml->parameter, "name", "Top");
            $Mcroptop->attributes()->value = $croptop;
            $Mcropbottom = $this->insertxml->query_attribute($xml->parameter, "name", "Bottom");
            $Mcropbottom->attributes()->value = $cropbottom;

            $this->insertxml->simplexml_import_simplexml($wrap, $xml);
        }
    }


    function setDistort ($wrap, $parameters, $data) {

        if ($parameters->getName() == 'adjust-corners'){

            $xml = $this->getFileName ('distort.xml');

            //ottieni i dati da final cut e mettili nelle variabili

            $botleft = $data->{'adjust-corners'}['botLeft'];
            $botright = $data->{'adjust-corners'}['botRight'];
            $topleft = $data->{'adjust-corners'}['topLeft'];
            $topright = $data->{'adjust-corners'}['topRight'];

            $botleft1 = strstr($botleft, ' ', true);
            $botleftX = round($botleft1*'2.4',1);
            $botleft2 = substr($botleft, strpos($botleft, " ") + 1);
            $botleftY = round($botleft2*'2.4',1);

            $botright1 = strstr($botright, ' ', true);
            $botrightX = round($botright1*'2.4',1);
            $botright2 = substr($botright, strpos($botright, " ") + 1);
            $botrightY = round($botright2*'2.4',1);

            $topleft1 = strstr($topleft, ' ', true);
            $topleftX = round($topleft1*'2.4',1);
            $topleft2 = substr($topleft, strpos($topleft, " ") + 1);
            $topleftY = round($topleft2*'2.4',1);

            $topright1 = strstr($topright, ' ', true);
            $toprightX = round($topright1*'2.4',1);
            $topright2 = substr($topright, strpos($topright, " ") + 1);
            $toprightY = round($topright2*'2.4',1);

            $mbotleft = $this->insertxml->query_attribute($xml->parameter, "name", "Bottom Left");
            $mbotleftX = $this->insertxml->query_attribute($mbotleft->parameter, "name", "X");
            $mbotleftY = $this->insertxml->query_attribute($mbotleft->parameter, "name", "Y");
            $mbotleftX->attributes()->value = $botleftX;
            $mbotleftY->attributes()->value = $botleftY;

            $mbotright = $this->insertxml->query_attribute($xml->parameter, "name", "Bottom Right");
            $mbotrightX = $this->insertxml->query_attribute($mbotright->parameter, "name", "X");
            $mbotrightY = $this->insertxml->query_attribute($mbotright->parameter, "name", "Y");
            $mbotrightX->attributes()->value = $botrightX;
            $mbotrightY->attributes()->value = $botrightY;

            $mtopleft = $this->insertxml->query_attribute($xml->parameter, "name", "Top Left");
            $mtopleftX = $this->insertxml->query_attribute($mtopleft->parameter, "name", "X");
            $mtopleftY = $this->insertxml->query_attribute($mtopleft->parameter, "name", "Y");
            $mtopleftX->attributes()->value = $topleftX;
            $mtopleftY->attributes()->value = $topleftY;

            $mtopright = $this->insertxml->query_attribute($xml->parameter, "name", "Top Right");
            $mtoprightX = $this->insertxml->query_attribute($mtopright->parameter, "name", "X");
            $mtoprightY = $this->insertxml->query_attribute($mtopright->parameter, "name", "Y");
            $mtoprightX->attributes()->value = $toprightX;
            $mtoprightY->attributes()->value = $toprightY;


            $this->insertxml->simplexml_import_simplexml($wrap, $xml);
        }
    }


    function setBlend ($wrap, $parameters, $data) {

        if ($parameters->getName() == 'adjust-blend'){

            $xml = $this->getFileName ('blend.xml');

            $opacity = $data->{'adjust-blend'}['amount'];
            $mode = $data->{'adjust-blend'}['mode'];
            $blendmode = strstr($mode, ' ', true);

            $Mopacity = $this->insertxml->query_attribute($xml->parameter, "name", "Opacity");
            $Mopacity->attributes()->value = $opacity;
            $Mblendmode = $this->insertxml->query_attribute($xml->parameter, "name", "Blend Mode");
            $Mblendmode->attributes()->value = $blendmode;

            $this->insertxml->simplexml_import_simplexml($wrap, $xml);
        }
    }


    /**
     * Get all setter
     *
     * @param $clip
     * @param $data
     */

    function insertParameters($clip, $data) {
        $class = get_class_methods($this);

        foreach ($data->children() as $parameters) {
            foreach($class as $method){
                if("set" == substr($method,0,3)){
                    $wrap = $this->insertxml->query_attribute($clip, "name", "Properties");
                    echo $this->{$method}($wrap, $parameters, $data);
                }

            }
        }
    }
}