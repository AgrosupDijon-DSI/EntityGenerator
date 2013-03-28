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

Entity Sample
-------------
This entity is an extract from the [sakila database](http://dev.mysql.com/doc/sakila/en/sakila.html)

~~~~~ php
namespace CNERTA\coucouBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="film")
 * @ORM\Entity
 */
class Film
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="film_id", type="smallint", nullable=true, unique=TRUE)
     */
     private $IDFilmId;


    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     *
     * @var string $title 
     */
     private $title;


    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @var text $description 
     */
     private $description;


    /**
     * @ORM\Column(name="release_year", type="datetime", nullable=true)
     *
     * @var datetime $releaseYear 
     */
     private $releaseYear;


    /**
     * @ORM\ManyToOne(targetEntity="Language", cascade={"persist", "remove", "merge"})
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="language_id", referencedColumnName="language_id")
     * })
     *
     * @var ArrayCollection Language $language 
     */
     private $language;


    /**
     * @ORM\Column(name="original_language_id", type="smallint", nullable=true)
     *
     * @var smallint $originalLanguageId 
     */
     private $originalLanguageId;


    /**
     * @ORM\Column(name="rental_duration", type="smallint", nullable=true)
     *
     * @var smallint $rentalDuration 
     */
     private $rentalDuration;

     
    /**
     * @ORM\Column(name="rental_rate", type="decimal", precision=4, scale=2, nullable=true)
     *
     * @var decimal $rentalRate 
     */
     private $rentalRate;


    /**
     * @ORM\Column(name="length", type="smallint", nullable=true)
     *
     * @var smallint $length 
     */
     private $length;

     
        /**
     * @ORM\Column(name="replacement_cost", type="decimal", precision=5, scale=2, nullable=true)
     *
     * @var decimal $replacementCost 
     */
     private $replacementCost;


    /**
     * @ORM\Column(name="rating", type="array", nullable=true)
     *
     * @var array $rating 
     */
     private $rating;


    /**
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     *
     * @var datetime $lastUpdate 
     */
     private $lastUpdate;


    /**
     * @ORM\OneToMany(targetEntity="Inventory", mappedBy="film", cascade={"persist", "remove", "merge"})
     *
     * @var Inventory $inventorys 
     */
     private $inventorys;


    /**
     * @return smallint $IDFilmId 
     */
    public function getFilmId()
    {
        return $this->IDFilmId;
    }


    /**
     * @param string $title 
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string $title 
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @param text $description 
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return text $description 
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param datetime $releaseYear 
     */
    public function setReleaseYear($releaseYear)
    {
        $this->releaseYear = $releaseYear;
    }

    /**
     * @return datetime $releaseYear 
     */
    public function getReleaseYear()
    {
        return $this->releaseYear;
    }


    /**
     * @param Language $language 
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @return Language $language 
     */
    public function getLanguage()
    {
        return $this->language;
    }


    /**
     * @param smallint $originalLanguageId 
     */
    public function setOriginalLanguageId($originalLanguageId)
    {
        $this->originalLanguageId = $originalLanguageId;
    }

    /**
     * @return smallint $originalLanguageId 
     */
    public function getOriginalLanguageId()
    {
        return $this->originalLanguageId;
    }


    /**
     * @param smallint $rentalDuration 
     */
    public function setRentalDuration($rentalDuration)
    {
        $this->rentalDuration = $rentalDuration;
    }

    /**
     * @return smallint $rentalDuration 
     */
    public function getRentalDuration()
    {
        return $this->rentalDuration;
    }

     
    /**
     * @param decimal $rentalRate 
     */
    public function setRentalRate($rentalRate)
    {
        $this->rentalRate = $rentalRate;
    }

    /**
     * @return decimal $rentalRate 
     */
    public function getRentalRate()
    {
        return $this->rentalRate;
    }


    /**
     * @param smallint $length 
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return smallint $length 
     */
    public function getLength()
    {
        return $this->length;
    }

     
    /**
     * @param decimal $replacementCost 
     */
    public function setReplacementCost($replacementCost)
    {
        $this->replacementCost = $replacementCost;
    }

    /**
     * @return decimal $replacementCost 
     */
    public function getReplacementCost()
    {
        return $this->replacementCost;
    }


    /**
     * @param array $rating 
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return array $rating 
     */
    public function getRating()
    {
        return $this->rating;
    }


    /**
     * @param datetime $lastUpdate 
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return datetime $lastUpdate 
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }


    /**
     * @param Inventory $inventory 
     */
    public function addInventory(Inventory $inventory) {

        if(!$inventory->getFilms()->contains($this)) {
            $inventory->getFilms()->add($this);
        }

        // Si l'objet fait déjà partie de la collection on ne l'ajoute pas
        if (!$this->inventorys->contains($inventory)) {
            $this->inventorys->add($inventory);
        }
    }

    /**
     * @param Mix (ArrayCollection/Inventory) $items 
     */
    public function setInventory($items)
    {
        if ($items instanceof ArrayCollection) {
            foreach ($items as $item) {
                $this->inventorys->add($item);
            }
        } elseif ($items instanceof Inventory) {
            $this->addInventory($items);
        } else {
            throw new \Exception("$items must be an instance of Inventory or ArrayCollection");
        }
    }

    /**
     * @return ArrayCollection $inventorys 
     */
    public function getInventorys() {
        return $this->inventorys;
    }


    public function __construct() {
        $this->inventory = new ArrayCollection();
    }
}
~~~~~
