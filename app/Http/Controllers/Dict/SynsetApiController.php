<?php

namespace App\Http\Controllers\Dict;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Models\Dict\Meaning;
use App\Models\Dict\Synset;
use App\Models\Dict\Syntype;

class SynsetApiController extends Controller
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
            'auth:dict.edit,/dict/synset',
            ['except' => ['membersList']]
        );
    }

    public function setStatus(int $id, int $status)
    {
        $synset = Synset::findOrFail($id);
        $synset->status = $status == 1 ? 1 : 0;
        $synset->save();
        return $synset->status;
    }

    public function removeMeaning(int $id, int $meaning_id)
    {
        $synset = Synset::find($id);
        $result = $synset->meanings()->detach($meaning_id);
        $synset->normalizeMemberOrder();

        return $result;
    }

    public function potentialMembersEdit(int $id, Request $request)
    {
        $synset = Synset::find($id);
        if (empty($synset)) {
            $synset = new Synset;
            $synset->lang_id = (int)$request->lang_id;
            $synset->pos_id = (int)$request->pos_id;
            //            $synset->save();
        }
        $syntype_values = Syntype::getList(1);

        $potential_members = $synset->searchPotentialMembers($request->comment, $request->without);

        return view(
            'dict.synset._potential_rows',
            compact('potential_members', 'synset', 'syntype_values')
        );
    }

    public function membersList(Request $request)
    {
        $limit = 1000;
        $search_lang = (int)$request->search_lang;
        $search_pos = (int)$request->search_pos;
        $term = '%' . $request->input('q') . '%';
        $list = [];

        $meanings = Meaning::select('meanings.*')
            ->join('lemmas', 'meanings.lemma_id', '=', 'lemmas.id')
            ->whereLangId($search_lang)
            ->wherePosId($search_pos)
            ->whereNotIn('meanings.id', (array)$request->without)
            ->where(function ($q) use ($term) {
                $q->where('lemma', 'like', $term)
                    ->orWhereIn('meanings.id', function ($q1) use ($term) {
                        $q1->select('meaning_id')->from('meaning_texts')
                            ->where('meaning_text', 'like', $term);
                    });
            })->take($limit)
            ->orderBy('lemma')->orderBy('lemma_id')->orderBy('meaning_n')
            ->get();

        foreach ($meanings as $meaning) {
            $list[] = [
                'id'  => $meaning->id,
                'text' => $meaning->lemma->lemma . ' (' . $meaning->meaning_n . '. ' . $meaning->getMeaningTextLocale() . ')'
            ];
        }

        return Response::json($list);
    }

    public function newMembersEdit(Request $request)
    {
        $members = Meaning::whereIn('id', (array)$request->meanings)->get();
        $syntype_values = Syntype::getList(1);

        return view(
            'dict.synset._member_rows',
            compact('members', 'syntype_values')
        );
    }

    /**
     * Сохраняет порядок существующих участников, не меняя состав синсета.
     */
    public function saveMemberOrder($id, Request $request)
    {
        $this->validate($request, [
            'core' => 'present|array|max:500',
            'core.*' => 'required|integer|min:1',
            'periphery' => 'present|array|max:500',
            'periphery.*' => 'required|integer|min:1',
        ]);

        $core_ids = array_values(array_map('intval', $request->input('core')));
        $periphery_ids = array_values(array_map('intval', $request->input('periphery')));
        $all_ids = array_merge($core_ids, $periphery_ids);

        if (count($all_ids) > 500 || count($all_ids) !== count(array_unique($all_ids))) {
            return response()->json([
                'message' => trans('dict.synset_order_invalid'),
            ], 422);
        }

        return DB::transaction(function () use ($id, $core_ids, $periphery_ids) {
            $synset = Synset::where('id', $id)->lockForUpdate()->firstOrFail();

            $members = DB::table('meaning_synset')
                ->where('synset_id', $synset->id)
                ->orderBy('meaning_id')
                ->lockForUpdate()
                ->get();

            $current_core_ids = [];
            $current_periphery_ids = [];

            foreach ($members as $member) {
                if ((int)$member->syntype_id === Syntype::TYPE_FULL) {
                    $current_core_ids[] = (int)$member->meaning_id;
                } else {
                    $current_periphery_ids[] = (int)$member->meaning_id;
                }
            }

            $submitted_core_ids = $core_ids;
            $submitted_periphery_ids = $periphery_ids;

            sort($submitted_core_ids, SORT_NUMERIC);
            sort($submitted_periphery_ids, SORT_NUMERIC);
            sort($current_core_ids, SORT_NUMERIC);
            sort($current_periphery_ids, SORT_NUMERIC);

            if ($submitted_core_ids !== $current_core_ids || $submitted_periphery_ids !== $current_periphery_ids) {
                return response()->json([
                    'message' => trans('dict.synset_order_members_changed'),
                ], 409);
            }

            $dominant_id = (int)$synset->dominant_id;

            if ($dominant_id) {
                if (!in_array($dominant_id, $core_ids, true)) {
                    return response()->json([
                        'message' => trans('dict.synset_order_dominant_core'),
                    ], 409);
                }

                $core_ids = array_values(array_filter($core_ids, function ($meaning_id) use ($dominant_id) {
                    return $meaning_id !== $dominant_id;
                }));

                array_unshift($core_ids, $dominant_id);
            }

            $ordered_ids = array_merge($core_ids, $periphery_ids);
            $positions = [];

            foreach ($ordered_ids as $index => $meaning_id) {
                $position = $index + 1;

                DB::table('meaning_synset')
                    ->where('synset_id', $synset->id)
                    ->where('meaning_id', $meaning_id)
                    ->update(['position' => $position]);

                $positions[] = [
                    'meaning_id' => $meaning_id,
                    'position' => $position,
                ];
            }

            return response()->json([
                'saved' => true,
                'synset_id' => (int)$synset->id,
                'core' => $core_ids,
                'periphery' => $periphery_ids,
                'positions' => $positions,
            ]);
        });
    }
}
