<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Models\Dict\Label;
use App\Models\Dict\Lang;

class LabelController extends Controller
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
        $this->middleware(
            'auth:ref.edit,/dict/label/',
            ['only' => ['create', 'store', 'edit', 'update', 'destroy']]
        );
        $this->url_args = Label::urlArgs($request);
        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    /**
     * Show the list of labels.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $labels = Label::search($url_args);
        $numAll = $labels->count();
        $labels = $labels->paginate($url_args['limit_num']);

        return view(
            'dict.label.index',
            compact('labels', 'numAll', 'args_by_get', 'url_args')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $lang_values = Lang::getList();
        $url_args = $this->url_args;

        return view(
            'dict.label.create',
            compact('lang_values', 'url_args')
        );
    }

    public function validateRequest(Request $request)
    {
        $this->validate($request, [
            'name_en'  => 'required|max:255',
            'name_ru'  => 'required|max:255',
        ]);

        $data = $request->all();
        return $data;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Label::create($this->validateRequest($request));

        return Redirect::to('/dict/label/')
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
        return Redirect::to('/dict/label/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $label = Label::find($id);
        if (!$label) {
            return Redirect::to('/dict/label/')
                ->withErrors('messages.record_not_exists');
        }
        $lang_values = Lang::getList();
        $url_args = $this->url_args;

        return view(
            'dict.label.edit',
            compact('label', 'lang_values', 'url_args')
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
        $label = Label::find($id);

        $label->fill($this->validateRequest($request, ',code,' . $label->id))->save();

        return Redirect::to('/dict/label/' . ($this->args_by_get))
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
                $label = Label::find($id);
                if ($label) {
                    $label_name = $label->name;
                    // check if wordforms and gramsets exists with this label
                    if ($label->meanings()->count() || $label->lemmas()->count()) {
                        $result['error_message'] = trans('dict.label_can_not_be_removed');
                    } else {
                        $label->delete();
                        $result['message'] = trans('dict.label_removed', ['name' => $label_name]);
                    }
                } else {
                    $error = true;
                    $result['error_message'] = trans('record_not_exists');
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
            return Redirect::to('/dict/label/' . ($this->args_by_get))
                ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/label/' . ($this->args_by_get))
                ->withSuccess($result['message']);
        }
    }

    /**
     * Gets list of labels for drop down list in JSON format
     * Test url: /dict/label/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function labelList(Request $request)
    {

        $label_name = '%' . $request->input('q') . '%';
        $lang_ids = (array)$request->input('lang_id');
        //        $lemma_id = (int)$request->input('lemma_id');

        $list = [];
        $labels = Label::where(function ($q) use ($label_name) {
            $q->where('name_en', 'like', $label_name)
                ->orWhere('name_ru', 'like', $label_name);
        });
        if (sizeof($lang_ids)) {
            $labels = $labels->whereIn('lang_id', $lang_ids);
        }

        $labels = $labels->orderBy('sequence_number')->get();

        foreach ($labels as $label) {
            $list[] = [
                'id'  => $label->id,
                'text' => $label->name
            ];
        }
        //dd($list);        
        //dd(sizeof($labels));
        return Response::json($list);

        /*        $lang_id = (int)$request->input('lang_id');

        $all_labels = Label::getList($lang_id);

        return Response::json($all_labels);*/
    }

    /*
     * test: /ru/dict/label/47/text_count
     */
    public function textCount($id, Request $request)
    {
        $without_link = $request->without_link;
        $label = Label::find($id);
        $count = $label->texts()->count();
        $count = number_format($count, 0, ',', ' ');
        if (!$count || $without_link) {
            return $count;
        }
        return '<a href="' . LaravelLocalization::localizeURL('/corpus/text?search_label=' . $label->id) . '">' . $count . '</a>';
    }

    public function wordformCount($id, Request $request)
    {
        $without_link = $request->without_link;
        $label = Label::find($id);
        $count = $label->wordforms()->count();
        $count = number_format($count, 0, ',', ' ');
        if (!$count || $without_link) {
            return $count;
        }
        return '<a href="' . LaravelLocalization::localizeURL('/dict/wordform?search_label=' . $label->id) . '">' . $count . '</a>';
    }
}
