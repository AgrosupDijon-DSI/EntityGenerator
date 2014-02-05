<?php

namespace Cnerta\EntityGeneratorBundle\Model;

/**
 * Relationship
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class Relationship
{

    /**
     * @var string id of the relationship (id from the mdp of power amc)
     */
    private $id;

    /**
     * @var string Name/Code of the Entity
     */
    private $name;

    /**
     * @var string Code of the Entity who is the Owner of the relationship
     */
    private $entityOwner;

    /**
     * @var string Code of the Entity in relationship with the Owner
     */
    private $entityInRelationship;

    /**
     * @var string Cardinality of the relationship (oneToOne | oneToMany | manyToOne | manyToMany)
     */
    private $cardinality;

    /**
     *
     * @param string $name
     * @param string $entityOwner
     * @param string $entityInRelationship
     * @param string $cardinality
     */
    public function __construct($name = null, $entityOwner = null, $entityInRelationship = null, $cardinality = null)
    {
        $this->name = $name;
        $this->entityOwner = $entityOwner;
        $this->entityInRelationship = $entityInRelationship;
        $this->cardinality = $cardinality;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEntityOwner()
    {
        return $this->entityOwner;
    }

    public function setEntityOwner($entityOwner)
    {
        $this->entityOwner = $entityOwner;
    }

    public function getEntityInRelationship()
    {
        return $this->entityInRelationship;
    }

    public function setEntityInRelationship($entityInRelationship)
    {
        $this->entityInRelationship = $entityInRelationship;
    }

    public function getCardinality()
    {
        return $this->cardinality;
    }

    public function setCardinality($cardinality)
    {
        $this->cardinality = $cardinality;
    }

}

