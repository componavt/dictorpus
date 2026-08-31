<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use App\Models\Corpus\Publication;
use App\Models\Corpus\Pubpart;

class PubpartController extends Controller
{
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

    protected function pubpartJson(Pubpart $pubpart): array
    {
        return [
            'id' => $pubpart->id,
            'publication_id' => $pubpart->publication_id,
            'sequence_number' => $pubpart->sequence_number,
            'title' => $pubpart->title,
            'number' => $pubpart->number,
            'year' => $pubpart->year,
            'issue_date' => $pubpart->issue_date,
            'text' => $pubpart->full_name,
        ];
    }

    public function simpleDestroy($id)
    {
        $pubpart = Pubpart::findOrFail($id);

        $deletion_error = $pubpart->deletion_error();

        if ($deletion_error) {
            return Response::json([
                'success' => false,
                'message' => $deletion_error,
            ], 422);
        }

        DB::transaction(function () use ($pubpart) {
            $pubpart->delete_without_text_links();
        });

        return Response::json([
            'success' => true,
        ]);
    }

    public function simpleStore(Request $request)
    {
        $publication = Publication::findOrFail(
            $request->input('publication_id')
        );

        $data = $this->simplePubpartData(
            $request,
            $publication
        );

        $data['publication_id'] = $publication->id;

        $data['sequence_number'] = Pubpart::nextSequenceNumber(
            $publication
        );

        $pubpart = Pubpart::create($data);

        return Response::json(
            $this->pubpartJson($pubpart)
        );
    }

    protected function simplePubpartData(
        Request $request,
        Publication $publication
    ): array {
        $data = [];

        if ($publication->is_periodic) {
            $number = trim($request->input('number'));

            if (!$number) {
                abort(422, 'Введите номер выпуска.');
            }

            /*
             * У периодики применяются только number, year, issue_date.
             * title намеренно очищаем.
             */
            $data['title'] = null;
            $data['number'] = $number;
            $data['year'] = $request->input('year') ?: null;
            $data['issue_date'] = $request->input('issue_date') ?: null;
        } else {
            $title = trim($request->input('title'));

            if (!$title) {
                abort(422, 'Введите заголовок раздела.');
            }

            /*
             * У непериодической публикации применяется только title.
             * Поля выпуска намеренно очищаем.
             */
            $data['title'] = $title;
            $data['number'] = null;
            $data['year'] = null;
            $data['issue_date'] = null;
        }

        return $data;
    }

    public function simpleUpdate(Request $request, $id)
    {
        $pubpart = Pubpart::findOrFail($id);

        /*
     * Не берём publication_id из request как источник истины.
     * Pubpart уже принадлежит конкретной публикации.
     */
        $publication = Publication::findOrFail(
            $pubpart->publication_id
        );

        $data = $this->simplePubpartData(
            $request,
            $publication
        );

        $pubpart->fill($data)->save();

        return Response::json(
            $this->pubpartJson($pubpart)
        );
    }
}
