@foreach ([
    'etymology' => 'dict.synset_etymology',
    'potential_candidates' => 'dict.synset_potential_candidates',
] as $field => $caption)

    <?php
        $field_value = old(
            $field,
            isset($synset) ? $synset->getAttribute($field) : ''
        );

        $field_value = is_scalar($field_value)
            ? (string) $field_value
            : '';
    ?>

    <div class="form-group synset-text-field {{ $errors->has($field) ? 'has-error' : '' }}">
        <label id="{{ $field }}-label"
               for="{{ $field }}-editor">
            {{ trans($caption) }}
        </label>

        <input type="hidden"
               id="{{ $field }}"
               name="{{ $field }}"
               value="{{ $field_value }}">

        <trix-editor id="{{ $field }}-editor"
                     input="{{ $field }}"
                     class="trix-content"
                     aria-labelledby="{{ $field }}-label"
                     data-synset-text-field>
        </trix-editor>

        @if ($field === 'potential_candidates')
            <p class="help-block">
                {{ trans('dict.synset_potential_candidates_hint') }}
            </p>
        @endif

        @if ($errors->has($field))
            <p class="help-block">
                {{ $errors->first($field) }}
            </p>
        @endif
    </div>

@endforeach