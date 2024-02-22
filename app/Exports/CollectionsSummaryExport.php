<?php

namespace App\Exports;

use App\Payment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class CollectionsSummaryExport implements FromView
{
    public function __construct(String $start, String $end)
    {
        $this->start = $start;
        $this->end = $end;
    }
    public function view(): View
    {
        $start_time = Carbon::parse($this->start);
        $end_time = Carbon::parse($this->end);
        $collections = Payment::whereDate('created_at', '>=', $start_time)
        ->whereDate('created_at', '<=', $end_time)
        ->with('payment_line_item', 'payment_mode')
        ->with(['client' => function($query){
            $query->with('salutation');
        }])
        ->with(['invoice' => function($query){
            $query->with(['invoice_line_item' => function($q){
                $q->with(['sale_agreement_line_item' => function($_q){
                    $_q->with(['booking_line_item' => function($_q1){
                        $_q1->with('niche.category', 'other', 'room', 'information', 'serviceType', 'booking_type');
                    }]);
                }]);
            }]);
        }])
        ->get();
        //var_dump($collections->toArray());exit;
        return view('exports.collections-summary', compact('collections'));
    }
}
