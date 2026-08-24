<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicationIdToSourcesTable extends Migration
{
    public function up()
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->unsignedInteger('publication_id')
                ->nullable()
                ->after('id');

            $table->index('publication_id', 'sources_publication_id_index');

            $table->foreign(
                'publication_id',
                'sources_publication_id_foreign'
            )
                ->references('id')
                ->on('publications')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->dropForeign(
                'sources_publication_id_foreign'
            );

            $table->dropIndex(
                'sources_publication_id_index'
            );

            $table->dropColumn('publication_id');
        });
    }
}