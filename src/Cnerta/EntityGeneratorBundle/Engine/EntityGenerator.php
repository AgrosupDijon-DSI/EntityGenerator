<?php

namespace EntityGeneratorBundle\Cnerta\Engine;

use EntityGeneratorBundle\Cnerta\Model\EntityList;

/**
 * Description of EntityGenerator
 * 
 * @author Valérian Girard <valerian.girard@educagri.fr>
 */
class EntityGenerator
{

    function __construct()
    {
        
    }

    public function generateEntity($app, EntityList $entityList, $namespace, $outputFolder, $createRepository)
    {

        $namespace = (substr($namespace, (strlen($namespace) - 1), strlen($namespace)) == "/" ) ? $namespace : $namespace . "/";
        $namespace = str_replace("/", "\\", $namespace);

        if (substr($outputFolder, (strlen($outputFolder) - 1), 1) != "/") {
            $outputFolder = $outputFolder . "/";
        }

        if (!is_dir($outputFolder . "Entity/")) {
            mkdir($outputFolder . "Entity/");
        }
        if (!is_dir($outputFolder . "EntityRepository/")) {
            mkdir($outputFolder . "EntityRepository/");
        }

        foreach ($entityList->getEntityList() as $entity) {
//            if($entity->getName() == "categorie_materiel") {
                
            if ($entity->isARelationManyToManyBetweenTwoEntity() === false) {
                $entityClass = $app['twig']->render('entity.php.twig', array(
                    "namespace" => $namespace,
                    "currentEntity" => $entity,
                    "entityList" => $entityList
                        ));

                if ($createRepository) {
                    $entityRepositoryClass = $app['twig']->render('entityRepository.php.twig', array(
                        "namespace" => $namespace,
                        "entity" => $entity
                            ));
                }

                $file = fopen($outputFolder . "Entity/" . $app['twig']->getExtension('bodTwigExt')->twig_entityname_filter($entity->getName()) . ".php", 'a');
                fwrite($file, $entityClass);
                fclose($file);

                if ($createRepository) {
                    $file = fopen($outputFolder . "EntityRepository/" . $app['twig']->getExtension('bodTwigExt')->twig_entityname_filter($entity->getName()) . "Repository.php", 'a');
                    fwrite($file, $entityRepositoryClass);
                    fclose($file);
                }
            }
        }
    }

}

