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
    
    public static function createWordBlock($text_id, $w_id) {
        if (!$text_id || !$w_id) { return null; }
        
        $text = Text::find($text_id);
        if (!$text) { return null; }
        
        $word_obj = self::whereTextId($text_id)->whereWId($w_id)->first();
        if (!$word_obj) {return null;}         

        if (!$word_obj->checkedMeaning() && !sizeof($word_obj->uncheckedMeanings())) { return null; }
                
        $str = '<div>'. ($word_obj->checkedMeaning() ? $word_obj->checkedMeaningForWordBlock()
                         : $word_obj->uncheckedMeaningsForWordBlock())
               .'</div>'. $text->createGramsetBlock($w_id)
               . $word_obj->editLinksForWordBlock();

        return $str;
    }
    
    public function checkedMeaningForWordBlock() {
        $meaning_checked = $this->checkedMeaning();
        $locale = LaravelLocalization::getCurrentLocale();
        return   '<p><a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$meaning_checked->lemma_id)
                 .'">'.$meaning_checked->lemma->lemma.'<span> '.$meaning_checked->lemma->pos->code.' ('
                 .$meaning_checked->getMultilangMeaningTextsString($locale)
                 .')</span></a></p>';
    }
    
    public function uncheckedMeaningsForWordBlock() {
        $locale = LaravelLocalization::getCurrentLocale();
        $str = '';
            foreach ($this->uncheckedMeanings() as $meaning) {
                $str .= '<p><a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$meaning->lemma_id)
                     .'">'.$meaning->lemma->lemma.'<span> '.$meaning->lemma->pos->code.' ('
                     .$meaning->getMultilangMeaningTextsString($locale)
                     .')</span></a>';
                if (User::checkAccess('corpus.edit')) {                
                    $str .= '<span class="fa fa-plus choose-meaning" data-add="'
                         .$meaning->id.'_'.$this->text_id.'_'.$this->s_id.'_'.$this->w_id.'" title="'
                         .\Lang::trans('corpus.mark_right_meaning').'" onClick="addWordMeaning(this)"></span></p>';
                }
            }
        if (User::checkAccess('corpus.edit')) {                
            $str.="<input class=\"add-wordform-link\" type=\"button\" value=\""
                    . \Lang::trans('corpus.new_meaning')."\" onClick=\"callAddWordform(this, '"
                    .$this->text_id."', '".$this->w_id."', '".$this->word."', '".$this->text->lang_id."', '".$locale."')\">";
        }
        return  $str;
    }
    
    // icons 'pensil' and 'sync'
    public function editLinksForWordBlock() {
        if (!User::checkAccess('corpus.edit')) { 
            return '';
        }
        $url = '/corpus/text/'.$this->text_id.'/edit/example/'.$this->s_id.'_'.$this->w_id;
        $str = '<p class="text-example-edit">';
        if (!$this->hasImportantExamples()) {
            $str.='<i class="fa fa-sync-alt fa-lg update-word-block" title="'
                .'" onclick="updateWordBlock('.$this->text_id.','.$this->w_id.')"></i>';
        }
        return  $str.'<a href="'.LaravelLocalization::localizeURL($url)
                .'" class="glyphicon glyphicon-pencil"></a></p>';
    }
    
    public function createLemmaBlock($text_id, $w_id) {
        $s_id = $this->s_id;
        if (!$s_id) {return null;} 
        
        $lemma_b = Lemma::whereIn('id', function ($q) use ($text_id, $w_id) {
            $q->select('lemma_id')->from('meanings')
              ->whereIn('id', function ($q2) use ($text_id, $w_id) {
                  $q2->select('meaning_id')->from('meaning_text')
                     ->whereTextId($text_id)->whereWId($w_id)
                     ->where('relevance','>',0);
                });                    
            })->orderBy('lemma');
        if (!$lemma_b->count()) {return null;} 
        $lemmas = $lemma_b->get();
        
        return self::lemmaBlock($this->word, $w_id, $lemmas, $text_id);
    }
    
    public static function lemmaBlock($word, $w_id, $lemmas, $text_id=null, $wordform_ids=[]) {
        $str = '<div><h3>'.$word.'</h3>';
        
        for ($i=0; $i<sizeof($lemmas); $i++) {
            $lemma_id = $lemmas[$i]->id;
            $str .= '<div class="lemma_b">'.(sizeof($lemmas)>1 ? ($i+1).'. ' : '')
                  . '<a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$lemma_id)
                  . '">'.$lemmas[$i]->lemma.'</a><br>'
                  . '<span> '.$lemmas[$i]->pos->name.'</span> <i>'
                  . $lemmas[$i]->featsToString().'</i>'
            
                  . self::meaningsBlock($lemma_id, $w_id, $text_id)
                  . self::gramsetsBlock($lemma_id, $w_id, $text_id, $wordform_ids)
                  . '</div>';
        }
        $str .= '</div>';
        return $str;
    }
    
    public static function meaningsBlock($lemma_id, $w_id, $text_id=null) {
        $locale = LaravelLocalization::getCurrentLocale();        
        $str = '<div class="meanings_b">';
        foreach (self::meaningsForLemmaBlock($lemma_id, $w_id, $text_id) as $meaning) {
            $str .= '<p>'.$meaning->getMultilangMeaningTextsString($locale).'</p>';
        }
        return $str. '</div>';
    }
    
    public static function meaningsForLemmaBlock($lemma_id, $w_id, $text_id=null) {
        $meanings = Meaning::whereLemmaId($lemma_id);
        if ($text_id) {
            $meanings->whereIn('id', function ($q) use ($text_id, $w_id) {
                  $q->select('meaning_id')->from('meaning_text')
                     ->whereTextId($text_id)->whereWId($w_id)
                     ->where('relevance','>',0);
            });
        }
        return $meanings->orderBy('meaning_n')->get();
    }
    
    public static function gramsetsBlock($lemma_id, $w_id, $text_id=null, $wordform_ids=[]) {
        $gramsets = $text_id ? self::textGramsetsForlemmaBlock($lemma_id, $w_id, $text_id)
                             : (sizeof($wordform_ids) ? self::wordformGramsetsForlemmaBlock($lemma_id, $wordform_ids) : null);
        if (!$gramsets) {
            return null;
        }
        $str = '<div class="gramsets_b">';                
        foreach ($gramsets as $gramset) {
            $str .= '<p>- '.$gramset->gramsetString().'</p>';
        }
        return $str. '</div>';                
    }
    
    public static function textGramsetsForlemmaBlock($lemma_id, $w_id, $text_id=null) {             
        return Gramset::whereIn('id', function ($q) use ($text_id, $w_id, $lemma_id) {
            $q->select('gramset_id')->from('text_wordform')
              ->whereTextId($text_id)->whereWId($w_id)
              ->where('relevance','>',0)
              ->whereIn('wordform_id', function ($q2) use ($lemma_id) {
                  $q2->select('wordform_id')->from('lemma_wordform')
                     ->whereLemmaId($lemma_id);
              });
        })->get();
    }
    
    public static function wordformGramsetsForlemmaBlock($lemma_id, $wordform_ids=[]) {             
        return Gramset::whereIn('id', function ($q) use ($wordform_ids, $lemma_id) {
            $q->select('gramset_id')->from('lemma_wordform')
              ->whereIn('wordform_id', $wordform_ids)
              ->whereLemmaId($lemma_id);
        })->get();
    }
    
}