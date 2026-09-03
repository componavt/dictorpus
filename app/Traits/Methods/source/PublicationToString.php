<?php

namespace App\Traits\Methods\source;

trait PublicationToString
{
    /**
     * Формирует полную библиографическую ссылку на публикацию,
     * к которой относится текущий source.
     *
     * Для периодики и непериодических публикаций используются
     * разные правила расположения года, частей и страниц.
     *
     * @return string|null
     */
    public function publicationToString()
    {
        if (!$this->publication) {
            return null;
        }

        $publication = $this->publication;

        if ($publication->is_periodic) {
            return $this->periodicPublicationToString(
                $publication
            );
        }

        return $this->nonPeriodicPublicationToString(
            $publication
        );
    }


    /**
     * Формирует ссылку на периодическое издание.
     *
     * Пока периодика использует стандартный сценарий: год,
     * номер выпуска и страницы определяются существующими
     * методами форматирования частей публикации.
     *
     * Метод выделен отдельно, чтобы правила периодики можно
     * было изменить независимо от непериодических публикаций.
     *
     * @param object $publication
     * @return string
     */
    protected function periodicPublicationToString($publication)
    {
        return $this->standardPublicationToString(
            $publication
        );
    }


    /**
     * Формирует ссылку на непериодическую публикацию.
     *
     * Если все выбранные части находятся на одинаковых страницах,
     * части объединяются, затем выводится общий год и одна общая
     * страница.
     *
     * Пример:
     * Uuši Šana. Jevanheli ... 8-9. 2011. С. 107
     *
     * Если страницы различаются, используется обычный формат,
     * где страницы добавляются к каждой части отдельно.
     *
     * @param object $publication
     * @return string
     */
    protected function nonPeriodicPublicationToString($publication)
    {
        if ($this->hasSamePubpartPages()) {
            return $this->publicationToStringWithSamePubpartPages(
                $publication
            );
        }

        return $this->standardPublicationToString(
            $publication
        );
    }


    /**
     * Формирует обычную ссылку на публикацию.
     *
     * Используется:
     * - для периодических изданий;
     * - для публикаций без выбранных частей;
     * - для непериодических публикаций, где разные части
     *   находятся на разных страницах.
     *
     * @param object $publication
     * @return string
     */
    protected function standardPublicationToString($publication)
    {
        $result = $this->publicationInfoToString(
            $publication
        );

        $pubparts = $this->pubpartsToString(
            $publication
        );

        $result = $this->appendSourceInfo(
            $result,
            $pubparts
        );

        if (!$this->hasPubpartPages()) {
            $result = $this->appendSourceInfo(
                $result,
                $this->oldPagesToString()
            );
        }

        return $result;
    }


    /**
     * Формирует ссылку для непериодической публикации,
     * у которой все выбранные части находятся на одной странице
     * или на одном диапазоне страниц.
     *
     * Части выводятся без повторения страниц, год публикации
     * помещается после частей, а общая страница добавляется один раз.
     *
     * Пример:
     * Uuši Šana. Jevanheli ... 8-9. 2011. С. 107
     *
     * @param object $publication
     * @return string
     */
    protected function publicationToStringWithSamePubpartPages(
        $publication
    ) {
        /*
         * Год пока не добавляем: в этом варианте он должен
         * следовать после названий частей.
         */
        $result = $this->publicationInfoToString(
            $publication,
            false
        );

        /*
         * Части пока выводятся без страниц: общая страница
         * будет добавлена один раз в самом конце.
         */
        $pubparts = $this->nonPeriodicPubpartsToString(
            false
        );

        $result = $this->appendSourceInfo(
            $result,
            $pubparts
        );

        if ($publication->year) {
            $result = $this->appendSourceInfo(
                $result,
                $publication->year
            );
        }

        return $this->appendSourceInfo(
            $result,
            $this->samePubpartPagesToString()
        );
    }


    /**
     * Формирует общую информацию о публикации:
     * автора, название и дополнительные сведения.
     *
     * Для непериодических публикаций год обычно добавляется
     * в конце этой части описания. Параметр $with_year нужен
     * для варианта с одинаковыми страницами частей, где год
     * должен быть выведен после pubpart.
     *
     * @param object $publication
     * @param bool $with_year
     * @return string
     */
    protected function publicationInfoToString(
        $publication,
        $with_year = true
    ) {
        $info = [];

        if ($publication->authors) {
            $info[] = $publication->authors;
        }

        if ($publication->title) {
            $info[] = $publication->title;
        }

        if ($publication->addition_info) {
            $info[] = $publication->addition_info;
        }

        /*
         * У периодики год входит в описание выпуска.
         * Для непериодической публикации выводим год здесь,
         * кроме специального сценария с общими страницами.
         */
        if (
            $with_year
            && !$publication->is_periodic
            && $publication->year
        ) {
            $info[] = $publication->year;
        }

        return join('. ', $info);
    }


    /**
     * Выбирает способ форматирования частей публикации.
     *
     * @param object $publication
     * @return string|null
     */
    protected function pubpartsToString($publication)
    {
        if ($publication->is_periodic) {
            return $this->periodicPubpartsToString();
        }

        return $this->nonPeriodicPubpartsToString();
    }


    /**
     * Формирует описание частей непериодической публикации.
     *
     * При $with_pages = true каждая часть выводится вместе
     * со своими страницами.
     *
     * При $with_pages = false страницы не добавляются,
     * а последовательные части с общим названием и номером
     * объединяются в диапазон.
     *
     * @param bool $with_pages
     * @return string
     */
    protected function nonPeriodicPubpartsToString(
        $with_pages = true
    ) {
        $parts = [];

        foreach ($this->pubparts as $pubpart) {
            $part = $this->nonPeriodicPubpartToString(
                $pubpart,
                $with_pages
            );

            if ($part) {
                $parts[] = $part;
            }
        }

        if (!$with_pages) {
            return $this->joinSequentialPubparts(
                $parts
            );
        }

        return join('; ', $parts);
    }


