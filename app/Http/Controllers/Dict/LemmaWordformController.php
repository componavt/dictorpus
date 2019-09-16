<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Library\Grammatic;

use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaBase;

class LemmaWordformController extends Controller
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
        $this->middleware('auth:dict.edit,/dict/lemma/', 
                          ['only' => ['store','edit','update','destroy', 'reload']]);
        
        $this->url_args = Lemma::urlArgs($request);  
        
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
    }

    /**
     * Saves links 1)text's word with lemma's meaning 
     *             2)wordform with gramset and dialects
     * 
     * @param Request $request
     * @return Null
     */
    public function store(Request $request)
    {
        $lemma_id = (int)$request->input('lemma_id');
        $text_id = (int)$request->input('text_id'); 
        $w_id = (int)$request->input('w_id'); 
        
        if (!$lemma_id || !$text_id || !$w_id) {
            return;
        }

        $lemma = Lemma::find($lemma_id);
        $text = Text::find($text_id);
        $word = Word::getByTextWid($text_id, $w_id);
        
        if (!$lemma || !$text || !$word || !$word->sentence_id) { return; }
        
        $meaning_id = $request->input('meaning_id'); 
        $gramset_id = $request->input('gramset_id'); 
        $dialects = (array)$request->input('dialects'); 
        
        $wordform = $request->input('wordform'); 
        if (!$wordform) {
            $wordform = $word -> word;
        }
        
        $lemma->addWordformFromText($wordform, $gramset_id, $dialects, $text_id, $w_id);
        $text->addLinkWithMeaning($lemma, $meaning_id, $w_id, $word);
        return 1;            
    }   
    
    /**
     * Shows the form for editing of lemma's wordforms.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $dialect_id = (int)$request->dialect_id;
        $dialect_name = Dialect::getNameByID($dialect_id);
        
        if (!$dialect_id) {
            $dialect_id = NULL;
        }

        $lemma = Lemma::find($id);
        $gramset_values = ['NULL'=>'']+Gramset::getGroupedList($lemma->pos_id,$lemma->lang_id,true);
        $dialect_values = ['NULL'=>'']+Dialect::getList($lemma->lang_id)+['all'=>'ДЛЯ ВСЕХ ДИАЛЕКТОВ'];
        
        $base_list = LemmaBase::baseList($lemma->lang_id, $lemma->pos_id);
                
        return view('dict.lemma_wordform.edit',
                    compact('base_list','dialect_id', 'dialect_name', 'dialect_values', 
                            'gramset_values', 'lemma', 'args_by_get', 'url_args'));
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
        $lemma= Lemma::findOrFail($id);

        $dialect_id = $request->dialect_id;
        if (!(int)$dialect_id) {
            $dialect_id = NULL;
        }
        // WORDFORMS UPDATING
        //remove all records from table lemma_wordform
        $lemma->updateBases($request->bases, $lemma->pos_id, $request->dialect_id_for_bases);
        $lemma-> wordforms()->wherePivot('dialect_id',$dialect_id)->detach();
        //add wordforms from full table of gramsets
        $lemma-> storeWordformGramsets($request->lang_wordforms, $request->lang_wordforms_dialect);
        //add wordforms without gramsets
        $lemma-> storeWordformsEmpty($request->empty_wordforms, $dialect_id);

        // updates links with text examples
//        $lemma->updateTextLinks();
                
        
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get).($this->args_by_get ? '&' : '?').'update_text_links=1')
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    public function destroy(Request $request, $id) {
        $error = false;
//        $status_code = 200;
        $result =[];
        if($id > 0) {
            try{
                $dialect_id = $request->dialect_id;
                $lemma = Lemma::findOrFail($id);
                if($lemma){
                    $result['message'] = \Lang::get('dict.wordforms_removed');
                    $lemma->wordforms->wherePivot('dialect_id',$dialect_id)->detach();
                } else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
//                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        } else{
            $error =true;
//            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
            return Redirect::to('/dict/lemma/'.$id.($this->args_by_get))
                           ->withErrors($result['error_message']);
        }
        return Redirect::to('/dict/lemma/'.$id.($this->args_by_get)."&update_text_links=1")
              ->withSuccess($result['message']);        
    }

    /**
     * Get bases from table OR from wordforms
     * Delete and create wordforms again
     * 
     * Example: /dict/lemma_wordform/22407_43/reload/
     * 
     * @param Int $id
     * @param Int $dialect_id
     * @return \Illuminate\Http\Response
     */
    public function reload($id, $dialect_id) {
        $lemma = Lemma::findOrFail($id);        
        
        $name_num = ($lemma->features && $lemma->features->number) ? Grammatic::nameNumFromNumberField($lemma->features->number) : null; 

        $stems = $lemma->getBases($dialect_id);
//dd($stems);        
//dd($name_num);        
        $lemma->wordforms()->wherePivot('dialect_id',$dialect_id)->detach();

        $gramset_wordforms = Grammatic::wordformsByStems($lemma->lang_id, $lemma->pos_id, $dialect_id, $name_num, $stems);
//dd($gramset_wordforms);        
        if ($gramset_wordforms) {
            $lemma->storeWordformsFromSet($gramset_wordforms, $dialect_id); 
            $lemma->updateTextLinks();
        }
        
        return view('dict.lemma_wordform._wordform_table', compact('lemma')); 
    }
}
