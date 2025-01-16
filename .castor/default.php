<?php

use Castor\Attribute\AsTask;

use function app\cache_clear;
use function app\install;
use function Castor\context;
use function Castor\fs;
use function Castor\io;
use function Castor\notify;
use function Castor\run;
use function Castor\variable;
use function db\migrate;
use function docker\build;
use function docker\up;
use function docker\worker\workers_start;
use function docker\worker\workers_stop;
use function ssl\generate_certificates;

/**
 * @return array<string, mixed>
 */
function create_default_variables(): array
{
    return [
        'project_name' => 'recettes',
        'root_domain' => 'recettes.test',
        'php_version' => '8.3',
        'extra_domains' => [],
    ];
}

#[AsTask(description: 'Builds and starts the infrastructure, then install the application (composer, yarn, ...)')]
function start(bool $test = false): void
{
    io()->title('Starting the stack');

    fs()->symlink('.env.docker', '.env.local');
    file_put_contents('.env.test.local', <<<EOF
    DATABASE_URL="postgres://symfony:password@postgres:5432/symfony_db"
    EOF
    );

    workers_stop();
    generate_certificates();
    build();
    up();
    cache_clear();
    install();
    migrate($test);
    workers_start();

    notify('The stack is now up and running.');
    io()->success('The stack is now up and running.');

    about();
}

#[AsTask(description: 'Displays some help and available urls for the current project')]
function about(): void
{
    io()->title('About this project');

    io()->comment('Run <comment>castor</comment> to display all available commands.');
    io()->comment('Run <comment>castor about</comment> to display this project help.');
    io()->comment('Run <comment>castor help [command]</comment> to display Castor help.');

    io()->section('Available URLs for this project:');
    $urls = [variable('root_domain'), ...variable('extra_domains')];

    $payload = @file_get_contents(sprintf('http://%s:8080/api/http/routers', variable('root_domain')));
    if ($payload) {
        $routers = json_decode($payload, true);
        $projectName = variable('project_name');
        foreach ($routers as $router) {
            if (!preg_match("{^{$projectName}-(.*)@docker$}", $router['name'])) {
                continue;
            }
            if ("frontend-{$projectName}" === $router['service']) {
                continue;
            }
            if (!preg_match('{^Host\\(`(?P<hosts>.*)`\\)$}', $router['rule'], $matches)) {
                continue;
            }
            $hosts = explode('`) || Host(`', $matches['hosts']);
            $urls = [...$urls, ...$hosts];
        }
    }
    io()->listing(array_map(fn ($url) => "https://{$url}", $urls));
}

#[AsTask(description: 'Opens the project in your browser')]
function open(): void
{
    run(['open', 'https://' . variable('root_domain')], context: context()->withQuiet());
}
