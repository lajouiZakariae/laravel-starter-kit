<?php

namespace App\Facades;

use App\Services\CodeGeneratorService;
use Illuminate\Support\Facades\Facade;

class CodeGeneratorServiceFacade extends Facade {
    protected static function getFacadeAccessor(): string {
        return CodeGeneratorService::class;
    }
}
