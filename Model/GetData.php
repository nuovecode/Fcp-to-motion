<?php
include_once __SITE_PATH . '/Model/'. 'Import.php';
include_once __SITE_PATH . '/Model/mtn_Clips/'. 'Storyline.php';
include_once __SITE_PATH . '/Model/Helpers/'. 'Math.php';

class getData {

    public function __construct() {

        $this->uploaded    = new ImportedData();
        $this->operation   = new Operation();
        $this->storyline   = new Storyline();
        $this->helperData  = new HelperData();

        $this->uploaded = $this->uploaded->getFile();
        $this->cliplist = $this->uploaded->library->event->project->sequence->spine->children();
    }

    /**
     * Clip in timeline
     * From main and secondary storyline
     * Model/mtn_Clips/Storyline.php
     *
     * @return array
     */

    public function getClipData() {

        $mergedStoryline = $this->storyline->mergeStoryline($this->uploaded);

        $clips = array();
        foreach ($mergedStoryline as $clip) {

            $source = $this->getCorrespondingSourceAsset($clip);

            if (isset($source['id']) && $this->helperData->checkSourceClipName($source) == true) {
                $clips[] = $clip;
            }
        }
        return $clips;

    }

    /**
     * Source Clip
     * @return array
     */


    public function getSourceClipData() {
        $clips = array();
        foreach ($this->uploaded->resources->children() as $clip) {
            if (isset($clip['id'])
                && $clip->getName() == 'asset') {
                $clips[] = $clip;
            }
        }
        return $clips;
    }


    /**
     * Audio Clip
     * @return array
     */


    public function getAudioClipData() {
        $clips = array();
        foreach ($this->cliplist as $clip ) {
            if(isset($clip->video->audio) || isset($clip->audio) ) {
                $clips[] = $clip;
            }
        }
        return $clips;
    }

    /**
     * Get timeline clip parameters
     *
     * @param $clip
     * @return array
     */

    public function getClipParameters($clip) {
        $parameters = array();
        foreach ($clip as $parameter) {
            if ($parameter->getName() != 'video') {
                $parameters[] = $parameter;
            }
        }
        return $parameters;
    }


    /**
     * Format
     * resources->format
     *
     * @return array
     */

    public function getFormat() {
        $clips = array();
        foreach ($this->uploaded->resources->children() as $clip) {
            if ($clip->getName() == 'format') {
                $clips[] = $clip;
            }
        }
        return $clips;
    }

    /**
     * Project/Sequence/scene
     * Get duration of sequence
     *
     * @return string
     */

    public function getSequenceDuration() {

        $xml      = $this->uploaded;
        $duration = ((string)$xml->library->event->project->sequence['duration']);

        return $duration;
    }

     /**
     * Project/Sequence/scene
     * Get scene settings
     *
     * @return array
     */

    public function getSequenceData() {

        $xml      = $this->uploaded;
        $formatId = ((string)$this->uploaded->library->event->project->sequence['format']);

        foreach($xml->resources->children() as $node) {
            switch($node['id']) {
                case $formatId:
                    return $node;
            }
        }
    }

    /**
     * Project/Sequence/scene
     * Get FRAME DURATION of sequence
     *
     * @return string
     */

    public function getFrameDuration() {
        $data = $this->getSequenceData();
        return $data['frameDuration'];
    }

    /**
     * Project/Sequence/scene
     * Project is NTSC or PAL Set the constant multiplier
     *
     * @return string
     **/

    function isNtsc() {

        $frameRate = $this-> getFrameDuration();
        if ($frameRate == '1001/24000s'
            or $frameRate == '1001/30000s'
            or $frameRate == '1001/60000s') {

            return '120000';

        } else {

            return '153600';
        }
    }

    /**
     * @param $data
     * @return bool
     */

    function isImage($data) {
        if ($data->getName() == 'video') {
            return true;
        }
    }


    /**
     * Ref of the clip video or image
     *
     * @param $data
     * @param $prop
     * @return string
     */

    function getRefId($data,$prop) {
        if (isset($data[$prop])) {
            return ((string)$data[$prop]);
        } else {
            return ((string)$data->video[$prop]);
        }
    }

    /**
     *  Clip adjustment
     *  <adjust-conform type="fill"/>
     *  <adjust-conform type="none"/>
     *  Default is Adjustment FIT
     *
     * @param $clip
     * @return string
     */


    function getAdjust($clip) {

        if ($clip->{'adjust-conform'}['type'] == 'fill') {
            return 'fill';
        } elseif ($clip->{'adjust-conform'}['type'] == 'none') {
            return 'none';
        } else {
            return'default';
        }
    }

