<?php namespace App\Traits\Export;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

trait TextExport
{
    public function annotatedText1Export($dir) {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText($this->text);

        // Создаем временный файл
        $filePath = $dir.'/annotated1_'.$this->id.'.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);
        
        return $filePath;
    }
}