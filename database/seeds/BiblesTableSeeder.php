<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiblesTableSeeder extends Seeder
{
    public function run()
    {
        $bibles = array(
            array('sequence_number' => 1, 'name_ru' => 'Бытие', 'name_en' => 'Genesis'),
            array('sequence_number' => 2, 'name_ru' => 'Исход', 'name_en' => 'Exodus'),
            array('sequence_number' => 3, 'name_ru' => 'Левит', 'name_en' => 'Leviticus'),
            array('sequence_number' => 4, 'name_ru' => 'Числа', 'name_en' => 'Numbers'),
            array('sequence_number' => 5, 'name_ru' => 'Второзаконие', 'name_en' => 'Deuteronomy'),
            array('sequence_number' => 6, 'name_ru' => 'Книга Иисуса Навина', 'name_en' => 'Joshua'),
            array('sequence_number' => 7, 'name_ru' => 'Книга Судей', 'name_en' => 'Judges'),
            array('sequence_number' => 8, 'name_ru' => 'Книга Руфь', 'name_en' => 'Ruth'),
            array('sequence_number' => 9, 'name_ru' => 'Первая книга Царств', 'name_en' => '1 Samuel'),
            array('sequence_number' => 10, 'name_ru' => 'Вторая книга Царств', 'name_en' => '2 Samuel'),
            array('sequence_number' => 11, 'name_ru' => 'Третья книга Царств', 'name_en' => '1 Kings'),
            array('sequence_number' => 12, 'name_ru' => 'Четвёртая книга Царств', 'name_en' => '2 Kings'),
            array('sequence_number' => 13, 'name_ru' => 'Первая книга Паралипоменон', 'name_en' => '1 Chronicles'),
            array('sequence_number' => 14, 'name_ru' => 'Вторая книга Паралипоменон', 'name_en' => '2 Chronicles'),
            array('sequence_number' => 15, 'name_ru' => 'Первая книга Ездры', 'name_en' => 'Ezra'),
            array('sequence_number' => 16, 'name_ru' => 'Книга Неемии', 'name_en' => 'Nehemiah'),
            array('sequence_number' => 17, 'name_ru' => 'Вторая книга Ездры', 'name_en' => '2 Esdras'),
            array('sequence_number' => 18, 'name_ru' => 'Книга Товита', 'name_en' => 'Tobit'),
            array('sequence_number' => 19, 'name_ru' => 'Книга Иудифи', 'name_en' => 'Judith'),
            array('sequence_number' => 20, 'name_ru' => 'Книга Есфирь', 'name_en' => 'Esther'),
            array('sequence_number' => 21, 'name_ru' => 'Книга Иова', 'name_en' => 'Job'),
            array('sequence_number' => 22, 'name_ru' => 'Псалтирь', 'name_en' => 'Psalms'),
            array('sequence_number' => 23, 'name_ru' => 'Книга Притчей Соломоновых', 'name_en' => 'Proverbs'),
            array('sequence_number' => 24, 'name_ru' => 'Книга Екклезиаста, или Проповедника', 'name_en' => 'Ecclesiastes'),
            array('sequence_number' => 25, 'name_ru' => 'Книга Песни песней Соломона', 'name_en' => 'Song of Solomon'),
            array('sequence_number' => 26, 'name_ru' => 'Книга Премудрости Соломона', 'name_en' => 'Wisdom of Solomon'),
            array('sequence_number' => 27, 'name_ru' => 'Книга Премудрости Иисуса, сына Сирахова', 'name_en' => 'Wisdom of Sirach'),
            array('sequence_number' => 28, 'name_ru' => 'Книга пророка Исаии', 'name_en' => 'Isaiah'),
            array('sequence_number' => 29, 'name_ru' => 'Книга пророка Иеремии', 'name_en' => 'Jeremiah'),
            array('sequence_number' => 30, 'name_ru' => 'Плач Иеремии', 'name_en' => 'Lamentations'),
            array('sequence_number' => 31, 'name_ru' => 'Послание Иеремии', 'name_en' => 'Epistle of Jeremiah'),
            array('sequence_number' => 32, 'name_ru' => 'Книга пророка Варуха', 'name_en' => 'Book of Baruch'),
            array('sequence_number' => 33, 'name_ru' => 'Книга пророка Иезекииля', 'name_en' => 'Ezekiel'),
            array('sequence_number' => 34, 'name_ru' => 'Книга пророка Даниила', 'name_en' => 'Daniel'),
            array('sequence_number' => 35, 'name_ru' => 'Книга пророка Осии', 'name_en' => 'Hosea'),
            array('sequence_number' => 36, 'name_ru' => 'Книга пророка Иоиля', 'name_en' => 'Joel'),
            array('sequence_number' => 37, 'name_ru' => 'Книга пророка Амоса', 'name_en' => 'Amos'),
            array('sequence_number' => 38, 'name_ru' => 'Книга пророка Авдия', 'name_en' => 'Obadiah'),
            array('sequence_number' => 39, 'name_ru' => 'Книга пророка Ионы', 'name_en' => 'Jonah'),
            array('sequence_number' => 40, 'name_ru' => 'Книга пророка Михея', 'name_en' => 'Micah'),
            array('sequence_number' => 41, 'name_ru' => 'Книга пророка Наума', 'name_en' => 'Nahum'),
            array('sequence_number' => 42, 'name_ru' => 'Книга пророка Аввакума', 'name_en' => 'Habakkuk'),
            array('sequence_number' => 43, 'name_ru' => 'Книга пророка Софонии', 'name_en' => 'Zephaniah'),
            array('sequence_number' => 44, 'name_ru' => 'Книга пророка Аггея', 'name_en' => 'Haggai'),
            array('sequence_number' => 45, 'name_ru' => 'Книга пророка Захарии', 'name_en' => 'Zechariah'),
            array('sequence_number' => 46, 'name_ru' => 'Книга пророка Малахии', 'name_en' => 'Malachi'),
            array('sequence_number' => 47, 'name_ru' => 'Первая книга Маккавейская', 'name_en' => '1 Maccabees'),
            array('sequence_number' => 48, 'name_ru' => 'Вторая книга Маккавейская', 'name_en' => '2 Maccabees'),
            array('sequence_number' => 49, 'name_ru' => 'Третья книга Маккавейская', 'name_en' => '3 Maccabees'),
            array('sequence_number' => 50, 'name_ru' => 'Третья книга Ездры', 'name_en' => '3 Esdras'),
            array('sequence_number' => 51, 'name_ru' => 'Евангелие от Матфея', 'name_en' => 'Gospel according to Matthew'),
            array('sequence_number' => 52, 'name_ru' => 'Евангелие от Марка', 'name_en' => 'Gospel according to Mark'),
            array('sequence_number' => 53, 'name_ru' => 'Евангелие от Луки', 'name_en' => 'Gospel according to Luke'),
            array('sequence_number' => 54, 'name_ru' => 'Евангелие от Иоанна', 'name_en' => 'Gospel according to John'),
            array('sequence_number' => 55, 'name_ru' => 'Деяния святых Апостолов', 'name_en' => 'Acts of the Holy Apostles'),
            array('sequence_number' => 56, 'name_ru' => 'Послание к Римлянам', 'name_en' => 'Epistle to the Romans'),
            array('sequence_number' => 57, 'name_ru' => 'Первое послание к Коринфянам', 'name_en' => 'First Epistle to the Corinthians'),
            array('sequence_number' => 58, 'name_ru' => 'Второе послание к Коринфянам', 'name_en' => 'Second Epistle to the Corinthians'),
            array('sequence_number' => 59, 'name_ru' => 'Послание к Галатам', 'name_en' => 'Epistle to the Galatians'),
            array('sequence_number' => 60, 'name_ru' => 'Послание к Ефесянам', 'name_en' => 'Epistle to the Ephesians'),
            array('sequence_number' => 61, 'name_ru' => 'Послание к Филиппийцам', 'name_en' => 'Epistle to the Philippians'),
            array('sequence_number' => 62, 'name_ru' => 'Послание к Колоссянам', 'name_en' => 'Epistle to the Colossians'),
            array('sequence_number' => 63, 'name_ru' => 'Первое послание к Фессалоникийцам', 'name_en' => 'First Epistle to the Thessalonians'),
            array('sequence_number' => 64, 'name_ru' => 'Второе послание к Фессалоникийцам', 'name_en' => 'Second Epistle to the Thessalonians'),
            array('sequence_number' => 65, 'name_ru' => 'Первое послание к Тимофею', 'name_en' => 'First Epistle to Timothy'),
            array('sequence_number' => 66, 'name_ru' => 'Второе послание к Тимофею', 'name_en' => 'Second Epistle to Timothy'),
            array('sequence_number' => 67, 'name_ru' => 'Послание к Титу', 'name_en' => 'Epistle to Titus'),
            array('sequence_number' => 68, 'name_ru' => 'Послание к Филимону', 'name_en' => 'Epistle to Philemon'),
            array('sequence_number' => 69, 'name_ru' => 'Послание к Евреям', 'name_en' => 'Epistle to the Hebrews'),
            array('sequence_number' => 70, 'name_ru' => 'Соборное послание Иакова', 'name_en' => 'Epistle of James'),
            array('sequence_number' => 71, 'name_ru' => 'Первое соборное послание Петра', 'name_en' => 'First Epistle of Peter'),
            array('sequence_number' => 72, 'name_ru' => 'Второе соборное послание Петра', 'name_en' => 'Second Epistle of Peter'),
            array('sequence_number' => 73, 'name_ru' => 'Первое соборное послание Иоанна', 'name_en' => 'First Epistle of John'),
            array('sequence_number' => 74, 'name_ru' => 'Второе соборное послание Иоанна', 'name_en' => 'Second Epistle of John'),
            array('sequence_number' => 75, 'name_ru' => 'Третье соборное послание Иоанна', 'name_en' => 'Third Epistle of John'),
            array('sequence_number' => 76, 'name_ru' => 'Соборное послание Иуды', 'name_en' => 'Epistle of Jude'),
            array('sequence_number' => 77, 'name_ru' => 'Откровение святого Иоанна Богослова', 'name_en' => 'Revelation of John the Theologian'),
        );

        foreach ($bibles as $bible) {
            $query = DB::table('bibles')
                ->where('sequence_number', $bible['sequence_number']);

            if ($query->exists()) {
                $query->update(array(
                    'name_ru' => $bible['name_ru'],
                    'name_en' => $bible['name_en'],
                ));
            } else {
                DB::table('bibles')->insert($bible);
            }
        }
    }
}
