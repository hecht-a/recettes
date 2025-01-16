<?php

use Rector\Config\RectorConfig;
use Rector\Doctrine\CodeQuality\Rector\Class_\AddReturnDocBlockToCollectionPropertyGetterByToManyAnnotationRector;
use Rector\Doctrine\CodeQuality\Rector\Property\ImproveDoctrineCollectionDocTypeInEntityRector;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromToOneRelationTypeRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $base = __DIR__;

    $rectorConfig->autoloadPaths([
        $base . '/tools/rector/vendor/autoload.php',
        $base . '/vendor/autoload.php',
    ]);
    $rectorConfig->symfonyContainerXml($base . '/var/cache/dev/App_KernelDevDebugContainer.xml');
    $rectorConfig->paths([
        $base . '/src',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_83);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,
        SymfonySetList::SYMFONY_70,
        SymfonySetList::SYMFONY_71,
    ]);
    $rectorConfig->sets([SetList::CODE_QUALITY]);
    $rectorConfig->sets([SetList::PRIVATIZATION]);
    $rectorConfig->sets([SetList::TYPE_DECLARATION]);
    $rectorConfig->rules([
        RestoreDefaultNullToNullableTypePropertyRector::class,
        ImproveDoctrineCollectionDocTypeInEntityRector::class,
        AddReturnDocBlockToCollectionPropertyGetterByToManyAnnotationRector::class,
        TypedPropertyFromToOneRelationTypeRector::class,
    ]);
};
