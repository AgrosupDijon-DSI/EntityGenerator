<?php

namespace EduterCNERTA\Model;

/**
 * Description of Attribute
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class Attribute
{

    /**
     * @var ID ID of the Attribute
     */
    private $ID;

    /**
     * @var string Name/Code of attribute in camelCase
     */
    private $name;

    /**
     * @var string Comment for the attribue
     */
    private $comment;

    /**
     *
     * @var string Type of the attribute
     */
    private $type;

    /**
     * @var int Length of the attribute
     */
    private $length;

    /**
     * @var string Default value for the attribute
     */
    private $defaultValue;

    /**
     * @var boolean Define if value of attibute can be null
     */
    private $isNullAble;

    /**
     * @var boolean Define if the attribute is an identifier
     */
    private $isIdentifier;

    /**
     * @var boolean Define if the attribute is a primary key
     */
    private $isPrimary;

    /**
     * @var boolean Define if value of attribute is unique
     */
    private $isUnique;

    /**
     * @var int The precision for a decimal (exact numeric) column (Applies only for decimal column)
     */
    private $precision;
    private $cardinality;
    private $relationEntity;
    private $relationName;
    private $relationAttribute;

    function __construct()
    {

    }
    public function getCardinality()
    {
        return $this->cardinality;
    }

    public function setCardinality($cardinality)
    {
        $this->cardinality = $cardinality;
    }

    public function getRelationEntity()
    {
        return $this->relationEntity;
    }

    public function setRelationEntity($relationEntity)
    {
        $this->relationEntity = $relationEntity;
    }

        public function getID()
    {
        return $this->ID;
    }

    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    public function getIsNullAble()
    {
        return $this->isNullAble;
    }

    public function setIsNullAble($isNullAble)
    {
        $this->isNullAble = $isNullAble;
    }

    public function getIsIdentifier()
    {
        return $this->isIdentifier;
    }

    public function setIsIdentifier($isIdentifier)
    {
        $this->isIdentifier = $isIdentifier;
    }

    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
    }

    public function getIsUnique()
    {
        return $this->isUnique;
    }

    public function setIsUnique($isUnique)
    {
        $this->isUnique = $isUnique;
    }

    public function getRelationAttribute()
    {
        return $this->relationAttribute;
    }

    public function setRelationAttribute($relationAttribute)
    {
        $this->relationAttribute = $relationAttribute;
    }


    public function getPrecision()
    {
        return $this->precision;
    }

    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    public function getRelationName()
    {
        return $this->relationName;
    }

    public function setRelationName($relationName)
    {
        $this->relationName = $relationName;
    }


}

