<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Library\MeaningExampleFilterInput;
use App\Library\MeaningExampleFilterValues;
use App\Library\MeaningCollocationCandidateData;
use App\Library\MeaningCollocationCandidateLinks;
use App\Library\MeaningExampleSearch;
use App\Models\User;
use App\Models\Dict\Meaning;
use App\Models\Dict\PartOfSpeech;

class MeaningCollocationCandidateController extends Controller
{
    public function index($meaning_id, Request $request)
    {
        $source = Meaning::with(['lemma.lang', 'lemma.pos', 'meaningTexts.lang'])->findOrFail($meaning_id);
        $filter_values = (new MeaningExampleFilterValues())->load($source);
        $filters = $this->filters($request, $filter_values);
        $synset_id = $filters['synset_id'] ?? null;
        $this->checkSynset($source, $synset_id);

        $search = new MeaningExampleSearch($source, null, [
            'distance_from' => $filters['distance_from'],
            'distance_to' => $filters['distance_to'],
            'source_threshold' => $filters['source_only_checked'],
            'partner_threshold' => $filters['only_checked'],
            'same_gramset' => (bool) $filters['same_gramset'],
            'whole_sentence' => (bool) $filters['whole_sentence'],
            'source_gramset_ids' => $filters['source_gramset_ids'],
            'corpus_ids' => $filters['corpus_ids'],
        ]);

        $candidate_page = $search->collocationCandidatesPage($filters['per_page'], $filters['page'], $filters['partner_pos_id']);
        $filters['page'] = $candidate_page['page'];
        $candidate_page['paginator']->appends($filters);
        $groups = (new MeaningCollocationCandidateData())->prepare($candidate_page['items']);
        $groups = (new MeaningCollocationCandidateLinks())->prepare($source, $groups, $filters);

        $lemma = $source->lemma;
        $partner = null;
        $meaning_labels = $source->showShortLabels();
        $back_to_url = LaravelLocalization::localizeURL('/dict/lemma/' . $lemma->id) . '#meaning-' . $source->id;
        $base_url = LaravelLocalization::localizeURL('/dict/meaning/' . $source->id . '/examples/candidates');
        $pos_values = PartOfSpeech::getGroupedList();

        return view('dict.meaning.candidates.index', compact(
            'back_to_url',
            'base_url',
            'candidate_page',
            'groups',
            'filters',
            'filter_values',
            'lemma',
            'meaning_labels',
            'partner',
            'pos_values',
            'source',
            'synset_id',
        ));
    }

    protected function filters(Request $request, array $filter_values)
    {
        $defaults = [
            'distance_from' => -1,
            'distance_to' => 1,
            'source_only_checked' => $request->input('only_checked', 0),
            'only_checked' => 0,
            'same_gramset' => 0,
            'whole_sentence' => 0,
            'per_page' => 25,
            'page' => 1,
            'synset_id' => 0,
            'partner_pos_id' => 0,
        ];

        $input = [];
        foreach ($defaults as $name => $default) {
            $input[$name] = $request->input($name, $default);
        }

        $validator = Validator::make($input, [
            'distance_from' => 'required|integer',
            'distance_to' => 'required|integer',
            'source_only_checked' => 'required|boolean',
            'only_checked' => 'required|boolean',
            'same_gramset' => 'required|boolean',
            'whole_sentence' => 'required|boolean',
            'per_page' => 'required|integer|min:1|max:100',
            'page' => 'required|integer|min:1|max:1000000',
            'synset_id' => 'required|integer|min:0',
            'partner_pos_id' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            abort(422, $validator->errors()->first());
        }

        $filters = array_map('intval', $input);
        if ($filters['partner_pos_id'] && !PartOfSpeech::where('id', $filters['partner_pos_id'])->exists()) {
            abort(422, trans('collocation_candidates.invalid_pos'));
        }
        if ($filters['distance_from'] > $filters['distance_to']) {
            abort(422, trans('meaning_examples.invalid_distance'));
        }
        if (!$filters['synset_id']) {
            unset($filters['synset_id']);
        }

        $extra_filters = (new MeaningExampleFilterInput())->read($request->all(), $filter_values);

        if ($extra_filters['errors']->count()) {
            abort(422, $extra_filters['errors']->first());
        }

        return array_merge($filters, $extra_filters['options']);
    }

    protected function checkSynset(Meaning $source, $synset_id)
    {
        if ($synset_id !== null && !$source->synsets()->where('synsets.id', $synset_id)->exists()) {
            abort(409, trans('collocations.synset_meaning_mismatch'));
        }
    }

    public function destroy($meaning_id, $collocation_id, Request $request)
    {
        if (!User::checkAccess('dict.edit')) {
            abort(403);
        }

        $source = Meaning::with('lemma')->findOrFail($meaning_id);
        $filter_values = (new MeaningExampleFilterValues())->load($source);
        $filters = $this->filters($request, $filter_values);

        $this->checkSynset($source, $filters['synset_id'] ?? null);

        DB::transaction(function () use ($source, $collocation_id) {
            Meaning::where('id', $source->id)->lockForUpdate()->firstOrFail();

            $collocation = $source->collocations()
                ->where('id', $collocation_id)->lockForUpdate()->firstOrFail();

            $collocation->removeWithHistory();
        });

        $url = LaravelLocalization::localizeURL('/dict/meaning/' . $source->id . '/examples/candidates');

        return redirect($url . '?' . http_build_query($filters))
            ->withSuccess(trans('collocations.deleted'));
    }
}
