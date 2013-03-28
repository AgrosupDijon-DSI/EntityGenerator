<?php

namespace Cnerta\EntityGeneratorBundle\Engine;

use Cnerta\EntityGeneratorBundle\Model\Entity;
use Cnerta\EntityGeneratorBundle\Model\Attribute;

/**
 * Parse the PowerAmc MDP XML for prepare entities to be generated
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class PowerAmcMPDParserEngine
{

    
    private $reservedKeyword = array("ACTION", "TIMESTAMP", "TIME", "TEXT", "NO", "ENUM", "DATE", "BIT", "ADD", "ALL", "ALTER", "ANALYZE", "AND", "AS", "ASC", "ASENSITIVE", "BEFORE", "BETWEEN", "BIGINT", "BINARY", "BLOB", "BOTH", "BY", "CALL", "CASCADE", "CASE", "CHANGE", "CHAR", "CHARACTER", "CHECK", "COLLATE", "COLUMN", "CONDITION", "CONSTRAINT", "CONTINUE", "CONVERT", "CREATE", "CROSS", "CURRENT_DATE", "CURRENT_TIME", "CURRENT_TIMESTAMP", "CURRENT_USER", "CURSOR", "DATABASE", "DATABASES", "DAY_HOUR", "DAY_MICROSECOND", "DAY_MINUTE", "DAY_SECOND", "DEC", "DECIMAL", "DECLARE", "DEFAULT", "DELAYED", "DELETE", "DESC", "DESCRIBE", "DETERMINISTIC", "DISTINCT", "DISTINCTROW", "DIV", "DOUBLE", "DROP", "DUAL", "EACH", "ELSE", "ELSEIF", "ENCLOSED", "ESCAPED", "EXISTS", "EXIT", "EXPLAIN", "FALSE", "FETCH", "FLOAT", "FLOAT4", "FLOAT8", "FOR", "FORCE", "FOREIGN", "FROM", "FULLTEXT", "GRANT", "GROUP", "HAVING", "HIGH_PRIORITY", "HOUR_MICROSECOND", "HOUR_MINUTE", "HOUR_SECOND", "IF", "IGNORE", "IN", "INDEX", "INFILE", "INNER", "INOUT", "INSENSITIVE", "INSERT", "INT", "INT1", "INT2", "INT3", "INT4", "INT8", "INTEGER", "INTERVAL", "INTO", "IS", "ITERATE", "JOIN", "KEY", "KEYS", "KILL", "LEADING", "LEAVE", "LEFT", "LIKE", "LIMIT", "LINES", "LOAD", "LOCALTIME", "LOCALTIMESTAMP", "LOCK", "LONG", "LONGBLOB", "LONGTEXT", "LOOP", "LOW_PRIORITY", "MATCH", "MEDIUMBLOB", "MEDIUMINT", "MEDIUMTEXT", "MIDDLEINT", "MINUTE_MICROSECOND", "MINUTE_SECOND", "MOD", "MODIFIES", "NATURAL", "NOT", "NO_WRITE_TO_BINLOG", "NULL", "NUMERIC", "ON", "OPTIMIZE", "OPTION", "OPTIONALLY", "OR", "ORDER", "OUT", "OUTER", "OUTFILE", "PRECISION", "PRIMARY", "PROCEDURE", "PURGE", "READ", "READS", "REAL", "REFERENCES", "REGEXP", "RELEASE", "RENAME", "REPEAT", "REPLACE", "REQUIRE", "RESTRICT", "RETURN", "REVOKE", "RIGHT", "RLIKE", "SCHEMA", "SCHEMAS", "SECOND_MICROSECOND", "SELECT", "SENSITIVE", "SEPARATOR", "SET", "SHOW", "SMALLINT", "SONAME", "SPATIAL", "SPECIFIC", "SQL", "SQLEXCEPTION", "SQLSTATE", "SQLWARNING", "SQL_BIG_RESULT", "SQL_CALC_FOUND_ROWS", "SQL_SMALL_RESULT", "SSL", "STARTING", "STRAIGHT_JOIN", "TABLE", "TERMINATED", "THEN", "TINYBLOB", "TINYINT", "TINYTEXT", "TO", "TRAILING", "TRIGGER", "TRUE", "UNDO", "UNION", "UNIQUE", "UNLOCK", "UNSIGNED", "UPDATE", "USAGE", "USE", "USING", "UTC_DATE", "UTC_TIME", "UTC_TIMESTAMP", "VALUES", "VARBINARY", "VARCHAR", "VARCHARACTER", "VARYING", "WHEN", "WHERE", "WHILE", "WITH", "WRITE", "XOR", "YEAR_MONTH", "ZEROFILL");

    
    /**
     * @var array engine configuration
     */
    private $conf = array("cardinality" => array(
            "0..*" => "oneToMany",
            "1..*" => "oneToMany",
            "0..1" => "oneToOne",
            "1..1" => "oneToOne"),
        "cardinalityReciproque" => array(
            "0..*" => "manyToOne",
            "1..*" => "manyToOne",
            "0..1" => "null",
            "1..1" => "null"),
        "type" => array(
            "char" => "string",
            "varchar" => "string",
            "int" => "integer",
            "real" => "decimal",
            "integer" => "integer",
            "smallint" => "smallint",
            "mediumint" => "integer",
            "tinyint" => "smallint",
            "bigint" => "bigint",
            "bool" => "boolean",
            "boolean" => "boolean",
            "dec" => "decimal",
            "decimal" => "decimal",
            "numeric" => "decimal",
            "date" => "date",
            "time" => "time",
            "year" => "datetime",
            "datetime" => "datetime",
            "timestamp" => "datetime",
            "text" => "text",
            "longtext" => "text",
            "blob" => "text",
            "longblob" => "text",
            "enum" => "array",
            "array" => "array",
            "float" => "float",
            "double precision" => "decimal",
            "fixed" => "decimal",
            "double" => "float"
        ));

    /**
     * @var DOMDocument
     */
    private $domDoc;

    function __construct($PAXmlPath)
    {
        $this->domDoc = new \DOMDocument();
        $this->domDoc->load($PAXmlPath);
    }

    public function parseEntity()
    {
        $infomationMessages = "";
        $aEntities = array();
        $aEntitiesTernary = array();
        $aEntitiesTernaryMake = array();

        $xPath = new \DOMXPath($this->domDoc);


        $tables = $xPath->query("//c:Tables/*");
        foreach ($tables as $table) {
            $isTernary = FALSE;
            $entity = new Entity();

            $entity->setID($table->getAttribute("Id"));
            $entity->setName($this->getNodeValue($table->getElementsByTagName("Code")->item(0)));
            $entity->setComment($this->getNodeValue($table->getElementsByTagName("Comment")->item(0)));

            // Search Primary Key attributes

            $keyRef = $xPath->query("//c:Tables/o:Table[@Id='" . $entity->getID() . "']/c:PrimaryKey/o:Key");
            if($keyRef->length <= 0) {
                return array("errorMessages" => "Table '" . $entity->getName() . "' has not Primary Key !");
                exit;                    
            }
            $keyRef = $keyRef->item(0)->getAttribute("Ref");
     

            // Get Primary Key ID
            $primaryKeysEls = $xPath->query("//c:Tables/o:Table[@Id='" . $entity->getID() . "']/c:Keys/o:Key[@Id='" . $keyRef . "']/c:Key.Columns/*");

            $primaryKeys = array();

            foreach ($primaryKeysEls as $primaryKeysEl) {
                $primaryKeys[] = $primaryKeysEl->getAttribute("Ref");
            }

            if(count($primaryKeys) > 1) {
                $entity->setHasCompositPrimaryKey(TRUE);
            }
            
            $entity->setIsTernary(FALSE);
            if (count($primaryKeys) >= 2) {
                if ($this->isTernary($entity->getID())) {
                    $isTernary = $this->hasAttributeNonKey($entity->getID());
                    if ($isTernary) {
                        $aEntitiesTernary[] = $entity->getID();
                        $entity->setIsTernary(TRUE);
                    }
                }
            }

            // Parse columns
            foreach ($table->getElementsByTagName("Column") as $column) {
                if ($column instanceof \DOMElement) {
                    if ($column->getAttribute("Id") != NULL) {
                        $attribute = new Attribute();

                        $attribute->setID($column->getAttribute("Id"));

                        $attName = $this->getNodeValue($column->getElementsByTagName("Code")->item(0));
                        if(!in_array($attName, $this->reservedKeyword)) {
                            $attribute->setName($attName);
                        } else {
                            return array("errorMessages" => "Attribute name '" . $attName . "' of the table '" . $entity->getName() . "' use a MySql reserved keyword !");
                        }

                        //TODO traiter #UNIQUE#
                        $attribute->setComment($this->getNodeValue($column->getElementsByTagName("Comment")->item(0)));
                        
                        try {
                            $attribute->setType($this->geTypeForDoctrine($this->getNodeValue($column->getElementsByTagName("DataType")->item(0))));
                            if ($attribute->getType() === NULL) {
                                throw new \Exception("Type not defined in Table :" . $entity->getName() . " for column : " . $attribute->getName(), 500);
                            }
                            $attribute->setLength($this->getNodeValue($column->getElementsByTagName("Length")->item(0)));
                            $attribute->setPrecision($this->getNodeValue($column->getElementsByTagName("Precision")->item(0)));


                            $attribute->setIsIdentifier($this->getNodeValue($column->getElementsByTagName("Identity")->item(0)));

                            if ($this->getNodeValue($column->getElementsByTagName("Code")->item(0)) == 1) {
                                $attribute->setIsNullAble(FALSE);
                            } else {
                                $attribute->setIsNullAble(TRUE);
                            }

                            if (in_array($attribute->getID(), $primaryKeys)) {
                                $attribute->setIsPrimary(TRUE);
                                $attribute->setIsUnique(TRUE);
                            } else {
                                $attribute->setIsPrimary(FALSE);
                                $attribute->setIsUnique(FALSE);
                            }

                            $entity->addAttribute($attribute);
                        } catch(\Exception $e) {
                            echo "\n\n";
                            print_r($e->getMessage());
                            echo "\n";
                        }
                    }
                }
            }

            $aEntities[$entity->getID()] = $entity;
        }

        // Parse References
        $references = $xPath->query("//c:References/*");

        foreach ($references as $reference) {

            $IDReference = $reference->getAttribute("Id");
            $parentTable = $reference->getElementsByTagName("ParentTable")->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref");
            $childTable = $reference->getElementsByTagName("ChildTable")->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref");

            $cardinality = $this->getNodeValue($reference->getElementsByTagName("Cardinality")->item(0));

            $referenceJoin = $xPath->query("//c:References/o:Reference[@Id='" . $IDReference . "']/c:Joins/o:ReferenceJoin")->item(0);

            $Obj1 = $referenceJoin->getElementsByTagName("Object1")->item(0)->getElementsByTagName("Column")->item(0)->getAttribute("Ref");
            $Obj2 = $referenceJoin->getElementsByTagName("Object2")->item(0)->getElementsByTagName("Column")->item(0)->getAttribute("Ref");

            // If the relation is not ternary
            if ($aEntities[$parentTable]->isTernary() === FALSE && $aEntities[$childTable]->isTernary() === FALSE) {

                if ($aEntities[$parentTable]->getAttribute($Obj1)->getIsPrimary() == TRUE) {
                    // Set a reference ton the chind in the parent entity
                    $aEntities[$parentTable]->addAttribute($this->duplicate($aEntities[$parentTable]->getAttribute($Obj1), $aEntities[$childTable], $Obj2, $this->conf["cardinality"][$cardinality], $aEntities[$parentTable]));
                } else {
                    $aEntities[$parentTable]->getAttribute($Obj1)->setCardinality($this->conf["cardinality"][$cardinality]);
                    $aEntities[$parentTable]->getAttribute($Obj1)->setRelationEntity($aEntities[$childTable]);
                    $aEntities[$parentTable]->getAttribute($Obj1)->setRelationAttribute($Obj2);
                }
                $aEntities[$parentTable]->setIsOwner(TRUE);


                if ($aEntities[$childTable]->getAttribute($Obj2)->getIsPrimary() == TRUE) {
                    $aEntities[$childTable]->addAttribute($this->duplicate($aEntities[$childTable]->getAttribute($Obj2), $aEntities[$parentTable], $Obj1, $this->conf["cardinalityReciproque"][$cardinality], $aEntities[$childTable]));
                } else {
                    $aEntities[$childTable]->getAttribute($Obj2)->setCardinality($this->conf["cardinalityReciproque"][$cardinality]);
                    $aEntities[$childTable]->getAttribute($Obj2)->setRelationEntity($aEntities[$parentTable]);
                    $aEntities[$childTable]->getAttribute($Obj2)->setRelationAttribute($Obj1);
                }
                $aEntities[$childTable]->setIsOwner(FALSE);

            } else {
                if (!in_array($parentTable, $aEntitiesTernaryMake) && !in_array($childTable, $aEntitiesTernaryMake)) {
                    $aEntitiesTernaryMake[] = $parentTable;
                    $aEntitiesTernaryMake[] = $childTable;

                    $tagNameOfSide = NULL;
                    $tagNameOfColumn = NULL;

                    $ternaryEntities = array();


                    if ($aEntities[$parentTable]->isTernary() === TRUE) {

                        $tagNameOfColumn = "Object2";
                        $tagNameOfSide = "ChildTable";
                        $otherSides = $xPath->query("//c:References/o:Reference/c:ParentTable/o:Table[@Ref='" . $aEntities[$parentTable]->getID() . "']");
                    } elseif ($aEntities[$childTable]->isTernary() === TRUE) {

                        $tagNameOfColumn = "Object1";
                        $tagNameOfSide = "ParentTable";

                        $otherSides = $xPath->query("//c:References/o:Reference/c:ChildTable/o:Table[@Ref='" . $aEntities[$childTable]->getID() . "']");
                    }

                    foreach ($otherSides as $otherSide) {
                        $domEL = $otherSide->parentNode->parentNode;

                        //count the number of foreignkey for determinate who is the owner in a manyToMany relation
                        $nbFK = $this->countForeignKey($domEL->getElementsByTagName($tagNameOfSide)->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref"));

                        do {
                            if (isset($ternaryEntities[$nbFK])) {
                                $nbFK++;
                            }
                        } while (isset($ternaryEntities[$nbFK]));


                        $ternaryEntities[$nbFK] = array(
                            "ID" => $domEL->getElementsByTagName($tagNameOfSide)->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref"),
                            "Column" => $domEL->getElementsByTagName($tagNameOfColumn)->item(0)->getElementsByTagName("Column")->item(0)->getAttribute("Ref"),
                            "nbFK" => $nbFK,
                            "cardinality" => $this->getNodeValue($domEL->getElementsByTagName("Cardinality")->item(0))
                        );
                    }

                    if (count($ternaryEntities) > 2) {
                        $infomationMessages = "the relationship ManyToMany \"" . $this->getNodeValue($reference->getElementsByTagName("Code")->item(0)) . "\" was not created because it connects more than two entities.\n";
                    } else {

                        krsort($ternaryEntities);

                        $isFirst = TRUE;
                        foreach ($ternaryEntities as $ternaryEntity) {

                            foreach ($ternaryEntities as $ternaryEntity2) {
                                if ($ternaryEntity["ID"] != $ternaryEntity2["ID"]) {

                                    if ($ternaryEntity2["cardinality"] == "0..*" || $ternaryEntity2["cardinality"] == "1..*") {
                                        $cardinality = "manyToMany";
                                        if ($isFirst) {
                                            $isFirst = FALSE;
                                            $cardinality = "ownerManyToMany";
                                        }
                                    } else {
                                        $cardinality = $this->conf["cardinality"][$ternaryEntity2["cardinality"]];
                                    }

                                    $att2 = $this->duplicate($aEntities[$ternaryEntity2["ID"]]->getAttribute($ternaryEntity2["Column"]), $aEntities[$ternaryEntity2["ID"]], $ternaryEntity2["Column"], $cardinality, $aEntities[$ternaryEntity2["ID"]]);
                                    $att2->setName($aEntities[$ternaryEntity2["ID"]]->getName() . "s");
                                    $att2->setRelationName($this->getNodeValue($reference->getElementsByTagName("Code")->item(0)));
                                    $aEntities[$ternaryEntity["ID"]]->addAttribute($att2);
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($aEntitiesTernary as $oneEntity) {
            unset($aEntities[$oneEntity]);
        }

        return array("entities" => $aEntities, "infomationMessages" => $infomationMessages);
    }

    /**
     * Return the node value
     *
     * @param \DomNode ou NULL $nodeValue
     * @return string
     */
    protected function getNodeValue(\DOMNode $nodeValue = NULL)
    {
        if ($nodeValue !== NULL) {
            return $nodeValue->nodeValue;
        }
        return NULL;
    }

    /**
     * Match a PowerAmc cardinality type with a Doctrine type
     *
     * @param string $type
     * @return string
     */
    protected function geTypeForDoctrine($type)
    {
        if ($type !== NULL) {
            $typeForConf = explode('(', strtolower($type));

            if (!isset($this->conf["type"][$typeForConf[0]])) {
                throw new \Exception("Unknown type : " . $type, 500);
            }

            return $this->conf["type"][$typeForConf[0]];
        }
        return NULL;
    }

    /**
     * Duplicates an attribute
     *
     * @param Attribute $attributeToDuplicate
     * @param string $relationEntity
     * @param string $relationAttribute
     * @param string $cardinality
     * @param Entity $entityParent
     * @return Attribute
     */
    protected function duplicate(Attribute $attributeToDuplicate, $relationEntity, $relationAttribute, $cardinality, $entityParent)
    {
        
        $attributeFK = new Attribute();
        $attributeFK->setID($attributeToDuplicate->getID() . count($entityParent->getAttributes()));
        $attributeFK->setName($attributeToDuplicate->getName());
        $attributeFK->setComment($attributeToDuplicate->getComment());
        $attributeFK->setType($attributeToDuplicate->getType());
        $attributeFK->setLength($attributeToDuplicate->getLength());
        $attributeFK->setPrecision($attributeToDuplicate->getPrecision());
        $attributeFK->setDefaultValue($attributeToDuplicate->getDefaultValue());
        $attributeFK->setIsIdentifier(FALSE);
        $attributeFK->setIsNullAble(TRUE);
        $attributeFK->setIsPrimary(FALSE);
        $attributeFK->setIsUnique(FALSE);

        $attributeFK->setCardinality($cardinality);
        $attributeFK->setRelationEntity($relationEntity);
        $attributeFK->setRelationAttribute($relationAttribute);

        return $attributeFK;
    }

    /**
     * Check if a table is a ternary relationship type (ManyToMany)
     *
     * For tha, we check :
     *  - if the number oh Primary key (PK) is the same that Foreign Key (FK)
     *  - if the ID of PK are the same that the ID of FK
     *
     * @param string $idTable
     * @return bool
     */
    protected function isTernary($idTable)
    {
        $aAttributPK = array();
        $aAttributFK = array();
        $xPath = new \DOMXPath($this->domDoc);

        $indexes = $xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index");

        foreach ($indexes as $index) {
            $indexID = $index->getAttribute("Id");
            $code = $this->getNodeValue($index->getElementsByTagName("Code")->item(0));
            $type = substr($code, (strlen($code) - 2), strlen($code));

            if ($type == "PK") {

                $attributesIDs = $xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index[@Id='" . $indexID . "']/c:IndexColumns/o:IndexColumn/c:Column/o:Column");
                foreach ($attributesIDs as $attributeID) {
                    $aAttributPK[] = $attributeID->getAttribute("Ref");
                }
            } elseif ($type == "FK") {
                $attributesIDs = $xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index[@Id='" . $indexID . "']/c:IndexColumns/o:IndexColumn/c:Column/o:Column");
                foreach ($attributesIDs as $attributeID) {
                    $aAttributFK[] = $attributeID->getAttribute("Ref");
                }
            }
        }

        if (count($aAttributPK) !== count($aAttributFK)) {
            return FALSE;
        }

        foreach ($aAttributPK as $att) {
            if (!in_array($att, $aAttributFK)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Check if the table has attributes that aren't PK
     *
     * @param type $idTable
     * @return bool
     */
    protected function hasAttributeNonKey($idTable)
    {
        $aIDColumn = array();
        $aIDKey = array();
        $xPath = new \DOMXPath($this->domDoc);

        $columns = $xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Columns/o:Column");
        foreach ($columns as $column) {
            $aIDColumn[] = $column->getAttribute("Id");
        }

        $keys = $xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Keys/o:Key/c:Key.Columns/o:Column");
        foreach ($keys as $key) {
            $aIDKey[] = $key->getAttribute("Ref");
        }

        if (count($aIDKey) !== count($aIDColumn)) {
            return FALSE;
        }

        foreach ($aIDColumn as $att) {
            if (!in_array($att, $aIDKey)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Counts the number of a table for FK
     *
     * @param string $idTable
     * @return int
     */
    protected function countForeignKey($idTable)
    {
        $nbFK = 0;
        $xPath = new \DOMXPath($this->domDoc);

        $indexes = $xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index");

        foreach ($indexes as $index) {
            $code = $this->getNodeValue($index->getElementsByTagName("Code")->item(0));
            $type = substr($code, (strlen($code) - 2), strlen($code));
            if ($type == "FK") {
                $nbFK++;
            }
        }

        return $nbFK;
    }

}
