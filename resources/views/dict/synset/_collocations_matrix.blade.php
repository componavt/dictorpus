@if (!count($collocation_matrix['groups']))
<p>{{ trans('collocations.matrix_empty') }}</p>
@else
<p class="help-block">
    {{ trans('collocations.matrix_hint') }}
</p>

<fieldset class="synset-collocations-members">
    <legend>{{ trans('collocations.members_title') }}</legend>

    <div class="synset-collocations-member-options">
        @foreach ($collocation_matrix['columns'] as $member)
            <label for="compare-member-{{ $member->id }}">
                <input type="checkbox" id="compare-member-{{ $member->id }}" class="synset-collocation-member-checkbox" value="{{ $member->id }}" checked>
                {{ $member->lemma->lemma }}<sup>{{ $member->meaning_n }}</sup>
            </label>
        @endforeach
    </div>

    <div>
        <button type="button" class="btn btn-link btn-sm" id="synset-collocations-select-all">
            {{ trans('collocations.members_all') }}
        </button>

        <button type="button" class="btn btn-link btn-sm" id="synset-collocations-clear-selection">
            {{ trans('collocations.members_none') }}
        </button>
    </div>

    <p class="help-block">
        {{ trans('collocations.members_hint') }}
    </p>
</fieldset>

<div class="synset-collocations-filters">
    <label for="synset-collocations-only-differences">
        <input type="checkbox" id="synset-collocations-only-differences" aria-describedby="synset-collocations-differences-hint"
            {{ $collocation_matrix['columns']->count() < 2 ? 'disabled' : '' }}>
        {{ trans('collocations.differences_only') }}
    </label>

    <p id="synset-collocations-differences-hint" class="help-block">
        {{ trans('collocations.differences_hint') }}
    </p>
</div>

<p id="synset-collocations-no-differences" class="help-block" role="status" hidden>
    {{ trans('collocations.differences_none') }}
</p>

<p id="synset-collocations-no-members" class="help-block" role="status" hidden>
    {{ trans('collocations.members_empty') }}
</p>

<p id="synset-collocations-no-records" class="help-block" role="status" hidden>
    {{ trans('collocations.members_no_records') }}
</p>

<div class="synset-collocations-matrix-scroll">
<?php
$core_member_ids = [];

