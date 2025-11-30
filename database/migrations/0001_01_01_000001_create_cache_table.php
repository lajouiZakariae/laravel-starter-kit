<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return new class extends Migration {
    public function __construct(private readonly Builder $builder) {}

    /**
     * Run the migrations.
     */
    public function up(): void {
        $this->builder->create('cache', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        $this->builder->create('cache_locks', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        $this->builder->dropIfExists('cache');
        $this->builder->dropIfExists('cache_locks');
    }
};
