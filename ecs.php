<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
    ])
    ->withRules([
        NoUnusedImportsFixer::class,
        OrderedImportsFixer::class,
        SingleLineAfterImportsFixer::class,
        DeclareStrictTypesFixer::class,
    ])
    ->withSkip([
        YodaStyleFixer::class,
    ])
    ->withPhpCsFixerSets(
        psr1: true,
        psr12: true,
        phpCsFixer: true,
    );
