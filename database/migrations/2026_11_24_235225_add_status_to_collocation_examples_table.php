<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusToCollocationExamplesTable extends Migration
{
    public function up()
    {
        Schema::table('collocation_examples', function (Blueprint $table) {
            $table->string('status', 16)->default('attached')->after('partner_w_id');
        });
    }

    public function down()
    {
        if (DB::table('collocation_examples')->where('status', '<>', 'attached')->exists()) {
            throw new \RuntimeException('Cannot remove example status while non-attached decisions exist.');
        }

        Schema::table('collocation_examples', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
