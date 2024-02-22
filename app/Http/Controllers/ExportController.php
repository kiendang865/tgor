<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Booking;
use App\BookingLineItems;
use App\Niche;
use App\MemorialRoom;
use App\Reference;
use App\Other;
use DateTime;
use App\BookingNicheItem; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class ExportController extends Controller implements FromCollection, WithHeadings, WithEvents
{
    use Exportable;
    
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $styleArray = [
            'font' => [
                'bold' => true,
            ]
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use ($styleArray){
                $event->sheet->getStyle('A1:I1')->applyFromArray($styleArray);
            },   
        ];
    }

    public function collection()
    {
        $service_niche = BookingLineItems::whereHas('booking_type', function (Builder $query) {
            $query->where([
                    ['reference_type', '=', 'booking_type'],
                    ['reference_value_text', '=', 'Niches'],
                ]);
            })->with(['niche','information', 'booking'])->get();
        foreach ($service_niche as $row) {
            $occupant_double = [];
            $death_anniversarys_double = [];
            foreach($row->information as $key=>$value){
                $occupant = $value->full_name;
                array_push($occupant_double, $occupant);
                if(!empty($value->death_anniversary)){
                    $death_anniversarys = date_format(date_create_from_format('Y-m-d H:i:s', $value->death_anniversary), 'd/m');
                }else
                {
                    $death_anniversarys = null;
                }
                array_push($death_anniversarys_double, $death_anniversarys);
            }
            $start_date = $this->emptyDate($row->lease_start_date);
            $expiry_date = $this->emptyDate($row->lease_expiry_date);
            $interment_date = $this->emptyDate($row->interment_date);
            //
            if(empty($row->niche)){
                $reference_no = null;
                $full_location = null;
            }else{
                $reference_no = $row->niche->reference_no;
                $full_location = $row->niche->full_location;
            }
            
            $niche[] = array(
                '0' => $row->booking->booking_no,
                '1' => $reference_no,
                '2' => $full_location,
                '3' => implode(', ', $occupant_double),
                '4' => $start_date,
                '5' => $expiry_date,
                '6' => implode(', ', $death_anniversarys_double),
                '7' => $interment_date,
                '8' => $row->status_niche->reference_value_text,
            );
        }
        return (collect($niche));
    }
    public function headings(): array
    {
        return [
            'Booking #',
            'Niche ID',
            'Location',
            'Occupant',
            'Started Lease Date',
            'Expired Lease Date',
            'Death Anniversary',
            'Interment Date',
            'Status',
        ];
    }

    /**
 * @OA\Get(
 *     tags={"Export"},
 *     path="/api/export",
 *     summary="Export list Niche",
 *     operationId="exportListNiche",
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server",
 *     )
 * )
 */
    public function export(){
        return Excel::download(new ExportController(), 'service-niche.xlsx');

   }

   public static function emptyDate($date){
    if(!empty($date)){
        $date = date_format(date_create_from_format('Y-m-d H:i:s', $date), 'd/m/Y');
    }
    else{
        $date = null;
    }
    return $date;
   }
}
