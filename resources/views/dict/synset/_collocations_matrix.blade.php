@if (!count($collocation_matrix['groups']))
<p>{{ trans('collocations.matrix_empty') }}</p>
@else
<p class="help-block">
    {{ trans('collocations.matrix_hint') }}
</p>

<div class="synset-collocations-matrix-scroll">
    <table class="table table-bordered synset-collocations-matrix">
        <thead>
            <tr>
                <th rowspan="2" scope="col" class="synset-collocation-partner">
                    {{ trans('collocations.partner') }}
                </th>

    @if ($core_members->count())
                <th colspan="{{ $core_members->count() }}" class="text-center">
                    {{ trans('dict.core') }}
                </th>
    @endif

    @if ($periphery_members->count())
                <th colspan="{{ $periphery_members->count() }}" class="text-center">
                    {{ trans('dict.periphery') }}
                </th>
    @endif
            </tr>

            <tr>
    @foreach ($collocation_matrix['columns'] as $member)
        <?php
        $member_url = LaravelLocalization::localizeURL('/dict/lemma/'.$member->lemma_id).'#meaning-'.$member->id;
        ?>
                <th scope="col" class="synset-collocation-column">
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
    @foreach ($collocation_matrix['groups'] as $group)
            <tr class="synset-collocation-category-row">
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

            <tr>
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
                ?>

                <td class="synset-collocation-cell">
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
                    <a href="{{ $editor_url }}" target="_blank" rel="noopener" title="{{ $cell_hint }}"
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

                    <a href="{{ $add_url }}" class="synset-collocation-add" target="_blank" rel="noopener" title="{{ $add_hint }}" aria-label="{{ $add_hint }}">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                    @else
                    <span class="sr-only">{{ trans('collocations.matrix_not_recorded') }}</span>
                    @endif
                @endif
                </td>
            @endforeach
            </tr>
        @endforeach
    @endforeach
        </tbody>
    </table>
</div>
@endif