<?php namespace App\Traits\Export;

use App\Models\Corpus\Word;

trait TextExport
{
    /**
     * Load xml from string, create SimpleXMLelement
     *
     * @param $text_xml - markup text
     * @param id - identifier of object Text or Transtext
     *
     * return Array [SimpleXMLElement object, error text if exists]
     */
    public static function toXML($text_xml, $id=NULL){
        libxml_use_internal_errors(true);
        if (!preg_match("/^\<\?xml/", $text_xml)) {
            $text_xml = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'.
                                     '<text>'.$text_xml.'</text>';
        }
        $sxe = simplexml_load_string($text_xml);
        $error_text = '';
        if (!$sxe) {
            $error_text = "XML loading error". ' (text_id='.$id.": $text_xml)\n";
            foreach(libxml_get_errors() as $error) {
                $error_text .= "\t". $error->message;
            }
        }
        return [$sxe,$error_text];
    }
    
    /**
     * Load XML from string, create DOMDocument
     *
     * @param string $text_xml - markup text
     * @param int|null $id - identifier of object Text or Transtext
     *
     * @return array [DOMDocument|null, string $error_text]
     */
    public static function toDOM(string $text_xml, $id = null): array
    {
        libxml_use_internal_errors(true);

        // Оборачиваем в корень <text>, если нет декларации
        if (!preg_match("/^\<\?xml/", $text_xml)) {
            $text_xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' .
                        '<text>' . $text_xml . '</text>';
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $error_text = '';
        if (!$dom->loadXML($text_xml)) {
            $error_text = "XML loading error (text_id=$id)\n";
            foreach (libxml_get_errors() as $error) {
                $error_text .= "\t" . $error->message;
            }
            $dom = null;
        }

        return [$dom, $error_text];
    }    

    public static function processSentenceForExport($sentence) {
        $sentence = trim(str_replace("\n"," ",strip_tags($sentence)));
        return str_replace("\'","'",$sentence);
    }
    
    public function toCONLL() {
        $out = "";
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) {
            return NULL;
        }
        $sentences = $sxe->xpath('//s');
        $is_last_item = sizeof($sentences);
        foreach ($sentences as $sentence) {
            $out .= "# text_id = ".$this->id."\n".
                    "# sent_id = ".$this->id."-".$sentence['id']."\n".
                    //$sentence->asXML()."\n".
                    "# text = ".self::processSentenceForExport($sentence->asXML())."\n";
            $trans_text = self::processSentenceForExport($this->getTransSentence($sentence['id']));
            if ($trans_text) {
                $out .= "# text_ru = ".$trans_text."\n";
            }
            $count = 1;
            foreach ($sentence->w as $w) {
                $words = Word::toCONLL($this->id, (int)$w['id'], (string)$w);
                if (!$words) {
                    $out .= "$count\tERROR\n";
                    continue;
                }
                foreach ($words as $line) {
                    $out .= "$count\t".
                            //$w->asXML().
                            $line."\n";
                }
                $count++;
            }
            if ($is_last_item-- > 1) {
                $out .= "\n";
            }
        }
        return $out;
    }
    
    public function breakIntoVerses() {
        $verses = [];
        $v_text = trim(preg_replace("/\r/",'',preg_replace("/\n/",'',preg_replace("/\|/",'',$this->text))));
        $prev_verse=0;
        while (preg_match("/^(.*?)\<sup\>(\d+)\<\/sup\>(.*)$/", $v_text, $regs)) {
            if ($prev_verse) {
                $verses[$prev_verse] = trim($regs[1]);
            }
            $prev_verse = $regs[2];
            $v_text = $regs[3];
        }
        $verses[$prev_verse]= trim($v_text);
//dd($this->id, $verses);        
        return $verses;
    }
    
    public function sentencesToLines() {
        $out = "";
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) {
            return NULL;
        }
        $sentences = $sxe->xpath('//s');
        $is_last_item = sizeof($sentences);
        foreach ($sentences as $sentence) {
            $words = [];
            foreach ($sentence->w as $w) {
                $words[] = Word::uniqueLemmaWords($this->id, (int)$w['id'], (string)$w);
            }
            $out .= join('|',$words)."\n";
        }
        return $out;
    }
    
}