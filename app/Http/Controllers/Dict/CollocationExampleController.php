<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\Controller;

use App\Library\CollocationExampleLink;
use App\Library\CollocationExampleSummary;
use App\Library\MeaningExampleFormatter;
use App\Models\Corpus\Text;
use App\Models\Dict\CollocationExample;
use App\Models\Dict\Meaning;

class CollocationExampleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:dict.edit,/dict/lemma/');
    }

    public function index($meaning_id, $collocation_id, Request $request)
    {
        $source = Meaning::with('lemma')->findOrFail($meaning_id);
        $collocation = $source->collocations()->with('collocateMeaning.lemma')->findOrFail($collocation_id);
        $examples = $collocation->corpusExamples()
            ->where('status', CollocationExample::STATUS_ATTACHED)
            ->get();
        $texts = Text::with(['corpuses', 'dialects'])
            ->whereIn('id', $examples->pluck('text_id')->unique()->all())
            ->get()->keyBy('id');
        $summary = new CollocationExampleSummary();
        $items = [];

        foreach ($examples as $example) {
            $text = $texts->get($example->text_id);

            $items[] = [
                'id' => $example->id,
                'html' => MeaningExampleFormatter::highlight([
                    'text_id' => $example->text_id,
                    'source_w_id' => $example->source_w_id,
                    'matches' => [[
                        'source_w_id' => $example->source_w_id,
                        'partner_w_id' => $example->partner_w_id,
                    ]],
                    'text_xml' => $example->sentence_xml,
                    'sentence' => trim(strip_tags((string)$example->sentence_xml)),
                ]),
                'text' => $text,
                'groups' => $summary->corpusGroups($text),
            ];
        }

        $synset_id = (int)$request->input('synset_id');
        $synset_url = $synset_id
            ? LaravelLocalization::localizeURL('/dict/synset/' . $synset_id . '/edit') : null;

        return view('dict.collocation.attached_examples', compact(
            'source',
            'collocation',
            'items',
            'synset_id',
            'synset_url'
        ));
    }

    public function store($meaning_id, $collocation_id, Request $request)
    {
        $this->validate($request, [
            'text_id' => 'required|integer|min:1',
            'source_w_id' => 'required|integer|min:1',
            'partner_w_id' => 'required|integer|min:1',
            'status' => 'in:attached,rejected',
        ]);

        $source = Meaning::with('lemma')->findOrFail($meaning_id);

        $collocation = $source->collocations()
            ->with('collocateMeaning.lemma')
            ->findOrFail($collocation_id);

        $text_id = (int) $request->input('text_id');
        $lang_id = DB::table('texts')->where('id', $text_id)->value('lang_id');

        if (
            !$lang_id
            || (int) $lang_id !== (int) $source->lemma->lang_id
            || (int) $lang_id !== (int) $collocation->collocateMeaning->lemma->lang_id
        ) {
            abort(422, trans('meaning_examples.different_language'));
        }

        $status = $request->input('status', CollocationExample::STATUS_ATTACHED);
        $link = new CollocationExampleLink();

        if ($status === CollocationExample::STATUS_REJECTED) {
            $example = $link->reject(
                $collocation->id,
                $text_id,
                (int)$request->input('source_w_id'),
                (int)$request->input('partner_w_id')
            );
        } else {
            $example = $link->attach(
                $collocation->id,
                $text_id,
                (int)$request->input('source_w_id'),
                (int)$request->input('partner_w_id')
            );
        }

        return response()->json([
            'id' => $example->id,
            'status' => $example->status,
            'label' => trans(
                $example->status === CollocationExample::STATUS_REJECTED
                    ? 'collocation_examples.rejected'
                    : 'collocation_examples.attached'
            ),
        ]);
    }

    /**
     * Удаляет решение по примеру, возвращая его в нерассмотренные.
     * Сочетаемость и корпусные оценки остаются без изменений.
     */
    public function destroy($meaning_id, $collocation_id, $example_id)
    {
        $source = Meaning::findOrFail($meaning_id);
        $collocation = $source->collocations()->findOrFail($collocation_id);
        $example = $collocation->corpusExamples()->findOrFail($example_id);

        $example->delete();

        return response()->json(['detached' => true]);
    }
}
