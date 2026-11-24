<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitializeSynsetPositions extends Command
{
    protected $signature = 'synsets:initialize-positions {--apply : Save positions}';
    protected $description = 'Initialize existing synset member positions';

    public function handle()
    {
        $apply = (bool)$this->option('apply');
        $synsets = DB::table('synsets')->orderBy('id')->get();
        $processed = 0;
        $skipped = 0;

        foreach ($synsets as $synset) {
            $result = DB::transaction(function () use ($synset, $apply) {
                $rows = DB::table('meaning_synset as ms')
                    ->join('meanings as m', 'm.id', '=', 'ms.meaning_id')
                    ->join('lemmas as l', 'l.id', '=', 'm.lemma_id')
                    ->where('ms.synset_id', $synset->id)
                    ->select('ms.*', 'l.lemma', 'm.meaning_n')
                    ->orderBy('l.lemma')
                    ->orderBy('m.meaning_n')
                    ->orderBy('ms.meaning_id')
                    ->lockForUpdate()
                    ->get();

                if (!count($rows)) {
                    return 'empty';
                }

                foreach ($rows as $row) {
                    if ($row->position !== null) {
                        return 'skip';
                    }
                }

                $core_ids = [];
                $members = [];
                $alphabetical_positions = [];

                foreach ($rows as $index => $row) {
                    $members[] = $row;
                    $alphabetical_positions[$row->meaning_id] = $index;

                    if ((int)$row->syntype_id === 1) {
                        $core_ids[] = (int)$row->meaning_id;
                    }
                }

                $frequencies = [];

                if ($core_ids) {
                    $frequency_rows = DB::table('meaning_text')
                        ->select('meaning_id', DB::raw('COUNT(*) as frequency'))
                        ->whereIn('meaning_id', $core_ids)
                        ->where('relevance', '>', 0)
                        ->groupBy('meaning_id')
                        ->get();

                    foreach ($frequency_rows as $row) {
                        $frequencies[$row->meaning_id] = (int)$row->frequency;
                    }
                }

                $dominant_id = (int)$synset->dominant_id;

                usort($members, function ($left, $right) use ($dominant_id, $frequencies, $alphabetical_positions) {
                    $left_dominant = (int)$left->meaning_id === $dominant_id;
                    $right_dominant = (int)$right->meaning_id === $dominant_id;

                    if ($left_dominant !== $right_dominant) {
                        return $left_dominant ? -1 : 1;
                    }

                    $left_core = (int)$left->syntype_id === 1;
                    $right_core = (int)$right->syntype_id === 1;

                    if ($left_core !== $right_core) {
                        return $left_core ? -1 : 1;
                    }

                    if ($left_core) {
                        $comparison = ($frequencies[$right->meaning_id] ?? 0)
                            <=> ($frequencies[$left->meaning_id] ?? 0);

                        if ($comparison !== 0) {
                            return $comparison;
                        }
                    }

                    return $alphabetical_positions[$left->meaning_id]
                        <=> $alphabetical_positions[$right->meaning_id];
                });

                $labels = [];

                foreach ($members as $index => $member) {
                    $position = $index + 1;

                    if ($apply) {
                        DB::table('meaning_synset')
                            ->where('synset_id', $synset->id)
                            ->where('meaning_id', $member->meaning_id)
                            ->update(['position' => $position]);
                    }

                    $labels[] = $position . ': ' . $member->lemma
                        . '[' . $member->meaning_n . ']'
                        . ((int)$member->meaning_id === $dominant_id ? ' *' : '');
                }

                $this->line('#' . $synset->id . ' — ' . implode('; ', $labels));

                return 'processed';
            });

            if ($result === 'processed') {
                $processed++;
            } elseif ($result === 'skip') {
                $skipped++;
                $this->line('#' . $synset->id . ' — skipped: positions already assigned');
            }
        }

        $this->info(($apply ? 'Saved' : 'Preview') . ': ' . $processed . '; skipped: ' . $skipped);

        return 0;
    }
}
