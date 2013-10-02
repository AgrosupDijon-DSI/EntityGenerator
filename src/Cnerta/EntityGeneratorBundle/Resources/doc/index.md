EntityGenerator
===============

Generalities
------------
- PHP 5.3
- PHP CLI
- Power AMC 12 or greater


Transform backup file to generic data
-------------------------------------
The first action of this tool is to parse the backup XML and convert to a basic object model entity<-attributes.
This action is made by the "PowerAmcMPDParserEngine" class.
In this class we defined the mapping configuration for data types.
You can easily add new data types or change the original mapping.

### Behaviors
If a table has only primary key, the tool treats the table as the central point of a manyToMany relation.
For determinate who is the owner of a manyToMany relation, we check which table has the most foreign key.

Below are defined how the relationships between entities :
- 0..* => "oneToMany",
- 1..* => "oneToMany",
- 0..1 => "oneToOne",
- 1..1 => "oneToOne"


Transform generic data to entities
----------------------------------
In a second time, the tool use the basic object model to create entities files and entities repositories file with TWIG templates.

"entity.php.twig" generate the entity
It manages the creation of :
- namespace
- Parameters (and his comment)
- Doctrine's annotations for each parameters (type, relation, constrained)
- getter/setter methods
- special methods for ArrayCollection objects (define relations between objects)

"entityRepository.php.twig"
It manages the creation of :
- namespace
- empty class

