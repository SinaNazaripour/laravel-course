<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Artisan;

class sendMail extends Command implements Isolatable #PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'say-hello {name} {--option} {--option2=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        var_dump("hello {$this->argument('name')}");
        if ($this->option("option")) {
            # code...
            var_dump("hello {$this->option('option2')}");
        }

        $name = $this->ask('what is your name');
        $password = $this->secret('what is your password');
        $choice = $this->choice("what is your ", ["a", "b"], 1);
        // if ($this->confirm("are you sure?")) {
        //     return "done";
        // }


        $this->table(['column1', "column2"], [["column1" => 1, "column2" => 2], ["column1" => 3, "column2" => 4]]);

        // $this->info('ended without error');
        $this->error('error');
    }
}
