<?php

use Carbon\Carbon; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .container {
            padding: 50px 45px;
        }

        body {
            font-family: Roboto, sans-serif;
        }

        table {
            border-collapse: collapse !important;
            width: 100%;
        }

        th {
            border-bottom: 2px solid #EFEFEF;
            font-size: 9px;
            color: #999999;
            text-align: left;
            padding: 0 7px 10px 7px;
        }

        td {
            font-size: 8px;
            text-align: left;
            padding: 8px;
        }

        tfoot>tr>td {
            border-top: 2px solid #DFE0EB;
            padding-top: 10px;
            font-size: 9px;
            color: #000000;
            font-weight: bold;
        }

        ul {
            list-style-type: lower-latin;
        }

        .px-20 {
            padding-left: 20px;
            padding-right: 20px;
        }

        .ml-20 {
            margin-left: 20px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <div class="container">
        <div>
            <img style="padding-left: 10px; float:left" src="{{$logo}}" width="150" />
            <div style="width: 1px; height: 22px; background: rgba(0, 0, 0, 0.5); float:left; margin-top: 8px"></div>
            <div style="font-size: 9px; line-height: 1.5; margin-left: 15px;margin-top: 3px; float:left">
                Christian Columbarium Pte Ltd<br />
                920 Old Choa Chu Kang Rd, Singapore 699815
            </div>
            <div style="clear: both"></div>
        </div>
        <div class="ml-20" style="margin-top: 25px;width:100%;margin-bottom: 10px;">
            <div style="font-size: 15px; line-height: 18px; float: left;width:76%;">
                Sales agreement for parlour rental
            </div>
            <div style="float: left; width:30%;">
                <div style="width:100%;">
                    <div style="font-size: 10px; float: left; line-height: 1.8;width:70%; font-family: Roboto, sans-serif !important;">Booking Reference #</div>
                    <div style="font-size: 10px; float: right;width:30%; line-height: 1.8">{{$sale_agreement->booking->booking_no}}</div>
                    <div style="clear: both"></div>
                </div>
                <div style="width:100%;">
                    <div style="font-size: 10px; float: left;width:70%; font-family: Roboto, sans-serif !important;">Date</div>
                    <div style="font-size: 10px; float: right;width:30%;">{{Carbon::parse($sale_agreement->sale_agreement_date)->format('d/m/Y')}}</div>
                    <div style="clear: both"></div>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>
        <div style="width: 100%; height: 4px; background: #EFEFEF; margin-top: 10px"></div>


        <div class="px-20" style="margin-top: 25px;width:100%">
            <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                <div>Main Contact</div>
            </div>
            <div style="float:left;width: 40%;">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">
                    @if($sale_agreement->client->salutation_name)
                    {{$sale_agreement->client->salutation_name->reference_value_text}}.
                    @endif
                    {{$sale_agreement->client->display_name}}</span>
                <br />
                <div style="font-size: 10px; line-height: 1.5">
                    {{$sale_agreement->client->passport}}
                    <br />
                    {{ $sale_agreement->client->display_address }}
                    <br />
                    {{ $sale_agreement->client->phone }}
                    <br />
                    {{ $sale_agreement->client->email }}
                </div>
            </div>
            @if($sale_agreement->sale_agreement_item[0]->booking_line_item->coordinator_full_name)
            <div style="width: 10%; font-size: 10px; padding-right:20px; padding-top: 8px; float:left">
                <div>Coordinator</div>
            </div>
            <div style="float:left;width: 40%;">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->coordinator_full_name}}
                </span>
                <br />
                <div style="font-size: 10px; line-height: 1.5">
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->mobile)
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->mobile}}
                    @endif
                </div>
            </div>
            @endif
            <div style="clear: both"></div>
        </div>

        <div class="px-20" style="margin-top: 25px;width:100%">
            @if($sale_agreement->sale_agreement_item[0]->booking_line_item->departed_full_name)
            <div style="width: 10%; font-size: 10px; padding-right:20px; padding-top: 8px; float:left">
                <div>Departed</div>
            </div>
            <div style="float:left;width: 40%;">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->sale_agreement_item[0]->booking_line_item->departed_full_name}}</span>
                <br />
                <div style="font-size: 10px; line-height: 1.5">
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->is_relationship_to_applicant && empty($sale_agreement->sale_agreement_item[0]->booking_line_item->date_of_death))
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->is_relationship_to_applicant->reference_value_text}}
                    @endif
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->date_of_death && empty($sale_agreement->sale_agreement_item[0]->booking_line_item->is_relationship_to_applicant))
                    {{Carbon::parse($sale_agreement->sale_agreement_item[0]->booking_line_item->date_of_death)->format('d/m/Y')}}
                    @endif
                </div>
            </div>
            @endif
            @if($sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director)
            <div style="width: 10%; font-size: 10px; padding-right:20px;padding-top: 8px; float:left">
                <div>Funeral Director</div>
            </div>
            <div style="float:left;width: 40%; ">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">{{ $sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->company_name }}</span>
                <br />
                <div style="font-size: 10px; line-height: 1.5">
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name && empty($sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name))
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name}}
                    @endif
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name && empty($sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name))
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name}}
                    @endif
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name && $sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name)
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name}} - {{$sale_agreement->sale_agreement_item[0]->booking_line_item->funeral_director->bank_name}}
                    @endif
                </div>
            </div>
            @endif
            <div style="clear: both"></div>
        </div>

        <div style="margin: 22px 0px; font-size: 10px; line-height: 16px;">Facility Rental</div>
        <div class="px-20" style="width: 100%"></div>
        <table style="width: 100%; float: none">
            <tr>
                <th style="width: 15%">#</th>
                <th style="width: 19%">Room</th>
                <th style="width: 25%">Event Type</th>
                <th style="width: 17%">Start Time</th>
                <th style="width: 17%">End Time</th>
                <th style="width: 13%">Amount<br>(SGD)</th>
                <th style="width: 13%">GST<br>(SGD)</th>
                <th style="width: 13%">Total<br>(SGD)</th>
            </tr>
            <tbody>
            @foreach($sale_agreement->sale_agreement_item as $key => $data)
                <tr>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{$data->booking_line_item->booking_no}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{$data->booking_line_item->room->room_no}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{$data->booking_line_item->event->reference_value_text}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{Carbon::parse($data->booking_line_item->check_in_date)->format('d/m/Y')}}
                        {{Carbon::parse($data->booking_line_item->check_in_time)->format('h:i A')}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{Carbon::parse($data->booking_line_item->check_out_date)->format('d/m/Y')}}
                        {{Carbon::parse($data->booking_line_item->check_out_time)->format('h:i A')}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{$data->booking_line_item->amount_format}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{number_format($data->booking_line_item->tax_amount, 2, '.', ',')}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                        {{$data->booking_line_item->total_amount}}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <!-- <div class="px-20" style="margin-top: 30px;font-size: 9px;">I confirm that the above information is correct</div> -->
        <div style="margin-top: 30px;font-size: 10px;">Terms and condition of use, I understand and agree:</div>
        <div class="px-20" style="margin-top: 15px">
            <div style="width: 50px; font-size: 10px; float: left">
            </div>
            <div style="float: left; width: 89%;">
                <div style="font-size: 10px;">1. To use the facilities for their intended purpose as stated</div>
                <div style="font-size: 10px;">2. That only Christians rituals are permitted</div>
                <div style="font-size: 10px;">3. That other religious rituals such as (but not limited to) chanting, burning of incense/ joss sticks, offerings etc and display of articles contrary to Christian beliefs are not permitted</div>
                <div style="font-size: 10px;">4. Pets, gambling, smoking and the consumption of alcoholic beverages anywhere on our premises are prohibited;</div>
                <div style="font-size: 10px;">5. I have inspected and taken over the facility in good order and agree that I am financially responsible for any damage(s) to, and/ or loss to/of the property</div>
                <div style="font-size: 10px;">6. To vacate the facility and return the key(s) upon departure of the cortege.</div>
            </div>
            <div style="margin: -10px 0px 0px 25px; float: left">

            </div>

            <div style="clear: both"></div>
        </div>
        <div style="margin-top: 30px;font-size: 10px;">I confirm that above information is correct</div>
        <div style="margin-top: 15px; width: 100%">
            <div style="float: left; width: 40%;">
                <div style="font-size: 10px;">Customer<br>name and signature:</div>
                <div style="width: 145px;height: 50px; margin-top: 20px">
                    @if(!empty($sale_agreement->signature_client))
                        <img src="{{$sale_agreement->signature_client}}" width="104" height="50"/>
                    @endif
                </div>
                @if(!empty($sale_agreement->client))
                <div class="py-20" style="font-size: 12px; text-align: left">
                    {{ $sale_agreement->client->display_name }}
                </div>
                @endif
            </div>
            <div style="float: left; width: 13%;"></div>
            <div style="float: left; width: 40%; margin-left: 3px">
                <div style="font-size: 10px;">For Christian Columbarium Pte Ltd,<br>name and signature:</div>
                <div style="width: 145px;height: 50px; margin-top: 20px">
                    @if(!empty($sale_agreement->signature_tgor_officer))
                        <img src="{{$sale_agreement->signature_tgor_officer}}" width="104" height="50"/>
                    @endif
                </div>
                @if(!empty($sale_agreement->admin))
                <div class="py-20" style="font-size: 12px; text-align: left">
                    {{ $sale_agreement->admin->display_name }}
                </div>
                @endif
            </div>
            <div style="clear: both"></div>
        </div>
    </div>
</body>

</html>