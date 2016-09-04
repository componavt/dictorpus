<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

use App\Models\Corpus\Source;

class SourceController extends Controller
{
/*    
    public function tempInsertVepsianSource()
    {
        $veps_sources = DB::connection('vepsian')
                            ->table('source')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('sources')->delete();
       
        foreach ($veps_sources as $veps_source):
            $source = new Source;
            $source->id = $veps_source->id;
            $source->title = $veps_source->title;
            $source->author = $veps_source->author;
            $source->year = $veps_source->year;
            $source->ieeh_archive_number1 = $veps_source->ieeh_archive_number1;
            $source->ieeh_archive_number2 = $veps_source->ieeh_archive_number2;
            $source->comment = $veps_source->comment;

            if ($veps_source->page_from) {
                $source->pages = $veps_source->page_from;
                if ($veps_source->page_to) {
                    $source->pages .= '-';
                }
            }
            if ($veps_source->page_to) {
                $source->pages .= $veps_source->page_to;
            }
                
            $source->updated_at = $veps_source->modified;
            $source->created_at = $veps_source->modified;
            $source->save();
            
        endforeach;
     }
 * 
 */
}
