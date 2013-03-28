<?php

namespace EntityGeneratorBundle\Cnerta\Model;

/**
 * Entity
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class Entity
{

    /**
     * @var string id of the Entity (id from the mdp of power amc)
     */
    private $id;

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
    private $attributeList;

    /**
     * @var array Relationship
     */
    private $relationshipList;

    /**
     * @var array of keys (primary)
     */
    private $keyList;

    public function __construct()
    {
        $this->keyList = array();
        $this->attributeList = array();
        $this->relationshipList = array();
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

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getAttributeList()
    {
        return $this->attributeList;
    }

    public function setAttributeList($attributeList)
    {
        $this->attributeList = $attributeList;
    }

    public function addAttribute(Attribute $attribute)
    {
        $this->attributeList[$attribute->getId()] = $attribute;
    }

    public function getAttribute($id)
    {
        if (array_key_exists($id, $this->attributeList)) {
            return $this->attributeList[$id];
        }
        return null;
    }

    public function getRelationshipList()
    {
        return $this->relationshipList;
    }

    public function setRelationshipList($relationshipList)
    {
        $this->relationshipList = $relationshipList;
    }

    public function addRelationship(Relationship $relationship)
    {
        $this->relationshipList[] = $relationship;
    }

    public function getKeyList()
    {
        return $this->keyList;
    }

    public function setKeyList($keyList)
    {
        $this->keyList = $keyList;
    }

    public function addKey(Key $key)
    {
        if (array_key_exists($key->getId(), $this->keyList)) {
            $this->keyList[$key->getId()]->merge($key);
        } else {
            $this->keyList[$key->getId()] = $key;
        }
    }

    public function getKey($id)
    {
        if (array_key_exists($id, $this->keyList)) {
            return $this->keyList[$id];
        }
        return null;
    }

    public function getPrimaryKeyList()
    {
        $primaryKeyList = array();
        foreach ($this->keyList as $key) {
            if ($key->isPrimary()) {
                $primaryKeyList[] = $key;
            }
        }
        return $primaryKeyList;
    }

    public function hasPrimaryKey()
    {
        return count($this->getPrimaryKeyList()) > 0;
    }

    public function hasCompositPrimaryKey()
    {
        $nbPrimaryKey = 0;
        /** @var $ey Key */
        foreach ($this->keyList as $key) {
            if ($key->isPrimary()) {
                $nbPrimaryKey++;
            }
        }

        return $nbPrimaryKey >= 2;
    }

    public function isARelationManyToManyBetweenTwoEntity()
    {
        if (count($this->attributeList) != count($this->keyList)) {
            return false;
        }

        if (count(array_diff_key($this->attributeList, $this->keyList)) >= 1) {
            return false;
        }

        return !$this->isThisEntityATernaryRelationshipBetweenManyEntity();
    }

    public function isThisEntityATernaryRelationshipBetweenManyEntity()
    {

        // When the entity is a ternary relation
        $check = true;
        foreach ($this->keyList as $key) {
            $check = $check && ($key->isPrimary() && $key->isForeign());
        }

        if ($check) {
            if (count($this->keyList) < 3) {
                return false;
            }
        }

        return $check;
    }

    public function getJoinColumName($attributeIdTargetEntity)
    {
        /** @var $key EntityGeneratorBundle\Cnerta\Model\Key */
        foreach ($this->getKeyList() as $key) {
            if ($key->getAttributeIdTarget() === $attributeIdTargetEntity) {
                return $this->getAttribute($key->getId())->getName();
            }
        }
    }

    public function countAttributes()
    {
        return count($this->attributeList);
    }

    public function getTheOtherPartOfTheRelationship($idEntityOfTheFirstPart)
    {
        foreach ($this->relationshipList as $relationship) {
            if ($relationship->getEntityOwner() != $idEntityOfTheFirstPart) {
                return $relationship->getEntityOwner();
            }
        }

        return null;
    }

}

