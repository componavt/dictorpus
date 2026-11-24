<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeSynsetDescrCommentNullable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE synsets MODIFY descr TEXT NULL, MODIFY comment TEXT NULL');
    }

    public function down()
    {
        DB::table('synsets')->whereNull('descr')->update(['descr' => '']);
        DB::table('synsets')->whereNull('comment')->update(['comment' => '']);
        DB::statement('ALTER TABLE synsets MODIFY descr TEXT NOT NULL, MODIFY comment TEXT NOT NULL');
    }
}
