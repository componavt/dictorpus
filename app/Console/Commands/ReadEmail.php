<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\EmailMessage;
use PhpMimeMailParser\Parser;

class ReadEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projectname:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // parse content
        $parser = new Parser();
        $parser->setStream(fopen('php://stdin', 'r'));

        // create object
        $email = new EmailMessage();
        $email->from = $parser->getHeader('from');
        $email->to = $parser->getHeader('to');
        $email->subject = $parser->getHeader('subject');
        $email->body_text = $parser->getMessageBody('text');
        $email->body_html = $parser->getMessageBody('html');
        $email->headers = serialize($parser->getHeaders());
        $email->save();
    }
}
