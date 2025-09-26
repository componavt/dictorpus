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
     * Gets markup text with links from words to related lemmas

     * @param string $search_word     - string of searching word
     * @param integer $search_sentence - ID of searching sentence object
     * @param boolean $with_edit      - 1 if it is the edit mode
     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function setLemmaLink($search_word=null, $search_sentence=null, $with_edit=true, $search_w=[], $preloaded=false){
        $markup_text = $this->text_xml;
        
        list($sxe,$error_message) = Text::toXML($markup_text,'');
        if ($error_message) { return $markup_text; }

        $sxe->addAttribute('class', 'sentence'.($search_sentence==$this->id ? ' word-marked' : ''));

        $sxe->attributes()->id = 'text_s'.$this->id;
        
        $words = $sxe->xpath('//w');
        foreach ($words as $word_node) {
            $w_id = (int)$word_node->attributes()->id;
            $word_obj = $this->words()->whereWId($w_id)->with('meanings')->with('gramsets')->first();
            $word_obj->setClassToWordBlock($word_node, $search_word, $search_w);
            $word_obj->addWordBlock($word_node, $with_edit, $preloaded);            
        }
        
        return $sxe->asXML();
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

    // icon 'pensil'
    public static function addEditExampleButton($link_block, $text_id, $s_id, $word_id) {
        if (!User::checkAccess('corpus.edit')) {
            return;
        }
        $button_edit_p = $link_block->addChild('p');
        $button_edit_p->addAttribute('class','text-example-edit'); 
        $button_edit = $button_edit_p->addChild('a',' ');//,'&#9999;'
        $button_edit->addAttribute('href',
                LaravelLocalization::localizeURL('/corpus/text/'.$text_id.
                        '/edit/example/'.$s_id.'_'.$word_id)); 
        $button_edit->addAttribute('class','glyphicon glyphicon-pencil');  
        return $link_block;
    }
    
    public static function createWordCheckedBlock($meaning_id, $text_id, $s_id, $w_id) {
        $meaning = Meaning::find($meaning_id);
        $text = Text::find($text_id);
        if (!$meaning || !$text) { return; }
        $locale = LaravelLocalization::getCurrentLocale();
        $url = '/corpus/text/'.$text_id.'/edit/example/'.$s_id.'_'.$w_id;
        
        return  '<div><a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$meaning->lemma_id)
             .'">'.$meaning->lemma->lemma.'<span> ('
             .$meaning->getMultilangMeaningTextsString($locale)
             .')</span></a></div>'

             .$text->createGramsetBlock($w_id)

             .'<p class="text-example-edit"><a href="'
             .LaravelLocalization::localizeURL($url)
             .'" class="glyphicon glyphicon-pencil"></a>';
    }
    
    public function createLemmaBlock($w_id) {
        if (!$w_id) { return null; }
        
        $meaning_checked = $this->meanings()->wherePivot('w_id',$w_id)->wherePivot('relevance','>',1)->first();
        $meaning_unchecked = $this->meanings()->wherePivot('w_id',$w_id)->wherePivot('relevance',1)->get();
        if (!$meaning_checked && !sizeof($meaning_unchecked)) { return null; }
        
        $word_obj = Word::whereTextId($this->id)->whereWId($w_id)->first();
        if (!$word_obj) {return null;} 
        return $word_obj->createLemmaBlock($this->id, $w_id);
    }
    
    public function createGramsetBlock($w_id) {
        $wordform = $this->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',2)->first();
        if ($wordform) {
            return '<p class="word-gramset">'.Gramset::getStringByID($wordform->pivot->gramset_id).'</p>';
        } elseif (User::checkAccess('corpus.edit')) { 
            $wordforms = $this->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',1)->get();
            if (!sizeof($wordforms)) { return null; }

            $str = '<div id="gramsets_'.$w_id.'" class="word-gramset-not-checked">';
            foreach ($wordforms as $wordform) {
                $gramset_id = $wordform->pivot->gramset_id;
                $str .= '<p>'.Gramset::getStringByID($gramset_id)
                     . '<span data-add="'.$this->id."_".$w_id."_".$wordform->id."_".$gramset_id
                     . '" class="fa fa-plus choose-gramset" title="'.\Lang::trans('corpus.mark_right_gramset').' ('
                     . $wordform->wordform.')" onClick="addWordGramset(this)"></span>'
                     . '</p>';
            }
            $str .= '</div>';
            return $str;
        }
    }
    
    public function buildGramsetBlock(\SimpleXMLElement $parent, $w_id) {
        $wordform = $this->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',2)->first();

        if ($wordform) {
            $p = $parent->addChild('p', Gramset::getStringByID($wordform->pivot->gramset_id));
            $p->addAttribute('class', 'word-gramset');
            return $p;
        } elseif (User::checkAccess('corpus.edit')) {
            $wordforms = $this->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',1)->get();
            if (!sizeof($wordforms)) { return null; }

            $div = $parent->addChild('div');
            $div->addAttribute('id', 'gramsets_'.$w_id);
            $div->addAttribute('class', 'word-gramset-not-checked');

            foreach ($wordforms as $wordform) {
                $gramset_id = $wordform->pivot->gramset_id;
                $p = $div->addChild('p', Gramset::getStringByID($gramset_id));
                $span = $p->addChild('span');
                $span->addAttribute('data-add', $this->id."_".$w_id."_".$wordform->id."_".$gramset_id);
                $span->addAttribute('class', 'fa fa-plus choose-gramset');
                $span->addAttribute('title', \Lang::trans('corpus.mark_right_gramset').' ('.$wordform->wordform.')');
                $span->addAttribute('onClick', 'addWordGramset(this)');
            }
            return $div;
        }
        return null;
    }
    
    public static function spellchecking($text, $lang_id) {
        list($markup_text) = Sentence::markup($text,1);
        $markup_text = self::addBlocksToWords($markup_text, $lang_id);
        return $markup_text;
    }

    public static function addBlocksToWords($text, $lang_id) {
        list($sxe,$error_message) = self::toXML($text);
        if ($error_message) { return $error_message; }

        foreach ($sxe->xpath('//w') as $word) {
            $word = Word::addBlockToWord($word, $lang_id);
        }
        return $sxe->asXML();
    }
}