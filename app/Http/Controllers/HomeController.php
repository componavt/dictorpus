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
//        $lemmas_choice = \Lang::choice('blob.choice_articles',substr($total_lemmas,-1,2), [], 'ru');
//        $total_texts = 1322;
//dd(substr($total_texts,-1,3));        
        $texts_choice = \Lang::choice('blob.choice_texts',$total_texts, [], $locale);
//        $texts_choice = trans_choice('blob.choice_texts',substr($total_texts,-1,2), [], 'ru');
        
        return view('welcome')
                ->with(['limit'=>$limit,
                        'lemmas_choice' => $lemmas_choice,
                        'texts_choice' => $texts_choice,
                        'total_dialects' => $total_dialects,
                        'total_lemmas' => $total_lemmas,
                        'total_texts' => $total_texts,
                       ]);
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
        $total_users = User::count(); 
        
//        $persFormatter = new \NumberFormatter("en-US", \NumberFormatter::PERCENT); 
//        $all_word_to_check = $persFormatter->format($total_checked_words/$total_words);

        $all_words_to_checked = 100*$total_checked_words/$total_words;
        $all_examples_to_checked = 100*$total_checked_examples/$total_examples;
        
        return view('page.stats')
                ->with([
                        'all_words_to_checked' => number_format($all_words_to_checked, 2,',', ' '),
                        'all_examples_to_checked' => number_format($all_examples_to_checked, 2,',', ' '),
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
                        'total_users' => number_format($total_users, 0, ',', ' '),
                       ]);
    }
}
