<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Redirect;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Syntype;

class SyntypeController extends Controller
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
        $this->middleware('auth:dict.edit,/dict/syntype', 
                          ['except'=>['index']]);
        
//        $this->url_args = Syntype::urlArgs($request);        
//        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index() {
/*        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $syntypes = Syntype::search($url_args);
        $numAll = $syntypes->count();
        $syntypes = $syntypes->paginate($url_args['limit_num']);         
        
        $informant_values = [NULL => ''] + Syntype::getSpeakerList();
        $lang_values = Lang::getList();        
        
        return view('dict.syntype.index',
                compact('syntypes', 'informant_values', 'lang_values', 'numAll', 
                        'args_by_get', 'url_args'));
*/    }
    
    public function show() {
//        return Redirect::to('/dict/syntype/'.($this->args_by_get));
    }
    
    public function store() {
//        return Redirect::to('/dict/syntype/'.($this->args_by_get));
    }
    
    public function edit($id)
    {
/*        $syntype = Syntype::find($id);
        
        $lang_values = [NULL => ''] + Lang::getList();
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        return view('dict.syntype.edit',
                compact('syntype', 'informant_values', 'lang_id', 'lang_values', 
                        'lemma_values', 'args_by_get', 'url_args'));
*/    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
/*        $syntype = Syntype::find($id);
        $this->validate($request, [
            'informant_id'  => 'required|integer',
            'filename'  => 'max:100',
        ]);
        
        $syntype->informant_id = (int)$request->informant_id;
        if ($syntype->filename != $request->filename) {
            Storage::disk('syntypes')->move($syntype->filename, $request->filename);
            $syntype->filename = $request->filename;
        }
        $syntype->save();
        $syntype->lemmas()->sync((array)$request->lemmas);
        
        return Redirect::to('/dict/syntype/'.($this->args_by_get))
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
/*        $error = false;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $syntype = Syntype::find($id);
                if($syntype){
                    $result['message'] = \Lang::get('dict.syntype_removed', ['name'=>$syntype->filename]);
                    $syntype->lemmas()->detach();
                    Storage::disk('syntypes')->delete($syntype->filename);
                    $syntype->delete();
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
            return Redirect::to('/dict/syntype/'.($this->args_by_get))
                           ->withErrors($result['error_message']);
        }
        return Redirect::to('/dict/syntype/'.($this->args_by_get))
              ->withSuccess($result['message']);
*/    
    }
    
}
