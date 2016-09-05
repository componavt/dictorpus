<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use LaravelLocalization;

use App\Models\Corpus\District;
use App\Models\Corpus\Place;
use App\Models\Corpus\PlaceName;

class PlaceController extends Controller
{
    /**
     * Show the list of places.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $places = Place::orderBy('name_'.$locale)->get();

        return view('corpus.place.index')->with(array('places' => $places));
    }
/*    
    public function tempInsertVepsianPlace()
    {
        $veps_distr_places = DB::connection('vepsian')
                            ->table('place')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('place_names')->delete();
        DB::connection('mysql')->statement('ALTER TABLE place_names AUTO_INCREMENT = 1');
        DB::connection('mysql')->table('places')->delete();
       
        foreach ($veps_distr_places as $veps_distr_place):
            if ($veps_distr_place->village_id != NULL) {
                $village = DB::connection('vepsian')
                             ->table('place_village')
                             ->where('id',$veps_distr_place->village_id)
                             ->first();
                $name_nu = $village->ru;
                $name_vep = $village->vep;
            } else {
                $name_nu = $name_vep = NULL;
            }

            $place = new Place;
            $place->id = $veps_distr_place->id;
            
            if ($veps_distr_place ->region_id == 2) {
                $place->region_id = 2;
            } else {
                $place->district_id = $veps_distr_place ->region_id;
                $district = District::find($veps_distr_place ->region_id);
                $place->region_id = $district -> region_id;
            }
            
            $place->name_ru = $name_nu;
            $place->save();
            
            if ($name_vep) {
                $place_name = new PlaceName;
                $place_name->place_id = $veps_distr_place->id;
                $place_name->lang_id = 1;
                $place_name->name = $name_vep;
                $place_name->save();
            }
            
        endforeach;
     }
 * 
 */

}
