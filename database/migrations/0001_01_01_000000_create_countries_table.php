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
        $this->builder->create('countries', function (Blueprint $table): void {
            $table->id();
            $table->string('iso_3166_1_alpha2', 2)->unique();
            $table->string('iso_3166_1_alpha3', 3)->unique();
            $table->json('common_name');
            $table->json('official_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        $this->builder->dropIfExists('countries');
    }
};
