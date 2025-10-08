<?php namespace App\Traits\Select;

use LaravelLocalization;

use App\Library\Grammatic;

use App\Models\User;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;
use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

trait TextWordBlock
{
    public function meaningsGramsetsByWid() {
        $wids = Word::where('text_id', $this->id)->pluck('w_id');

        $meanings = Meaning::join('meaning_text', 'meaning_text.meaning_id', '=', 'meanings.id')
            ->where('text_id', $this->id)
            ->whereIn('w_id', $wids)
            ->with('lemma')
            ->with('meaningTexts')
            ->get();

        $gramsets = Gramset::join('text_wordform', 'text_wordform.gramset_id', '=', 'gramsets.id')
            ->where('text_id', $this->id)
            ->whereIn('w_id', $wids)
            ->get();

        $wordforms = Wordform::whereIn('id', function ($q) use ($wids) {
                $q->select('wordform_id')->from('text_wordform')
                ->where('text_id', $this->id)
                ->whereIn('w_id', $wids);
            })->pluck('wordform','id')->toArray();
            
        $words_with_important_examples = Word::where('text_id', $this->id)
                ->whereIn('id',function ($q) {
                    $q->select('word_id')->from('meaning_text')
                      ->where('relevance', '>', 5);
                })->pluck('w_id')->toArray();
                
        return [$this->listByWidRelevance($meanings), $this->listByWidRelevance($gramsets), $wordforms, $words_with_important_examples];
    }

    public function listByWidRelevance($list) {
        $out = [];
        foreach ($list as $m) {
            $w = $m->w_id;
            if (!isset($out[$w])) {
                $out[$w] = ['checked' => null, 'unchecked' => null];
            }
            if ($m->relevance > 1) {
                $out[$w]['checked'] = $m; 
            } elseif ($m->relevance == 1) {
                $out[$w]['unchecked'][] = $m;
            }
        }
        return $out;
    }    
    
    /**
     * Преобразует текст перед выводом на отдельной странице (Text show).
     * Собирает предложения и расставляет блоки со ссылками на леммы 
     * и вызов функций редактирования.
     * 
     * Вызывается из представления corpus.text.show.text
     * 
     * @param array $url_args
     * @return string
     */
    public function textForPage($url_args, $meanings=[], $gramsets=[], $wordforms=[], $words_with_important_examples=[]) { 
//mb_internal_encoding("UTF-8");
//mb_regex_encoding("UTF-8");        
        if ($this->text_structure) :
            $this->text_xml = $this->text_structure;
            $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
            foreach ($sentences as $s) {
                $s->text_xml = mb_ereg_replace('[¦^]', '', $s->text_xml);
                $this->text_xml = mb_ereg_replace("\<s id=\"".$s->s_id."\"\/\>", 
                        $s->text_xml, $this->text_xml);                
            }
        endif; 
        if ($this->text_xml) :
            return $this->setLemmaLink($this->text_xml, 
                    $url_args['search_word'] ?? null, 
                    $url_args['search_sentence'] ?? null,
                    true, 
                    $url_args['search_wid'] ?? [], 
                    $meanings, $gramsets, $wordforms,$words_with_important_examples);
        endif; 
        return nl2br($this->text);
    }
    
