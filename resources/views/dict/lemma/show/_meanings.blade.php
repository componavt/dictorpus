@foreach ($lemma->meanings as $meaning)
    <?php
    $short_labels = $meaning->showShortLabels();
    $has_photo = $meaning->hasPhoto();
    $dialect_list = $meaning->dialectListToString();
    ?>

    <div class="lemma-meaning lemma-meaning-layout" id="meaning-{{ $meaning->id }}">
        <div class="lemma-meaning-left">
            <h3 class="meaning-heading">
                {{ $meaning->meaning_n }} {{ trans('dict.meaning') }}

                @if (count($short_labels))
                    ({{ join(', ', $short_labels) }})
                @endif
            </h3>

            <div class="meaning-definitions">
                @include('dict.lemma.show.meaning.texts')
            </div>

            <div class="meaning-concept-block">
                @include('dict.lemma.show.meaning.concepts')

                @if ($has_photo)
                    <div class="meaning-photo" id="meaning-photo_{{ $meaning->id }}">
                        <img class="img-loading" id="img-photo-loading_{{ $meaning->id }}"
                            src="{{ asset('images/loading.gif') }}" alt="">
                    </div>
                @endif
            </div>

            @if (isset($meaning_relations[$meaning->id]))
                <ul class="meaning-relations">
                    @include('dict.lemma.show.meaning.relations', ['meaning_relations' => $meaning_relations[$meaning->id]])
                </ul>
            @endif

            @include('dict.lemma.show.meaning.translations')

            @if ($dialect_list)
                <p class="meaning-dialects">
                    <b>{{ trans('dict.dialects_usage') }}:</b>
                    {{ $dialect_list }}
                </p>
            @endif

            @include('dict.lemma.show.meaning.collocations')
        </div>

        <div class="lemma-meaning-examples">
            <img class="img-loading" id="img-loading_{{ $meaning->id }}" src="{{ asset('images/loading.gif') }}" style="display: none;" alt="">

            <div id="meaning-examples_{{ $meaning->id }}">
                @include('dict.lemma.example.all', $example_previews[$meaning->id])
            </div>
        </div>
    </div>
@endforeach