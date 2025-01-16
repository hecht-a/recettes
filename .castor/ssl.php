<?php

namespace ssl;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Symfony\Component\Process\ExecutableFinder;

use function Castor\capture;
use function Castor\context;
use function Castor\io;
use function Castor\run;
use function Castor\variable;
use function docker\docker_compose_run;

#[AsTask(description: 'Generates SSL certificates (with mkcert if available or self-signed if not)', aliases: ['generate-certificates'])]
function generate_certificates(
    #[AsOption(description: 'Force the certificates re-generation without confirmation', shortcut: 'f')]
    bool $force = false,
): void {
    $sslDir = variable('root_dir') . '/infrastructure/docker/services/router/certs';

    if (file_exists("{$sslDir}/cert.pem") && !$force) {
        io()->comment('SSL certificates already exists.');
        io()->note('Run "castor docker:generate-certificates --force" to generate new certificates.');

        return;
    }

    io()->title('Generating SSL certificates');

    if ($force) {
        if (file_exists($f = "{$sslDir}/cert.pem")) {
            io()->comment('Removing existing certificates in infrastructure/docker/services/router/certs/*.pem.');
            unlink($f);
        }

        if (file_exists($f = "{$sslDir}/key.pem")) {
            unlink($f);
        }
    }

    $finder = new ExecutableFinder();
    $mkcert = $finder->find('mkcert');

    if ($mkcert) {
        $pathCaRoot = capture(['mkcert', '-CAROOT']);

        if (!is_dir($pathCaRoot)) {
            io()->warning('You must have mkcert CA Root installed on your host with "mkcert -install" command.');

            return;
        }

        $rootDomain = variable('root_domain');

        run([
            'mkcert',
            '-cert-file', "{$sslDir}/cert.pem",
            '-key-file', "{$sslDir}/key.pem",
            $rootDomain,
            "*.{$rootDomain}",
            ...variable('extra_domains'),
        ]);

        io()->success('Successfully generated SSL certificates with mkcert.');

        if ($force) {
            io()->note('Please restart the infrastructure to use the new certificates with "castor up" or "castor start".');
        }

        return;
    }

    run(['infrastructure/docker/services/router/generate-ssl.sh'], context: context()->withQuiet());

    io()->success('Successfully generated self-signed SSL certificates in infrastructure/docker/services/router/certs/*.pem.');
    io()->comment('Consider installing mkcert to generate locally trusted SSL certificates and run "castor docker:generate-certificates --force".');

    if ($force) {
        io()->note('Please restart the infrastructure to use the new certificates with "castor up" or "castor start".');
    }
}

#[AsTask(description: 'Disable ssl for yarn and npm', aliases: ['disable-ssl'])]
function disable(): void
{
    docker_compose_run('yarn config set strict-ssl false');
    docker_compose_run('npm config set strict-ssl false');
}
