<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePubpartsTable extends Migration
{
    public function up()
    {
        /* pubpart может быть:
            номером газеты;
            номером журнала;
            томом;
            частью сборника;
            главой или иной озаглавленной частью Uuzi Sana;
         */
        Schema::create('pubparts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('publication_id');
            
            // нужен прежде всего для выпусков газеты
            $table->smallInteger('year')->unsigned()->nullable();
            
            /*
             * Заполняется, если известна точная дата номера.
             */
            $table->date('issue_date')->nullable();
            
            /*
             * Не unsignedInteger: номер выпуска может быть,
             * например, «35–37», «35/36».
             */
            $table->string('number', 50)->nullable();


            $table->string('title', 255)->nullable();

            $table->unsignedSmallInteger('sequence_number')->nullable();

            $table->index(
                array('publication_id', 'year'),
                'pubparts_publication_year_index'
            );

            $table->index(
                array('publication_id', 'title'),
                'pubparts_publication_title_index'
            );

            $table->index(
                array('publication_id', 'number', 'year'),
                'pubparts_publication_number_year'
            );

            $table->foreign(
                'publication_id',
                'pubparts_publication_id_foreign'
            )
                ->references('id')
                ->on('publications')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pubparts');
    }
}