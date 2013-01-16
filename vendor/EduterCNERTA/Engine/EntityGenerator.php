<?php

namespace EduterCNERTA\Engine;

/**
 * Description of EntityGenerator
 * 
 * @author Valérian Girard <valerian.girard@educagri.fr>
 */
class EntityGenerator {

    function __construct() {
        
    }

    public function generateEntity($app, $aEntities, $namespace, $outputFolder, $createRepository) {

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



        foreach ($aEntities as $entity) {
            
            $entityClass = $app['twig']->render('entity.php.twig', array(
                "namespace" => $namespace,
                "entity" => $entity
                    ));

            if($createRepository) {
                $entityRepositoryClass = $app['twig']->render('entityRepository.php.twig', array(
                    "namespace" => $namespace,
                    "entity" => $entity
                        ));
            }

            $file = fopen($outputFolder . "Entity/" . $app['twig']->getExtension('bodTwigExt')->twig_entityname_filter($entity->getName()) . ".php", 'a');
            fwrite($file, $entityClass);
            fclose($file);

            if($createRepository) {
                $file = fopen($outputFolder . "EntityRepository/" . $app['twig']->getExtension('bodTwigExt')->twig_entityname_filter($entity->getName()) . "Repository.php", 'a');
                fwrite($file, $entityRepositoryClass);
                fclose($file);
            }
        }
    }

}

