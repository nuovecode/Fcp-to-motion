<?php
class ManageXml {

    /**
     * https://gist.github.com/hakre/4761677
     *
     * @link http://stackoverflow.com/q/767327/367456
     * @link http://eval.in/9568
     * @link http://3v4l.org/1sI05
     */
    /**
     * Insert XML into a SimpleXMLElement
     *
     * @param SimpleXMLElement $parent
     * @param string $xml
     * @param bool $before
     * @return bool XML string added
     */

    function simplexml_import_xml(SimpleXMLElement $parent, $xml, $before = false) {
        $xml = (string)$xml;


        /** check if there is something to add */

        if ($nodata = !strlen($xml) or $parent[0] == NULL) {
            return $nodata;
        }

        /** add the XML */

        $node     = dom_import_simplexml($parent);
        $fragment = $node->ownerDocument->createDocumentFragment();
        $fragment->appendXML($xml);

        if ($before) {
            return (bool)$node->parentNode->insertBefore($fragment, $node);
        }

        return (bool)$node->appendChild($fragment);
    }

    /**
     * Insert SimpleXMLElement into SimpleXMLElement
     *
     * @param SimpleXMLElement $parent
     * @param SimpleXMLElement $child
     * @param bool $before
     * @return bool SimpleXMLElement added
     */

    function simplexml_import_simplexml(SimpleXMLElement $parent, SimpleXMLElement $child, $before = false)
    {
        /** check if there is something to add */
        if ($child[0] == NULL) {
            return true;
        }
        /** if it is a list of SimpleXMLElements default to the first one */
        $child = $child[0];
        // insert attribute
        if ($child->xpath('.') != array($child)) {
            $parent[$child->getName()] = (string)$child;
            return true;
        }
        $xml = $child->asXML();
        /** remove the XML declaration on document elements */
        if ($child->xpath('/*') == array($child)) {
            $pos = strpos($xml, "\n");
            $xml = substr($xml, $pos + 1);
        }

        return $this->simplexml_import_xml($parent, $xml, $before);
    }

    /**
     * Grab first node with a certain attribute name and value
     * Usage: query_attribute($SimpleXmlNode->Node, "Name", "value")->Node;
     *
     * @param $xmlNode
     * @param $attr_name
     * @param $attr_value
     * @return mixed
     */

    function query_attribute($xmlNode, $attr_name, $attr_value) {
        foreach($xmlNode as $node) {
            switch($node[$attr_name]) {
                case $attr_value:
                    return $node;
            }
        }
    }


}



