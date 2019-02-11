<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;
use App\Models\Dict\Gramset;
use App\Models\Dict\GramsetCategory;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;

class GramsetController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:ref.edit,/dict/gramset/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_pos'      => (int)$request->input('search_pos'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 25;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
        
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
    }
    
     /**
     * Show the list of gramsets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $gram_fields = [];

/*        if (!$this->url_args['search_lang'] || !$this->url_args['search_pos']) {
            $gramsets = NULL;
            $numAll = 0;
        } else {
*/            $gramsets = Gramset::orderBy('sequence_number')
                      ->join('gramset_pos', 'gramsets.id', '=', 'gramset_pos.gramset_id');
            
            if ($this->url_args['search_lang']) {
                $gramsets = $gramsets->where('lang_id',$this->url_args['search_lang']);
            }
                      
            if ($this->url_args['search_pos']) {
                $gramsets = $gramsets->where('pos_id',$this->url_args['search_pos']);
            }
            
            $gramsets=$gramsets->groupBy('gramsets.id');
            $numAll = sizeof($gramsets->get());
            $gramsets = $gramsets->paginate($this->url_args['limit_num']);         

            $all_gram_fields = GramCategory::getNames();    
            // remove empty columns
            foreach ($all_gram_fields as $field) {
                foreach ($gramsets as $gramset) {
                    if ($gramset->{'gram'.ucfirst($field)} != NULL) {
                        $gram_fields[] = $field;
                        continue 2;
                    }
                }
            }        
        //} 
              
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('gramsets');
        $lang_values = Lang::getListWithQuantity('gramsets');

        $url_args_for_out = $this->url_args;
        unset($url_args_for_out['page']);
        $args_by_get_for_out = Lang::searchValuesByURL($url_args_for_out);
        
        
        return view('dict.gramset.index')
                ->with([
                        'gram_fields' => $gram_fields,
                        'gramsets' => $gramsets,
                        'lang_values' => $lang_values, 
                        'numAll' => $numAll,
                        'pos_values' => $pos_values, 
                        'args_by_get'    => $this->args_by_get,
                        'args_by_get_for_out' => $args_by_get_for_out,
                        'url_args'       => $this->url_args,
                    ]);
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pos_values = PartOfSpeech::getGroupedList();
        $lang_values = Lang::getList();
        
        $grams = [];        
        foreach (GramCategory::all()->sortBy('sequence_number') as $gc) {         //   id is gram_category_id
            $grams[$gc->name_en] = ['name'=> $gc->name,
                                    'grams' => [NULL=>''] + Gram::getList($gc->id)];
        }

        return view('dict.gramset.create')
                  ->with(['grams' => $grams,
                          'lang_values'=>$lang_values,
                          'pos_values'=>$pos_values,
                          'args_by_get'    => $this->args_by_get,
                          'url_args'       => $this->url_args,
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
        $this->validate($request, [
            'gram_id_number'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_case'  => 'numeric|required_without_all:gram_id_number,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_tense'  => 'numeric|required_without_all:gram_id_case,gram_id_number,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_person'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_mood'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_negation'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_infinitive'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_voice,gram_id_participle', 
            'gram_id_voice'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_participle', 
            'gram_id_participle'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice', 
            'gram_id_reflexive'  => 'numeric', 
            'sequence_number' => 'numeric',
            'parts_of_speech' => 'required|array',
            'langs' => 'required|array',
        ]);

        foreach (GramCategory::getNames() as $gc_name) {
            $column = 'gram_id_'.$gc_name;
            if (!$request[$column]) {
                $request[$column] = NULL;
            }
        }

        $gramset = Gramset::create($request->all());
        
//        $gramset ->parts_of_speech()->attach($request['parts_of_speech']);
        
        foreach ($request['parts_of_speech'] as $p_id) {
            foreach ($request['langs'] as $l_id) {
                $gramset-> parts_of_speech()->attach($p_id, ['lang_id'=>$l_id]);
                }
        }
 
        $back_url = '/dict/gramset/?'.$this->args_by_get;

        return Redirect::to($back_url)
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
        return Redirect::to('/dict/gramset/');
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
        
        $gramset = Gramset::find($id); 
        $pos_values = PartOfSpeech::getGroupedList();
        
        $pos_value = [];
        foreach ($gramset->parts_of_speech as $pos) {
            $pos_value[] = $pos->id;
        }        
        
        $lang_values = Lang::getList();
        $lang_value = [];
        foreach ($gramset->langs as $lang) {
            $lang_value[] = $lang->id;
        }        

        $gramset_category_values = GramsetCategory::getList();
        
        $grams = [];        
        foreach (GramCategory::all()->sortBy('sequence_number') as $gc) {         //   id is gram_category_id
            $grams[$gc->name_en] = ['name'=> $gc->name,
                                    'grams' => [NULL=>''] + Gram::getList($gc->id)];
        }

        return view('dict.gramset.edit',
                  compact('grams', 'gramset', 'gramset_category_values',
                          'lang_value', 'lang_values', 'pos_value',
                          'pos_values', 'args_by_get', 'url_args'));
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
        $this->validate($request, [
            'gram_id_number'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_case'  => 'numeric|required_without_all:gram_id_number,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_tense'  => 'numeric|required_without_all:gram_id_case,gram_id_number,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_person'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_mood'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_negation'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_infinitive'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_voice,gram_id_participle', 
            'gram_id_voice'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_participle', 
            'gram_id_participle'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice', 
            'gram_id_reflexive'  => 'numeric', 
            'sequence_number' => 'numeric',
            'parts_of_speech' => 'required|array',
            'langs' => 'required|array',
        ]);

        foreach (GramCategory::getNames() as $gc_name) {
            $column = 'gram_id_'.$gc_name;
            if (!$request[$column]) {
                $request[$column] = NULL;
            }
        }
        
        $gramset = Gramset::find($id);
        $gramset->fill($request->all())->save();

        $gramset->parts_of_speech()->detach();
//        $gramset->parts_of_speech()->attach($request['parts_of_speech']);

        foreach ($request['parts_of_speech'] as $pos_id) {
            foreach ($request['langs'] as $lang_id) {
                $gramset-> parts_of_speech()->attach($pos_id, ['lang_id'=>$lang_id]);
                }
        }
 
        $back_url = '/dict/gramset/'.$this->args_by_get;

        return Redirect::to($back_url)
                       ->withSuccess(\Lang::get('messages.updated_success'));        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $error = false;
        $status_code = 200;
        $result =[];

        $back_url = '/dict/gramset/'.$this->args_by_get;
                
        if($id != "" && $id > 0) {
            try{
                $gramset = Gramset::find($id);
                if($gramset){
                    $parts_of_speech = $gramset->parts_of_speech();
                    if (!$gramset->wordforms()->count()) {
                        $gramset_name = $gramset->gramsetString();
                        $parts_of_speech->detach();
                        $gramset->delete();
                        $result['message'] = \Lang::get('dict.gramset_removed', ['name'=>$gramset_name]);
                    } else {
                        $error = true;
                        $result['error_message'] = \Lang::get('dict.gramset_has_wordform');
                    }    
                } else {
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
                return Redirect::to($back_url)
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to($back_url)
                  ->withSuccess($result['message']);
        }
    }

    /**
     * When we add column lang_id in table gramset_pos,
     * we old records associated with vepsian lang (lang_id=1) and
     * dublicated existing records for 3 karelian langs (lang_id=4..6)
     *
     * @return null
     */
/*    
    public function tempInsertGramsetPosLang()
    {
        $langs = [4,5,6];
        $gramset_pos = DB::table('gramset_pos')->where('lang_id',1)->get();
        foreach ($gramset_pos as $rec) {
            foreach ($langs as $lang) {
            DB::table('gramset_pos')->insert([
                    'gramset_id' => $rec->gramset_id,
                    'pos_id' => $rec->pos_id,
                    'lang_id' => $lang
                ]);
            }
        }
    }
*/    
    /**
     * Reads some gramsets for non-reflexive verbs and 
     * inserts the same records for reflexive verbs.
     *
     * @return null
     */
/*    
    public function tempInsertGramsetsForReflexive()
    {
        $reflexive_sequence_number=143;
        $lang_id = 6; // lude lang
        $pos_id = 11; // verb
        $reflex_verb = 47; // id of grammatical attribure 'reflexive verb'
        $seq_nums = [0=>71, 82=>95, 106=>119]; // ranges of sequence numbers  
        $langs = [1, 4, 5, 6];
        
        foreach ($seq_nums as $min=>$max) {
            $gramsets = Gramset::orderBy('sequence_number')
                               ->join('gramset_pos', 'gramsets.id', '=', 'gramset_pos.gramset_id')
                               ->where('lang_id',$lang_id)
                               ->where('pos_id',$pos_id)
                               ->where('sequence_number','>',$min)
                               ->where('sequence_number','<',$max)
                               ->get();
            foreach ($gramsets as $gramset) {
                $ref_gramset = Gramset::create([
                    'gram_id_mood' => $gramset->gram_id_mood, 
                    'gram_id_tense' => $gramset->gram_id_tense, 
                    'gram_id_person' => $gramset->gram_id_person, 
                    'gram_id_number' => $gramset->gram_id_number, 
                    'gram_id_negation' => $gramset->gram_id_negation, 
                    'gram_id_reflexive' => $reflex_verb,
                    'sequence_number' => $reflexive_sequence_number++,
                ]);

                foreach ($langs as $l_id) {
                    $ref_gramset-> parts_of_speech()
                                -> attach($pos_id, ['lang_id'=>$l_id]);
                }
            }
        }
    }
 * 
 */
}
