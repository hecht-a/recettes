<?php

namespace App\Infra\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('do:controller')]
class MakeControllerCommand extends AbstractMakeCommand
{
    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Créer un controller')
            ->addArgument('controllerName', InputArgument::REQUIRED, 'Nom du controller')
            ->addOption('api', null, InputOption::VALUE_NONE, "Génère un controller pour l'API")
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $controllerPath = ucfirst((string) $input->getArgument('controllerName'));
        if (!str_ends_with($controllerPath, 'Controller')) {
            $controllerPath .= 'Controller';
        }
        if (!is_string($controllerPath)) {
            throw new \RuntimeException('controllerPath doit être une chaine de caractère');
        }
        $parts = explode('/', $controllerPath);
        if (1 === count($parts)) {
            $namespace = '';
            $className = $parts[0];
        } else {
            $namespace = '\\' . implode('\\', array_slice($parts, 0, -1));
            $className = $parts[count($parts) - 1];
        }

        $api = $input->getOption('api');
        $basePath = false === $api ? 'src/Http/Controller/' : 'src/Http/Api/Controller/';

        $params = [
            'namespace' => $namespace,
            'class_name' => $className,
            'api' => $api,
        ];

        $this->createFile('controller', $params, "{$basePath}{$controllerPath}.php");

        $io->success('Le controller a bien été créé');

        return Command::SUCCESS;
    }
}
