<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;

use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Synset;
use App\Models\Dict\Syntype;

class SynsetController extends Controller
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
        $this->middleware('auth:dict.edit,/dict/synset', 
                          ['except'=>['index']]);
        
        $this->url_args = Synset::urlArgs($request);        
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index() {
/*        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $synsets = Synset::search($url_args);
        $numAll = $synsets->count();
        $synsets = $synsets->paginate($url_args['limit_num']);         
        
        $informant_values = [NULL => ''] + Synset::getSpeakerList();
        $lang_values = Lang::getList();        
        
        return view('dict.synset.index',
                compact('synsets', 'informant_values', 'lang_values', 'numAll', 
                        'args_by_get', 'url_args'));
*/    }
    
    public function show() {
//        return Redirect::to('/dict/synset/'.($this->args_by_get));
    }
    
    public function validateRequest(Request $request, $code_rule='') {
        $this->validate($request, [
            'lang_id'=> 'required|numeric',
            'pos_id' => 'numeric',
            ]);
        
        $data = $request->only(['lang_id', 'pos_id', 'comment']);
        return $data;
    }
    
    public function create()
    {       
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $lang = Lang::find($url_args['search_lang']);
        if (!$lang) {
            Redirect::to('/service/dict/synsets/'.($this->args_by_get))
                    ->withErrors('Выберите язык');
        }
        $pos_values = PartOfSpeech::getList();
        $syntype_values = Syntype::getList(1);
        $action = 'create';
        
        return view('dict.synset.modify',
                compact('action', 'lang', 'pos_values', 'syntype_values', 
                        'args_by_get', 'url_args'));
    }
    
    public function store(Request $request) {
//dd($request->all());        
        if (!empty($request->meanings)) {
            $synset = Synset::create($this->validateRequest($request));
            $synset->meanings()->sync((array)$request->meanings);
            $synset->pos_id = $synset->meanings()->first()->lemma->pos_id;
            $synset->save();
        }
        return Redirect::to('/service/dict/synsets/'.($this->args_by_get))
                ->withSuccess(\Lang::get('messages.created_success'));;
    }
    
    public function edit($id)
    {
        $synset = Synset::find($id);
        $potential_members = $synset->searchPotentialMembers();
        
        $pos_values = [NULL=>''] + PartOfSpeech::getList();
        $syntype_values = Syntype::getList(1);
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $action = 'edit';
        
        return view('dict.synset.modify',
                compact('action', 'pos_values', 'synset', 'potential_members', 
                        'syntype_values', 'args_by_get', 'url_args'));
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
        $synset = Synset::find($id);
        
        $synset->comment = $request->comment;
        $synset->save();
        $synset->meanings()->sync((array)$request->meanings);
        
        return Redirect::to('/service/dict/synsets/'.($this->args_by_get))
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
        $result =[];
        if(!empty($id)) {
            try{
                $synset = Synset::find($id);
                if($synset){
                    $result['message'] = \Lang::get('dict.synset_removed', ['num'=>$synset->id]);
                    $synset->meanings()->detach();
                    $synset->delete();
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
            return Redirect::to('/dict/synset/'.($this->args_by_get))
                           ->withErrors($result['error_message']);
        }
        return Redirect::to('/service/dict/synsets/'.($this->args_by_get))
              ->withSuccess($result['message']);
    
    }
}
