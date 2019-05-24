<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Command
{
    protected $signature = 'emails:send';

    protected $description = 'Sending emails to the users.';

    public function __construct()
    {
        parent::__construct();
    }

            public function handle()
            {
                $data = array(
                    'name' => "xxx",
               );
        
                Mail::send('emails.test', $data, function ($message) {
        
                    $message->from('ahmedaboemam123@gmail.com@gmail.com');
        
                    $message->to('ahmedaboemam123@gmail.com')->subject('xxx');
        
                });
                
                $this->info('The emails are send successfully!');
            }
}
 