    /**
     * Gets markup text with links from words to related lemmas

     * @param string $markup_text     - text with markup tags
     * @param string $search_word     - string of searching word
     * @param integer $search_sentence - ID of searching sentence object
     * @param boolean $with_edit      - 1 if it is the edit mode
     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function setLemmaLink($markup_text=null, $search_word=null, $search_sentence=null, $with_edit=true, $search_w=[], $meanings=[], $gramsets=[], $wordforms=[], $words_with_important_examples=[], $preloaded=false){
        if (!$markup_text) {
            $markup_text = $this->text_xml;
        }
        list($sxe,$error_message) = self::toXML($markup_text,'');
        if ($error_message) { return $markup_text; }
//dd($gramsets);        
        $sentences = $sxe->xpath('//s');
        foreach ($sentences as $sentence) {
                $s_id = (int)$sentence->attributes()->id;
            foreach ($sentence->children() as $word) {            
                $word = 
                $this->editWordBlock($word, $s_id, $search_word, $with_edit, $search_w, $meanings, $gramsets, $wordforms, $words_with_important_examples, $preloaded);
            }
            
            // назначаем класс
            $sentence_class = "sentence";
            if ($search_sentence && $search_sentence==$s_id) {
                $sentence_class .= " word-marked";
            }
            $sentence->addAttribute('class',$sentence_class);
            
            // меняем id
            $sentence->attributes()->id = 'text_s'.$s_id;
        }
        
        return $sxe->asXML();
    }

    /**
     * 
     * @param SimpleXMLElement $word 
     * @param integer $s_id   
     * @param string $search_word     - string of searching word
     * @param boolean $with_edit      - 1 if it is the edit mode
     * @param array $search_w         - array ID of searching word object
     * @param boolean $preloaded      - 1 if with preloaded word blocks
     * @return SimpleXMLElement $word
     */
    public function editWordBlock($word, $s_id, $search_word=null, $with_edit=null, $search_w=[], $meanings=[], $gramsets=[], $wordforms=[], $words_with_important_examples=[], $preloaded=false) {
        $w_id = (int)$word->attributes()->id;
        if (!$w_id) { return $word; }
        
        $word_class = '';
        if (!empty($meanings[$w_id]['checked']) || !empty($meanings[$w_id]['unchecked']) && count($meanings[$w_id]['unchecked'])) {
            list ($word, $word_class) = 
                $this->addWordBlock($word,    $s_id, 
                                    $meanings[$w_id] ?? [], 
                                    $gramsets[$w_id] ?? [], 
                                    $wordforms, 
                                    in_array($w_id, $words_with_important_examples), 
                                    $with_edit,     $preloaded);
            
        } elseif (User::checkAccess('corpus.edit')) {
            $word_class = 'lemma-linked call-add-wordform';
        }

        if ($search_word && Grammatic::changeLetters((string)$word, $this->lang_id) == $search_word 
                || sizeof($search_w) && in_array($w_id,$search_w)) {
            $word_class .= ' word-marked';
        }

        if ($word_class) {
            $word->addAttribute('class',$word_class);
        }
        return $word;
    }

    /**
     * 
     * @param SimpleXMLElement $word 
     * @param integer $s_id
     * @param integer $meanings_checked
     * @param integer $meanings_unchecked
     * @param boolean $with_edit
     * @param boolean $preloaded
     * @return SimpleXMLElement
     */
    public function addWordBlock($word, $s_id, $meanings=[], $gramsets=[], $wordforms=[], $has_important_examples=false, $with_edit=null, $preloaded=false) {       
        $w_id = (int)$word->attributes()->id;
        $s_word = Grammatic::changeLetters((string)$word,$this->lang_id);
        $word_class = 'lemma-linked';
        
        // создаём <div>
        $link_block = $word->addChild('div');
        
        // задаём атрибуты
        $link_block->addAttribute('id','links_'.$w_id);
        $link_block->addAttribute('class','links-to-lemmas');
        $link_block->addAttribute('data-downloaded',(int)$preloaded);
        
        if (!$preloaded) {
            add_loading_image_to_xml($link_block);
            if (User::checkAccess('corpus.edit') && $with_edit) { // icon 'pensil'
                $link_block = self::addEditExampleButton($link_block, $this->id, $s_id, $w_id);
            }
        } else {
            $link_block = $this->addMeaningsBlock($link_block, $s_id, $w_id, $meanings, $gramsets, $wordforms, $has_important_examples, $s_word);
        }
                
        if (!empty($meanings['checked'])) {
            $word_class .= ' meaning-checked';
        } elseif (!empty($meanings['unchecked']) && count($meanings['unchecked'])>1) {
            $word_class .= ' polysemy';                
        } else {
            $word_class .= ' meaning-not-checked';
        }
        
        if (!empty($gramsets['checked'])) {
            $word_class .= ' gramset-checked';
        } elseif (!empty($gramsets['unchecked'])) {
            $word_class .= ' gramset-not-checked';            
        } else {
            $word_class .= ' no-gramsets';
        }
        return [$word, $word_class];        
    }

        public function addMeaningsBlock(\SimpleXMLElement $parent, $s_id, $w_id, $meanings=[], $gramsets=[], $wordforms=[], $has_important_examples=false, $s_word='') {
        if (empty($meanings['checked']) && empty($meanings['unchecked'])) { return null; }
                
        $block_div = $parent->addChild('div');
        if (!empty($meanings['checked'])) {
            $this->buildCheckedMeaningNode($block_div, $meanings['checked']);
        } else {
            $this->buildUncheckedMeaningsNode($block_div, $meanings['unchecked'], $s_word);
        }
        // грамсеты
        $this->buildGramsetBlock($block_div, $w_id, $gramsets, $wordforms);

        // ссылки редактирования
        $this->buildEditLinksNode($block_div, $s_id, $w_id, $has_important_examples);

        return $parent;
    }
    
