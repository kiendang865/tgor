<?php

namespace App\Exports;

use App\Niche;
use App\BookingLineItems;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportNiche implements FromView
{
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function view(): View
    {
        $niches = Niche::whereIn('id', $this->ids)
        ->with(['type', 'category'])
        ->with(['booking_line_item' => function($query){
            $query->select('id', 'duration_of_lease');
            $query->with(['information' => function($q){
                $q->select('id', 'booking_line_items_id', 'full_name');
            }]);
        }])->get();
        return view('exports.niches', [
            'niches' => $niches->toArray()
        ]);
    }
}
