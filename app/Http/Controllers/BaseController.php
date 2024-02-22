<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reference;

class BaseController extends Controller
{
    public function store(Request $request)
    {
       $data = [
            'is_tgor'    => ['Word of Mouth (Referral)', 'Church/ Pastor', 'Attended funeral wake at TGOR', 'MM Magazine Ad', 'Search Engine', 'Google Ads', 'Social Media', 'Others'],
            'religion'   => ['Christian', 'Catholic', 'Others'],
            'salutation' => ['Mr', 'Mrs', 'Ms', 'Miss', 'Master', 'Mdm', 'Dr', 'Major', 'Rev'],
            'preferred_contact_by' => ["Email", "Mail", "Phone", "Text"],
            'type_niche' => [ 'Single',  'Double'],
            'tv_dimention' => ['15 Inch', '24 Inch', "27 Inch", "32 Inch", "42 Inch", "100 Inch"],
            'booking_type' => ['Niches', 'Memorial Rooms', 'Additional Services'],
            'other_type' => ['Rent', 'Sale'],
            'relationship_to_applicant' =>  ['Spouse', 'Parent', 'Child', 'Relative', 'Friend', 'Sibling'],
            'category_niche' => ['Standard', 'Premium'],
            'booking_status'  =>  ['Booked', 'Agreement', 'Partially Invoiced', 'Fully Invoiced', 'Partially Paid', 'Fully Paid','Cancelled', 'Draft','Invoice Generated','Receipt Generated'],
            'contractor_required' => ['Yes', 'No'],
            'admin_room_status'   => ['Available', 'Unavailable'],
            'admin_status'  =>  ['Active', 'Inactive'],
            'room_type'     =>  ['Daily', 'Hourly (Min 4 Hours)','Complimentary'],
            'event_default' =>  ['Funeral Wake','Memorial Service','Retreat / Meeting','TGOR - Memorial Service'],
            'duration_other'    =>  ['Half a day', 'Two hours'],
            'co_liense' => ['Yes', 'No'],
            'relationship_with_lisense' =>  ['Spouse', 'Parent', 'Child', 'Relative', 'Friend', 'Sibling'],
            'other_status'  =>  ['Available', 'Unavailable'],
            'payment_mode'  =>  ['Cheque', 'Cash', 'Bank Transfer', 'PayNow'],
            'amount_type'  =>  ['Value', 'Percentage'],
            'discount_type'  =>  ['Pastor', 'Church','Bulk','Staff'],
            'status_services_niches' => ['Vacant', 'Reserved', 'Sold - Unoccupied', 'Sold - Occupied','Cancelled'],
            'status_services_rooms' => ['Available', 'Occupied', 'Reserved','Cancelled'],
            'status_services_products' => ['Sold', 'Rented Out','Cancelled'],
            'referral'  => ['Yes', 'No'],
            'service_report'    => ['Niches', "Memorial Rooms", "Additional Services", "Renewal" ,"Collections Summary"],
            'other_category'    => ['Niche', "Memorial Room"]
        ];

        foreach($data as $key => $item) {
            foreach($item as $value) {
                $result = Reference::where([
                                ['reference_type', '=', $key],
                                ['reference_value_text', '=', $value],
                        ])->first();
                if($result === null) {
                    Reference::create([
                        'reference_type'       => $key,
                        'reference_value_text' => $value
                    ]);
                }
            }
        }
        return response()->json(
            [
                'status' => 'Generate success'
            ], 200);
    }
    
}