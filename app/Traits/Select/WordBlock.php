<?php namespace App\Traits\Select;

use LaravelLocalization;

use App\Library\Grammatic;

use App\Models\User;

trait WordBlock
{
    public function setClassToWordBlock($word_node, $search_word=null, $search_w=[]) {
        $word_class = '';
        
        if ($this->meanings->count()) {
            $word_class = 'lemma-linked';
            if ($this->meanings->where('pivot.relevance', '>', 1)->count()) { // meanings are checked
                $word_class .= ' meaning-checked';
            } elseif ($this->meanings->where('pivot.relevance', 1)->count()>1) { // has more one meanings
                $word_class .= ' polysemy';                
            } else {
                $word_class .= ' meaning-not-checked';
            }

            if (!$this->gramsets->where('pivot.relevance', '>', 0)->count()) {
                $word_class .= ' no-gramsets';
            } elseif ($this->gramsets->where('pivot.relevance',2)->count()) {
                $word_class .= ' gramset-checked';
            } else { 
                $word_class .= ' gramset-not-checked';
            }
           
        } elseif (User::checkAccess('corpus.edit')) {
            $word_class = 'lemma-linked call-add-wordform';
        }

        if ($search_word && Grammatic::changeLetters((string)$word_node, $this->lang_id) == $search_word 
                || sizeof($search_w) && in_array($this->id,$search_w)) {
            $word_class .= ' word-marked';
        }

        if ($word_class) {
            $word_node->addAttribute('class',$word_class);
        }
    }

    /**
     * 
     * @param SimpleXMLElement $word 
     * @param integer $meanings_checked
     * @param integer $meanings_unchecked
     * @param boolean $with_edit
     * @param boolean $preloaded
     * @return SimpleXMLElement
     */
    public function addWordBlock($word, $with_edit=null, $preloaded=false) {
        $w_id = (int)$word->attributes()->id;
        $s_id = $this->s_id;
        
        $link_block = $word->addChild('div');
        $link_block->addAttribute('id','links_'.$w_id);
        $link_block->addAttribute('class','links-to-lemmas');
        $link_block->addAttribute('data-downloaded',(int)$preloaded);
        
        if (!$preloaded) {
            add_loading_image_to_xml($link_block);
            if (User::checkAccess('corpus.edit') && $with_edit) { // icon 'pensil'
                self::addEditExampleButton($link_block);
            }
        } else {
            self::addMeaningsBlock($link_block);
        }                
    }
    
    // icon 'pensil'
    public function addEditExampleButton($link_block) {
        if (!User::checkAccess('corpus.edit')) {
            return;
        }
        $button_edit_p = $link_block->addChild('p');
        $button_edit_p->addAttribute('class','text-example-edit'); 
        $button_edit = $button_edit_p->addChild('a',' ');//,'&#9999;'
        $button_edit->addAttribute('href',
                LaravelLocalization::localizeURL('/corpus/text/'.$this->text_id.
                        '/edit/example/'.$this->s_id.'_'.$this->w_id)); 
        $button_edit->addAttribute('class','glyphicon glyphicon-pencil');  
        return $link_block;
    }

    public function addMeaningsBlock(\SimpleXMLElement $parent) {
        if (empty($this->checkedMeaning()) && !sizeof($this->uncheckedMeanings())) { return null; }
                
        $block_div = $parent->addChild('div');
        if (!empty($this->checkedMeaning())) {
            $this->buildCheckedMeaningNode($block_div);
        } else {
            $this->buildUncheckedMeaningsNode($block_div);
        }
        // грамсеты
        $this->buildGramsetBlock($block_div);

        // ссылки редактирования
        $this->buildEditLinksNode($block_div);

        return $parent;
    }
    
    // Было checkedMeaningForWordBlock() → стало buildCheckedMeaningNode()
    public function buildCheckedMeaningNode(\SimpleXMLElement $parent) {
        $meaning_checked = $this->checkedMeaning();
        $locale = LaravelLocalization::getCurrentLocale();
        $p = $parent->addChild('p');
        $a = $p->addChild('a', htmlspecialchars($meaning_checked->lemma->lemma, ENT_XML1));
        $a->addAttribute('href', LaravelLocalization::localizeURL('dict/lemma/'.$meaning_checked->lemma_id));

        $a->addChild('span', 
            ' '.$meaning_checked->lemma->pos->code.' ('.$meaning_checked->getMultilangMeaningTextsString($locale).')'
        );

    }

