        @include('experiments.bible_language.for_all_row', ['title' => $title1, 'totals' => $stats[$var.'_total']])        
        @include('experiments.bible_language.for_all_row', ['title' => $title2." к общему количеству слов", 'totals' => $stats[$var.'_to_all'], 'sign' => '%'])
        @include('experiments.bible_language.for_all_row', ['title' => $title2." среди размеченных слов", 'totals' => $stats[$var.'_to_linked'], 'sign' => '%'])
