<?php

namespace App\Infra\Maker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment;

abstract class AbstractMakeCommand extends Command
{
    public function __construct(private readonly Environment $twig, protected string $projectDir)
    {
        parent::__construct();
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function createFile(string $template, array $params, string $output): void
    {
        $content = $this->twig->render("@maker/$template.twig", $params);
        $filename = "{$this->projectDir}/{$output}";
        $directory = dirname($filename);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        file_put_contents($filename, $content);
    }

    /**
     * On construit la liste utilisée pour l'autocompletion.
     *
     * @return string[]
     */
    public function getDomains(string $path): array
    {
        $domains = [];
        $files = (new Finder())->in($path)->depth(0)->directories();
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $domains[] = $file->getBasename();
        }

        return $domains;
    }

    /**
     * Demande à l'utilisateur de choisir un domaine.
     */
    protected function askDomain(SymfonyStyle $io): string
    {
        $domains = $this->getDomains("{$this->projectDir}/src/Domain");

        return $this->ask($io, 'Sélectionner un domain', $domains);
    }

    /**
     * Demande à l'utilisateur de choisir un domaine de controller.
     */
    protected function askControllerDomain(SymfonyStyle $io): string
    {
        $domains = $this->getDomains("{$this->projectDir}/src/Http/Controller");

        return $this->ask($io, 'Sélectionner un domaine de controller', $domains);
    }

    /**
     * Pose une question à l'utilisateur.
     *
     * @param ?iterable<string> $values
     */
    protected function ask(SymfonyStyle $io, string $question, ?iterable $values = null): string
    {
        $q = new Question($question);

        if ($values) {
            $q->setAutocompleterValues($values);
        }

        return (string) $io->askQuestion($q);
    }
}
