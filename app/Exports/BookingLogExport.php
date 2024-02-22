<?php

namespace App\Exports;

use App\BookingLineItems;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BookingLogExport implements FromView
{
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function view(): View
    {
        $booking_line_items = BookingLineItems::whereIn('id', $this->ids)->with('client', 'funeral_director', 'status')->get();
        // var_dump($booking_line_items->toArray());exit;
        return view('exports.booking-log', [
            'booking_line_items' => $booking_line_items
        ]);
    }
}