foreach ($core_members as $core_member) {
    $core_member_ids[$core_member->id] = true;
}
?>
    <table class="table table-bordered synset-collocations-matrix">
        <thead>
            <tr>
                <th rowspan="2" scope="col" class="synset-collocation-partner">
                    {{ trans('collocations.partner') }}
                </th>

    @if ($core_members->count())
                <th colspan="{{ $core_members->count() }}" class="text-center" data-member-section="core">
                    {{ trans('dict.core') }}
                </th>
    @endif

    @if ($periphery_members->count())
                <th colspan="{{ $periphery_members->count() }}" class="text-center" data-member-section="periphery">
                    {{ trans('dict.periphery') }}
                </th>
    @endif
            </tr>

            <tr>
    @foreach ($collocation_matrix['columns'] as $member)
        <?php
        $member_url = LaravelLocalization::localizeURL('/dict/lemma/'.$member->lemma_id).'#meaning-'.$member->id;
        ?>
                <th scope="col" class="synset-collocation-column" data-member-id="{{ $member->id }}" data-member-group="{{ isset($core_member_ids[$member->id]) ? 'core' : 'periphery' }}">
                    <span class="synset-collocation-name">
                        <a href="{{ $member_url }}" target="_blank" rel="noopener"
                            title="{{ trans('collocations.meaning', ['num' => $member->meaning_n,
                            ]) }}">{{ $member->lemma->lemma }}</a><sup>{{ $member->meaning_n }}</sup>
                    </span>

        @if ((int) $synset->dominant_id === (int) $member->id)
                        <small class="synset-collocation-dominant">{{ trans('dict.dominant') }}</small>
        @endif
                </th>
    @endforeach
            </tr>
        </thead>

        <tbody>
    @foreach ($collocation_matrix['groups'] as $group_index => $group)
            <tr class="synset-collocation-category-row" data-category-key="{{ $group_index }}">
                <th colspan="{{ $collocation_matrix['columns']->count() + 1 }}">
                    <em>{{ $group['category'] ? $group['category']->name
                        : trans('collocations.without_category') }}</em>
                </th>
            </tr>

        @foreach ($group['rows'] as $row)
            <?php
            $partner_meaning = $row['meaning'];
            $partner_url = LaravelLocalization::localizeURL('/dict/lemma/'.$partner_meaning->lemma_id).'#meaning-'.$partner_meaning->id;
            ?>

            <tr class="synset-collocation-data-row" data-category-key="{{ $group_index }}">
                <th scope="row"
                    class="synset-collocation-partner">
                    <span class="synset-collocation-name">
                        <a href="{{ $partner_url }}" target="_blank" rel="noopener"
                            title="{{ trans('collocations.meaning', ['num' => $partner_meaning->meaning_n,
                            ]) }}">{{ $partner_meaning->lemma->lemma }}</a><sup>{{ $partner_meaning->meaning_n }}</sup>
                    </span>
                </th>

            @foreach ($collocation_matrix['columns'] as $member)
                <?php
                $collocation = $row['cells'][$member->id] ?? null;

                $has_examples = !empty($collocation_matrix['example_pairs']
                        [$member->id]
                        [$partner_meaning->id]
                );

                $examples_url = LaravelLocalization::localizeURL(
                    '/dict/meaning/'.$member->id.'/examples/search'
                ).'?'.http_build_query([
                    'distance_from' => -1,
                    'distance_to' => 1,
                    'only_checked' => 0,
                    'partner_meaning_id' => $partner_meaning->id,
                    'same_gramset' => 0,
                    'synset_id' => $synset->id,
                    'whole_sentence' => 1,
                ]);

                $examples_hint = trans('collocations.matrix_examples_hint', [
                    'source' => $member->lemma->lemma .' ('.$member->meaning_n.')',
                    'partner' => $partner_meaning->lemma->lemma .' ('.$partner_meaning->meaning_n.')',
                ]);
                ?>

                <td class="synset-collocation-cell" data-member-id="{{ $member->id }}" data-recorded="{{ $collocation ? '1' : '0' }}">
                @if ($collocation)
                    <?php
                    $cell_hint = trans('collocations.matrix_cell_hint',
                        ['source' => $member->lemma->lemma .' ('.$member->meaning_n.')',
                        'partner' => $partner_meaning->lemma->lemma .' ('.$partner_meaning->meaning_n.')',
                        'gloss' => $collocation->display_gloss !== '' ? $collocation->display_gloss : trans('collocations.matrix_recorded')]
                    );

                    $editor_url = LaravelLocalization::localizeURL('/dict/meaning/'.$member->id.'/collocations')
                            .'?'.http_build_query(['edit' => $collocation->id, 'synset_id' => $synset->id]);
                    ?>

                    @if (User::checkAccess('dict.edit'))
                    <a href="{{ $editor_url }}" title="{{ $cell_hint }}"
                        aria-label="{{ trans('collocations.edit').': '.$cell_hint }}">
                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                    </a>
                    @else
                    <span title="{{ $cell_hint }}">
                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                        <span class="sr-only">
                            {{ trans('collocations.matrix_recorded') }}
                        </span>
                    </span>
                    @endif
                @else
                    @if (User::checkAccess('dict.edit'))
                        <?php
                        $add_url = LaravelLocalization::localizeURL('/dict/meaning/'.$member->id.'/collocations').'?'.http_build_query([
                            'category_id' => $group['category'] ? $group['category']->id : '',
                            'partner_id' => $partner_meaning->id,
                            'synset_id' => $synset->id,
                        ]);

                        $add_hint = trans('collocations.matrix_add_hint', [
                            'source' => $member->lemma->lemma .' ('.$member->meaning_n.')',
                            'partner' => $partner_meaning->lemma->lemma .' ('.$partner_meaning->meaning_n.')',
                        ]);
                        ?>

                    <a href="{{ $add_url }}" class="synset-collocation-add" title="{{ $add_hint }}" aria-label="{{ $add_hint }}">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                    @else
                    <span class="sr-only">{{ trans('collocations.matrix_not_recorded') }}</span>
                    @endif
                @endif
                @if ($has_examples)
                <a  href="{{ $examples_url }}" class="synset-collocation-examples"
                    title="{{ $examples_hint }}" aria-label="{{ $examples_hint }}">
                        {{ trans('collocations.matrix_examples') }}
                    </a>
                </td>
                @endif
            @endforeach
            </tr>
        @endforeach
    @endforeach
        </tbody>
    </table>
</div>
@endif