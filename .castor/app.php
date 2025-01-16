<?php

namespace app;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\watch;
use function docker\docker_compose_run;

#[AsTask(description: 'Installs the application (composer, yarn, ...)', aliases: ['install'])]
function install(): void
{
    io()->title('Installing the application');

    io()->section('Installing PHP dependencies');
    docker_compose_run('composer install -n --prefer-dist --optimize-autoloader');

    yarn_install();
    yarn_build();

    \qa\install();
}

#[AsTask(description: 'Update the application (composer)', aliases: ['Update'])]
function update_php(bool $withTools = false): void
{
    io()->title('Updating the application');

    io()->section('Updating PHP dependencies');
    docker_compose_run('composer update');
    docker_compose_run('composer bump');

    \qa\update();
}

#[AsTask(description: 'Installs yarn dependencies', name: 'yarn:install', aliases: ['yarn'])]
function yarn_install(): void
{
    io()->title('Installing yarn dependencies');
    docker_compose_run('yarn install --frozen-lockfile');
}

#[AsTask(description: 'Builds yarn dependencies', name: 'yarn:build', aliases: ['yarn-build'])]
function yarn_build(
    #[AsOption(description: 'Run in watch mode')]
    bool $watch = false,
): void
{
    if ($watch) {
        io()->title('Watching yarn dependencies');
        docker_compose_run('yarn encore dev --watch');

        return;
    }

    io()->title('Building yarn dependencies');
    if ('ci' == context()->name) {
        docker_compose_run('yarn encore dev', c: context()->withAllowFailure());
    } else {
        docker_compose_run('yarn build');
    }
}

#[AsTask(description: 'Clear the application cache', aliases: ['cache-clear'])]
function cache_clear(): void
{
    io()->title('Clearing the cache');

    docker_compose_run('rm -rf var/cache/ && bin/console cache:warmup');
}

#[AsTask]
function asset_watcher(): void
{
    $c = context()->withAllowFailure();

    docker_compose_run('bin/console sass:build', c: $c);
    docker_compose_run('bin/console asset-map:compile', c: $c);

    $eventCounts = [];
    watch('assets/...', function (string $file, string $action) use (&$eventCounts, $c) {
        $file = str_replace('/home/ahecht/Documents/projets/cerfrance/cerfrance-analytics/', '', $file);
        if (str_contains($file, '~')) {
            return;
        }

        if (!isset($eventCounts[$file])) {
            $eventCounts[$file] = 0;
        }

        $eventCounts[$file]++;

        if ($eventCounts[$file] === 2) {
            docker_compose_run('rm -rf public/assets', c: $c);
            $exitCode = docker_compose_run('bin/console sass:build', c: $c)->getExitCode();

            if ($exitCode === 0) {
                docker_compose_run('bin/console asset-map:compile', c: $c);
                io()->writeln("File <comment>$file</comment> has been $action.");
            }

            $eventCounts[$file] = 0;
        }
    });
}
