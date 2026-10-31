<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Library\CollocationExampleLink;
use App\Models\Dict\Meaning;

class CollocationExampleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:dict.edit,/dict/lemma/');
    }

    public function store($meaning_id, $collocation_id, Request $request)
    {
        $this->validate($request, [
            'text_id' => 'required|integer|min:1',
            'source_w_id' => 'required|integer|min:1',
            'partner_w_id' => 'required|integer|min:1',
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

        $example = (new CollocationExampleLink())->attach(
            $collocation->id,
            $text_id,
            (int) $request->input('source_w_id'),
            (int) $request->input('partner_w_id')
        );

        return response()->json([
            'id' => $example->id,
            'label' => trans('collocation_examples.attached'),
        ]);
    }

    /**
     * Открепляет один пример, сохраняя сочетаемость и корпусные оценки.
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
