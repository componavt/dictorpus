<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\Settings;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Storage;
use Carbon\Carbon;
use DB;

use App\Library\Export;

use App\Models\Corpus\Collection;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;
//use App\Models\Corpus\Word;
use App\Models\Dict\Concept;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;
//use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class ExportController extends Controller
{
    public function __construct(Request $request)
    {
        $for_editors = ['concordance'];
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:admin,/', ['except' => $for_editors]);
        $this->middleware('auth:corpus.edit,/', ['only' => $for_editors]);
    }

    public function index()
    {
        $langs = [];
        foreach (Lang::projectLangIDs() as $l_id) {
            $langs[$l_id] = Lang::getNameById($l_id);
        }

        return view('service.export', compact('langs'));
    }

    /*
     * annotation for CONLL
     */
    public function annotationConll()
    {
        $filename = 'export/conll/annotation.txt';

        Storage::disk('public')->put($filename, "# Parts of speech");
        $parts_of_speech = PartOfSpeech::all()->sortBy('name_en');
        foreach ($parts_of_speech as $pos) {
            Storage::disk('public')->append($filename, $pos->name_en . "\t" . $pos->code);
        }

        Storage::disk('public')->append($filename, "\n# Lemma features");
        $lemma_feature = new LemmaFeature;
        foreach ($lemma_feature->feas_conll_codes as $name => $info) {
            $named_keys = [];
            if (preg_match("/^(.+)_id$/", $name, $regs) && is_array(trans('dict.' . $regs[1] . 's'))) {
                $named_keys = trans('dict.' . $regs[1] . 's');
                //dd($named_keys);                
            }
            foreach ($info as $key => $code) {
                Storage::disk('public')->append($filename, "$name=" . (isset($named_keys[$key]) ? $named_keys[$key] : $key) . "\t$code");
            }
        }

        Storage::disk('public')->append($filename, "\n# Grammatical attributes");
        $gram_categories = GramCategory::all()->sortBy('sequence_number');
        foreach ($gram_categories as $gram_category) {
            $grams = Gram::where('gram_category_id', $gram_category->id)->orderBy('sequence_number')->get();
            foreach ($grams as $gram) {
                Storage::disk('public')->append($filename, $gram_category->name_en . '=' . $gram->name_en . "\t" . $gram->conll);
            }
        }

        print  '<p><a href="' . Storage::url($filename) . '">annotation</a>';
    }

    /*
     * vepkar-20190129-vep
     */
    public function lemmasToUniMorph(Request $request)
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');

        $date = Carbon::now();
        $date_now = $date->toDateString();
        $dir_name = "export/unimorph/" . $date_now . "/";
        Storage::disk('public')->makeDirectory($dir_name);

        $lang_id = (int)$request->input('search_lang');

        if ($lang_id) {
            Export::lemmasToUnimorph($lang_id, $dir_name);
            return;
        }

        foreach (Lang::projectLangIDs() as $lang_id) {
            Export::lemmasToUnimorph($lang_id, $dir_name);
        }
    }

    /*
     * vepkar-20190129-vep
     */
    public function compoundsToUniMorph()
    {
        //        ini_set('max_execution_time', 7200);
        //        ini_set('memory_limit', '512M');
        $date = Carbon::now();
        $date_now = $date->toDateString();
        $dir_name = "export/unimorph/" . $date_now . "/";
        Storage::disk('public')->makeDirectory($dir_name);

        foreach ([4, 5, 6, 1] as $lang_id) {
            //            $lang_id = 1;
            $lang = Lang::find($lang_id);
            $filename = $dir_name . 'vepkar-' . $date_now . '-' . $lang->code . '_compounds.txt';

            $lemmas = Lemma::where('lang_id', $lang_id)
                ->where('pos_id', PartOfSpeech::getPhraseID())
                //                    ->where('id',1416)
                //                    ->take(100)
                ->orderBy('lemma')
                ->get();
            $count = 0;
            foreach ($lemmas as $lemma) {
                $line = $lemma->compoundToUniMorph();
                if ($line) {
                    $count++;
                    if ($count == 1) {
                        Storage::disk('public')->put($filename, $line);
                    } else {
                        Storage::disk('public')->append($filename, $line);
                    }
                }
            }
            if ($count) {
                print  '<p><a href="' . Storage::url($filename) . '">' . $lang->name_en . '</a>';
            }
        }
    }

    /*
     * vepkar-20190129-vep
     */
    public function lemmasWithPOS()
    {
        $date = Carbon::now();
        $date_now = $date->toDateString();

        $lang_id = 1;
        $lang = Lang::find($lang_id);
        $filename = 'export/vepkar-' . $date_now . '-lemma-pos-' . $lang->code . '.txt';
        $lemmas = Lemma::where('lang_id', $lang_id)
            //                    ->where('id',1416)
            //                    ->take(100)
            ->orderBy('lemma')
            ->get();
        $count = 0;
        foreach ($lemmas as $lemma) {
            $line = $lemma->lemma . '|' . ($lemma->pos ? $lemma->pos->code : 'Unknoun POS');
            if ($count == 0) {
                Storage::disk('public')->put($filename, $line);
            } else {
                Storage::disk('public')->append($filename, $line);
            }
            $count++;
        }
        print  '<p><a href="' . Storage::url($filename) . '">Lemmas with POS</a>';
    }

    /*
     * vepkar-20190129-vep
     */
    public function textsToCONLL()
    { //Request $request
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        //dd(ini_get('memory_limit'));
        $date = Carbon::now();
        $date_now = $date->toDateString();
        foreach ([4, 5, 6, 1] as $lang_id) {
            //            $lang_id = 6;
            $lang = Lang::find($lang_id);
            $filename = 'export/conll/vepkar-' . $date_now . '-' . $lang->code . '.txt';
            Storage::disk('public')->put($filename, "# " . $lang->name);
            $texts = Text::where('lang_id', $lang_id)
                //                    ->where('id',1416)
                ->get();
            foreach ($texts as $text) {
                Storage::disk('public')->append($filename, $text->toCONLL());
            }
            print  '<p><a href="' . Storage::url($filename) . '">' . $lang->name . '</a>';
        }
    }

    /*
     * export for word2vec 
     * each sentences in new line
     * sentences - words separated by | 
     * vepkar-20190129-vep
     * 
     */
    public function sentencesToLines()
    { //Request $request
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        //dd(ini_get('memory_limit'));
        $date = Carbon::now();
        $date_now = $date->toDateString();
        $lang_id = 1;
        $lang = Lang::find($lang_id);
        $filename = 'export/vepkar-' . $date_now . '-' . $lang->code . '.txt';
        Storage::disk('public')->put($filename, "# " . $lang->name);
        $texts = Text::where('lang_id', $lang_id)
            //                    ->where('id',1416)
            ->get();
        foreach ($texts as $text) {
            Storage::disk('public')->append($filename, $text->sentencesToLines());
        }
        print  '<p><a href="' . Storage::url($filename) . '">' . $lang->name . '</a>';
    }

    public function bible()
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        //dd(ini_get('memory_limit'));
        $date = Carbon::now();
        $date_now = $date->toDateString();
        //        foreach ([4, 5, 6, 1] as $lang_id) {
        $lang_id = 1;
        $lang = Lang::find($lang_id);
        $filename = 'export/bible/vepkar-' . $date_now . '-' . $lang->code . '.txt';
        Storage::disk('public')->put($filename, "# " . $lang->name);
        $lines = Export::exportBible($lang_id);
        foreach ($lines as $book => $chapters) {
            foreach ($chapters as $chapter => $verses) {
                foreach ($verses as $verse => $v_text) {
                    Storage::disk('public')->append($filename, "book=$book\tchapter=$chapter\tverse=$verse\t$v_text");
                }
            }
        }
        //print  '<p><a href="'.Storage::url($filename).'">'.$lang->name.'</a>';            
        //        }
        print "done.";
    }

    public function forMobile()
    {
        ini_set('max_execution_time', 100000);
        ini_set('memory_limit', '512M');

        $filename = 'export/for_mobile/langs.csv';
        Storage::disk('public')->put($filename, '');
        foreach (Lang::projectLangs() as $lang) {
            Storage::disk('public')->append($filename, $lang->id . ",\"" . $lang->name_en . "\",\"" . $lang->name_ru . '"');
        }

        $filename = 'export/for_mobile/parts_of_speech.csv';
        Storage::disk('public')->put($filename, '');
        foreach (PartOfSpeech::get() as $pos) {
            Storage::disk('public')->append($filename, $pos->id . ",\"" . $pos->name_en . "\",\"" . $pos->name_ru . '"');
        }

        $filename = 'export/for_mobile/gramsets.csv';
        Storage::disk('public')->put($filename, '');
        foreach (Export::gramsetsForMobile() as $gramset_id => $info) {
            Storage::disk('public')->append($filename, $gramset_id . ",\"" . $info['en'] . "\",\"" . $info['ru'] . '"');
        }


        $filename = 'export/for_mobile/lemmas.csv';
        Storage::disk('public')->put($filename, '');
        //dd(Export::lemmasForMobile());        
        foreach (Export::lemmasForMobile() as $lemma_id => $info) {
            Storage::disk('public')->append($filename, $lemma_id . ",\"" . $info['lemma'] . "\"," . $info['lang_id'] . "," . $info['pos_id'] . ",\"" . $info['meaning_ru'] . '"');
        }

        $filename = 'export/for_mobile/wordforms.csv';
        Export::wordformsForMobile($filename);
        Storage::disk('public')->put($filename, '');
        foreach (Export::wordformsForMobile() as $wordform_id => $info) {
            Storage::disk('public')->append($filename, $wordform_id . "," . $info['lemma_id'] . ",\"" . $info['wordform'] . "\"," . $info['gramset_id']);
        }

        print "done.";
    }

    public function forSpeech()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2000M');

        $lang_id = 5; // livvic
        $dirname = 'export/for_speech/';
        /*        
        $texts = Text::whereLangId($lang_id)
                ->orderBy('id')->get();
        
        foreach ($texts as $text) {
            Storage::disk('public')->put($dirname.'text_'.$text->id.'.txt', $text->text);
        }
*/
        $words = [];
        $lemmas = Lemma::whereLangId($lang_id)->get();
        foreach ($lemmas as $lemma) {
            $words[] = $lemma->lemma;
        }

        $wordforms = Wordform::whereIn('id', function ($q1) use ($lang_id) {
            $q1->select('wordform_id')->from('lemma_wordform')
                ->whereIn('lemma_id', function ($q2) use ($lang_id) {
                    $q2->select('id')->from('lemmas')
                        ->whereLangId($lang_id);
                });
        })->get();

        foreach ($wordforms as $wordform) {
            $words[] = $wordform->wordform;
        }

        $words = array_unique($words);
        sort($words);

        $filename = $dirname . 'dictionary.txt';
        Storage::disk('public')->put($filename, join("\n", $words));
        /*        foreach ($words as $word) {
            Storage::disk('public')->append($filename, $word);
        }*/

        print "done.";
    }

    public function multidict()
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $lang_id = 5; // livvic
        $label_id = 3; // for multimedia dictionary
        $lemmas = Lemma::selectFromMeaningText()
            ->join('parts_of_speech', 'parts_of_speech.id', '=', 'lemmas.pos_id')
            ->whereLangId($lang_id)
            ->whereIn('lemmas.id', function ($q) use ($label_id) {
                $q->select('lemma_id')->from('label_lemma')
                    ->whereLabelId($label_id);
            })
            ->groupBy('lemma_id', 'word_id')
            ->latest(DB::raw('count(*)'));
        $lemma_coll = $lemmas->get(['lemma', 'lemma_id']);
        $lemmas = [];
        foreach ($lemma_coll as $lemma) {
            if (isset($lemmas[$lemma->lemma_id])) {
                $lemmas[$lemma->lemma_id]['frequency'] = 1 + $lemmas[$lemma->lemma_id]['frequency'];
                continue;
            }
            $lemmas[$lemma->lemma_id] = [
                'lemma_id' => $lemma->lemma_id,
                'lemma' => $lemma->lemma,
                'frequency' => 1
            ];
        }
        $lemmas = collect($lemmas)->sortByDesc('frequency')->unique('lemma');
        //dd($lemmas);
        $filename = 'export/multidict.csv';
        Storage::disk('public')->put($filename, "ID\tлемма");
        foreach ($lemmas as $lemma) {
            Storage::disk('public')->append($filename, $lemma['lemma_id'] . "\t" . $lemma['lemma']);
        }
        print "done.";
    }

    public function concepts()
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $categories = ConceptCategory::all()->sortBy('id');
        $filename = 'export/concepts.csv';
        Storage::disk('public')->put($filename, "Категория\tID понятия\tПонятие");
        foreach ($categories->sortBy('id') as $category) {
            Storage::disk('public')->append($filename, $category->id . "\t" . $category->name_ru);
            /*            foreach ($category->concepts as $concept) {
                Storage::disk('public')->append($filename, $category->id."\t".$concept->id."\t".$concept->text_ru);
            }*/
        }
        print "done.";
    }

    public function conceptsWithoutImages()
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $concepts = Concept::whereNull('wiki_photo')
            ->orWhere('wiki_photo', '')->get();
        $filename = 'export/concepts_without_images.csv';
        Storage::disk('public')->put($filename, "ID\tпонятие");
        foreach ($concepts as $concept) {
            Storage::disk('public')->append($filename, $concept->id . "\t" . $concept->text_ru);
        }
        print "done.";
    }

    public function multidictWithoutConcepts()
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $lang_id = 5; // livvic
        $label_id = 3; // for multimedia dictionary
        $meanings = Meaning::whereIn('lemma_id', function ($q) use ($lang_id) {
            $q->select('id')->from('lemmas')
                ->whereLangId($lang_id);
        })->whereIn('lemma_id', function ($q) use ($label_id) {
            $q->select('lemma_id')->from('label_lemma')
                ->whereLabelId($label_id);
        })->whereNotIn('id', function ($q) {
            $q->select('meaning_id')->from('concept_meaning');
        })->orderBy('lemma_id')
            ->orderBy('meaning_n')
            ->get();

        $filename = 'export/multidict_without_concepts.csv';
        Storage::disk('public')->put($filename, "ID\tлемма\tN\tзначение");
        $lang_ru = Lang::where('code', 'ru')->first()->id;
        foreach ($meanings as $meaning) {
            $meaning_text = $meaning->meaningTexts()->where('lang_id', $lang_ru)->first();
            if (!$meaning_text) {
                dd('Пустое ' . $meaning->meaning_n . ' значение ' . $meaning->id . ' у леммы ' . $meaning->lemma_id);
                // select lemma_id, meaning_n from meanings where id not in (select meaning_id from meaning_texts where lang_id=2) and lemma_id in (select id from lemmas where lang_id=5);                                
            }
            Storage::disk('public')->append($filename, $meaning->lemma_id . "\t" . $meaning->lemma->lemma . "\t" .
                $meaning->meaning_n . "\t" . $meaning_text->meaning_text);
        }
        print "done.";
    }

    public function runes()
    {
        $cid = 2;
        $genre_arr = Genre::find(Collection::getCollectionGenres($cid))
            ->getSubGenreIds();
        $lang_id = Collection::getCollectionLangs($cid);

        $texts = Text::whereIn('lang_id', $lang_id)
            ->whereIn('id', function ($q) use ($genre_arr) {
                $q->select('text_id')->from('genre_text')
                    ->whereIn('genre_id', $genre_arr);
            })->orderBy('id')->pluck('id')->toArray();

        $transtexts = Transtext::whereIn('id', function ($q) use ($texts) {
            $q->select('transtext_id')->from('texts')
                ->whereIn('id', $texts);
        })->orderBy('id')->pluck('id')->toArray();

        dd(join(',', $transtexts));
        /*        $filename = 'export/runes.sql';
        Storage::disk('public')->put($filename, "ID\tлемма\tN\tзначение");
        $lang_ru = Lang::where('code','ru')->first()->id;
        foreach ($meanings as $meaning) {
            $meaning_text = $meaning->meaningTexts()->where('lang_id',$lang_ru)->first();
            if (!$meaning_text) {
                dd('Пустое '.$meaning->meaning_n.' значение '.$meaning->id.' у леммы '.$meaning->lemma_id);
// select lemma_id, meaning_n from meanings where id not in (select meaning_id from meaning_texts where lang_id=2) and lemma_id in (select id from lemmas where lang_id=5);                                
            }
            Storage::disk('public')->append($filename, $meaning->lemma_id."\t".$meaning->lemma->lemma."\t".
                    $meaning->meaning_n."\t".$meaning_text->meaning_text);
        }
        print "done.";*/
    }

    public function oloDict(Request $request)
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');

        $dir_name = "export/olo_dict/";
        Export::oloDict($dir_name);
    }

    public function concordance(Request $request)
    {
        $ids = (array)($request->text_id);
        //dd($ids);        
        $table = Text::concordanceForIds($ids);
        //dd($table); 

        $filename = "concordance" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Часть речи', 'ФОРМА', 'Пример', 'Исходное написание', 'Начальная форма', 'Перевод', 'Количество употреблений']);

        foreach ($table as $pos => $gramsets) {
            ksort($gramsets);
            foreach ($gramsets as $gramset => $words) {
                ksort($words);
                foreach ($words as $word => $cyrwords) {
                    ksort($cyrwords);
                    foreach ($cyrwords as $cyrword => $lemmas) {
                        ksort($lemmas);
                        foreach ($lemmas as $lemma => $meanings) {
                            ksort($meanings);
                            foreach ($meanings as $meaning => $count) {
                                fputcsv($handle, [$pos, $gramset, $word, $cyrword, $lemma, $meaning, $count]);
                            }
                        }
                    }
                }
            }
        }

        // Перемещаем указатель в начало файла
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Возвращаем файл пользователю
        return Response::make($csvContent, 200, $headers);
    }

    public function annotatedText(Text $text, $type)
    {
        /**        $filename = 'annotated1_'.$text->id.'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $csvContent = $text->annotatedText1Export();
        return Response::make($csvContent, 200, $headers);**/
        Settings::setTempDir(env('TMP_DIR'));
        return response()->download($text->annotatedTextExport(env('TMP_DIR'), $type))
            ->deleteFileAfterSend(true);
    }

    public function forYandex()
    {
        $dir_name = "export/for_yandex/" . date('Y-m-d') . '/';

        Export::forYandex($dir_name);
    }
}