    /**
     * Формирует описание одной части непериодической публикации.
     *
     * Если у части нет заголовка, используется её порядковый номер.
     * Страницы добавляются только при $with_pages = true.
     *
     * @param object $pubpart
     * @param bool $with_pages
     * @return string|null
     */
    protected function nonPeriodicPubpartToString(
        $pubpart,
        $with_pages = true
    ) {
        $result = trim($pubpart->title ?: '');

        if (!$result && $pubpart->sequence_number) {
            $result = '№ ' . $pubpart->sequence_number;
        }

        if (!$result) {
            return null;
        }

        if (!$with_pages) {
            return $result;
        }

        return $this->appendPubpartPages(
            $result,
            $pubpart
        );
    }


    /**
     * Проверяет, что у source выбрана хотя бы одна часть
     * и у всех выбранных частей заполнены одинаковые страницы.
     *
     * Если хотя бы у одной части страницы не указаны,
     * либо страницы различаются, объединять их нельзя.
     *
     * @return bool
     */
    protected function hasSamePubpartPages()
    {
        if (!$this->pubparts || !$this->pubparts->count()) {
            return false;
        }

        $pages_values = [];

        foreach ($this->pubparts as $pubpart) {
            $pages = trim(
                (string) ($pubpart->pivot->pages ?? '')
            );

            if ($pages === '') {
                return false;
            }

            $pages_values[$pages] = true;
        }

        return count($pages_values) === 1;
    }


    /**
     * Возвращает страницы, общие для всех выбранных частей.
     *
     * Метод вызывается только после hasSamePubpartPages(),
     * поэтому берёт pages у первой части.
     *
     * @return string|null
     */
    protected function samePubpartPagesToString()
    {
        if (!$this->hasSamePubpartPages()) {
            return null;
        }

        $pubpart = $this->pubparts->first();

        $pages = trim(
            (string) ($pubpart->pivot->pages ?? '')
        );

        if (!$pages) {
            return null;
        }

        return 'С. ' . $pages;
    }


    /**
     * Объединяет последовательные числовые части с одинаковым
     * текстовым началом в диапазон.
     *
     * Пример:
     * Jevanheli Markin kirjuttamana. Matka Jerusalimih. 8
     * Jevanheli Markin kirjuttamana. Matka Jerusalimih. 9
     *
     * превращается в:
     *
     * Jevanheli Markin kirjuttamana. Matka Jerusalimih. 8-9
     *
     * Непоследовательные номера не объединяются:
     *
     * 8; 10
     *
     * а не:
     *
     * 8-10
     *
     * @param array $parts
     * @return string
     */
    protected function joinSequentialPubparts(array $parts)
    {
        $groups = [];

        foreach ($parts as $part) {
            if (!preg_match(
                '/^(.*?)(\d+)$/u',
                $part,
                $matches
            )) {
                $groups[] = [
                    'text' => $part,
                    'prefix' => null,
                    'from' => null,
                    'to' => null,
                ];

                continue;
            }

            $prefix = trim($matches[1]);
            $number = (int) $matches[2];

            $last_group_index = count($groups) - 1;

            if ($last_group_index >= 0) {
                $last_group = $groups[$last_group_index];

                if (
                    $last_group['prefix'] === $prefix
                    && $last_group['to'] !== null
                    && $number === $last_group['to'] + 1
                ) {
                    $groups[$last_group_index]['to'] = $number;

                    continue;
                }
            }

            $groups[] = [
                'text' => $part,
                'prefix' => $prefix,
                'from' => $number,
                'to' => $number,
            ];
        }

        $result = [];

        foreach ($groups as $group) {
            if ($group['prefix'] === null) {
                $result[] = $group['text'];

                continue;
            }

            if ($group['from'] === $group['to']) {
                $result[] = $group['prefix'] .
                    ' ' .
                    $group['from'];

                continue;
            }

            $result[] = $group['prefix'] .
                ' ' .
                $group['from'] .
                '-' .
                $group['to'];
        }

        return join('; ', $result);
    }

    protected function periodicPubpartsToString()
    {
        $issuesByYear = [];

        foreach ($this->pubparts as $pubpart) {
            $year = $pubpart->year ?: '';
            $issue = $this->periodicPubpartToString($pubpart);

            if (!$issue) {
                continue;
            }

            if (!isset($issuesByYear[$year])) {
                $issuesByYear[$year] = [];
            }

            $issuesByYear[$year][] = $issue;
        }

        $groups = [];

        foreach ($issuesByYear as $year => $issues) {
            $prefix = $year ? $year . '. ' : '';

            $groups[] = $prefix . join('; ', $issues);
        }

        return join('; ', $groups);
    }

    protected function periodicPubpartToString($pubpart)
    {
        $number = trim($pubpart->number ?: '');
        $date = $this->issueDateToString($pubpart->issue_date);

        if ($date && $number) {
            $result = $date . ' (№ ' . $number . ')';
        } elseif ($date) {
            $result = $date;
        } elseif ($number) {
            $result = '№ ' . $number;
        } else {
            return null;
        }

        return $this->appendPubpartPages($result, $pubpart);
    }

    protected function issueDateToString($date)
    {
        if (!$date || $date === '0000-00-00') {
            return null;
        }

        return date('d.m', strtotime($date));
    }

    protected function hasPubpartPages()
    {
        foreach ($this->pubparts as $pubpart) {
            if (trim($pubpart->pivot->pages ?: '')) {
                return true;
            }
        }

        return false;
    }
}
