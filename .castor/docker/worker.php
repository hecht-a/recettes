<?php

namespace docker\worker;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\run;
use function docker\get_workers;

#[AsTask(description: 'Starts the workers', name: 'start', aliases: ['start-workers'])]
function workers_start(): void
{
    io()->title('Starting workers');

    $workers = get_workers();

    if (!$workers) {
        return;
    }

    $context = context()->withQuiet();

    run([
        'docker',
        'update',
        '--restart=unless-stopped',
        ...$workers,
    ], context: $context);

    run([
        'docker',
        'start',
        ...$workers,
    ], context: $context);
}

#[AsTask(description: 'Stops the workers', name: 'stop', aliases: ['stop-workers'])]
function workers_stop(): void
{
    io()->title('Stopping workers');

    $workers = get_workers();

    if (!$workers) {
        return;
    }

    run([
        'docker',
        'update',
        '--restart=no',
        ...$workers,
    ]);

    run([
        'docker',
        'stop',
        ...$workers,
    ]);
}
