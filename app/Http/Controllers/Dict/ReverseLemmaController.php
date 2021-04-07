<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Library\Str;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\ReverseLemma;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReverseLemmaController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:dict.edit,/dict/reverse_lemma/', 
                          ['only' => ['tmpCreateAllReverse']]);
        
        $this->url_args = ReverseLemma::urlArgs($request);  
        
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function index()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $reverse_lemmas = ReverseLemma::search($url_args);
        if (!$reverse_lemmas) {
           $numAll = 0; 
        } else {
            $numAll = $reverse_lemmas->count();
//dd($numAll);            
//dd($lemmas->toSql()); 
            $reverse_lemmas = $reverse_lemmas->paginate($this->url_args['limit_num']);         
//dd($lemmas);        
        }
        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('reverseLemmas');
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');

        return view('dict.reverse_lemma.index',
                compact('lang_values', 'reverse_lemmas', 'numAll',
                        'pos_values', 'args_by_get', 'url_args'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function inflexionGroups()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $groups = $gramset_heads = [];
        if ($url_args['search_lang'] && $url_args['search_pos'] && $url_args['search_dialect']) {
            $gramsets = Gramset::dictionaryGramsets($url_args['search_pos'], NULL, $url_args['search_lang']);
            array_unshift($gramsets, array_pop($gramsets));

            $groups = ReverseLemma::inflexionGroups($url_args['search_lang'], $url_args['search_pos'], $url_args['search_dialect'], $gramsets, $url_args['join_harmony']);
            
            $gramset_heads = Gramset::dictionaryGramsetNames($url_args['search_lang'], $url_args['search_pos']);
            array_unshift($gramset_heads, array_pop($gramset_heads));
        }
//dd($groups, $gramset_heads);        
        $lang_values = Lang::getListWithQuantity('reverseLemmas', true);
        $pos_values = PartOfSpeech::getChangeableListWithQuantity('lemmas');
        $dialect_values = $url_args['search_lang'] ? ['NULL'=>''] + Dialect::getList($url_args['search_lang']) : ['NULL'=>''] + Dialect::getList();

        return view('dict.reverse_lemma.inflexion_groups',
                compact('dialect_values', 'gramset_heads', 'groups', 
                        'lang_values', 'pos_values', 'args_by_get', 'url_args'));
    }

    public function tmpCreateAllReverse() {
        $is_all_checked = false;
        while (!$is_all_checked) {
            $lemmas = Lemma::whereNotIn('id', function($query){
                        $query->select('id')->from('reverse_lemmas');
                    }) ->orderBy('id')
                    ->take(100)->get();
            if (!sizeof($lemmas)) {
                $is_all_checked = true;
            }

            foreach ($lemmas as $lemma) {
                $lemma->createReverseLemma();
            }            
        }        
    }
}
