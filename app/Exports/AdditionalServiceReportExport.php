<?php

namespace App\Exports;

use App\Payment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class AdditionalServiceReportExport implements FromView
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
        ->whereHas('invoice.invoice_line_item.sale_agreement_line_item.booking_line_item.booking_type', function($query){
            $query->where(
                [
                    ['reference_type', '=', 'booking_type'],
                    ['reference_value_text', 'Additional Services'],
                ]
            );
        })
        ->with('payment_line_item', 'client', 'payment_mode')
        ->with(['invoice' => function($query){
            $query->with(['invoice_line_item' => function($q){
                $q->with(['sale_agreement_line_item' => function($_q){
                    $_q->with(['booking_line_item' => function($_q1){
                        $_q1->with('other','contractor', 'serviceType', 'booking_type');
                    }]);
                }]);
            }]);
        }])
        ->get();
        //var_dump($collections->toArray());exit;
        return view('exports.additional-service', compact('collections'));
    }
}
