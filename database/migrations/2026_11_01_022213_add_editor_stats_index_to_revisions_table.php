<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddEditorStatsIndexToRevisionsTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `revisions` ADD INDEX `revisions_editor_stats_index` (`user_id`, `revisionable_type`, `revisionable_id`, `updated_at`, `key`), ALGORITHM=INPLACE, LOCK=NONE');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `revisions` DROP INDEX `revisions_editor_stats_index`, ALGORITHM=INPLACE, LOCK=NONE');
    }
}
