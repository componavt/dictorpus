<?php namespace App\Traits\Select;

use LaravelLocalization;

use App\Library\Grammatic;

use App\Models\User;
use App\Models\Corpus\Sentence;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaWordform;

trait WordBlock
{
    public static function addBlockToWord($word, $lang_id) {
//dd((string)$word);       
        $w_id = (int)$word->attributes()->id;
        $word_for_search = Grammatic::changeLetters((string)$word,$lang_id);

        $wordforms = LemmaWordform::where('wordform_for_search', 'like', $word_for_search)           
                                  ->whereLangId($lang_id);
        $lemmas = Lemma::where('lemma_for_search', 'like', $word_for_search)           
                                  ->whereLangId($lang_id);
        
        $word->addAttribute('word',$word_for_search);            
        
        if (!$wordforms->count() && !$lemmas->count()) {
            $word->addAttribute('class','no-wordforms');
        } else {
            $word->addAttribute('class','word-linked');            
            $word=Sentence::addLemmasBlock($word,$w_id);
        }
        return $word;
    }
    


    public static function addLinkToLemma($link_block, $lemma, $meaning, $id, $has_checked_meaning, $with_edit) {
        $link_div = $link_block->addChild('div');
        $link = $link_div->addChild('a',$lemma->lemma);
        $link->addAttribute('href',LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id));

        $locale = LaravelLocalization::getCurrentLocale();
        $link->addChild('span',' ('.$meaning->getMultilangMeaningTextsString($locale).')');
        // icon 'plus' - for choosing meaning
        if ($with_edit && !$has_checked_meaning && User::checkAccess('corpus.edit')) {
            $link_div= self::addEditMeaningButton($link_div, $id);
        }
        return $link_block;
    }

    // icon 'plus' - for choosing gramset
    public static function addEditGramsetButton($link_div, $id) {
        $add_link = $link_div->addChild('span');
        $add_link->addAttribute('data-add',$id);
        $add_link->addAttribute('class','fa fa-plus choose-gramset'); //  fa-lg 
        $add_link->addAttribute('title',trans('corpus.mark_right_meaning'));
        $add_link->addAttribute('onClick','addWordGramset(this)');
        return $link_div;
    }

    // icon 'plus' - for choosing meaning
    public static function addEditMeaningButton($link_div, $id) {
        $add_link = $link_div->addChild('span');
        $add_link->addAttribute('data-add',$id);
        $add_link->addAttribute('class','fa fa-plus choose-meaning'); //  fa-lg 
        $add_link->addAttribute('title',trans('corpus.mark_right_meaning'));
        return $link_div;
    }
    
}