    // Было uncheckedMeaningsForWordBlock() → стало buildUncheckedMeaningsNode()
    public function buildUncheckedMeaningsNode(\SimpleXMLElement $parent) {
        $locale = LaravelLocalization::getCurrentLocale();

        foreach ($this->uncheckedMeanings() as $meaning) {
            $p = $parent->addChild('p');
            $a = $p->addChild('a', htmlspecialchars($meaning->lemma->lemma, ENT_XML1));
            $a->addAttribute('href', LaravelLocalization::localizeURL('dict/lemma/'.$meaning->lemma_id));

            $a->addChild('span', 
                ' '.$meaning->lemma->pos->code.' ('.$meaning->getMultilangMeaningTextsString($locale).')'
            );

            if (User::checkAccess('corpus.edit')) {
                $span = $p->addChild('span', ' ');
                $span->addAttribute('class', 'fa fa-plus choose-meaning');
                $span->addAttribute('data-add', $meaning->id.'_'.$this->text_id.'_'.$this->s_id.'_'.$this->w_id);
                $span->addAttribute('title', \Lang::trans('corpus.mark_right_meaning'));
                $span->addAttribute('onClick', "addWordMeaning(this)");
            }
        }

        if (User::checkAccess('corpus.edit')) {
            $input = $parent->addChild('input');
            $input->addAttribute('class', 'add-wordform-link');
            $input->addAttribute('type', 'button');
            $input->addAttribute('value', \Lang::trans('corpus.new_meaning'));
            $input->addAttribute(
                'onclick',
                "callAddWordform(this, '".$this->text_id."', '".$this->w_id."', '".$this->word."', '".$this->text->lang_id."', '".$locale."')"
            );
        }
    }

    public function buildGramsetBlock(\SimpleXMLElement $parent) {
        $w_id = $this->w_id;
        $checked_gramset = $this->gramsets()->wherePivot('relevance',2)->first();

        if (!empty($checked_gramset)) {
            $p = $parent->addChild('p', $gramset->gramsetString());
            $p->addAttribute('class', 'word-gramset');
            return;
        } elseif (User::checkAccess('corpus.edit')) {
            $gramsets = $this->gramsets()->wherePivot('relevance',1)->get();
            if (empty($gramsets)) { return; }

            $div = $parent->addChild('div');
            $div->addAttribute('id', 'gramsets_'.$w_id);
            $div->addAttribute('class', 'word-gramset-not-checked');

            foreach ($gramsets as $gramset) {
                $wordform=$this->wordforms()->whereId($gramset->pivot->wordform_id)->first();
                $p = $div->addChild('p', $gramset->gramsetString());
                $span = $p->addChild('span');
                $span->addAttribute('data-add', $this->id."_".$w_id."_".$wordform->id."_".$gramset->id);
                $span->addAttribute('class', 'fa fa-plus choose-gramset');
                $span->addAttribute('title', \Lang::trans('corpus.mark_right_gramset').' ('.$wordform->wordform.')');
                $span->addAttribute('onClick', 'addWordGramset(this)');
            }
            return $div;
        }
        return null;
    }
    
    // Было editLinksForWordBlock() → стало buildEditLinksNode()
    public function buildEditLinksNode(\SimpleXMLElement $parent) {
        if (!User::checkAccess('corpus.edit')) {
            return null;
        }

        $url = '/corpus/text/'.$this->text_id.'/edit/example/'.$this->s_id.'_'.$this->w_id;
        $p = $parent->addChild('p');
        $p->addAttribute('class', 'text-example-edit');

        if (!$this->hasImportantExamples()) {
            $i = $p->addChild('i', ' ');
            $i->addAttribute('class', 'fa fa-sync-alt fa-lg update-word-block');
            $i->addAttribute('title', '');
            $i->addAttribute('onclick', "updateWordBlock(".$this->text_id.",".$this->w_id.")");
        }

        $a = $p->addChild('a', ' ');
        $a->addAttribute('href', LaravelLocalization::localizeURL($url));
        $a->addAttribute('class', 'glyphicon glyphicon-pencil');

    //    return $p;
    }    























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