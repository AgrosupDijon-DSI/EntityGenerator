<?php

namespace EduterEntityGeneratorBundle\Cnerta\TwigExtentions;

use EduterEntityGeneratorBundle\Cnerta\Model\Attribute;
use EduterEntityGeneratorBundle\Cnerta\Model\Entity;

class MyTwigExtension extends \Twig_Extension
{

    public function __construct()
    {
        
    }

    /**
     * @inherited
     */
    public function getFilters()
    {
        return array(
            'entityName' => new \Twig_Filter_Method($this, 'twig_entityname_filter'),
            'functionName' => new \Twig_Filter_Method($this, 'twig_functionName_filter'),
            'formatAttributeName' => new \Twig_Filter_Method($this, 'twig_attributeName_filter'),
            'getReturnTypeOfAttribute' => new \Twig_Filter_Method($this, 'twig_getReturnTypeOfAttribute_filter'),
            'vardump' => new \Twig_Filter_Method($this, 'twig_vardump_filter',  array('is_safe' => array('html'))),
            'printr' => new \Twig_Filter_Method($this, 'twig_printr_filter',  array('is_safe' => array('html'))),
        );
    }

    /**
     * @inherited
     */
    public function getFunctions()
    {
        return array(
//            'getJoinColumName' => new \Twig_Function_Method($this, 'getJoinColumName'),
            'needIsOrHasFunction' => new \Twig_Function_Method($this, 'needIsOrHasFunction'),
            'getObjectForThisAttributeType' => new \Twig_Function_Method($this, 'getObjectForThisAttributeType'),
        );
    }

    /**
     * @inherited
     */
    public function getName()
    {
        return 'bodTwigExt';
    }

    public function twig_entityname_filter($value)
    {
        $value = str_replace("_", " ", $value);
        $value = str_replace("-", " ", $value);
        $value = ucwords($value);
        $value = str_replace(" ", "", $value);
        return $value;
    }

    public function twig_functionName_filter($value, $prefix = "get")
    {
        return $prefix . $this->twig_entityname_filter($value);
    }

    public function twig_attributeName_filter($value, $isId = FALSE)
    {
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

    /**
     * Return the adapted value of an Doctrine type
     * 
     * @param string $value
     * @return string
     */
    public function twig_getReturnTypeOfAttribute_filter($value)
    {
        switch (strtolower($value)) {
            case "datetime": return "\DateTime";
            case "datetimetz": return "\DateTime";
            case "date": return "\DateTime";
            case "time": return "\DateTime";
            case "text": return "string";
        }

        return $value;
    }

    /**
     * Return the adapted Object of an Doctrine type
     *
     * @param string $value
     * @return string
     */
    public function getObjectForThisAttributeType($value)
    {
        switch (strtolower($value)) {
            case "datetime": return "\DateTime";
            case "datetimetz": return "\DateTime";
            case "date": return "\DateTime";
            case "time": return "\DateTime";
        }

        return null;
    }

    /**
     * Affiche le contenue de la variable $value avec un print_r et met fin à l'exécution du script.
     *
     * @param \Twig_Environment $env
     * @param mixed $value
     */
    public function twig_printr_filter($value, $exitOrNot = true)
    {
        ob_start();

        echo "<pre>";
        print_r($value);
        echo "</pre>";
        if ($exitOrNot) {
            echo ob_get_clean();
            exit;
        }
        return ob_get_clean();
    }

    /**
     * Affiche le contenue de la variable $value avec un var_dump et met fin à l'exécution du script.
     *
     * @param \Twig_Environment $env
     * @param mixed $value
     */
    public function twig_vardump_filter($value, $exitOrNot = true)
    {
        ob_start();

        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        if ($exitOrNot) {
            echo ob_get_clean();
            exit;
        }
        return ob_get_clean();
    }


    public function needIsOrHasFunction($value)
    {
        $pattern = '/^(is|has)[A-Z0-9]{1,}/';

        return (bool)preg_match($pattern, $value);
    }

}
