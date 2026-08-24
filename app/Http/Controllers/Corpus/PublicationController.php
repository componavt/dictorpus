<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

use App\Models\Corpus\Publication;

class PublicationController extends Controller
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
        $this->middleware('auth:corpus.edit,/corpus/publication/', ['only' =>
        ['create', 'store', 'edit', 'update', 'destroy']]);

        $this->url_args = Publication::urlArgs($request);

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

        $publications = Publication::search($url_args);

        $numAll = $publications->count();
        $publications = $publications->paginate($url_args['limit_num']);

        return view(
            'corpus.publication.index',
            compact('publications', 'numAll', 'args_by_get', 'url_args')
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

        return view('corpus.publication.create', compact('args_by_get', 'url_args'));
    }

    public function validateRequest(Request $request)
    {
        $this->validate($request, [
            'authors'  => 'max:255',
            'title'  => 'required|max:255',
            'year'  => 'integer',
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
        $data = $this->validateRequest($request);
        $publication = Publication::create($data);

        return Redirect::to('/corpus/publication/'. ($this->args_by_get))
            ->withSuccess(trans('messages.created_success'));
    }

    public function simpleStore(Request $request)
    {
        $data = $this->validateRequest($request);
        $publication = Publication::create($data);
        return Response::json([$publication->id, $publication->name]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/corpus/publication/'. ($this->args_by_get));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $url_args = $this->url_args;
        $publication = Publication::find($id);

        return view('corpus.publication.edit', compact('publication', 'url_args'));
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
        $data = $this->validateRequest($request);
        $publication = Publication::find($id);
        $publication->fill($data)->save();

        return Redirect::to('/corpus/publication/' . ($this->args_by_get))
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
                $publication = Publication::find($id);
                if ($publication) {
                    $title = $publication->title;
                    if ($publication->texts()->count() > 0) {
                        $error = true;
                        $result['error_message'] = trans('corpus.publication_has_texts', ['name' => $title]);
                    } else {
                        $publication->delete();
                        $result['message'] = trans('corpus.publication_removed', ['name' => $title]);
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
            return Redirect::to('/corpus/publication/' . ($this->args_by_get))
                ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/publication/' . ($this->args_by_get))
                ->withSuccess($result['message']);
        }
    }
}
