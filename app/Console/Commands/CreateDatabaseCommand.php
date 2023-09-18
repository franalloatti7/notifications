<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:database {dbname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command create database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $dbname = $this->argument('dbname');
   
            $hasDb = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "."'".$dbname."'");
   
            if(empty($hasDb)) {
                DB::select('CREATE DATABASE '. $dbname);
                $this->info("Database '$dbname' created successfully.");
            }
            else {
                $this->info("Database $dbname already exists.");
            }
        }
        catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
