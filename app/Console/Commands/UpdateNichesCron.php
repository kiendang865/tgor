<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\BookingLineItems;
use App\Niche;
use App\Reference;
use Carbon\Carbon;

class UpdateNichesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateniches:cron';

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
        $now = now();
        $niche1 = Niche::whereNotNull('booking_id')->with('booking_item')
        ->whereHas('booking_item', function($query) use($now) {
            $query->whereDate('lease_expiry_date', '<',$now->toDateTimeString());
            $query->with('booking_type');
            $query->whereHas('booking_type', function($q){
                $q->where('reference_value_text', 'Niches');
            });
        })->pluck('id')->toArray();
        $niche2 = Niche::whereNotNull('booking_id')
        ->with(['booking_item' => function($query) use($now){
            $query->whereDate('lease_start_date', '<',$now->toDateTimeString());
            $query->whereDate('lease_expiry_date', '>',$now->toDateTimeString());
            $query->with('booking_type');
            $query->whereHas('booking_type', function($q){
                $q->where('reference_value_text', 'Niches');
            });
        }])
        ->whereHas('booking_item', function($query) use($now) {
            $query->whereDate('lease_start_date', '<',$now->toDateTimeString());
            $query->whereDate('lease_expiry_date', '>',$now->toDateTimeString());
            $query->with('booking_type');
            $query->whereHas('booking_type', function($q){
                $q->where('reference_value_text', 'Niches');
            });
        })->get()->pluck('booking_item.booking_id', 'id')->toArray();
        $arr_exists = array_diff($niche1, array_keys($niche2));
        if(count($arr_exists)){
            Niche::whereIn('id', $arr_exists)->update(['booking_line_item' => null, 'booking_id' => null]);
        }
        foreach ($niche2 as $id_niche => $id_booking_line) {
            $booking_line_item = BookingLineItems::find($id_booking_line);
            Niche::where('id' , $id_niche)
            ->update([
                'booking_id' => $booking_line_item->booking_id,
                'booking_line_item' => $id_booking_line
            ]);
        }
        $this->info('UpdateNiches:Cron Cummand Run successfully!');
    }
}
