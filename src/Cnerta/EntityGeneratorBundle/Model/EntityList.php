<?php

namespace Cnerta\EntityGeneratorBundle\Model;

/**
 * Entity
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class EntityList
{

    private $entityList;

    public function __construct()
    {
        $this->entityList = array();
    }

    public function getEntityById($id)
    {
         if (array_key_exists($id, $this->entityList)) {
            return $this->entityList[$id];
        }
        return null;
    }

    public function getEnityByCode($code)
    {
        foreach($this->entityList as $entity) {
            if($entity->getName() === $code) {
                return $entity;
            }
        }

        return null;
    }

    public function addEnity(Entity $entity)
    {
        $this->entityList[$entity->getId()] = $entity;
    }

    public function getEntityList()
    {
        return $this->entityList;
    }

    /**
     * Chose witch entity is the Owner
     * @param Entity $RelationshipEntity
     */
    public function whoIsTheOwner(Entity $RelationshipEntity)
    {
        $relationshipList = $RelationshipEntity->getRelationshipList();

        usort($relationshipList, function(Relationship $a, Relationship $b) {
                    $a = $this->getEntityById($a->getEntityOwner());
                    $b = $this->getEntityById($b->getEntityOwner());
                    
                    return ($a->countAttributes() > $b->countAttributes()) ? -1 : 1;
                });

        return $relationshipList[0]->getEntityOwner();
    }

}

