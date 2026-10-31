<?php

namespace App\Http\Controllers\Dict;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Library\MeaningCollocationCandidateData;
use App\Library\MeaningExampleAvailability;
use App\Library\SynsetCollocationCandidateData;
use App\Library\SynsetCollocationCandidateFilters;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Synset;
use App\Models\Dict\Syntype;

class SynsetController extends Controller
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
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware(
            'auth:dict.edit,/dict/synset',
            ['except' => ['index']]
        );

        $this->url_args = Synset::urlArgs($request);
        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    public function index()
    {
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
*/
    }

    public function show()
    {
        //        return Redirect::to('/dict/synset/'.($this->args_by_get));
    }

    public function validateRequest(Request $request, $code_rule = '')
    {
        $this->validate($request, [
            'lang_id' => 'required|numeric',
            'pos_id' => 'numeric',
        ]);
        $data = $request->only(['lang_id', 'pos_id', 'comment', 'descr', 'dominant_id', 'collocates']);
        return $data;
    }

    public function create()
    {
        $default_pos_id = 1;
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lang = Lang::find($url_args['search_lang']);
        if (!$lang) {
            Redirect::to('/service/dict/synsets/' . ($this->args_by_get))
                ->withErrors('Выберите язык');
        }
        $pos_values = PartOfSpeech::getList();
        $syntype_values = Syntype::getList(1);
        $action = 'create';

        return view(
            'dict.synset.modify',
            compact(
                'action',
                'default_pos_id',
                'lang',
                'pos_values',
                'syntype_values',
                'args_by_get',
                'url_args'
            )
        );
    }

    /**
     * Создаёт непустой синсет и открывает редактор или список.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'meanings' => 'required|array',
        ], [
            'meanings.required' => trans('dict.synset_members_required'),
        ]);

        $synset = Synset::create($this->validateRequest($request));

        $synset->meanings()->sync((array) $request->meanings);
        $synset->pos_id = $synset->meanings()->first()->lemma->pos_id;
        $synset->save();

        return $this->redirectAfterSave($request, $synset, 'messages.created_success');
    }

    /**
     * Открывает редактор синсета и загружает сочетаемость его участников.
     */
    public function edit($id)
    {
        $synset = Synset::with([
            'core.lemma',
            'core.collocations.collocateMeaning.lemma',
            'core.collocations.conceptCategory',
            'periphery.lemma',
            'periphery.collocations.collocateMeaning.lemma',
            'periphery.collocations.conceptCategory',
        ])->findOrFail($id);

        $core_members = $synset->coreWithFrequencies()->values();
        $periphery_members = $synset->periphery->values();
        $collocation_members = $core_members
            ->merge($periphery_members)
            ->values();

        $collocation_matrix = $synset->collocationsMatrix($collocation_members);

        $collocation_matrix['example_pairs'] = (new MeaningExampleAvailability())->forMatrix($collocation_matrix);

        $potential_members = $synset->searchPotentialMembers();

        $pos_values = [NULL => ''] + PartOfSpeech::getList();
        $syntype_values = Syntype::getList(1);

        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $action = 'edit';

        return view(
            'dict.synset.modify',
            compact(
                'action',
                'args_by_get',
                'collocation_matrix',
                'core_members',
                'periphery_members',
                'pos_values',
                'potential_members',
                'synset',
                'syntype_values',
                'url_args'
            )
        );
    }

    /**
     * Сохраняет синсет и открывает редактор или список по выбранной кнопке.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $synset = Synset::findOrFail($id);

        $synset->comment = $request->comment;
        $synset->descr = $request->descr;
        $synset->collocates = $request->collocates;
        $synset->dominant_id = (int) $request->dominant_id;
        $synset->save();

        $synset->meanings()->sync((array) $request->meanings);

        return $this->redirectAfterSave($request, $synset, 'messages.updated_success');
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
        $result = [];
        if (!empty($id)) {
            try {
                $synset = Synset::find($id);
                if ($synset) {
                    $result['message'] = trans('dict.synset_removed', ['num' => $synset->id]);
                    $synset->meanings()->detach();
                    $synset->delete();
                } else {
                    $error = true;
                    $result['error_message'] = trans('messages.record_not_exists');
                }
            } catch (\Exception $ex) {
                $error = true;
                //                    $status_code = $ex->getCode();
                $result['error_code'] = $ex->getCode();
                $result['error_message'] = $ex->getMessage();
            }
        } else {
            $error = true;
            //            $status_code = 400;
            $result['message'] = 'Request data is empty';
        }

        if ($error) {
            return Redirect::to('/dict/synset/' . ($this->args_by_get))
                ->withErrors($result['error_message']);
        }
        return Redirect::to('/service/dict/synsets/' . ($this->args_by_get))
            ->withSuccess($result['message']);
    }

    /**
     * Возвращает в редактор или список после сохранения синсета.
     * Сохраняет язык интерфейса и параметры исходного списка.
     */
    protected function redirectAfterSave(Request $request, Synset $synset, $message_key)
    {
        $path = $request->input('after_save') === 'list'
            ? '/service/dict/synsets/'
            : '/dict/synset/' . $synset->id . '/edit';

        $url = LaravelLocalization::localizeURL($path)
            . $this->args_by_get;

        return Redirect::to($url)
            ->withSuccess(trans($message_key));
    }

    /**
     * Показывает корпусных кандидатов для всех участников синсета.
     */
    public function collocationCandidates($id, Request $request)
    {
        $synset = Synset::with([
            'core.lemma',
            'core.meaningTexts.lang',
            'periphery.lemma',
            'periphery.meaningTexts.lang',
        ])->findOrFail($id);

        $core_members = $synset->coreWithFrequencies()->values();

        $all_members = $core_members
            ->merge($synset->periphery)
            ->unique('id')
            ->values();

        $core_ids = $core_members->pluck('id')->all();

        $filter_service = new SynsetCollocationCandidateFilters();
        $selected_member_ids = $filter_service->members($request, $all_members);

        $members = $all_members->filter(function ($member) use ($selected_member_ids) {
            return in_array((int) $member->id, $selected_member_ids, true);
        })->values();

        $filter_values = $filter_service->values($all_members);
        $filters = $filter_service->read($request, $filter_values);
        $options = $filter_service->options($filters);

        $pos_values = PartOfSpeech::getGroupedList();

        $synset_id = (int) $synset->id;

        $base_url = LaravelLocalization::localizeURL(
            '/dict/synset/' . $synset_id . '/examples/candidates'
        );

        $candidate_data = new SynsetCollocationCandidateData();

        $rows = $candidate_data->prepare($members, $options, $filters['partner_pos_id']);

        $partner_total = count($rows);

        $rows = $candidate_data->filterRows($rows, $members->count(), $filters['row_mode']);

        if ($filters['unrecorded_only']) {
            $rows = $candidate_data->filterUnrecorded($rows);
        }

        $partner_count = count($rows);

        $candidate_groups = (new MeaningCollocationCandidateData())->prepare($rows);

        $example_query = $filters;
        unset(
            $example_query['partner_pos_id'],
            $example_query['row_mode'],
            $example_query['unrecorded_only']
        );

        $example_query = array_merge($example_query, [
            'synset_id' => $synset_id,
            'page' => 1,
            'per_page' => 20,
        ]);

        foreach ($candidate_groups as &$group) {
            foreach ($group['items'] as &$item) {
                foreach ($item['cells'] as $source_id => &$cell) {
                    $query = array_merge($example_query, ['partner_meaning_id' => $item['meaning_id']]);
                    $cell['examples_url'] = LaravelLocalization::localizeURL('/dict/meaning/' . $source_id . '/examples/search') . '?' . http_build_query($query);
                }
                unset($cell);
            }
            unset($item);
        }
        unset($group);

        $back_url = LaravelLocalization::localizeURL('/dict/synset/' . $synset->id . '/edit') . $this->args_by_get;

        return view('dict.synset.candidates', compact(
            'all_members',
            'back_url',
            'base_url',
            'candidate_groups',
            'core_ids',
            'filter_values',
            'filters',
            'members',
            'partner_count',
            'partner_total',
            'pos_values',
            'selected_member_ids',
            'synset',
            'synset_id'
        ));
    }
}
