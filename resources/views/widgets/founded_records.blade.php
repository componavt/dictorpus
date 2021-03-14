        <p><?php
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
             switch($numAll%20):
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
           endif; ?>
        </p>
