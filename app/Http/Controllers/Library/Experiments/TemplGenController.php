<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Library\Experiments\TemplGen;

class TemplGenController extends Controller
{
    public function __construct()
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:dict.edit,/');
    }

    public function index(Request $request) {
        $lang_id = (int)$request->input('lang_id');
        $what = $request->input('what');
        if ($what=='names') {
            $table = TemplGen::getNames($lang_id);
        } else {
            
        }
//dd($table);        
        print "<table border=1>";
        foreach ($table as $row_id => $row) {
            $count_rows = count($row['lemmas']);
            $first_lemma = array_pop($row['lemmas']);
            print '<tr>';
            print '<td align="right">'.$first_lemma[0].'</td>';
            print "<td rowspan=".$count_rows." valign=top>".$row['template']."</td>";
/*            for($i=0; $i<count($first_lemma); $i++) {
                print '<td align="right">'.$first_lemma[$i].'</td>';
            }*/
            print '<td>'.join(', ', $first_lemma).'</td>';
/*            for($i=0; $i<3; $i++) {
                print "<td rowspan=".$count_rows." valign=top>".$row[$i]."</td>";
            }*/
            print '</tr>';
            
            foreach ($row['lemmas'] as $lemma) {
                print '<tr>';
                print '<td align="right">'.$lemma[0].'</td>';
/*                for($i=0; $i<count($lemma); $i++) {
                    print '<td align="right">'.$lemma[$i].'</td>';
                }*/
                print '<td>'.join(', ', $lemma).'</td></tr>';
            }
        }
        print "</table>";
    }
    
}
