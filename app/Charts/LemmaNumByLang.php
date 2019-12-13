<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

//https://dev.to/arielsalvadordev/use-laravel-charts-in-laravel-5bbm

class LemmaNumByLang extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->options(['scales' =>[
            'xAxes' =>
            [
//                [
                    'scaleLabel' =>
                    [
                        'display' => true,
                        'labelString' => 'xxxxxxx',
                    ],
  //              ]
            ],

            'yAxes' =>
            [
    //            [
                    'scaleLabel' =>
                    [
                        'display' => true,
                        'labelString' => 'yyyyyyyyyyy',
                    ],
      //          ]
            ],
            ]
        ]);
    }
    
    public static function chartSetAxes($xAxes = 'Time(in 24 hrs)', $yAxes = 'No Of Tickets', $showXaxis = true, $showYaxis = true)
    {
        $axesArray = [
            'xAxes' =>
            [
//                [
                    'scaleLabel' =>
                    [
                        'display' => $showXaxis,
                        'labelString' => $xAxes,
                    ],
  //              ]
            ],

            'yAxes' =>
            [
    //            [
                    'scaleLabel' =>
                    [
                        'display' => $showYaxis,
                        'labelString' => $yAxes,
                    ],
      //          ]
            ],


        ];

        return $axesArray;
    }
    
    }
