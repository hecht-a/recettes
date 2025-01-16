<?php

namespace docker;

use Castor\Attribute\AsContext;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Castor\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Process;

use function Castor\cache;
use function Castor\capture;
use function Castor\context;
use function Castor\finder;
use function Castor\fs;
use function Castor\io;
use function Castor\log;
use function Castor\run;
use function Castor\variable;

#[AsTask(description: 'Builds the infrastructure', aliases: ['build'])]
function build(): void
{
    io()->title('Building infrastructure');

    $userId = variable('user_id');
    $phpVersion = variable('php_version');

    $command = [
        'build',
        '--build-arg', "USER_ID={$userId}",
        '--build-arg', "PHP_VERSION={$phpVersion}",
    ];

    docker_compose($command, withBuilder: true);
}

#[AsTask(description: 'Builds and starts the infrastructure', aliases: ['up'])]
function up(): int
{
    io()->title('Starting infrastructure');

    try {
        docker_compose(['up', '--detach', '--no-build']);

        return Command::SUCCESS;
    } catch (ExceptionInterface $e) {
        io()->error('An error occured while starting the infrastructure.');
        io()->note('Did you forget to run "castor docker:build"?');
        io()->note('Or you forget to login to the registry?');

        throw $e;
    }
}

#[AsTask(description: 'Stops the infrastructure', aliases: ['stop'])]
function stop(): void
{
    io()->title('Stopping infrastructure');

    docker_compose(['stop']);
}

#[AsTask(description: 'Opens a shell (bash) into a builder container', aliases: ['builder'])]
function builder(): void
{
    $c = context()
        ->withTimeout(null)
        ->withTty()
        ->withEnvironment($_ENV + $_SERVER)
        ->withAllowFailure()
    ;
    docker_compose_run('bash', c: $c);
}

#[AsTask(description: 'Displays infrastructure logs', aliases: ['logs'])]
function logs(): void
{
    docker_compose(['logs', '-f', '--tail', '150'], c: context()->withTty());
}

#[AsTask(description: 'Lists containers status', aliases: ['ps'])]
function ps(): void
{
    docker_compose(['ps']);
}

#[AsTask(description: 'Cleans the infrastructure (remove container, volume, networks)', aliases: ['destroy'])]
function destroy(
    #[AsOption(description: 'Force the destruction without confirmation', shortcut: 'f')]
    bool $force = false,
): void {
    io()->title('Destroying infrastructure');

    if (!$force) {
        io()->warning('This will permanently remove all containers, volumes, networks... created for this project.');
        io()->note('You can use the --force option to avoid this confirmation.');
        if (!io()->confirm('Are you sure?', false)) {
            io()->comment('Aborted.');

            return;
        }
    }

    docker_compose(['down', '--remove-orphans', '--volumes', '--rmi=local'], withBuilder: true);
    $files = finder()
        ->in(variable('root_dir') . '/infrastructure/docker/services/router/certs/')
        ->name('*.pem')
        ->files()
    ;
    fs()->remove($files);
}

#[AsContext(default: true)]
function create_default_context(): Context
{
    $data = create_default_variables() + [
        'project_name' => 'app',
        'root_domain' => 'app.test',
        'extra_domains' => [],
        'php_version' => '8.2',
        'docker_compose_files' => [
            'compose.router.yml',
            'compose.worker.yml',
            'compose.yml',
        ],
        'macos' => false,
        'power_shell' => false,
        'user_id' => posix_geteuid(),
        'root_dir' => \dirname(__DIR__),
        'env' => $_SERVER['CI'] ?? false ? 'ci' : 'dev',
    ];

    if (file_exists($data['root_dir'] . '/infrastructure/docker/compose.override.yml')) {
        $data['docker_compose_files'][] = 'compose.override.yml';
    }

    $data['composer_cache_dir'] = cache('composer_cache_dir', function (): string {
        $emptyContext = new Context();

        $composerCacheDir = capture(['composer', 'global', 'config', 'cache-dir', '-q'], onFailure: '', context: $emptyContext);
        // If PHP is broken, the output will not be a valid path but an error message
        if (!is_dir($composerCacheDir)) {
            $composerCacheDir = sys_get_temp_dir() . '/castor/composer';
            // If the directory does not exist, we create it. Otherwise, docker
            // will do, as root, and the user will not be able to write in it.
            if (!is_dir($composerCacheDir)) {
                mkdir($composerCacheDir, 0777, true);
            }
        }

        return $composerCacheDir;
    });

    $platform = strtolower(php_uname('s'));
    if (str_contains($platform, 'darwin')) {
        $data['macos'] = true;
        $data['docker_compose_files'][] = 'compose.docker-for-x.yml';
    } elseif (\in_array($platform, ['win32', 'win64'])) {
        $data['docker_compose_files'][] = 'compose.docker-for-x.yml';
        $data['power_shell'] = true;
    }

    if ($data['user_id'] > 256000) {
        $data['user_id'] = 1000;
    }

    if (0 === $data['user_id']) {
        log('Running as root? Fallback to fake user id.', 'warning');
        $data['user_id'] = 1000;
    }

    return new Context($data, pty: 'dev' === $data['env']);
}

