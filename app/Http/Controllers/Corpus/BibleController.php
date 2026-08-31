<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

use App\Models\Corpus\Bible;

class BibleController extends Controller
{
    public $url_args = [];
    public $args_by_get = '';

    /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/bible/', ['except' => ['index', 'bibleList']]);
        $this->url_args = Bible::urlArgs($request);

        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $bibles = Bible::search($url_args)->get()->sortBy('number_in_genres');
        $numAll = $bibles->count();

        return view(
            'corpus.bible.index',
            compact('bibles', 'numAll', 'args_by_get', 'url_args')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        return view(
            'corpus.bible.create',
            compact('args_by_get', 'url_args')
        );
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
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
        ]);

        Bible::create($request->all());

        return Redirect::to('/corpus/bible/' . ($this->args_by_get))
            ->withSuccess(trans('messages.created_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/corpus/bible/' . ($this->args_by_get));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $bible = Bible::find($id);

        return view(
            'corpus.bible.edit',
            compact('bible', 'args_by_get', 'url_args')
        );
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
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
        ]);

        $bible = Bible::find($id);
        $bible->fill($request->all())->save();

        return Redirect::to('/corpus/bible/' . ($this->args_by_get))
            ->withSuccess(trans('messages.updated_success'));
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
        $result = [];
        if ($id != "" && $id > 0) {
            try {
                $bible = Bible::find($id);
                if ($bible) {
                    $bible_name = $bible->name;
                    if ($bible->texts()->count() > 0) {
                        $error = true;
                        $result['error_message'] = trans('corpus.bible_has_text', ['name' => $bible_name]);
                    } else {
                        $bible->delete();
                        $result['message'] = trans('corpus.bible_removed', ['name' => $bible_name]);
                    }
                } else {
                    $error = true;
                    $result['error_message'] = trans('messages.record_not_exists');
                }
            } catch (\Exception $ex) {
                $error = true;
                $status_code = $ex->getCode();
                $result['error_code'] = $ex->getCode();
                $result['error_message'] = $ex->getMessage();
            }
        } else {
            $error = true;
            $status_code = 400;
            $result['message'] = 'Request data is empty';
        }

        if ($error) {
            return Redirect::to('/corpus/bible/' . ($this->args_by_get))
                ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/bible/' . ($this->args_by_get))
                ->withSuccess($result['message']);
        }
    }

    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/bible/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function bibleList(Request $request)
    {
        $bible_name = '%' . $request->input('q') . '%';

        $list = [];
        $bibles = Bible::where(function ($q) use ($bible_name) {
            $q->where('name_en', 'like', $bible_name)
                ->orWhere('name_ru', 'like', $bible_name);
        })->orderBy('sequence_number')->get();

        foreach ($bibles as $bible) {
            $list[] = [
                'id'  => $bible->id,
                'text' => $bible->name
            ];
        }

        return Response::json($list);
    }
}
