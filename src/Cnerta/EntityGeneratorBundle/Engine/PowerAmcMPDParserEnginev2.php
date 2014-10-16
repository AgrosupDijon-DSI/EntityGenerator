<?php

namespace Cnerta\EntityGeneratorBundle\Engine;

use Cnerta\EntityGeneratorBundle\Model\Entity;
use Cnerta\EntityGeneratorBundle\Model\Attribute;
use Cnerta\EntityGeneratorBundle\Model\EntityList;
use Cnerta\EntityGeneratorBundle\Model\Relationship;
use Cnerta\EntityGeneratorBundle\Model\Key;

/**
 * Parse the PowerAmc MDP XML for prepare entities to be generated
 *
 * @author Valerian Girard <valerian.girard@educagri.fr>
 */
class PowerAmcMPDParserEnginev2
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
            "long varchar" => "text",
            "blob" => "blob",
            "longblob" => "blob",
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
    private $xPath;

    function __construct($PAXmlPath)
    {
        $this->domDoc = new \DOMDocument();
        $this->domDoc->load($PAXmlPath);
        $this->xPath = new \DOMXPath($this->domDoc);
    }

    public function parseEntity()
    {
        $infomationMessages = "";
        $entityList = new EntityList();

        // Parsing table for an entity transformation
        $tables = $this->xPath->query("//c:Tables/*");
        foreach ($tables as $table) {
            $entity = new Entity();

            $entity->setId($table->getAttribute("Id"));
            $entity->setName($this->getNodeValue($table->getElementsByTagName("Code")->item(0)));
            $entity->setComment($this->getNodeValue($table->getElementsByTagName("Comment")->item(0)));

            // Search Primary Key attributes
            $keyRef = $this->xPath->query("//c:Tables/o:Table[@Id='" . $entity->getId() . "']/c:PrimaryKey/o:Key");
            if ($keyRef->length <= 0) {
                return array("errorMessages" => "Table '" . $entity->getName() . "' has not Primary Key !");
                exit;
            }
            $keyRef = $keyRef->item(0)->getAttribute("Ref");

            // Get Primary Key ID
            $primaryKeysEls = $this->xPath->query("//c:Tables/o:Table[@Id='" . $entity->getId() . "']/c:Keys/o:Key[@Id='" . $keyRef . "']/c:Key.Columns/*");
                
            foreach ($primaryKeysEls as $primaryKeysEl) {
                $entity->addKey(new Key($primaryKeysEl->getAttribute("Ref"), true, false));
            }

            // Array of id of primary key(s) and foreign key(s)
            foreach($this->getForeignKeyList($entity->getId()) as $key) {
                $entity->addKey(new Key($key["id"], false, true, $key['enityIdTarget'], $key['attributeIdTarget']));
            }

            // Parse columns
            foreach ($table->getElementsByTagName("Column") as $column) {

                if ($column instanceof \DOMElement) {

                    if ($column->getAttribute("Id") != NULL) {
                        $attribute = new Attribute();

                        $attribute->setId($column->getAttribute("Id"));

                        // Check if attribute name isn't a reseved keyword of MySqsl
                        $attName = $this->getNodeValue($column->getElementsByTagName("Code")->item(0));
                        if (!in_array( strtoupper($attName), $this->reservedKeyword)) {
                            $attribute->setName($attName);
                        } else {
                            return array("errorMessages" => "Attribute name '" . $attName . "' of the table '" . $entity->getName() . "' use a MySql reserved keyword !");
                        }


                        $attribute->setComment($this->getNodeValue($column->getElementsByTagName("Comment")->item(0)));

                        try {
                            $attribute->setType($this->geTypeForDoctrine($this->getNodeValue($column->getElementsByTagName("DataType")->item(0))));

                            if ($attribute->getType() === NULL) {
                                throw new \Exception("Type not defined in Table :" . $entity->getName() . " for column : " . $attribute->getName(), 500);
                            }

                            if($attribute->getType() == 'decimal') {
                                $attribute->setPrecision($this->getNodeValue($column->getElementsByTagName("Length")->item(0)));
                                $attribute->setScale($this->getNodeValue($column->getElementsByTagName("Precision")->item(0)));
                            } else {
                                $attribute->setLength($this->getNodeValue($column->getElementsByTagName("Length")->item(0)));
                                $attribute->setPrecision($this->getNodeValue($column->getElementsByTagName("Precision")->item(0)));
                            }
                            



                            

                            $attribute->setIsIdentifier($this->getNodeValue($column->getElementsByTagName("Identity")->item(0)));

                            if ($this->getNodeValue($column->getElementsByTagName("Code")->item(0)) == 1) {
                                $attribute->setIsNullAble(false);
                            } else {
                                $attribute->setIsNullAble(true);
                            }

                            

                            if ($entity->getKey($attribute->getId()) != null) {
                                if($entity->getKey($attribute->getId())->isPrimary()) {

                                    $attribute->setIsPrimary(true);
                                    $attribute->setIsUnique(true);
                                    $attribute->setIsNullAble(false);
                                    
                                } elseif($entity->getKey($attribute->getId())->isForeign()) {

                                    $attribute->setForeignKey($entity->getKey($attribute->getId())->getEntityIdTarget());
                                    $attribute->setIsPrimary(false);
                                    $attribute->setIsUnique(false);
                                    
                                }
                            } else {
                                $attribute->setIsPrimary(false);
                                $attribute->setIsUnique(false);
                            }

                            $entity->addAttribute($attribute);

                        } catch (\Exception $e) {
                            echo "\n\n";
                            print_r($e->getMessage());
                            echo "\n";
                        }

                    }

                }

            }

            $entityList->addEnity($entity);
        } // END of parsing Table


        // Parse References
        $references = $this->xPath->query("//c:References/*");

        foreach ($references as $reference) {

            $relationship = new Relationship();

            $relationship->setId($reference->getAttribute("Id"));

            $relationship->setName($this->getNodeValue($reference->getElementsByTagName("Code")->item(0)));

//            $parentTable = $reference->getElementsByTagName("ParentTable")->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref");
            $relationship->setEntityOwner($reference->getElementsByTagName("ParentTable")->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref"));
//            $childTable = $reference->getElementsByTagName("ChildTable")->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref");
            $relationship->setEntityInRelationship($reference->getElementsByTagName("ChildTable")->item(0)->getElementsByTagName("Table")->item(0)->getAttribute("Ref"));

            $relationship->setCardinality($this->conf["cardinality"][$this->getNodeValue($reference->getElementsByTagName("Cardinality")->item(0))]);

            $relationshipNonOwner = clone $relationship;
            if($relationshipNonOwner->getCardinality() == "oneToMany") {
                $relationshipNonOwner->setCardinality("manyToOne");
            }

            if($relationship->getCardinality() == "oneToOne") {
                $relationship->setCardinality("ownerOneToOne");
            }

            // Add the relationShip to his Owner
            $entityList->getEntityById($relationship->getEntityOwner())->addRelationship($relationship);
            // Add the relationShip to the non Owner
            $entityList->getEntityById($relationship->getEntityInRelationship())->addRelationship($relationshipNonOwner);

        }

        return array("entities" => $entityList, "infomationMessages" => $infomationMessages);
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
        $attributeFK->setID($attributeToDuplicate->getId() . count($entityParent->getAttributes()));
        $attributeFK->setName($attributeToDuplicate->getName());
        $attributeFK->setComment($attributeToDuplicate->getComment());
        $attributeFK->setType($attributeToDuplicate->getType());
        $attributeFK->setLength($attributeToDuplicate->getLength());
        $attributeFK->setPrecision($attributeToDuplicate->getPrecision());
        $attributeFK->setDefaultValue($attributeToDuplicate->getDefaultValue());
        $attributeFK->setIsIdentifier(false);
        $attributeFK->setIsNullAble(true);
        $attributeFK->setIsPrimary(false);
        $attributeFK->setIsUnique(false);

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

        $indexes = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index");

        foreach ($indexes as $index) {
            $indexID = $index->getAttribute("Id");
            $code = $this->getNodeValue($index->getElementsByTagName("Code")->item(0));
            $type = substr($code, (strlen($code) - 2), strlen($code));

            if ($type == "PK") {

                $attributesIDs = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index[@Id='" . $indexID . "']/c:IndexColumns/o:IndexColumn/c:Column/o:Column");
                foreach ($attributesIDs as $attributeID) {
                    $aAttributPK[] = $attributeID->getAttribute("Ref");
                }
            } elseif ($type == "FK") {
                $attributesIDs = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index[@Id='" . $indexID . "']/c:IndexColumns/o:IndexColumn/c:Column/o:Column");
                foreach ($attributesIDs as $attributeID) {
                    $aAttributFK[] = $attributeID->getAttribute("Ref");
                }
            }
        }

        if (count($aAttributPK) !== count($aAttributFK)) {
            return false;
        }

        foreach ($aAttributPK as $att) {
            if (!in_array($att, $aAttributFK)) {
                return false;
            }
        }

        return true;
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

        $columns = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Columns/o:Column");
        foreach ($columns as $column) {
            $aIDColumn[] = $column->getAttribute("Id");
        }

        $keys = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Keys/o:Key/c:Key.Columns/o:Column");
        foreach ($keys as $key) {
            $aIDKey[] = $key->getAttribute("Ref");
        }

        if (count($aIDKey) !== count($aIDColumn)) {
            return false;
        }

        foreach ($aIDColumn as $att) {
            if (!in_array($att, $aIDKey)) {
                return false;
            }
        }

        return true;
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

        $indexes = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index");

        foreach ($indexes as $index) {
            $code = $this->getNodeValue($index->getElementsByTagName("Code")->item(0));
            $type = substr($code, (strlen($code) - 2), strlen($code));
            if ($type == "FK") {
                $nbFK++;
            }
        }

        return $nbFK;
    }

    /**
     * Return an array of id attributes who are ForeingKey
     * @param string $idTable
     * @return array()
     */
    protected function getForeignKeyList($idTable)
    {
        $keys = array();
        $indexes = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index");

        foreach ($indexes as $index) {
            $indexID = $index->getAttribute("Id");
            $reference = $index->getElementsByTagName("LinkedObject")
                    ->item(0)
                    ->getElementsByTagName("Reference")
                    ->item(0);

            $code = $this->getNodeValue($index->getElementsByTagName("Code")->item(0));
            $type = strtolower(substr($code, (strlen($code) - 2), strlen($code)));

            if ($type == "fk") {
                
                $attributesIDs = $this->xPath->query("//c:Tables/o:Table[@Id='" . $idTable . "']/c:Indexes/o:Index[@Id='" . $indexID . "']/c:IndexColumns/o:IndexColumn/c:Column/o:Column");

                foreach ($attributesIDs as $attributeID) {

                    $entityIdTarget = null;
                    $attributeIdTarget = null;

                    if($reference != null) {
                        $reference = $reference->getAttribute("Ref");

                        // Check if we dont fond the id of attribute in the ParentTable
                        $parentTable = $this->xPath->query('//c:References/o:Reference[@Id="' . $reference . '"]/c:ParentTable/o:Table')->item(0)->getAttribute("Ref");
                        
                        if($this->isColumnbelongsThisTable($attributeID->getAttribute("Ref"), $parentTable) === false) {
                            
                            $entityIdTarget = $parentTable;
                            $attributeIdTarget = $this->xPath->query('//c:References/o:Reference[@Id="' . $reference . '"]/c:Joins/o:ReferenceJoin/c:Object1/o:Column')->item(0)->getAttribute("Ref");

                        } else {
                            // Check if we dont fond the id of attribute in the ChildTable
                            $childTable = $this->xPath->query('//c:References/o:Reference[@Id="' . $reference . '"]/c:ChildTable/o:Table')->item(0)->getAttribute("Ref");

                            if($this->isColumnbelongsThisTable($attributeID->getAttribute("Ref"), $childTable) === false) {
                                $entityIdTarget = $childTable;
                                $attributeIdTarget = $this->xPath->query('//c:References/o:Reference[@Id="' . $reference . '"]/c:Joins/o:ReferenceJoin/c:Object2/o:Column')->item(0)->getAttribute("Ref");
                            }
                        }

                        $keys[] = array("id" => $attributeID->getAttribute("Ref"), "enityIdTarget" => $entityIdTarget, "attributeIdTarget" => $attributeIdTarget);
                    }

                } // end foreach $attributesIDs
                
            } // end if "fk"
        } // end foreach $indexes

        return $keys;
    }



    private function isColumnbelongsThisTable($idColumn, $idTable) {
        return $this->xPath->query('//c:Tables/o:Table[@Id="' . $idTable . '"]/c:Columns/o:Column[@Id="' . $idColumn . '"]')->item(0) !== null;
    }

}
