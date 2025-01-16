<?php

namespace db;

use Castor\Attribute\AsTask;

use function Castor\io;
use function docker\docker_compose_run;

#[AsTask(description: 'Migrates database schema', aliases: ['migrate'])]
function migrate(bool $test = false): void
{
    io()->title('Migrating the database schema');

    $env = $test ? 'test' : 'dev';
    docker_compose_run("bin/console doctrine:database:create --if-not-exists --env={$env}");
    docker_compose_run("bin/console doctrine:migration:migrate -n --allow-no-migration --env={$env}");
}

#[AsTask(description: 'Migrates database schema', aliases: ['fixtures'])]
function fixtures(bool $test = false): void
{
    io()->title('Populating the database with fixtures');

    $env = $test ? 'test' : 'dev';
    docker_compose_run("bin/console doctrine:fixture:load -n --env={$env}");
}
