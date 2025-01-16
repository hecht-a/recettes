<?php

namespace App\Infra\Maker;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('do:entity')]
class MakeEntityCommand extends AbstractMakeCommand
{
    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Créer une entité dans le domaine choisi')
            ->addArgument('entityName', InputArgument::REQUIRED, "Nom de l'entité")
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domain = $this->askDomain($io);
        $entity = ucfirst((string) $input->getArgument('entityName'));

        /** @var Application $application */
        $application = $this->getApplication();
        $command = $application->find('make:entity');
        $arguments = [
            'command' => 'make:entity',
            'name' => "\\App\\Domain\\$domain\\Entity\\$entity",
        ];
        $greetInput = new ArrayInput($arguments);

        return $command->run($greetInput, $output);
    }
}
