<?php

namespace App\Infra\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('do:crud')]
class MakeCrudCommand extends AbstractMakeCommand
{
    protected function configure(): void
    {
        $this
            ->setDescription('Créer des controllers pour un CRUD')
            ->addArgument('controllerName', InputArgument::OPTIONAL, 'Nom du controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domain = ucfirst($this->askControllerDomain($io));

        $methods = ['create', 'read', 'update', 'delete'];

        $controllerName = $input->getArgument('controllerName');

        if (null === $controllerName) {
            $controllerName = $this->ask($io, 'Saisir le nom du crud');
        }

        $controllerName = ucfirst($controllerName);
        if (!str_ends_with($controllerName, 'Controller')) {
            $controllerName .= 'Controller';
        }
        if (!is_string($controllerName)) {
            throw new \RuntimeException('controllerName doit être une chaine de caractère');
        }
        $parts = explode('/', $controllerName);
        if (1 === count($parts)) {
            $namespace = '';
            $className = $parts[0];
        } else {
            $namespace = '\\'.implode('\\', array_slice($parts, 0, -1));
            $className = $parts[count($parts) - 1];
        }

        $basePath = "src/Http/Controller/{$domain}/crud";

        foreach ($methods as $method) {
            $method = ucfirst($method);

            $params = [
                'namespace' => $namespace,
                'class_name' => "{$method}{$className}",
            ];

            $this->createFile('crud', $params, "{$basePath}/{$method}{$controllerName}.php");
        }

        $io->success('Le crud a bien été créé');

        return Command::SUCCESS;
    }
}
