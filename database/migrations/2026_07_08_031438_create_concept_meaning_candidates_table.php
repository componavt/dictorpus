<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateConceptMeaningCandidatesTable extends Migration
{
    public function up(): void
    {
        Schema::create('concept_meaning_candidates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('meaning_id');
            $table->unsignedInteger('concept_id');

            $table->string('source_taskpos', 32);
            $table->string('source_primary_glossru', 255);
            $table->string('source_group_key', 320);

            $table->unsignedInteger('source_meaning_count')->default(0);
            $table->unsignedInteger('source_concept_meaning_count')->default(0);
            $table->unsignedInteger('candidate_rank')->nullable();

            $table->enum('review_status', ['new', 'accepted', 'rejected', 'applied'])
                ->default('new');

            $table->string('review_note', 500)->nullable();
            $table->dateTime('reviewed_at')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                ['meaning_id', 'concept_id', 'source_group_key'],
                'cmc_meaning_concept_group_unique'
            );

            $table->index('meaning_id', 'cmc_meaning_idx');
            $table->index('concept_id', 'cmc_concept_idx');
            $table->index('review_status', 'cmc_review_status_idx');
            $table->index(['source_taskpos', 'source_primary_glossru'], 'cmc_source_group_idx');

            $table->foreign('meaning_id', 'cmc_meaning_fk')
                ->references('id')
                ->on('meanings')
                ->onDelete('cascade');

            $table->foreign('concept_id', 'cmc_concept_fk')
                ->references('id')
                ->on('concepts')
                ->onDelete('cascade');
        });

        DB::statement("
            ALTER TABLE `concept_meaning_candidates`
            CONVERT TO CHARACTER SET utf8
            COLLATE utf8_unicode_ci
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('concept_meaning_candidates');
    }
};
