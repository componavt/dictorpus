<?php

namespace App\Http\Controllers\Dict;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Library\CollocationExampleSummary;
use App\Library\MeaningCollocationCandidateData;
use App\Library\MeaningExampleAvailability;
use App\Library\SynsetCollocationCandidateData;
use App\Library\SynsetCollocationCandidateFilters;
use App\Library\SynsetTextFields;
use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;
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

    /**
     * Проверяет и очищает переданные текстовые поля синсета.
     * Отсутствующие в запросе поля не изменяются.
     */
    protected function validatedTextFields(Request $request)
    {
        $this->validate($request, [
            'descr' => 'string',
            'etymology' => 'string|max:60000',
            'potential_candidates' => 'string|max:60000',
        ], [], [
            'descr' => trans('dict.synset_general_meaning'),
            'etymology' => trans('dict.etymology'),
            'potential_candidates' => trans('dict.synset_potential_candidates'),
        ]);

        $raw = $request->all();
        $data = [];

        foreach (['etymology', 'potential_candidates'] as $field) {
            if (array_key_exists($field, $raw)) {
                $data[$field] = SynsetTextFields::clean($raw[$field]);
            }
        }

        return $data;
    }

    public function validateRequest(Request $request, $code_rule = '')
    {
        $this->validate($request, [
            'lang_id' => 'required|numeric',
            'pos_id' => 'numeric',
        ]);

        $data = $request->only([
            'lang_id',
            'pos_id',
            'comment',
            'descr',
            'dominant_id',
        ]);

        return array_merge(
            $data,
            $this->validatedTextFields($request)
        );
    }

    public function create()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $lang = Lang::find($url_args['search_lang']);

        if (!$lang) {
            return Redirect::to('/service/dict/synsets/' . $this->args_by_get)->withErrors('Выберите язык');
        }

        $pos_values = PartOfSpeech::getList();
        $syntype_values = Syntype::getList(1);
        $action = 'create';
        $default_pos_id = old('pos_id', 1);
        $default_pos_id = is_scalar($default_pos_id) ? (int) $default_pos_id : null;
        $form_state = $this->memberFormState();

        return view('dict.synset.modify', array_merge(compact(
            'action',
            'args_by_get',
            'default_pos_id',
            'lang',
            'pos_values',
            'syntype_values',
            'url_args'
        ), $form_state));
    }

    /**
     * Создаёт синсет из выбранных значений.
     */
    public function store(Request $request)
    {
        $members = $this->validatedMembers($request);
        $data = $this->validateRequest($request);
        $data['pos_id'] = (int) $request->input('pos_id');
        $data['dominant_id'] = $request->input('dominant_id') ? (int) $request->input('dominant_id') : null;
        $data['descr'] = $data['descr'] ?? '';
        $data['comment'] = $data['comment'] ?? '';

        $synset = DB::transaction(function () use ($data, $members) {
            $synset = Synset::create($data);
            $synset->meanings()->sync($members);
            $synset->normalizeMemberOrder();

            return $synset;
        });

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
        $collocation_matrix['attached_examples'] = (new CollocationExampleSummary())->forMatrix($collocation_matrix);

        $potential_members = $synset->searchPotentialMembers();

        $pos_values = [NULL => ''] + PartOfSpeech::getList();
        $syntype_values = Syntype::getList(1);

        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $action = 'edit';
        $default_pos_id = old('pos_id', $synset->pos_id);
        $default_pos_id = is_scalar($default_pos_id) ? (int) $default_pos_id : null;
        $form_state = $this->memberFormState();

        if ($form_state['restore_members']) {
            $submitted_ids = array_merge($form_state['restored_members']->pluck('id')->all(), array_keys($form_state['new_member_values']));
            $potential_members = collect($potential_members)->reject(function ($meaning) use ($submitted_ids) {
                return in_array((int) $meaning->id, $submitted_ids);
            })->values();
        }

        return view(
            'dict.synset.modify',
            array_merge(compact(
                'action',
                'default_pos_id',
                'args_by_get',
                'collocation_matrix',
                'core_members',
                'periphery_members',
                'pos_values',
                'potential_members',
                'synset',
                'syntype_values',
                'url_args'
            ), $form_state)
        );
    }

    /**
     * Сохраняет синсет и открывает редактор или список.
     */
    public function update(Request $request, $id)
    {
        $synset = Synset::findOrFail($id);
        $request->merge(['lang_id' => $synset->lang_id]);
        $members = $this->validatedMembers($request);
        $text_fields = $this->validatedTextFields($request);

        DB::transaction(function () use ($request, $synset, $members, $text_fields) {
            $synset->pos_id = (int) $request->input('pos_id');
            $synset->comment = $request->comment ?? '';
            $synset->descr = $request->descr ?? '';
            $synset->dominant_id = $request->input('dominant_id') ? (int) $request->input('dominant_id') : null;
            $synset->fill($text_fields);
            $synset->save();
            $synset->meanings()->sync($members);
            $synset->normalizeMemberOrder();
        });

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
        $args_by_get = $this->args_by_get;

        return view('dict.synset.candidates', compact(
            'all_members',
            'args_by_get',
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

    /**
     * Собирает участников из строк формы и выбранных new_members.
     */
    protected function validatedMembers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang_id' => 'required|integer|exists:langs,id',
            'pos_id' => 'required|integer|exists:parts_of_speech,id',
            'meanings' => 'array|max:500',
            'meanings.*' => 'array',
            'meanings.*.syntype_id' => 'required|integer|exists:syntypes,id',
            'member_notes' => 'array|max:500',
            'member_notes.*' => 'string|max:10000',
            'new_members' => 'array|max:500',
            'new_members.*' => 'required|integer|min:1|exists:meanings,id',
            'dominant_id' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        $members = [];

        foreach ($request->input('meanings', []) as $id => $attributes) {
            if (!ctype_digit((string) $id) || (int) $id < 1) {
                $validator->errors()->add('meanings', trans('dict.synset_members_invalid'));
                continue;
            }

            $members[(int) $id] = ['syntype_id' => (int) $attributes['syntype_id']];
        }

        foreach ($request->input('new_members', []) as $id) {
            $id = (int) $id;

            if (!isset($members[$id])) {
                $members[$id] = ['syntype_id' => Syntype::TYPE_FULL];
            }
        }

        if (!$members) {
            $validator->errors()->add('new_members', trans('dict.synset_members_required'));
        }

        if (count($members) > 500) {
            $validator->errors()->add('meanings', trans('dict.synset_members_invalid'));
        }

        if ($validator->errors()->count()) {
            $this->throwValidationException($request, $validator);
        }

        $meanings = Meaning::with('lemma')->whereIn('id', array_keys($members))->get();
        $pos_values = PartOfSpeech::getList();
        $expected_pos_id = (int) $request->input('pos_id');

        if ($meanings->count() !== count($members)) {
            $validator->errors()->add('meanings', trans('dict.synset_members_invalid'));
        }

        foreach ($meanings as $meaning) {
            if (!$meaning->lemma || (int) $meaning->lemma->lang_id !== (int) $request->input('lang_id')) {
                $validator->errors()->add('meanings', trans('dict.synset_members_language'));
                continue;
            }

            if ((int) $meaning->lemma->pos_id !== $expected_pos_id) {
                $validator->errors()->add('meanings', trans('synset_form.member_pos_mismatch', [
                    'member' => $meaning->lemma->lemma . ' [' . $meaning->meaning_n . '; #' . $meaning->id . ']',
                    'actual' => $pos_values[$meaning->lemma->pos_id] ?? trans('synset_form.pos_unknown'),
                    'expected' => $pos_values[$expected_pos_id],
                ]));
            }
        }

        $dominant_id = (int) $request->input('dominant_id', 0);

        if ($dominant_id) {
            if (!isset($members[$dominant_id])) {
                $validator->errors()->add('dominant_id', trans('dict.synset_dominant_member'));
            } elseif ((int)$members[$dominant_id]['syntype_id'] !== Syntype::TYPE_FULL) {
                $validator->errors()->add('dominant_id', trans('dict.synset_order_dominant_core'));
            }
        }

        if ($validator->errors()->count()) {
            $this->throwValidationException($request, $validator);
        }

        $notes = $request->input('member_notes', []);

        foreach ($members as $id => &$attributes) {
            if (array_key_exists($id, $notes)) {
                $attributes['collocation_note'] = SynsetTextFields::clean($notes[$id]);
            }
        }
        unset($attributes);

        return $members;
    }

    /**
     * Восстанавливает отправленный состав, не подменяя его составом из БД.
     */
    protected function memberFormState()
    {
        $restore_members = old('synset_form') === '1';
        $old_meanings = $restore_members ? old('meanings', []) : [];
        $old_meanings = is_array($old_meanings) ? $old_meanings : [];
        $old_new_ids = $restore_members ? old('new_members', []) : [];
        $old_new_ids = is_array($old_new_ids) ? $old_new_ids : [];
        $valid_id = function ($id) {
            return is_scalar($id) && ctype_digit((string) $id) && (int) $id > 0;
        };

        $restored_ids = array_slice(array_values(array_filter(array_keys($old_meanings), $valid_id)), 0, 500);
        $old_new_ids = array_slice(array_values(array_filter($old_new_ids, $valid_id)), 0, 500);
        $restored_by_id = Meaning::with('lemma')->whereIn('id', $restored_ids)->get()->keyBy('id');

        $restored_members = collect($restored_ids)->map(function ($id) use ($restored_by_id) {
            return $restored_by_id->get($id);
        })->filter(function ($meaning) {
            return $meaning && $meaning->lemma;
        })->values();

        $selected_new_members = Meaning::with('lemma')->whereIn('id', $old_new_ids)->get();
        $syntype_values = Syntype::getList(1);
        $restored_member_types = [];
        $new_member_values = [];

        foreach ($restored_members as $meaning) {
            $attributes = $old_meanings[$meaning->id] ?? [];
            $type = is_array($attributes) ? ($attributes['syntype_id'] ?? Syntype::TYPE_FULL) : Syntype::TYPE_FULL;
            $restored_member_types[$meaning->id] = is_scalar($type) && isset($syntype_values[$type])
                ? (int) $type : Syntype::TYPE_FULL;
        }

        foreach ($selected_new_members as $meaning) {
            if ($meaning->lemma) {
                $new_member_values[$meaning->id] = $meaning->lemma->lemma . ' [' . $meaning->meaning_n . '] — ' . $meaning->getMeaningTextLocale();
            }
        }

        $form_dominant_id = old('dominant_id');
        $form_dominant_id = is_scalar($form_dominant_id) ? (int) $form_dominant_id : null;

        return compact('restore_members', 'restored_members', 'restored_member_types', 'new_member_values', 'form_dominant_id');
    }
}
