<?php

use Illuminate\Database\Seeder;

use App\Library\Experiments\Dmarker;

class DmarkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $markers = [
            1=> ['рефлексы п.-ф. *aa в первом слоге слова', false],
            2=> ['рефлексы п.-ф. *ää в первом слоге слова', false],
            3=> ['дистрибуция глухих/ звонких смычно- взрывных согласных в интервокальном положении', false],
            4=> ['дистрибуция глухих/звонких смычно-взрывных согласных в положении после звонких согласных l, r, n, m', false],
            5=> ['дистрибуция глухих/звонких переднеязычных щелевых согласных', false],
            6=> ['рефлексы приб.-фин. *Vi в положении перед переднеязычным щелевым в первом слоге слова', true],
            7=> ['рефлексы приб.-фин. *Vi в положении перед ne/ni во втором слоге и далее в слове', true],
            8=> ['рефлексы приб.-фин. *Vi в положении перед ne/ni в третьем слоге слова и далее в слове', true],
            9=> ['наличие на конце слова Vin', true],
            10=> ['рефлексы приб.-фин. *Co/Cö конца слова', true],
            11=> ['рефлексы приб.-фин. *Vi в положении перед z/s в третьем слоге', true],
            12=> ['рефлексы приб.-фин. *Vi в положении перед z/s в четвертом слоге', true],
            13=> ['рефлексы приб.-фин. *Vi в положении перед st в третьем слоге и далее в слове', true],
            14=> ['Согласные конца слова', true],
            15=> ['Гармония гласных', true],
            16=> ['j/d’ в начале слова', true],
            17=> ['j/d’ в середине слова', true],
            18=> ['j/d’', true],
            19=> ['nh/hn', false],
            20=> ['C+гемината', true],
            21=> ['Переднеязычные щелевые в начале слова', false],
            22=> ['Переднеязычные щелевые в начале слова', false],
            23=> ['Переднеязычные щелевые в начале слова', false],
            24=> ['Переднеязычные щелевые в конце слова', false],
            25=> ['Переднеязычные щелевые в конце слова', false],
            26=> ['Африката сс', true],
            27=> ['Эссив', true],
            28=> ['Транслатив', false],
            29=> ['Местоимения «я», «ты»', false],
            30=> ['Местоимение «он»', false],
            31=> ['1 л. мн. през. инд', false]
        ];
        foreach ($markers as $id => $marker) {
            Dmarker::create(['id'=>$id, 
                             'name' => $marker[0], 
                             'absence'=>$marker[1]]);
        }
    }
}