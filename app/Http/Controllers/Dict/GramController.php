<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
//use DB;
//use LaravelLocalization;

use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;
use App\Models\Dict\PartOfSpeech;

class GramController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/gram/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Show the list of grammatical attributes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gram_categories = GramCategory::all();
        $grams = array();
        
        foreach ($gram_categories as $gc) {         //   id is gram_category_id
            $grams[$gc->name] = Gram::getByCategory($gc->id);
        }

        return view('dict.gram.index')->with(array('grams' => $grams));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gram_categories = GramCategory::getList();

        return view('dict.gram.create')
                  ->with(['gram_categories' => $gram_categories]);
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
            'name_short_en'  => 'max:15',
            'name_en'  => 'required|max:255',
            'name_short_ru'  => 'max:15',
            'name_ru'  => 'required|max:255',
            'sequence_number' => 'numeric'
        ]);
        
        $gram = Gram::create($request->all());
        
        return Redirect::to('/dict/gram/')
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
        $gram = Gram::find($id);
        
        return view('dict.gram.show')
                  ->with(['gram'=>$gram]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gram = Gram::find($id); 
        $gram_categories = GramCategory::getList();
        
        return view('dict.gram.edit')
                  ->with(['gram' => $gram,
                          'gram_categories' => $gram_categories,
                         ]);
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
            'name_short_en'  => 'max:15',
            'name_en'  => 'required|max:255',
            'name_short_ru'  => 'max:15',
            'name_ru'  => 'required|max:255',
            'sequence_number' => 'numeric'
        ]);
        
        $gram = Gram::find($id);
        $gram->fill($request->all())->save();
        
        return Redirect::to('/dict/gram/')
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
                $gram = Gram::find($id);
                if($gram){
                    $gram_name = $gram->name;
                    // TODO!!! check if gramsets with this gram exist
                    $gram->delete();
                    $result['message'] = \Lang::get('dict.gram_removed', ['name'=>$gram_name]);
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
                return Redirect::to('/dict/gram/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/gram/')
                  ->withSuccess($result['message']);
        }
    }
}
