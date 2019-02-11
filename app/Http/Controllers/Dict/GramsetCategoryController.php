<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Redirect;

use App\Models\Dict\GramsetCategory;

class GramsetCategoryController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/gramset_category/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Show the list of grammatical attributes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gramset_categories = GramsetCategory::all()->sortBy('sequence_number');

        return view('dict.gramset_category.index', compact('gramset_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dict.gramset_category.create');
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
        
        $gram = GramsetCategory::create($request->all());
        
        return Redirect::to('/dict/gramset_category/')
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
        return Redirect::to('/dict/gramset_category');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gramset_category = GramsetCategory::find($id); 
        
        return view('dict.gramset_category.edit', compact('gramset_category'));
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
        
        $gramset_category = GramsetCategory::find($id);
        $gramset_category->fill($request->all())->save();
        
        return Redirect::to('/dict/gramset_category')
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
                $gramset_category = GramsetCategory::find($id);
                if($gramset_category){
                    $name = $gramset_category->name;
                    // TODO!!! check if gramsets with this gramset_category exist
                    $gramset_category->delete();
                    $result['message'] = \Lang::get('dict.category_removed', ['name'=>$name]);
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
                return Redirect::to('/dict/gramset_category')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/gramset_category')
                  ->withSuccess($result['message']);
        }
    }
}
