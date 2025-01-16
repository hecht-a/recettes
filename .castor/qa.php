<?php

namespace qa;

use Castor\Attribute\AsArgument;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use function Castor\fs;
use function Castor\io;
use function db\fixtures;
use function docker\docker_compose_run;
use function docker\docker_exit_code;

#[AsTask(description: 'Runs all QA tasks')]
function all(): int
{
    install();
    $cs = cs();
    $phpstan = phpstan();
    $rector = rector();

    return max($cs, $phpstan, $rector);
}

#[AsTask(description: 'Installs tooling')]
function install(): void
{
    io()->title('Installing QA tooling');

    io()->section('Installing PHPUnit');
    docker_exit_code('vendor/bin/simple-phpunit install');

    io()->section('Installing PHP-CS-Fixer');
    docker_compose_run('composer install -o', workDir: '/var/www/tools/php-cs-fixer');

    io()->section('Installing PHPStan');
    docker_compose_run('composer install -o', workDir: '/var/www/tools/phpstan');
    fs()->symlink('.env.docker', '.env.local');

    io()->section('Installing Rector');
    docker_compose_run('composer install -o', workDir: '/var/www/tools/rector');
    fs()->symlink('.env.docker', '.env.local');
}

#[AsTask(description: 'Updates tooling')]
function update(): void
{
    io()->title('Update QA tooling');

    io()->section('Updating PHP-CS-Fixer');
    docker_compose_run('composer update', workDir: '/var/www/tools/php-cs-fixer');
    docker_compose_run('composer bump', workDir: '/var/www/tools/php-cs-fixer');

    io()->section('Updating PHPStan');
    docker_compose_run('composer update', workDir: '/var/www/tools/phpstan');
    docker_compose_run('composer bump', workDir: '/var/www/tools/phpstan');

    io()->section('Updating Rector');
    docker_compose_run('composer update', workDir: '/var/www/tools/rector');
    docker_compose_run('composer bump', workDir: '/var/www/tools/rector');
}

#[AsTask(description: 'Runs PHPUnit', aliases: ['phpunit'])]
function phpunit(
    #[AsOption(description: 'Run only the specified test', shortcut: 'f')]
    ?string $filter = null,
    #[AsOption(description: 'Run fixtures in test env')]
    bool $fixtures = false,
    #[AsArgument('path', 'File path to run phpstan on')]
    ?string $path = null,
): int {
    if ($fixtures) {
        fixtures(true);
    }

    if ($path) {
        return docker_exit_code('vendor/bin/simple-phpunit ' . $path);
    }

    if ($filter) {
        return docker_exit_code('vendor/bin/simple-phpunit --filter ' . $filter);
    }

    return docker_exit_code('vendor/bin/simple-phpunit');
}

#[AsTask(description: 'Runs PHPStan', aliases: ['phpstan'])]
function phpstan(
    #[AsArgument('path', 'File path to run phpstan on')]
    ?string $path = null,
    bool $generateBaseline = false,
    bool $clearCache = false,
): int {
    if ($clearCache) {
        docker_exit_code('phpstan clear-result-cache');
    }

    if ($generateBaseline) {
        return docker_exit_code('phpstan --generate-baseline');
    }

    if ($path) {
        return docker_exit_code('phpstan analyse -v ' . $path);
    }

    return docker_exit_code('phpstan -v');
}

#[AsTask(description: 'Runs Rector', aliases: ['rector'])]
function rector(bool $dryRun = false): int
{
    if ($dryRun) {
        return docker_exit_code('rector --dry-run');
    }

    return docker_exit_code('rector');
}

#[AsTask(description: 'Fixes Coding Style', aliases: ['cs'])]
function cs(bool $dryRun = false): int
{
    if ($dryRun) {
        return docker_exit_code('php-cs-fixer fix --dry-run --diff');
    }

    return docker_exit_code('php-cs-fixer fix');
}
