<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use LaravelLocalization;
use Response;

use App\Models\Corpus\Place;

use App\Models\Dict\Concept;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class ConceptController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
//dd($request->all());        
//var_dump($request->input('limit_num'));
        $this->middleware('auth:ref.edit,/dict/concept/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = Concept::urlArgs($request); 
//dd($this->url_args);        
//var_dump($this->url_args); 
//print '<br>';
        $this->args_by_get = search_values_by_URL($this->url_args);
//var_dump($this->url_args);        
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//dd($this->url_args);        
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
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $concept_category_values = ConceptCategory::getList();
        $pos_values = Concept::getPOSList();

        return view('dict.concept.create', compact('concept_category_values', 'pos_values', 'args_by_get', 'url_args'));
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
        $concept->updateWikiSrc();
        
        return Redirect::to('/dict/concept'.($this->args_by_get))
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
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $concept = Concept::find($id); 
        $concept_category_values = ConceptCategory::getList();
        $pos_values = Concept::getPOSList();

        return view('dict.concept.edit', 
                compact('concept', 'concept_category_values', 'pos_values', 
                        'args_by_get', 'url_args'));
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
//dd($request->all());        
        $this->validateForm($request);
        
        $concept = Concept::find($id);
        $old_wiki_photo = $concept->wiki_photo;
        $concept->fill($request->all())->save();
        if ($old_wiki_photo != $concept->wiki_photo || !$concept->src) {
            $concept->updateWikiSrc();
        }
        
        return Redirect::to('/dict/concept'.($this->args_by_get))
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
                    $concept_name = $concept->text;
                    $concept->meanings()->detach();
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
        $label_id = (int)$request->input('label_id');
        $status_in_label = $request->input('status_in_label');

        $list = [];
        $concepts = Concept::where(function($q) use ($concept_text){
                            $q->where('text_en','like', $concept_text)
                              ->orWhere('text_ru','like', $concept_text);
                         });
        if ($category_id) {                 
            $concepts ->where('concept_category_id',$category_id);
        }
        
        if ($pos_id && $pos_id !=PartOfSpeech::getIDByCode('PHRASE')) {                 
            $concepts ->where('pos_id',$pos_id);
        }
        
        if ($label_id) {
            $concepts->whereIn('id', function ($q) use ($label_id, $status_in_label) {
                $q->select('concept_id')->from('concept_meaning')
                  ->whereIn('meaning_id', function ($q2) use ($label_id, $status_in_label) {
                      $q2->select('id')->from('meanings')
                         ->whereIn('lemma_id', function ($q3) use ($label_id, $status_in_label) {
                             $q3->select('lemma_id')->from('label_lemma')
                                ->whereLabelId($label_id);
                             if ($status_in_label !== '') {
                                $q3->whereStatus($status_in_label);
                             }
                         });
                  });
            });
        }
//dd(to_sql($concepts));        
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
        $search_lang=$request->input('search_lang') ?? 6;
        $search_lang_name = Lang::getNameById($search_lang);
        
        $place_values = Place::whereIn('id',function ($query) use ($search_lang) {
                $query->select('place_id')->from('dialect_place')
                      ->whereIn('dialect_id',function ($q1) use ($search_lang) {
                        $q1->select('id')->from('dialects')
                           ->where('lang_id',$search_lang);
                      });
            })->whereIn('id',function ($query) use ($search_lang) {
                $query->select('place_id')->from('meaning_place')
                      ->whereIn('meaning_id', function($q1) use ($search_lang) {
                        $q1->select('id')->from('meanings')
                           ->whereIn('lemma_id', function($q2) use ($search_lang) {
                            $q2->select('id')->from('lemmas')
                               ->where('lang_id',$search_lang);
                          });
                      });
            })->pluck('name_'.$locale, 'id')->toArray();
            
        $search_places=(array)$request->input('search_places');
        $search_places=array_intersect($search_places, array_keys($place_values));
        if (!sizeof($search_places)) {
            $search_places = array_keys($place_values);
        }
//dd($search_places, $place_values);        
        $concepts = Concept::orderBy('text_'.$locale)->pluck('text_'.$locale, 'id')->toArray();
        $concept_lemmas = [];
//dd($concepts);        
        
        foreach($concepts as $concept_id => $concept_text) {  
//dd($concept_id);            
            $count = 0;
            foreach ($search_places as $place_id) {
                $concept_lemmas[$concept_text][$place_values[$place_id]] = [];
                $lemma_coll = Lemma::whereLangId($search_lang)
                    ->whereIn('id', function ($query) use ($concept_id, $place_id) {
                        $query->select('lemma_id')->from('meanings')
                              ->whereIn('id', function ($q) use ($concept_id) {
                                 $q->select('meaning_id')->from('concept_meaning')
                                   ->whereConceptId($concept_id);
                              })
                              ->whereIn('id', function ($query) use ($place_id) {
                                 $query->select('meaning_id')->from('meaning_place')
                                       ->wherePlaceId($place_id);
                              });
                    })->get();//pluck('lemma')->toArray();
                    
                $lemmas = [];
                foreach ($lemma_coll as $lemma) {
                    $phonetic = $lemma->phoneticListToString();
                    if (!$phonetic) {
                        $phonetic = $lemma->lemma;
                    }
                    $lemmas[$lemma->id] = $phonetic;
                }
                $concept_lemmas[$concept_text][$place_values[$place_id]]=$lemmas;
                $count += sizeof($lemmas);
            }
            if (!$count) {
                unset ($concept_lemmas[$concept_text]);
            }
        } 
//dd($concept_lemmas);  
        $place_names = array_values($search_places);
//        $place_values = Place::getList(); //[NULL => ''] + 
        $lang_values = Lang::getProjectList();
        
        return view('dict.concept.sosd', 
                compact('concept_lemmas', 'search_lang_name', 
                        'search_lang', 'search_places', 'lang_values', 'place_values'));
    }
    
    public function photoPreview($id)
    {
        $concept = Concept::find($id);
        if (!$concept->wiki_photo) {
            return ' ';
        }
        $photo = $concept->photoPreview();//Info;
        if (!$photo) {
            return view('dict.concept._photo_reload', with(['obj'=>'concept', 'id'=>$id,
                'url'=>'/dict/concept/'.$id.'/photo_preview']));
        }
        return view('dict.concept._photo_preview', compact('photo'));
    }
/*    
    public function photoView($id)
    {
        $concept = Concept::find($id);
        $photo = $concept->photoInfo();
        if (!$photo) {
            return view('dict.concept._photo_reload', with(['obj'=>'concept', 'id'=>$id,
                'url'=>'/dict/concept/".$id."/photo_preview']));
        }
        return view('dict.concept._photo_preview', compact('photo'));
    }*/
}
