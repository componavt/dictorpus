<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dict\MeaningText;

class MeaningTextController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/meaning_text/', ['only' => 'create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    /** 
     * Joins meaning_text.meaning_text for a same meaning_id and a same lang_id
     * with glue '; ' 
     */
    public function tempJoinMeaningText()
    {
        $meanings = DB::table('meaning_texts')
                            ->select(DB::raw('meaning_id, lang_id, count(*) as count'))
                            ->groupBy('meaning_id','lang_id')
                            ->having('count', '>', 1)
                            ->orderBy('meaning_id')
                            //->take(1)
                            ->get();
                //DB::select('select meaning_id, lang_id, count(*) as count '
                //  . 'from meaning_texts group by meaning_id,lang_id having count>1 LIMIT 1');
        
        foreach ($meanings as $meaning) {
            print "<p>----------meaning_id=".$meaning->meaning_id;
            $meaning_texts = MeaningText::where('meaning_id',$meaning->meaning_id)
                                        ->where('lang_id',$meaning->lang_id)
                                        ->orderBy('id')->get();
            $new_meaning_text = array();
            $meaning_text_updated = $meaning_texts->first();
            
            foreach ($meaning_texts as $key=>$meaning_text) {
                $new_meaning_text[] = $meaning_text->meaning_text;
                if ($key>0) { // not the first element
                    print "<p>".$meaning_text->id." is deleted</p>"; 
                    $meaning_text->delete();
                }
            }
            $meaning_text_updated->meaning_text = join('; ',$new_meaning_text);
            print "<p>".$meaning_text_updated->id." = ".$meaning_text_updated->meaning_text."</p>";
            $meaning_text_updated->save();
//            $meaning_text_updated->update(['meaning_text'=>join('; ',$new_meaning_text)]);
        }
    }
}
