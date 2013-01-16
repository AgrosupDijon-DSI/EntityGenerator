#!/usr/bin/env php
<?php
//Bootstrap our Silex application
$app = require __DIR__ . '/src/bootstrap.php';

//Include the namespaces of the components we plan to use
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//Instantiate our Console application
$console = new Application('entity generator', '0.1');

//Register a command to run from the command line
//Our command will be started with "./console.php sync"
$console->register('entity:generator')
    ->setDefinition(array(
        //Create a "--test" optional parameter
        new InputOption('file', 'Path of the PowerAmc MPD backup', InputOption::VALUE_REQUIRED, ''),
        new InputOption('namespace', 'NameSpace for the entities (use slash "/" instead of backslash "\\")', InputOption::VALUE_REQUIRED, ''),
        new InputOption('output', 'Path for the output folder', InputOption::VALUE_REQUIRED, ''),
        new InputOption('createRepository', 'Create the Repository class', InputOption::VALUE_OPTIONAL, 'FALSE'),
    ))
    ->setDescription('Generate entities from Power AMC MPD xml backup file <info>v1.0</info>')
    ->setHelp('Usage: <info>./console.php entity:generator --file="~/model.PDM" --namespace="a/name/space" --output="~/project/"</info>')
    ->setCode(
        function(InputInterface $input, OutputInterface $output) use ($app) {
            $hasError = FALSE;
            if (!$input->getOption('file')) {
                $output->write("<error>You must set a file path</error>\n\n");
                $hasError = TRUE;
            } else {
                if(is_file($input->getOption('file'))) {
                    if(!is_readable($input->getOption('file'))) {
                        $output->write("<error>The file is not readable</error>\n\n");
                        $hasError = TRUE;
                    }
                } else {
                    $output->write("<error>Wrong path for the file</error>\n\n");
                    $hasError = TRUE;
                }
            }
            if (!$input->getOption('namespace')) {
                $output->write("<error>You must set a nemespace</error>\n\n");
                $hasError = TRUE;
            }
            if (!$input->getOption('output')) {
                $output->write("<error>You must set the path for the output folder</error>\n\n");
                $hasError = TRUE;
            }

            if($hasError) {
                exit;
            }


            $engine = new \EduterCNERTA\Engine\PowerAmcMPDParserEngine($input->getOption('file'));
            $aParserResult = $engine->parseEntity();

            if(isset($aParserResult["infomationMessages"]) && $aParserResult["infomationMessages"] != "") {
                $output->write("<error>" . $aParserResult["infomationMessages"] . "</error>\n");
            }
            if(isset($aParserResult["errorMessages"]) && $aParserResult["errorMessages"] != "") {
                $output->write("<error>" . $aParserResult["errorMessages"] . "</error>\n");
                exit;
            }

            $entityGenerator = new EduterCNERTA\Engine\EntityGenerator();
            $entityGenerator->generateEntity($app, $aParserResult["entities"], $input->getOption('namespace'), $input->getOption('output'), $input->getOption('createRepository'));


            $output->write("\n<info>Done</info>\n");
        }
);

$console->run();