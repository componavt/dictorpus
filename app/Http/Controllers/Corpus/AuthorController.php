<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use LaravelLocalization;
use Redirect;
use Response;

use App\Models\Dict\Lang;

use App\Models\Corpus\Author;

class AuthorController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:corpus.edit,/corpus/author/', ['only' => ['create','store','edit','update','destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search_name = $request->input('search_name');
        $locale = LaravelLocalization::getCurrentLocale();
        $authors = Author::orderBy('name_'.$locale);
        
        if ($search_name) {
            $authors = $authors->whereIn('id', function ($q) use ($search_name) {
                $q->where('name_en','like', $search_name)
                  ->orWhere('name_ru','like', $search_name);
            })->whereIn('id', function ($q2) use ($search_name) {
                $q2->select('author_id')->from('author_text')
                  ->where('name', 'like', $search_name);
            });
        }
        $numAll = $authors->count();
        $authors = $authors->get();
        
        return view('corpus.author.index',
                    compact('authors', 'search_name', 'numAll'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $project_langs=Lang::projectLangs(); 
        return view('corpus.author.create', compact('project_langs'));
    }

    public function validateRequest(Request $request) {
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
        ]);
        
        return $request->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$this->validateRequest($request);       
        $author = Author::create($data);
        $author->updateNames((array)$request->names);
        
        return Redirect::to('/corpus/author')
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    public function simpleStore(Request $request)
    {
        $data=$this->validateRequest($request);       
        $author = Author::create($data);
        $author->updateNames((array)$request->names);
        return Response::json([$author->id, $author->name]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/corpus/author/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $author = Author::find($id); 
        $project_langs=Lang::projectLangs(); 
        
        return view('corpus.author.edit', compact('author', 'project_langs'));
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
        $data=$this->validateRequest($request);       
        $author = Author::find($id);
        $author->fill($data)->save();
        $author->updateNames((array)$request->names);
        
//        return Redirect::to('/corpus/author/?search_id='.$author->id)
        return Redirect::to('/corpus/author/')
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
                $author = Author::find($id);
                if($author){
                    $author_name = $author->name;
                    if ($author->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.author_has_text', ['name'=>$author_name]);                        
                    } else {
                        foreach ($author->authorNames as $name) {
                            $name -> delete();
                        }
                        $author->delete();
                        $result['message'] = \Lang::get('corpus.author_removed', ['name'=>$author_name]);
                    }
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
                return Redirect::to('/corpus/author/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/author/')
                  ->withSuccess($result['message']);
        }
    }
}
