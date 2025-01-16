<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__,
        __DIR__ . '/.castor'
    ])
    ->ignoreVCSIgnored(true)
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => false,
        'header_comment' => ['header' => ''],
        'linebreak_after_opening_tag' => true,
        'no_superfluous_phpdoc_tags' => true,
        'phpdoc_summary' => false,
    ])
    ->setFinder($finder)
;
