<?php
$note_key = 'member_notes.'.$member->id;
$note_id = 'member-collocation-note-'.$member->id;
$note_value = old($note_key, $member->pivot->collocation_note);
$note_value = is_scalar($note_value) ? (string)$note_value : '';
?>

<div class="form-group synset-text-field {{ $errors->has($note_key) ? 'has-error' : '' }}">
    <label id="{{ $note_id }}-label" for="{{ $note_id }}-editor">
        {{ trans('collocations.member_note') }}
    </label>

    @if (User::checkAccess('dict.edit'))
        <input type="hidden"
               id="{{ $note_id }}"
               name="member_notes[{{ $member->id }}]"
               form="synset-edit-form"
               value="{{ $note_value }}">

        <trix-editor id="{{ $note_id }}-editor"
                     input="{{ $note_id }}"
                     class="trix-content"
                     aria-labelledby="{{ $note_id }}-label"
                     aria-describedby="{{ $note_id }}-hint"
                     data-synset-text-field>
        </trix-editor>

        <p id="{{ $note_id }}-hint" class="help-block">
            {{ trans('collocations.member_note_hint') }}
        </p>

        @if ($errors->has($note_key))
            <p class="help-block">{{ $errors->first($note_key) }}</p>
        @endif
    @else
        <div class="trix-content">
            {!! \App\Library\SynsetTextFields::clean($note_value) !!}
        </div>
    @endif
</div>