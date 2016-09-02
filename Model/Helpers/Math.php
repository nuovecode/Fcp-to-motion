<?php
class Operation {

    /**
     * Solves fractions in seconds
     * (for timing settings)
     * @param  String $data
     *
     * @return int mixed
     **/

    function solveFraction($data){

        if (strpos($data,'/') == true) {
            $num = explode('/', rtrim($data, "s"));
            return $num[0]/$num[1];
        } else {
            return rtrim($data, "s");
        }
    }
}