<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMimeMailParser\Parser;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Handler;

class EmailParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses an incoming email';

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
    
	
	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		
		$parser = new Parser(); //Declare parser
		$parser->setStream(fopen('php://stdin', 'r')); //Open read stream email
		
		$from = $parser->getHeader('from'); //Get FROM 
		$to = $parser->getHeader('to'); //Get TO
		$subject = $parser->getHeader('subject'); //Get Subject
		
		$text = $parser->getMessageBody('text'); //Get message in plain text if available 
		$html = $parser->getMessageBody('html'); //Get message in plain html if available 
		
		DB::insert('insert into parsed_emails (mail_to, mail_from, subject, text, html) values (?, ?, ?, ?, ?)', [$to, $from, $subject, $text, $html]); //Put it in the database
		
		
	}
}
