<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Response;
//use Illuminate\Support\Facades\DB;

class RistikanzaTextController extends Controller
{
    protected $ethnographicCorpus = 15;
    
    public function ethnographic(Request $request)
    {
        $url_args = Text::urlArgs($request);
        $url_args['search_corpus'] = [$this->ethnographicCorpus];

        $texts = Text::search($url_args)
            ->with([
                'dialects',
                'lang',
                'transtext'
            ]);
        $n_records = $texts->count();

        $texts = $texts->paginate(
            $url_args['portion']
        );

        $items = $texts->getCollection()
            ->map(function ($text) {
                return [
                    'id' => $text->id,
                    'title' => $text->title,
                    'lang' => $text->lang ? $text->lang->name : '',
                    'dialect' => $text->dialectsToArray(),
                    'transtitle' => $text->transtext ? $text->transtext->title : '',
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'data' => $items,
            'current_page' => $texts->currentPage(),
            'last_page' => $texts->lastPage(),
            'per_page' => $texts->perPage(),
            'total' => $texts->total(),
            'n_records' => $n_records,
        ]);
    }

    public function show(int $id)
    {
        $text = Text::query()
            ->whereId($id)
            ->with([
                'lang',
                'authors',
                'corpuses'
            ])
            ->firstOrFail();

        return response()->json($this->textData($text));
    }
    
    protected function textData(Text $text): array
    {
        $lang_id = $text->lang_id;
        return [
            'id' => $text->id,
            'title' => $text->title,
            'lang' => optional($text->lang)->short,

/*            'authors' => $text->authors
                ->map(function ($author) use ($lang_id) {
                    $name = $author->getNameByLang($lang_id);
                    return [
                        'id' => $author->id,
                        'name' => $name ? $name : $author->name,
                    ];
                })
                ->values()
                ->all(),

            'corpuses' => $text->corpuses
                ->map(function ($corpus) use ($lang_id) {
                    return [
                        'id' => $corpus->id,
                        'name' => $corpus->name,
                    ];
                })
                ->values()
                ->all(),*/
            'authors' => $text->authors
//                ->pluck('name', 'id')
                ->mapWithKeys(function ($author) {
                    return [
                        $author->id => $author->name,
                    ];
                })
                ->all(),

            'corpuses' => $text->corpuses
//                ->pluck('name', 'id')
                ->mapWithKeys(function ($corpus) {
                    return [
                        $corpus->id => $corpus->name,
                    ];
                })
                ->all(),

        ];
    }
}
