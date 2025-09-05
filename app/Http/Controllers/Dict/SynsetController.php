<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
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
        
//        $this->url_args = Synset::urlArgs($request);        
//        $this->args_by_get = search_values_by_URL($this->url_args);
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
    
    public function store(Request $request) {
        if (!empty($request->syntypes)) {
            $synset = Synset::create($this->validateRequest($request));
            foreach ($request->syntypes as $meaning_id => $syntype_id) {
                $synset->meanings()->attach([$meaning_id=>['syntype_id'=>$syntype_id]]);
            }
            $synset->pos_id = $synset->meanings()->first()->lemma->pos_id;
            $synset->save();
        }
        return Redirect::to('/service/dict/synsets/'.($this->args_by_get));
    }
    
    public function edit($id)
    {
        $synset = Synset::find($id);
        
        $lang_values = Lang::getProjectList();
        $pos_values = [NULL=>''] + PartOfSpeech::getList();
        $syntype_values = Syntype::getList(1);
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        return view('dict.synset.edit',
                compact('synset', 'lang_values', 'pos_values', 'syntype_values',
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
/*        $synset = Synset::find($id);
        $this->validate($request, [
            'informant_id'  => 'required|integer',
            'filename'  => 'max:100',
        ]);
        
        $synset->informant_id = (int)$request->informant_id;
        if ($synset->filename != $request->filename) {
            Storage::disk('synsets')->move($synset->filename, $request->filename);
            $synset->filename = $request->filename;
        }
        $synset->save();
        $synset->lemmas()->sync((array)$request->lemmas);
        
        return Redirect::to('/dict/synset/'.($this->args_by_get))
                       ->withSuccess(\Lang::get('messages.updated_success'));
*/    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $informant_id=null)
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
    
    public function setStatus(int $id, int $status) {
        $synset = Synset::findOrFail($id);
        $synset->status = $status == 1 ? 1 : 0;
        $synset->save();
        return $synset->status;
    }
}
