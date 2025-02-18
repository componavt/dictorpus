<?php namespace App\Traits\Methods;

use App\Models\Corpus\Text;

trait getSentencesFromXML
{
    public function getSentencesFromXML($with_words = false) {
        $sentences = [];
        if (!$this->text_xml) {
            return $sentences;
        }
        list($sxe,$error_message) = Text::toXML($this->text_xml,$this->id);
        if ($error_message) {
            return $sentences;
        }
        foreach ($sxe->xpath('//s') as $s) {
            $s_id = (int)$s->attributes()->id;
            $sentence = mb_ereg_replace('[¦^]', '', $s->asXML());
            $sentences[$s_id]['sentence'] = $sentence;
            if ($with_words) {
                list($wxe,$error_message) = Text::toXML($sentence);
                if ($error_message) {
                    return [];
                }
                foreach ($wxe->xpath('//w') as $w) {
                    $sentences[$s_id]['words'][(int)$w->attributes()->id] = (string)$w;
                }
            }
        }
        return $sentences;        
    }
}