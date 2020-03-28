<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use LaravelLocalization;
use Response;

use App\Library\Str;

use App\Models\Corpus\Place;

use App\Models\Dict\Concept;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;

class ConceptController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:ref.edit,/dict/concept/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = Concept::urlArgs($request);          
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $concepts = Concept::search($url_args);
        
        $numAll = $concepts->count();
        $concepts = $concepts->paginate($url_args['limit_num']);         
        
        $category_values = ConceptCategory::getList();
        
        return view('dict.concept.index',
                    compact('category_values', 'concepts', 'numAll', 'args_by_get', 'url_args'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $concept_category_values = ConceptCategory::getList();
        $pos_values = Concept::getPOSList();

        return view('dict.concept.create', compact('concept_category_values', 'pos_values'));
    }

    public function validateForm(Request $request) {
        $this->validate($request, [
            'concept_category_id'  => 'required|max:4',
            'pos_id' => 'required|numeric',
            'text_en'  => 'max:150',
            'text_ru'  => 'required|max:150',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);
        $concept = Concept::create($request->all());
        
        return Redirect::to('/dict/concept/')
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/dict/concept/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $concept = Concept::find($id); 
        $concept_category_values = ConceptCategory::getList();
        $pos_values = Concept::getPOSList();

        return view('dict.concept.edit', 
                compact('concept', 'concept_category_values', 'pos_values'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateForm($request);
        
        $concept = Concept::find($id);
        $concept->fill($request->all())->save();
        
        return Redirect::to('/dict/concept/')
            ->withSuccess(\Lang::get('messages.updated_success'));        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $error = false;
        $status_code = 200;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $concept = Concept::find($id);
                if($concept){
                    $concept_name = $concept->name;
                    $concept->delete();
                    $result['message'] = \Lang::get('dict.concept_removed', ['name'=>$concept_name]);
                }
                else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        }else{
            $error =true;
            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
                return Redirect::to('/dict/concept/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/concept/')
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of concepts for drop down list in JSON format
     * Test url: /dict/concept/list?category_id=A11
     * 
     * @return JSON response
     */
    public function conceptList(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();
        
        $concept_text = '%'.$request->input('q').'%';
        $category_id = $request->input('category_id');
        $pos_id = (int)$request->input('pos_id');

        $list = [];
        $concepts = Concept::where(function($q) use ($concept_text){
                            $q->where('text_en','like', $concept_text)
                              ->orWhere('text_ru','like', $concept_text);
                         });
        if ($category_id) {                 
            $concepts = $concepts ->where('concept_category_id',$category_id);
        }
        
        if ($pos_id) {                 
            $concepts = $concepts ->where('pos_id',$pos_id);
        }
        
        $concepts = $concepts->orderBy('text_'.$locale)->get();
                         
        foreach ($concepts as $concept) {
            $list[]=['id'  => $concept->id, 
                     'text'=> $concept->text];
        }  
//dd($list);        
//dd(sizeof($concepts));
        return Response::json($list);
    }
    
    public function SOSD(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $search_lang='6';
        $search_lang_name = Lang::getNameById($search_lang);
        
        $places = Place::whereIn('id',function ($query) use ($search_lang) {
            $query->select('place_id')->from('dialect_place')
                  ->whereIn('dialect_id',function ($q1) use ($search_lang) {
                    $q1->select('dialect_id')->from('dialect_lemma')
                        ->whereIn('lemma_id',function ($q2) use ($search_lang) {
                            $q2->select('id')->from('lemmas')
                               ->where('lang_id',$search_lang);
                        });
                  });
        })->pluck('name_'.$locale, 'id')->toArray();
//dd($places);        
        
        $concepts = Concept::orderBy('text_'.$locale)->pluck('text_'.$locale, 'id')->toArray();
        $concept_lemmas = [];
//dd($concepts);        
        
        foreach($concepts as $concept_id => $concept_text) {  
//dd($concept_id);            
            $count = 0;
            foreach ($places as $place_id => $place_name) {
                $concept_lemmas[$concept_text][$place_name] = [];
                $lemmas = Lemma::whereLangId($search_lang)
                    ->whereIn('id', function ($query) use ($concept_id) {
                        $query->select('lemma_id')->from('meanings')
                              ->whereIn('id', function ($q) use ($concept_id) {
                                 $q->select('meaning_id')->from('concept_meaning')
                                   ->whereConceptId($concept_id);
                              });
                    })
                    ->whereIn('id', function ($query) use ($place_id) {
                        $query->select('lemma_id')->from('lemma_place')
                                   ->wherePlaceId($place_id);
//                    })->get();
//dd($lemmas);                    
/*                foreach ($lemmas as $lemma) {
                    $concept_lemmas[$concept_text][$place_name][$lemma->id]=$lemma->lemma;
                }*/
                    })->pluck('lemma')->toArray();
                $concept_lemmas[$concept_text][$place_name]=join(', ',$lemmas);
                $count += sizeof($lemmas);
            }
            if (!$count) {
                unset ($concept_lemmas[$concept_text]);
            }
        } 
//dd($concept_lemmas);  
        $place_names = array_values($places);
        return view('dict.concept.sosd', 
                compact('concept_lemmas', 'search_lang_name', 'place_names'));
    }
}
