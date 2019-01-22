<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Corpus\Word;
use App\Models\Dict\Lang;

class WordController extends Controller
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
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/text/', 
                         ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_lang'     => $request->input('search_lang'),
                    'search_word'     => $request->input('search_word'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 100;
        } elseif ($this->url_args['limit_num']>10000) {
            $this->url_args['limit_num'] = 1000;
        }   
       
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
    }
    
    /**
     * SQL: select lower(word) as l_word, count(*) as frequency from words where text_id in (select id from texts where lang_id=1) group by word order by frequency DESC, l_word LIMIT 30;
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function frequencyDict(Request $request) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        if ($url_args['search_lang']) {
            $lang_id = $url_args['search_lang'];
            $words = Word::select(DB::raw('lower(word) as l_word'),DB::raw('count(word) as frequency'))
                   ->whereIn('text_id', function($query) use ($lang_id){
                                $query->select('id')->from('texts')
                                      ->where('lang_id',$lang_id);
                        })
                  ->groupBy(DB::raw('lower(word)'))
                  ->orderBy(DB::raw('count(word)'), 'DESC');
                        
        if ($url_args['search_word']) {
            $words = $words->where(DB::raw('lower(word)'),'like',mb_strtolower($url_args['search_word'], 'UTF-8'));
        } 

//var_dump($words->toSql());        
            $words = $words 
                    ->take($this->url_args['limit_num'])
                    ->get();
        } else {
            $words = NULL;
        }
        $lang_values = Lang::getList();
        
        return view('corpus.word.freq_dict',
                compact('lang_values', 'words', 'args_by_get', 'url_args'));
    }
}
