        <div class="corpus-text" style='display:flex'>
        @if ($text->cyrtext)
            <div id="cyrtext-c">
            @include('corpus.text.show.cyrtext')
            </div>
        
            <span id='hide-cyrtext' class="button-close" title="убрать колонку">Х</span>
            <span id='show-cyrtext' class="button-close" style="display:none" title="вернуть колонку">&gt;&gt;</span>            
            <div style="margin-right:20px"></div>
        @endif      
            <div>
            @include('corpus.text.show.text')
            </div>
        @if ($text->transtext)
            <div style='margin-left:20px'></div>
            
            <div id="transtext-c">
            @include('corpus.text.show.transtext')
            </div>
            
            <span id='hide-transtext' class="button-close" title="убрать колонку">Х</span>
            <span id='show-transtext' class="button-close" style="display:none" title="вернуть колонку">&lt;&lt;</span>
        @endif      
        </div>
