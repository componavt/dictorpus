<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

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
        
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_pos'      => (int)$request->input('search_pos'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 50;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
        
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
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
            $groups = ReverseLemma::inflexionGroups($url_args['search_lang'], $url_args['search_pos'], $url_args['search_dialect']);
            $gramset_heads = Gramset::dictionaryGramsetNames($url_args['search_lang'], $url_args['search_pos']);
        }
//dd($groups);        
        $lang_values = Lang::getListWithQuantity('reverseLemmas');
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');
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
