<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class DistributionChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
/*        $this->options(['scales'=> [
            'xAxes' => ['ticks' => ['max' => 2030]]]
//                    'xAxes'=> ['stacked'=> true],
//                    'yAxes'=> ['stacked'=> true]]
        ]);*/
    }
    
    public function colors()
    {
        return ['663399', '00BFFF', 'FF9900', '66CDAA'];
    }
}
