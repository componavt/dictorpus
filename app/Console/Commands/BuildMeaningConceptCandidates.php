<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dict\MeaningText;

class BuildMeaningConceptCandidates extends Command
{
    protected $name = 'vepkar:build-meaning-candidates';

    protected $description = 'Build rows in concept_meaning_candidates from (pos, primary_gloss_ru) groups.';

    public function fire()
    {
        $onlyPos = $this->option('pos');

        if ($onlyPos) {
            $this->info('Building concept meaning candidates (POS=' . $onlyPos . ')...');
        } else {
            $this->info('Building concept meaning candidates (all POS)...');
        }

        $stats = MeaningText::rebuildConceptMeaningCandidates($onlyPos ?: null);

        $this->info('--- Concept meaning candidates rebuild summary ---');
        $this->info('Cleared old "new" rows:      ' . $stats['cleared_new']);
        $this->info('Groups total:                ' . $stats['groups_total']);
        $this->info('Groups with targets:         ' . $stats['groups_with_targets']);
        $this->info('Rows prepared (theoretical): ' . $stats['rows_prepared']);
        $this->info('Rows inserted (uniq by key): ' . $stats['rows_inserted']);
        $this->info('------------------------------------------------');

        return 0;
    }

    protected function getOptions()
    {
        return [
            ['pos', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Only one taskpos.', null],
        ];
    }
}
