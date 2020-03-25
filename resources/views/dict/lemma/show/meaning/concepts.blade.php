            @if (sizeof($meaning->concepts))
                <b>{{trans('dict.concept')}}:</b> 
                
                <?php $tmp = []; 
                foreach ($meaning->concepts as $concept):
                    $tmp[] = '<a href="'.LaravelLocalization::localizeURL('/dict/lemma').'?search_concept='.$concept->id.'">'. 
                             $concept->text. "</a>";
                endforeach;
                ?>
            
                {!!join('; ', $tmp)!!}
            @endif
