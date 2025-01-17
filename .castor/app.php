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
        if (str_contains($file, '~')) {
            return;
        }

        if (!isset($eventCounts[$file])) {
            $eventCounts[$file] = 0;
        }

        ++$eventCounts[$file];

        if (2 === $eventCounts[$file]) {
            docker_compose_run('rm -rf public/assets', c: $c);
            $exitCode = docker_compose_run('bin/console sass:build', c: $c)->getExitCode();

            if (0 === $exitCode) {
                docker_compose_run('bin/console asset-map:compile', c: $c);
                io()->writeln("File <comment>$file</comment> has been $action.");
            }

            $eventCounts[$file] = 0;
        }
    });
}
