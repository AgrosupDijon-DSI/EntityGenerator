<?php

namespace EduterCNERTA\Model;

/**
 * Entity
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class Entity
{

    /**
     * @var ID ID of the Entity
     */
    private $ID;

    /**
     * @var string Name/Code of the Entity
     */
    private $name;

    /**
     * @var string Comment for Entity
     */
    private $comment;

    /**
     * @var array Attribute Array of Attribute
     */
    private $attributes;

    /**
     * @var array
     */
    private $relations;

    /**
     * @var bool
     */
    private $isOwner;

    /**
     * @var bool
     */
    private $isTernary;

    /**
     * @var bool
     */
    private $hasCompositPrimaryKey;
    
    function __construct()
    {
        $this->attributes = array();
        $this->relations = array();
        $this->hasCompositPrimaryKey = FALSE;
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

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[$attribute->getID()] = $attribute;
    }

    public function getAttribute($Id)
    {
        return $this->attributes[$Id];
    }

    public function getAttributePK()
    {
        foreach ($this->attributes as $attribue) {
            if ($attribue->getIsPrimary() === TRUE) {
                return $attribue;
            }
        }
    }

    public function getRelations()
    {
        return $this->relations;
    }

    public function setRelations($type, Entity $entity)
    {
        $this->relations[] = array("type" => $type, "entity" => $entity);
    }

    public function getID()
    {
        return $this->ID;
    }

    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function getIsOwner()
    {
        return $this->isOwner;
    }

    public function setIsOwner($isOwner)
    {
        $this->isOwner = $isOwner;
    }

    public function isTernary()
    {
        return $this->isTernary;
    }

    public function setIsTernary($isTernary)
    {
        $this->isTernary = $isTernary;
    }

    public function hasCompositPrimaryKey() {
        return $this->hasCompositPrimaryKey;
    }
    
    public function getHasCompositPrimaryKey() {
        return $this->hasCompositPrimaryKey;
    }

    public function setHasCompositPrimaryKey($hasCompositPrimaryKey) {
        $this->hasCompositPrimaryKey = $hasCompositPrimaryKey;
    }

}

