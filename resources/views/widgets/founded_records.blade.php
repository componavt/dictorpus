        <p>
            {{trans_choice('messages.founded_count', $numAll>20 ? ($numAll%10==0 ? $numAll : $numAll%10)  : $numAll, ['count'=>number_format($numAll, 0, ',', ' ')])}}

            <?php       /*
           if (!$numAll):
                print trans('messages.not_founded_records');
           elseif (LaravelLocalization::getCurrentLocale() == 'en'): 
             switch($numAll):
                case 1:
                    print trans('messages.founded_1record', ['count'=>$numAll]);
                    break;
                default:
                    print trans('messages.founded_records', ['count'=>$numAll]);
             endswitch;
           else:
             switch($numAll>20 ? $numAll%10 : $numAll):
                case 1:
                    print trans('messages.founded_1record', ['count'=>$numAll]);
                    break;
                case 2:
                case 3:
                case 4:
                    print trans('messages.founded_2record', ['count'=>$numAll]);
                    break;
                default:
                    print trans('messages.founded_records', ['count'=>$numAll]);
             endswitch;
           endif; */ ?>
        </p>