#[AsContext(name: 'ci')]
function create_ci_context(): Context
{
    $buildId = $_SERVER['CASTOR_CI_BUILD_ID'] ?? throw new \RuntimeException('"CASTOR_CI_BUILD_ID" is not defined.');

    $c = create_default_context();

    return $c
        ->withData(
            [
                'project_name' => $c['project_name'] . "-ci-$buildId",
                'docker_compose_files' => [...array_filter(
                    $c['docker_compose_files'],
                    fn (string $file): bool => !in_array($file, ['compose.override.yml', 'compose.router.yml', 'compose.yml', 'compose.worker.yml']),
                )],
            ],
        )
//        ->withEnvironment([
//            'WEBPACK_DUMP_CONFIG' => '1'
//        ])
    ;
}

/**
 * @param array<string> $subCommand
 */
function docker_compose(array $subCommand, ?Context $c = null, bool $withBuilder = false): Process
{
    $c ??= context();

    $domains = [variable('root_domain'), ...variable('extra_domains')];
    $domains = '`' . implode('`) || Host(`', $domains) . '`';

    $c = $c
        ->withTimeout(null)
        ->withEnvironment([
            'PROJECT_NAME' => variable('project_name'),
            'PROJECT_ROOT_DOMAIN' => variable('root_domain'),
            'PROJECT_DOMAINS' => $domains,
            'USER_ID' => variable('user_id'),
            'COMPOSER_CACHE_DIR' => variable('composer_cache_dir'),
            'PHP_VERSION' => variable('php_version'),
            'BUILDKIT_PROGRESS' => 'plain',
        ])
    ;

    $command = [
        'docker',
        'compose',
        '-p', variable('project_name'),
    ];

    $files = variable('docker_compose_files');
    if ('ci' === $c->name) {
        $files = array_filter(variable('docker_compose_files'), fn (string $file): bool => 'compose.yml' !== $file);
    }

    foreach ($files as $file) {
        $command[] = '-f';
        $command[] = variable('root_dir') . '/infrastructure/docker/' . $file;
    }

    if ($withBuilder) {
        $command[] = '-f';
        $command[] = variable('root_dir') . '/infrastructure/docker/compose.builder.yml';
    }

    $command = array_merge($command, $subCommand);

    return run($command, context: $c);
}

function docker_compose_run(
    string $runCommand,
    ?Context $c = null,
    string $service = 'builder',
    bool $noDeps = true,
    ?string $workDir = null,
    bool $portMapping = false,
    bool $withBuilder = true,
): Process {
    $command = [
        'run',
        '--rm',
    ];

    if ($noDeps) {
        $command[] = '--no-deps';
    }

    if ($portMapping) {
        $command[] = '--service-ports';
    }

    if (null !== $workDir) {
        $command[] = '-w';
        $command[] = $workDir;
    }

    $command[] = $service;
    $command[] = '/bin/sh';
    $command[] = '-c';
    $command[] = "exec {$runCommand}";

    return docker_compose($command, c: $c, withBuilder: $withBuilder);
}

function docker_exit_code(
    string $runCommand,
    ?Context $c = null,
    string $service = 'builder',
    bool $noDeps = true,
    ?string $workDir = null,
    bool $withBuilder = true,
): int {
    $c = ($c ?? context())->withAllowFailure();

    $process = docker_compose_run(
        runCommand: $runCommand,
        c: $c,
        service: $service,
        noDeps: $noDeps,
        workDir: $workDir,
        withBuilder: $withBuilder,
    );

    return $process->getExitCode() ?? 0;
}

/**
 * Find worker containers for the current project.
 *
 * @return array<string>
 */
function get_workers(): array
{
    $command = [
        'docker',
        'ps',
        '-a',
        '--filter', 'label=docker-starter.worker.' . variable('project_name'),
        '--quiet',
    ];
    $out = capture($command);

    if (!$out) {
        return [];
    }

    $workers = explode("\n", $out);

    return array_map('trim', $workers);
}
