<?php

namespace App\Library\Experiments;

use Illuminate\Database\Eloquent\Model;
//use DB;

use App\Models\Dict\Lang;
use App\Models\Dict\ReverseLemma;

class TemplGen extends Model
{
    public static function getNames($lang_id) {
/*        $lemmas = Lemma::whereIn('pos_id', [1,5])
                       ->whereLangId($lang_id)->get();*/
        $rlemmas = ReverseLemma::whereIn('id', function ($q) use ($lang_id) {
            $q->select('id')->from('lemmas')
              ->whereIn('pos_id', [1,5])
              ->whereLangId($lang_id);
        })->orderBy('reverse_lemma')//->whereId(44534)
        ->get();
        
        $lang = Lang::find($lang_id);
        $dialect_id = $lang->mainDialect();
        
        $functionName = "getNames{$lang_id}";
        return self::$functionName($rlemmas, $dialect_id);
    }
    
    public static function getNames6($rlemmas, $dialect_id) {
        $forms = [];
        $p1 = 3;
        $p2 = 4;
        foreach ($rlemmas as $rlemma) {
            $lemma = $rlemma->lemma;
            $affix = $rlemma->affix;
            $w1 = $lemma->wordformsByGramsetDialect($p1, $dialect_id)->first();
            $w2 = $lemma->wordformsByGramsetDialect($p2, $dialect_id)->first();
            if (!$w1 || !$w2) {
                continue;
            }
            if (!preg_match("/^(.+)n$/", $w1->pivot->affix, $regs)) {
                continue;
            }
            $a1 = $regs[1];
            
            if (preg_match("/^(.+)d$/", $w2->pivot->affix, $regs)) {
                $a2 = '-';
            } elseif (preg_match("/^(.+)te$/", $w2->pivot->affix, $regs)) {
                $a2 = $regs[1];
            } else {
                continue;
            }
            $forms[$affix.'_'.$a1.'_'.$a2]['lemmas'][] = [$lemma->lemma, $w1->wordform, $w2->wordform];
            $forms[$affix.'_'.$a1.'_'.$a2]['template'] = ($affix ? '|'.$affix : ''). ' ['.$a1. ($a2=='-' ? '' : ', '.$a2). ']';
/*            $forms[$affix.'_'.$a1.'_'.$a2][0] = $affix;
            $forms[$affix.'_'.$a1.'_'.$a2][1] = $a1;
            $forms[$affix.'_'.$a1.'_'.$a2][2] = $a2;*/
        }
//        asort($forms);
        return $forms;
    }

    public static function getNames5($rlemmas, $dialect_id) {
        $forms = [];
        $p1 = 3;
        $p2 = 4;
        $p3 = 10;
        foreach ($rlemmas as $rlemma) {
            $lemma = $rlemma->lemma;
//            $lemma->reloadStemAffixByWordforms();     
//            $lemma->updateWordformAffixes(true);
            
            $affix = $rlemma->affix;
            $w1 = $lemma->wordformsByGramsetDialect($p1, $dialect_id)->first();
            $w2 = $lemma->wordformsByGramsetDialect($p2, $dialect_id)->first();
            $w3 = $lemma->wordformsByGramsetDialect($p3, $dialect_id)->first();
            if (!$w1 || !$w2 || !$w3) {
                continue;
            }
            if (!preg_match("/^(.+)n$/", $w1->pivot->affix, $regs)) {
                continue;
            }
            $a1 = $regs[1];

            if (!preg_match("/^(.+)h$/", $w3->pivot->affix, $regs)) {
                continue;
            } 
            if ($regs[1]!=$a1) {
                $a1 .= '/'.$regs[1];
            }
            
            if (preg_match("/^(.*)[td][uy]$/", $w2->pivot->affix, $regs)) {
                $a2 = $regs[1];
            } else {
                $a2 = '-';
            }
            
            $forms[$affix.'_'.$a1.'_'.$a2]['lemmas'][] = [$lemma->lemma, $w1->wordform, $w3->wordform, $w2->wordform];
            $forms[$affix.'_'.$a1.'_'.$a2]['template'] = ($affix ? '|'.$affix : ''). ' ['.$a1. ($a2=='-' ? '' : ', '.$a2). ']';
        }
        return $forms;
        
    }    
    public static function getNames4($rlemmas, $dialect_id) {
        $forms = [];
        $p1 = 3;
        $p2 = 4;
        $p3 = 10;
        foreach ($rlemmas as $rlemma) {
            $lemma = $rlemma->lemma;
//            $lemma->reloadStemAffixByWordforms();     
//            $lemma->updateWordformAffixes(true);
            
            $affix = $rlemma->affix;
            $w1 = $lemma->wordformsByGramsetDialect($p1, $dialect_id)->first();
            $w2 = $lemma->wordformsByGramsetDialect($p2, $dialect_id)->first();
            $w3 = $lemma->wordformsByGramsetDialect($p3, $dialect_id)->first();
            if (!$w1 || !$w2 || !$w3) {
                continue;
            }
            if (!preg_match("/^(.+)n$/", $w1->pivot->affix, $regs)) {
                continue;
            }
            $a1 = $regs[1];

            if (!preg_match("/^(.+)h$/", $w3->pivot->affix, $regs)) {
                continue;
            } 
            if ($regs[1]!=$a1) {
                $a1 .= '/'.$regs[1];
            }
            
            if (preg_match("/^(.+)t[aä]$/", $w2->pivot->affix, $regs)) {
                $a2 = $regs[1];
            } else {
                $a2 = '-';
            }
            
            $forms[$affix.'_'.$a1.'_'.$a2]['lemmas'][] = [$lemma->lemma, $w1->wordform, $w3->wordform, $w2->wordform];
            $forms[$affix.'_'.$a1.'_'.$a2]['template'] = ($affix ? '|'.$affix : ''). ' ['.$a1. ($a2=='-' ? '' : ', '.$a2). ']';
        }
        return $forms;
    }
}

