<?php

namespace EntityGeneratorBundle\Cnerta\Model;

/**
 * Key
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class Key
{

    /**
     *
     * @var string
     */
    private $id;

    /**
     *
     * @var string entity target of the key
     */
    private $entityIdTarget;

    /**
     *
     * @var string attribute target of the key
     */
    private $attributeIdTarget;

    /**
     *
     * @var boolean
     */
    private $isPrimary;

    /**
     *
     * @var boolean
     */
    private $isForeign;

    public function __construct($id, $isPrimary, $isForeign, $entityIdTarget = null, $attributeIdTarget = null)
    {
        $this->id = $id;
        $this->isPrimary = $isPrimary;
        $this->isForeign = $isForeign;
        $this->entityIdTarget = $entityIdTarget;
        $this->attributeIdTarget = $attributeIdTarget;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function isPrimary()
    {
        return (bool)$this->isPrimary;
    }

    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
    }

    public function isForeign()
    {
        return (bool)$this->isForeign;
    }

    public function setIsForeign($isForeign)
    {
        $this->isForeign = $isForeign;
    }

    public function getEntityIdTarget()
    {
        return $this->entityIdTarget;
    }

    public function setEntityIdTarget($entityIdTarget)
    {
        $this->entityIdTarget = $entityIdTarget;
    }

    public function getAttributeIdTarget()
    {
        return $this->attributeIdTarget;
    }

    public function setAttributeIdTarget($attributeIdTarget)
    {
        $this->attributeIdTarget = $attributeIdTarget;
    }

    public function merge(Key $mergeKey)
    {

        $this->isPrimary |= $mergeKey->isPrimary();

        $this->isForeign |= $mergeKey->isForeign();

        $this->entityIdTarget = ($mergeKey->getEntityIdTarget() == null) ? $this->entityIdTarget : $mergeKey->getEntityIdTarget();

        $this->attributeIdTarget = ($mergeKey->getAttributeIdTarget() == null) ? $this->attributeIdTarget : $mergeKey->getAttributeIdTarget();
    }

}