    // Было checkedMeaningForWordBlock() → стало buildCheckedMeaningNode()
    public function buildCheckedMeaningNode(\SimpleXMLElement $parent, $meaning_checked) {
        $locale = LaravelLocalization::getCurrentLocale();
        $p = $parent->addChild('p');
        $a = $p->addChild('a', htmlspecialchars($meaning_checked->lemma->lemma, ENT_XML1));
        $a->addAttribute('href', LaravelLocalization::localizeURL('dict/lemma/'.$meaning_checked->lemma_id));

        $a->addChild('span', 
            ' '.$meaning_checked->lemma->pos->code.' ('.$meaning_checked->getMultilangMeaningTextsString($locale).')'
        );

    }

    // Было uncheckedMeaningsForWordBlock() → стало buildUncheckedMeaningsNode()
    public function buildUncheckedMeaningsNode(\SimpleXMLElement $parent, $unchecked_meanings, $s_word='') {
        $locale = LaravelLocalization::getCurrentLocale();

        foreach ($unchecked_meanings as $meaning) {
            $p = $parent->addChild('p');
            $a = $p->addChild('a', htmlspecialchars($meaning->lemma->lemma, ENT_XML1));
            $a->addAttribute('href', LaravelLocalization::localizeURL('dict/lemma/'.$meaning->lemma_id));

            $a->addChild('span', 
                ' '.$meaning->lemma->pos->code.' ('.$meaning->getMultilangMeaningTextsString($locale).')'
            );

            if (User::checkAccess('corpus.edit')) {
                $span = $p->addChild('span', ' ');
                $span->addAttribute('class', 'fa fa-plus choose-meaning');
                $span->addAttribute('data-add', $meaning->id.'_'.$this->id.'_'.$meaning->s_id.'_'.$meaning->w_id);
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
                "callAddWordform(this, '".$this->id."', '".$meaning->w_id."', '".$s_word."', '".$this->lang_id."', '".$locale."')"
            );
        }
    }

    public function buildGramsetBlock(\SimpleXMLElement $parent, $w_id, $gramsets, $wordforms) {
        if (!empty($gramsets['checked'])) {
            $p = $parent->addChild('p', $gramsets['checked']->gramsetString());
            $p->addAttribute('class', 'word-gramset');
            return $p;
        } elseif (User::checkAccess('corpus.edit') && !empty($gramsets['unchecked'])) {
            $div = $parent->addChild('div');
            $div->addAttribute('id', 'gramsets_'.$w_id);
            $div->addAttribute('class', 'word-gramset-not-checked');

            foreach ($gramsets['unchecked'] as $gramset) {
                $p = $div->addChild('p', $gramset->gramsetString());
                $span = $p->addChild('span');
                $span->addAttribute('data-add', $this->id."_".$w_id."_".$gramset->wordform_id."_".$gramset->id);
                $span->addAttribute('class', 'fa fa-plus choose-gramset');
                $span->addAttribute('title', \Lang::trans('corpus.mark_right_gramset').' ('.($wordforms[$gramset->wordform_id] ?? '').')');
                $span->addAttribute('onClick', 'addWordGramset(this)');
            }
            return $div;
        }
        return null;
    }
    
    // Было editLinksForWordBlock() → стало buildEditLinksNode()
    public function buildEditLinksNode(\SimpleXMLElement $parent, $s_id, $w_id, $has_important_examples=true) {
        if (!User::checkAccess('corpus.edit')) {
            return null;
        }

        $url = '/corpus/text/'.$this->id.'/edit/example/'.$s_id.'_'.$w_id;
        $p = $parent->addChild('p');
        $p->addAttribute('class', 'text-example-edit');

        if (!$has_important_examples) {
            $i = $p->addChild('i', ' ');
            $i->addAttribute('class', 'fa fa-sync-alt fa-lg update-word-block');
            $i->addAttribute('title', '');
            $i->addAttribute('onclick', "updateWordBlock(".$this->id.",".$w_id.")");
        }

        $a = $p->addChild('a', ' ');
        $a->addAttribute('href', LaravelLocalization::localizeURL($url));
        $a->addAttribute('class', 'glyphicon glyphicon-pencil');

    //    return $p;
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