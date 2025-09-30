<?php namespace App\Traits\Select;

use LaravelLocalization;

use App\Library\Grammatic;

use App\Models\User;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;
use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;

trait SentenceWordBlock
{
    /**
     * Устанавить разметку с блоками слов

     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function addWordBlocks($search_w=[], $markup_text=null){
        if (!$markup_text) {
            $markup_text = $this->text_xml;
        }
        list($sxe,$error_message) = Text::toXML($markup_text,'');
//dd($error_message, $markup_text);        
        if ($error_message) {
            return $markup_text;
        }
//        $s_id = (int)$sentence->attributes()->id;
//dd($sentence);         
        $words = $sxe->xpath('//w');
        foreach ($words as $word) {
            $word = $this->addWordBlock($word, $search_w);
        }
        return $sxe->asXML();
    }
    
    /**
     * Пометить искомые слова

     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function markSearchWords($search_w=[], $markup_text=null){
        if (!$markup_text) {
            $markup_text = $this->text_xml;
        }
        list($sxe,$error_message) = Text::toXML($markup_text,'');
        if ($error_message || !sizeof($search_w)) {
            return $markup_text;
        }

        $words = $sxe->xpath('//w');
        foreach ($words as $word) {
//            $word = $this->addWordBlock($word, $search_w);
            $w_id = (int)$word->attributes()->id;
            if (!$w_id) { continue; }
            if (in_array($w_id,$search_w)) {
                $word->addAttribute('class', 'word-marked');
            }
        }
        return $sxe->asXML();
    }
    
    public function addWordBlock($word, $search_w=[]) {
        $w_id = (int)$word->attributes()->id;
        if (!$w_id) { return $word; }
        $word['id'] = $this->text_id.'_'.$w_id;
        
        $meanings_checked = $this->text->meanings()->wherePivot('w_id',$w_id)
                          ->wherePivot('relevance', '>', 1)->count();
        $meanings_unchecked = $this->text->meanings()->wherePivot('w_id',$w_id)
                          ->wherePivot('relevance', 1)->count();
        $word_class = '';
        if ($meanings_checked || $meanings_unchecked) {
            $wordform_checked = $this->text->wordforms()->wherePivot('w_id',$w_id)
                              ->wherePivot('relevance', '>', 1)->count();
            $word_class = 'word-linked';
            $word = self::addLemmasBlock($word, $this->text_id.'_'.$w_id, 
                    $meanings_checked && $wordform_checked ? 'word-checked' : 'word-unchecked');            
        }

        if (sizeof($search_w) && in_array($w_id,$search_w)) {
            $word_class .= ' word-marked';
        }

        if ($word_class) {
            $word->addAttribute('class',$word_class);
        }
        return $word;
    }

    public static function addLemmasBlock($word, $block_id, $block_class='') {
        $link_block = $word->addChild('div');
        $link_block->addAttribute('id','links_'.$block_id);
        $link_block->addAttribute('class','links-to-lemmas '.$block_class);
        $link_block->addAttribute('data-downloaded',0);
        
        $load_img = $link_block->addChild('img');
        $load_img->addAttribute('class','img-loading');
        $load_img->addAttribute('src','/images/waiting_small.gif');
                
        return $word;        
    }
}