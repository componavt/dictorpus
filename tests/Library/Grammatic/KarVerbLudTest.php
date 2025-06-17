<?php

//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarVerbLud;

// php artisan make:test Library\Grammatic\KarVerbLudTest
// ./vendor/bin/phpunit tests/Library/Grammatic\KarVerbLudTest

class KarVerbLudTest extends TestCase
{
    public function testSuggestTemplates() {
        $verbs = [
	    3461 => 'kaččoda',
            42494 => 'kuččuda',
	    41301 => 'eččidä',
	    14596 => 'ottada',
	    50380 => 'kyzydä',
	    22172 => 'andada',
	    14594 => 'elädä',
	    45142 => 'itkei',
	    43596 => 'särbäi',
	    29444 => 'd’uoda',
	    62863 => 'viedä',
	    41336 => 'suada',
	    29594 => 'tulda',
	    3525 => 'mändä',
	    67094 => 'purda',
	    22260 => 'pagišta',
	    44615 => 'pestä',
	    43235 => 'magata',
	    41869 => 'rubeta',
	    70904 => 'haravoita',
	    62330 => 'suvaita',
	];        
        $result = [];
        foreach ($verbs as $lemma_id=>$verb) {
            $result[$lemma_id] = KarVerbLud::suggestTemplates($verb);
        }
	$expected = [
	    3461 => ['kač|čoda [o]'],
            42494 => ['kuč|čuda [u]'],
	    41301 => ['eč|čidä [i]'],
	    14596 => ['ot|tada [a]'],
	    50380 => ['kyzy|dä []'],
	    22172 => ['anda|da []'],
	    14594 => ['elä|dä []'],
	    45142 => ['itke|i []'],
	    43596 => ['särbä|i []'],
	    29444 => ['d’uo|da []'],
	    62863 => ['vie|dä []'],
	    41336 => ['sua|da []'],
	    29594 => ['tul|da [e]'],
	    3525 => ['män|dä [e]'],
	    67094 => ['pur|da [e]'],
	    22260 => ['pagi|šta [že]'],
	    44615 => ['pe|stä [ze]'],
	    43235 => ['maga|ta [da]'],
	    41869 => ['rube|ta [da]'],
	    70904 => ['haravoi|ta [če]'],
	    62330 => ['suvai|ta [če]'],
      ];
 
        $this->assertEquals( $expected, $result);        
    }
}
