<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Transform\Rector\FuncCall\FuncCallToNewRector;
use Rector\Transform\Rector\StaticCall\StaticCallToMethodCallRector;
use Rector\Transform\ValueObject\StaticCallToMethodCall;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\ArgumentFuncCallToMethodCallRector;
use RectorLaravel\Rector\FuncCall\ConfigToTypedConfigMethodCallRector;
use RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\MethodCall\WhereToWhereLikeRector;
use RectorLaravel\Rector\StaticCall\RequestStaticValidateToInjectRector;
use RectorLaravel\Set\LaravelSetList;
use RectorLaravel\Set\LaravelSetProvider;
use RectorLaravel\ValueObject\ArgumentFuncCallToMethodCall;
use RectorLaravel\ValueObject\ArrayFuncCallToMethodCall;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/database',
        __DIR__ . '/config',
        __DIR__ . '/lang',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withImportNames(
        importDocBlockNames: false,
        importShortClasses: true,
        importNames: true,
        removeUnusedImports: true,
    )
    ->withSetProviders(LaravelSetProvider::class)
    ->withComposerBased(laravel: true)
    ->withRules([
        HelperFuncCallToFacadeClassRector::class,
        RequestStaticValidateToInjectRector::class,
        ConfigToTypedConfigMethodCallRector::class,
        EmptyToBlankAndFilledFuncRector::class,
        RemoveDumpDataDeadCodeRector::class,
        WhereToWhereLikeRector::class,
    ])
    ->withConfiguredRule(StaticCallToMethodCallRector::class, [
        new StaticCallToMethodCall(
            'Illuminate\Support\Facades\Artisan',
            '*',
            'Illuminate\Contracts\Console\Kernel',
            '*'
        ),
        new StaticCallToMethodCall(
            'Illuminate\Support\Facades\Cache',
            '*',
            'Illuminate\Cache\CacheManager',
            '*'
        ),
        new StaticCallToMethodCall(
            'Illuminate\Support\Facades\DB',
            '*',
            'Illuminate\Database\DatabaseManager',
            '*'
        ),
        new StaticCallToMethodCall(
            'Illuminate\Support\Facades\URL',
            '*',
            'Illuminate\Routing\UrlGenerator',
            '*'
        ),
        new StaticCallToMethodCall(
            'Illuminate\Support\Facades\Config',
            '*',
            'Illuminate\Config\Repository',
            '*'
        ),
        new StaticCallToMethodCall('Illuminate\Support\Facades\Auth', '*', 'Illuminate\Auth\AuthManager', '*'),
    ])
    ->withConfiguredRule(ArgumentFuncCallToMethodCallRector::class, [
        new ArrayFuncCallToMethodCall('config', 'Illuminate\Contracts\Config\Repository', 'set', 'get'),
        new ArgumentFuncCallToMethodCall('auth', 'Illuminate\Contracts\Auth\Guard'),
    ])
    ->withSets([
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_FACTORIES,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
    ])
    ->withPhpSets()
    ->withTypeCoverageLevel(10)
    ->withDeadCodeLevel(10)
    ->withCodeQualityLevel(10);
