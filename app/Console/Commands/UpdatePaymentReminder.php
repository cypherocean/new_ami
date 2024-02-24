<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentReminder;
use Carbon\Carbon;

class UpdatePaymentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'records:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update records every day';

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
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now();
        $tomorrow = $today->addDay();
        $tomorrowFormatted = $tomorrow->format('Y-m-d');
        PaymentReminder::where('date', '<', Carbon::today()->format('Y-m-d'))->update(['date' => $tomorrowFormatted]);
    }
}
