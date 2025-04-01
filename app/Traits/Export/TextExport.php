<?php namespace App\Traits\Export;

//use PhpOffice\PhpWord\PhpWord;
//use PhpOffice\PhpWord\IOFactory;
//use PhpOffice\PhpWord\SimpleType\JcTable;
//use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
//use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

use App\Models\Corpus\Sentence;

trait TextExport
{
    public function annotatedTextExport($dir, $type=1) {
        $filePath = $dir.'/annotated'.$type.'_'.$this->id.'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $row=1;
        $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
        $trans_sentences = empty($this->transtext) ? [] : $this->transtext->getSentencesFromXML();
        $cyr_sentences = empty($this->cyrtext) ? [] : $this->cyrtext->getSentencesFromXML(true);
        $cyr_words = !empty($this->cyrtext) ? $this->cyrtext->getWordsFromXML(true) : [];
        
        foreach ($sentences as $sentence) {
            $words = $this->words()->whereSId($sentence->s_id)->orderBy('w_id')->get();
            list($sheet, $row) = $this->{'annotatedTextExportSentence'.($type==3 ? '3' : '')}($sheet, $row, trim(strip_tags($sentence->text_xml)),
                    strip_tags($cyr_sentences[$sentence->s_id]['sentence']), 
                    strip_tags($trans_sentences[$sentence->s_id]['sentence']));            
            list($sheet, $row) = $this->{'annotatedTextExportWords'.($type==3 ? '3' : '')}($sheet, $words, $cyr_words, $row, $type);
            $sheet->setCellValue('A'.$row++, "");
            $sheet->setCellValue('A'.$row++, "");
        }


        // Сохранение
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
/***** box/spout ******        
        // Создаем Excel-Writer
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath); // Сохранение в файл

        $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
        $trans_sentences = empty($this->transtext) ? [] : $this->transtext->getSentencesFromXML();
        $cyr_sentences = empty($this->cyrtext) ? [] : $this->cyrtext->getSentencesFromXML(true);
        foreach ($sentences as $sentence) {
            $words = $this->words()->whereSId($sentence->s_id)->orderBy('w_id')->get();
            $words_count = sizeof($words);
            $row = $empty_row = array_fill(0, $words_count, "");
            if (!empty($cyr_sentences[$sentence->s_id])) {
                $row[0] = strip_tags($cyr_sentences[$sentence->s_id]['sentence']);
                $writer->addRow(WriterEntityFactory::createRowFromArray($row));
            }
            if (!empty($trans_sentences[$sentence->s_id])) {
                $row[0] = strip_tags($trans_sentences[$sentence->s_id]['sentence']);
                $writer->addRow(WriterEntityFactory::createRowFromArray($row));
            }
            $row[0] = strip_tags($sentence->text_xml);
            $writer->addRow(WriterEntityFactory::createRowFromArray($row));
            
            $out = [];
            foreach ($words as $word) {
                $out[4][$word->w_id]=$word->word;
            }
//dd($out);            
            $writer->addRow(WriterEntityFactory::createRowFromArray($out[4]));
            $writer->addRow(WriterEntityFactory::createRowFromArray($empty_row));
            $writer->addRow(WriterEntityFactory::createRowFromArray($empty_row));
        }

        $writer->close(); // Закрываем файл
        return $filePath;
        
/**** CSV *****
        $handle = fopen('php://temp', 'r+');
        $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
        $trans_sentences = empty($this->transtext) ? [] : $this->transtext->getSentencesFromXML();
        $cyr_sentences = empty($this->cyrtext) ? [] : $this->cyrtext->getSentencesFromXML(true);
        foreach ($sentences as $sentence) {
            $words = $this->words()->whereSId($sentence->s_id)->orderBy('w_id')->get();
            $words_count = sizeof($words);
            $row = array_fill(0, $words_count, "\u{00A0}");
            if (!empty($cyr_sentences[$sentence->s_id])) {
                $row[0] = strip_tags($cyr_sentences[$sentence->s_id]['sentence']);
                fputcsv($handle, $row, "\t");                     
            }
            if (!empty($trans_sentences[$sentence->s_id])) {
                $row[0] = strip_tags($trans_sentences[$sentence->s_id]['sentence']);
                fputcsv($handle, $row, "\t");                     
            }
            $row[0] = strip_tags($sentence->text_xml);
            fputcsv($handle, $row, "\t");                     
            
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
        
        return $csvContent;*/
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
    
    public function annotatedTextExportSentence($sheet, $row, $sentence, $cyr_sentence, $trans_sentence) {
        if (!empty($cyr_sentence)) {
            $sheet->setCellValue('A'.$row++, $cyr_sentence);
        }
        if (!empty($trans_sentence)) {
            $sheet->setCellValue('A'.$row++, $trans_sentence);
        }
        $sheet->setCellValue('A'.$row++, $sentence);
        return [$sheet, $row];
    }
    
    public function annotatedTextExportSentence3($sheet, $row, $sentence, $cyr_sentence, $trans_sentence) {
        $sheet = $this->sheetCellSet($sheet, 'A'.$row, !empty($cyr_sentence) ? $cyr_sentence : '');
        $sheet = $this->sheetCellSet($sheet, 'C'.$row, !empty($trans_sentence) ? $trans_sentence : '');
        $sheet = $this->sheetCellSet($sheet, 'B'.$row, $sentence);
        $row++;
        return [$sheet, $row];
    }
    
    public function annotatedTextExportWords($sheet, $words, $cyr_words, $row, $type=1) {
        $out = [];
        $i = 1;
        foreach ($words as $word) {
            $letter = Coordinate::stringFromColumnIndex($i);   
            $gramset = $word->checkedGramset();
            $meaning = $word->checkedMeaning();
            $mg = [!empty($meaning) ? $meaning->textByLangCode('ru') : ''];
            if (!empty($gramset)) {
                $mg[1] = $gramset->gramsetString();
            }
            if ($type == 1) {
                $out[1][$letter]=$mg[0];
                $out[2][$letter]=!empty($mg[1]) ? $mg[1] : '';                    
            } else {
                $out[1][$letter]= join("-", $mg);
            }
            $out[3][$letter]= empty($cyr_words[$word->w_id]) ? '' : $cyr_words[$word->w_id];
            $out[4][$letter]=$word->word;
            $i++;
        }
//        $sheet->getRowDimension(1)->setRowHeight(30);
        foreach ($out as $r) {
//                $sheet->getRowDimension($row)->setRowHeight(-1); // автоматическая высота строки                    
            foreach ($r as $letter => $c) {
                $sheet = $this->sheetCellSet($sheet, $letter.$row, $c);
            } 
            $row++;
        }
        return [$sheet, $row];
    }
    
    public function annotatedTextExportWords3($sheet, $words, $cyr_words, $row) {
        $out = [];
        $i = 1;
        foreach ($words as $word) {
            $gramset = $word->checkedGramset();
            $meaning = $word->checkedMeaning();
            $sheet = $this->sheetCellSet($sheet, 'A'.$row, empty($cyr_words[$word->w_id]) ? '' : $cyr_words[$word->w_id]);
            $sheet = $this->sheetCellSet($sheet, 'B'.$row, $word->word);
            $c = !empty($meaning) ? $meaning->textByLangCode('ru') : ''; 
            if (!empty($gramset)) {
                $c .= '-'.$gramset->toLGR();
            }
            $sheet = $this->sheetCellSet($sheet, 'C'.$row, $c);
            $row++;
        }
        return [$sheet, $row];
    }
    
    public function sheetCellSet($sheet, $cell, $contant) {
        $sheet->setCellValue($cell, empty($contant) ? "\u{2002}" : $contant);

        $sheet->getStyle($cell)->getAlignment()->setWrapText(true); // перенос по словам                    
        $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_TOP); // вертикальное выравнивание по верху
        $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // рамка //BORDER_MEDIUM
//                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1); 1 cлишком много, дробные не поддерживаются
        return $sheet;
    }
    
}