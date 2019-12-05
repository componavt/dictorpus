<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use LaravelLocalization;

use App\Models\User;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;
use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the start page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = 3;
        $locale = LaravelLocalization::getCurrentLocale();
        $total_lemmas = Lemma::count();
        $total_texts = Text::count();
        $total_dialects = Dialect::count();
        $lemmas_choice = \Lang::choice('blob.choice_articles',$total_lemmas, [], $locale);
        $texts_choice = \Lang::choice('blob.choice_texts',$total_texts, [], $locale);        
        $video = Text::videoForStart();
        return view('welcome',
                    compact('lemmas_choice', 'limit', 'texts_choice', 'total_dialects', 
                            'total_lemmas', 'total_texts', 'video'));
    }
    
    /**
     * @return \Illuminate\Http\Response
     */
    public function stats()
    {
        $total_active_editors = User::countActiveEditors(); 
        $total_checked_examples = Text::countCheckedExamples();
        $total_checked_words = Text::countCheckedWords(); 
        $total_examples = Text::countExamples();
//dd($total_examples);        
        $total_informants = Informant::count();
        $total_lemmas = Lemma::count();
        $total_meanings = Meaning::count();
        $total_places = Place::count();
        $total_recorders = Recorder::count();
        $total_relations = Meaning::countRelations();
        $total_texts = Text::count();
        $total_translations = Meaning::countTranslations();
        $total_wordforms = Wordform::count();
        $total_words = Word::count(); 
        $total_marked_words = Word::countMarked();
        $total_users = User::count(); 
        
//        $persFormatter = new \NumberFormatter("en-US", \NumberFormatter::PERCENT); 
//        $all_word_to_check = $persFormatter->format($total_checked_words/$total_words);

        $marked_words_to_all = 100*$total_marked_words/$total_words;
        $checked_words_to_marked = 100*$total_checked_words/$total_marked_words;
        $checked_examples_to_all = 100*$total_checked_examples/$total_examples;
        
        $lang_marked = Lang::countMarked();
        $lang_lemmas = Lang::countLemmas();
        $lang_wordforms = Lang::countWordforms();
        
        return view('page.stats')
                ->with([
                        'checked_examples_to_all' => number_format($checked_examples_to_all, 2,',', ' '),
                        'checked_words_to_marked' => number_format($checked_words_to_marked, 2,',', ' '),
                        'lang_lemmas' => $lang_lemmas,
                        'lang_marked' => $lang_marked,
                        'lang_wordforms' => $lang_wordforms,
                        'marked_words_to_all' => number_format($marked_words_to_all, 1,',', ' '),
                        'total_active_editors' => number_format($total_active_editors, 0, ',', ' '),
                        'total_checked_examples' => number_format($total_checked_examples, 0, ',', ' '),
                        'total_checked_words' => number_format($total_checked_words, 0, ',', ' '),
                        'total_examples' => number_format($total_examples, 0, ',', ' '),
                        'total_informants' => number_format($total_informants, 0, ',', ' '),
                        'total_lemmas' => number_format($total_lemmas, 0, ',', ' '),
                        'total_meanings' => number_format($total_meanings, 0, ',', ' '),
                        'total_places' => number_format($total_places, 0, ',', ' '),
                        'total_recorders' => number_format($total_recorders, 0, ',', ' '),
                        'total_relations' => number_format($total_relations, 0, ',', ' '),
                        'total_texts' => number_format($total_texts, 0, ',', ' '),
                        'total_translations' => number_format($total_translations, 0, ',', ' '),
                        'total_wordforms' => number_format($total_wordforms, 0, ',', ' '),
                        'total_words' => number_format($total_words, 0, ',', ' '),
                        'total_marked_words' => number_format($total_marked_words, 0, ',', ' '),
                        'total_users' => number_format($total_users, 0, ',', ' '),
                       ]);
    }    
}