    /**
     * Get corresponding ASSET
     * with r1, r2, r3 format id.
     *
     * @param $data
     * @return mixed
     */

    function getCorrespondingSourceAsset ($data) {
        if (isset($data['ref'])) {
            $ref = ((string)$data['ref']);
        } else {
            $ref = ((string)$data->video['ref']);
        }
        foreach($this->getSourceClipData() as $node) {
            switch($node['id']) {
                case $ref:
                    return $node;
            }
        }
    }

    /**
     * Get corresponding FORMAT
     * with r1, r2, r3 format id.
     *
     * @param $data
     * @return mixed
     */

    function getCorrespondingSourceFormat ($data) {

        $asset =  $this->getCorrespondingSourceAsset($data);
        $format = ((string)$asset['format']);

        foreach($this->getFormat() as $node) {
            switch($node['id']) {
                case $format:
                    return $node;
            }
        }
    }

    /**
     * Clip dimensions depending on
     * adjustment: FILL, FIT or NONE
     *
     * @param $sourceClip
     * @return array
     */

    function getDimensions($sourceClip) {

        foreach($this->getClipData() as $clip) {
            switch($this->getRefId($clip,'ref')) {
                case ((string)$sourceClip['id']):
                    /** Timeline REF/ Source ID = Adjustment type */
                    $adj_table[$this->getRefId($clip,'ref')] = $this->getAdjust($clip);

                    $project = $this->getSequenceData();
                    $format  = ((string)$sourceClip["format"]);

                    foreach($this->getFormat() as $node) {
                        switch($node['id']) {
                            case $format:

                                $prjRatio  = $project['width']/$project['height'];
                                $clipRatio = $node['width']/$node['height'];

                                $id = ((string)$sourceClip['id']);
                                if ($adj_table[$id] == 'none') {

                                    $width = $node["width"];
                                    $height = $node["height"];

                                } elseif ($adj_table[$id] == 'fill') {

                                    if ($clipRatio < $prjRatio) {
                                        $width = $project['width'];
                                        $height = $project['width'] * $node['height']/$node['width'];
                                    } else {
                                        $width = $project['height'] * $node['width']/ $node['height'];
                                        $height = $project['height'];
                                    }

                                } else { /** Default or FIT */

                                    if ($clipRatio >= $prjRatio) {
                                        $width = $project['width'];
                                        $height = $project['width'] * $node['height']/$node['width'];
                                    } else {
                                        $width = $project['height'] * $node['width']/ $node['height'];
                                        $height = $project['height'];
                                    }
                                }
                                return array('width' => $width, 'height' => $height);
                        }
                    }
            }
        }
    }

    /**
     * Return Clip Start point
     *
     * @param $data
     * @return mixed
     */

    function getStartPoint($data) {

        $sourceClip = $this->getCorrespondingSourceAsset($data);
        $sourceStart = $this->operation->solveFraction($sourceClip['start'], "", -1);
        $clipStart = $this->operation->solveFraction($data['start'], "", -1);

        return $clipStart - $sourceStart;
    }

    /**
     * Get Clip Offset
     *
     * @param $data
     * @return int
     */


    function getOffset($data) {

        if(isset($data['child-offset'] )) {

            return floatval($data['child-offset']);

        } else {

            return floatval($this->operation->solveFraction($data['offset']));
        }
    }

    /**
     * Get Timeline clip lane
     * Order of the clip
     *
     * @param $data
     * @return string
     */


    function getClipLane($data) {
        if (isset($data['lane'])) {
            return $data['lane'];
        } else {
            return '0';
        }
    }

    /**
     * Table with id connected to the SOURCE CLIPS
     * in final cut (r1, r2 ..) as key and in motion
     * (1000, 10001 ..) as value.
     *
     * @param $start
     * @return array
     */

    function getClipSourceLinkId($start) {
        $id_table = array();
        $id_motn = $start;
        foreach ($this->getSourceClipData() as $clip) {
            $id_fcp = ((string)$clip["id"]);
            if (!array_key_exists($id_fcp, $id_table) ){
                $id_table[$id_fcp] = $id_motn++;
            }
        }
        return $id_table;
    }

    /**
     * Table with id connected to the FAKE AUDIO clips
     * id in final cut (fakelink0, fakelink1 ..) as key
     * and in motion (3000, 30001 ..) as value.
     *
     * @param $start
     * @return array
     */

    function getClipAudioLinkId($start) {
        $id_table = array();
        $id_motn = $start;
        foreach ($this->getClipData() as $clip) {
            $id_fcp = ((string)$clip['fakelink']);
            if (!array_key_exists($id_fcp, $id_table)){
                $id_table[$id_fcp] = $id_motn++;
            }
        }
        return $id_table;
    }

}

