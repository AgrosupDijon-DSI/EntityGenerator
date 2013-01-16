<?php

namespace EduterCNERTA\TwigExtentions;
use EduterCNERTA\Model\Attribute;

class MyTwigExtension extends \Twig_Extension {

    public function __construct() {
        
    }

    /**
     * @inherited
     */
    public function getFilters() {
        return array(
            'entityName' => new \Twig_Filter_Method($this, 'twig_entityname_filter'),
            'functionName' => new \Twig_Filter_Method($this, 'twig_functionName_filter'),
            'attributeName' => new \Twig_Filter_Method($this, 'twig_attributeName_filter')
        );
    }

    /**
     * @inherited
     */
    public function getFunctions() {
        return array(
            'getJoinColumName' => new \Twig_Function_Method($this, 'getJoinColumName'),
        );
    }

    /**
     * @inherited
     */
    public function getName() {
        return 'bodTwigExt';
    }

    public function twig_entityname_filter($value) {
        $value = str_replace("_", " ", $value);
        $value = str_replace("-", " ", $value);
        $value = ucwords($value);
        $value = str_replace(" ", "", $value);
        return $value;
    }

    public function twig_functionName_filter($value, $prefix = "get") {
        return $prefix . $this->twig_entityname_filter($value);
    }

    public function twig_attributeName_filter($value, $isId = FALSE) {
        $value = str_replace("_", " ", $value);
        $value = str_replace("-", " ", $value);

        if (count(explode(" ", $value) > 1)) {
            $value = ucwords($value);
        }

        $value = str_replace(" ", "", $value);

        if (strtolower(substr($value, 0, 2)) == "id") {
            $value = substr($value, 2, strlen($value));
            $isId = TRUE;
        }
        if ($isId) {
            $value = "id" . $value;
        } else {
            $value = lcfirst($value);
        }

        return $value;
    }

    public function getJoinColumName(Attribute $attribute, $relationAttribute) {
//        if ($attribute->getName() == $attribute->getRelationEntity()->getAttribute($attribute->getRelationAttribute())->getName()) {
//            return $relationAttribute->getName() . "_" . $attribute->getName();
//        } else {
            return $attribute->getName();
//        }
    }

}
