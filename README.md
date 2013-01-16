EntityGenerator
===============

EntityGenerator transform Physical Data Model (optimized for MySql) from PowerAMC software to Doctrine's entities directly usable with Symfony 2.
EntityGenerator provide relations between entities with annotation.

For now, only manyToMany relations who own more than two relations dosen't work good.


How it work (quickly)
-----------
This project use Silex, the PHP micro-framework based on the Symfony2 Components.

From the MPD backup file of PowerDesigner (this is a flat XML), the EntityGenerator extract informations about tables models and her relations.
Is extract from tables models :
- table code (a unique identifier of the table name), comment
- columns name (a unique identifier of column name), type, length and comment

After extraction, EntityGenerator create entities files who for each parameter has his annotation for use with Doctrine 2.
And for each entity an EntityRepository is create.
With this entities files we can easily create database with Doctrine's commands lines.


EntityGenerator is only usable with commands lines.

If your MPD has ManyToMany relations who own more than two relations, EntityGenerator will tell you what relations will not be created.

[-> How it works in detail](src/doc/index.md)

What is ignored during transformation
-------------------------------------
- trigger
- view
- enum type (not yet implemented in Doctrine)
- ManyToMany relations who own more than two relations


Install
-------
Just download the package and use it !


Commands lines
--------------
Usage: `php console.php entity:generator --file="~/model.MPD" --namespace="a/name/space" --output="~/project/"`
- `--file` is the path of the PowerAMC PDM backup
- `--namespace` is the namespace for entities (use slash "/" instead of backslash "\\")
- `--output` is the path for the output folder


What next ?
-----------
In the future we hope we can manage manyToMany relation who own more than two relations.

Order by name the propoerties
Adding pre_persis and pre_update

TODO URGENT : Pour avoir la précision exacte il faut aditionner la précision et le scale
TODO dans la class SelectAlimPerso le code pour aliment est le suivant, mais le ID du JoinColumn est en double
/**
     * @ORM\ManyToOne(targetEntity="Aliment", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="ID", referencedColumnName="ID")
     * })
     *
     * @var Aliment $aliment Identifiant auto
     */
     private $aliment;




Change : 
[2013-01-16] when a getter return an array, he is suffix with "List"
[2012-05-22] chaining possibility on set methods
