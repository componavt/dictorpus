<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

use App\Models\Corpus\Publication;
use App\Models\Corpus\Pubpart;

class PubpartController extends Controller
{
    public function simpleStore(Request $request)
    {
        $publication = Publication::findOrFail(
            $request->input('publication_id')
        );

        $pubpart = Pubpart::create(
            $this->simpleStoreData($request, $publication)
        );

        return Response::json([
            'id' => $pubpart->id,
            'text' => $pubpart->full_name,
            'year' => $pubpart->year,
        ]);
    }

    protected function simpleStoreData(Request $request, Publication $publication)
    {
        $data = [
            'publication_id' => $publication->id,
            'sequence_number' => Pubpart::nextSequenceNumber($publication),
        ];

        if ($publication->is_periodic) {
            $number = trim($request->input('number'));

            if (!$number) {
                abort(422, 'Введите номер выпуска.');
            }

            $data['number'] = $number;
            $data['year'] = $request->input('year') ?: null;
            $data['issue_date'] = $request->input('issue_date') ?: null;
        } else {
            $title = trim($request->input('title'));

            if (!$title) {
                abort(422, 'Введите заголовок раздела.');
            }

            $data['title'] = $title;
        }

        return $data;
    }

    /**
     * Gets list of pubparts for drop down list in JSON format
     * Test url: /corpus/pubpart/list?publication_id=1
     * 
     * @return JSON response
     */
    public function pList(Request $request)
    {
        $name = '%' . $request->input('q') . '%';
        $publication_id = $request->input('publication_id');

        $list = [];
        $pubparts = Pubpart::where(function ($q) use ($name) {
            $q->where('number', 'like', $name)
                ->orWhere('title', 'like', $name);
        });
        if ($publication_id) {
            $pubparts = $pubparts->where('publication_id', $publication_id);
        }

        $pubparts = $pubparts->orderBy('sequence_number')->get();

        foreach ($pubparts as $pubpart) {
            $list[] = [
                'id'  => $pubpart->id,
                'text' => $pubpart->full_name
            ];
        }
        return Response::json($list);
    }
}
