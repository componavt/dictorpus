<?php namespace App\Traits\Export;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\JcTable;

use App\Models\Corpus\Sentence;

trait TextExport
{
    public function annotatedText1Export(/*$dir*/) {
        $handle = fopen('php://temp', 'r+');
        $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
        $trans_sentences = empty($this->transtext) ? [] : $this->transtext->getSentencesFromXML();
        $cyr_sentences = empty($this->cyrtext) ? [] : $this->cyrtext->getSentencesFromXML(true);
        foreach ($sentences as $sentence) {
            $words = $this->words()->whereSId($sentence->s_id)->orderBy('w_id')->get();
            $words_count = sizeof($words);
            if (!empty($cyr_sentences[$sentence->s_id])) {
                fputcsv($handle, [strip_tags($cyr_sentences[$sentence->s_id]['sentence'])]);                     
            }
            if (!empty($trans_sentences[$sentence->s_id])) {
                fputcsv($handle, [strip_tags($trans_sentences[$sentence->s_id]['sentence'])]);                     
            }
            fputcsv($handle, [strip_tags($sentence->text_xml)]);     
            
            $out = [];
            foreach ($words as $word) {
                $out[4][$word->w_id]=$word->word;
            }
//dd($out);            
            fputcsv($handle, $out[4], "\t");     
            fwrite($handle, "\n\n");     
        }
        // Перемещаем указатель в начало файла
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return $csvContent;
/*        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman'); // Название шрифта
        $phpWord->setDefaultFontSize(12); // Размер шрифта
        $tableStyle = [
            'alignment' => JcTable::CENTER, // Центрируем таблицу
            'autofit'    => true,
            'borderSize' => 6,       // Толщина границы (в twip, 1 pt = 20 twip, 6 = 0.3 pt)
            'borderColor' => '000000', // Цвет границы (черный)
            'cellMargin' => 50       // Отступ внутри ячеек
        ];
        $section = $phpWord->addSection();
        
        $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
        $trans_sentences = empty($this->transtext) ? [] : $this->transtext->getSentencesFromXML();
        $cyr_sentences = empty($this->cyrtext) ? [] : $this->cyrtext->getSentencesFromXML(true);
        foreach ($sentences as $sentence) {
            $words = $this->words()->whereSId($sentence->s_id)->orderBy('w_id')->get();
            $words_count = sizeof($words);
            $table = $section->addTable($tableStyle);        
            if (!empty($cyr_sentences[$sentence->s_id])) {
                $table->addRow();
                $table->addCell(null, ['gridSpan' => $words_count])->addText(strip_tags($cyr_sentences[$sentence->s_id]['sentence']));                     
            }
            if (!empty($trans_sentences[$sentence->s_id])) {
                $table->addRow();
                $table->addCell(null, ['gridSpan' => $words_count])->addText(strip_tags($trans_sentences[$sentence->s_id]['sentence']));                     
            }
            $table->addRow();
            $table->addCell(null, ['gridSpan' => $words_count])->addText(strip_tags($sentence->text_xml));     
            $section->addTextBreak();
            
            $table->addRow();
            foreach ($words as $word) {
                $table->addCell()->addText($word->word);
            }
        }
        // Создаем временный файл
        $filePath = $dir.'/annotated1_'.$this->id.'.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);
*/        
    }
}