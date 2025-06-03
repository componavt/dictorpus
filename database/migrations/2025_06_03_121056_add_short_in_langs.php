<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Dict\Lang;
class AddShortInLangs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('langs', function (Blueprint $table) {
            $table->string('short_ru', 32)->after('name_ru')->nullable();
        });
        foreach ([4=>'собственно карельское наречие', 5=>'ливвиковское наречие', 6=>'людиковское наречие'] as $lang_id => $short) {
            $lang = Lang::find($lang_id);
            $lang->short_ru = $short;
            $lang->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('langs', function (Blueprint $table) {
            $table->dropColumn('short_ru');
        });
    }
}
