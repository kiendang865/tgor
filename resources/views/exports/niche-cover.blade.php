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
            font-family: "Roboto", sans-serif;
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
            <div style="font-size: 9px; line-height: 1.5; margin-left: 15px;margin-top: 2px; float:left">
                Christian Columbarium Pte Ltd<br />
                920 Old Choa Chu Kang Rd, Singapore 699815
            </div>
            <div style="clear: both"></div>
        </div>
        <div class="ml-20" style="margin-top: 25px;width:100%;margin-bottom: 10px;">
            <div style="font-size: 15px; line-height: 18px; float: left;width:76%;">
                Sales Agreement for Niche Lease
            </div>
            <div style="float: left; width:30%;">
                <div style="width:100%;">
                    <div style="font-size: 10px ; float: left; line-height: 1.8;width:70%; ">Booking Reference #</div>
                    <div style="font-size: 10px; float: right;width:30%; line-height: 1.8">{{$sale_agreement->booking->booking_no}}</div>
                    <div style="clear: both"></div>
                </div>
                <div style="width:100%;">
                    <div style="font-size: 10px; float: left;width:70%;">Date</div>
                    <div style="font-size: 10px; float: right;width:30%;">{{Carbon::parse($sale_agreement->sale_agreement_date)->format('d/m/Y')}}</div>
                    <div style="clear: both"></div>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>
        <div style="width: 100%; height: 4px; background: #EFEFEF; margin-top: 10px"></div>


        <div class="px-20" style="margin-top: 25px;width:100%">
            <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                <div>Licensee</div>
            </div>
            <div style="float:left;width: 40%;">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">
                    @if($sale_agreement->client->salutation_name)
                    {{$sale_agreement->client->salutation_name->reference_value_text}}.
                    @endif
                    {{$sale_agreement->client->display_name}}</span>
                <br />
                <div style="font-size: 11px; line-height: 1.5">
                    {{$sale_agreement->client->passport}}
                    <br />
                    {{ $sale_agreement->client->display_address }}
                    <br />
                    {{ $sale_agreement->client->phone }}
                    <br />
                    {{ $sale_agreement->client->email }}
                </div>
            </div>
            @if($sale_agreement->sale_agreement_item[0]->booking_line_item->is_co_license->reference_value_text == "Yes")
            <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                <div>Co-Licensee</div>
            </div>
            <div style="float:left;width: 40%;">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->sale_agreement_item[0]->booking_line_item->co_license_name}}</span>
                <br />
                <div style="font-size: 11px; line-height: 1.5">
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->co_license_street_name}}
                    <br />
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->co_license_email}}
                </div>
            </div>
            @endif
            <div style="clear: both"></div>
        </div>
        <div class="px-20" style="margin-top: 25px;width:100%">
            @if(!empty($sale_agreement->sale_agreement_item[0]->booking_line_item->information)&&count($sale_agreement->sale_agreement_item[0]->booking_line_item->information) > 0)
            @if(!empty($sale_agreement->sale_agreement_item[0]->booking_line_item->information[0]))
            <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                <div>Occupant 1</div>
            </div>
            <div style="float:left;width: 40%;">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->sale_agreement_item[0]->booking_line_item->information[0]->full_name}}</span>
                <br />
                <div style="font-size: 9px; line-height: 1.5">
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->information[0]->is_relationship_to_applicant)
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->information[0]->is_relationship_to_applicant->reference_value_text}} -
                    @endif
                    {{Carbon::parse($sale_agreement->sale_agreement_item[0]->booking_line_item->information[0]->death_anniversary)->format('d/m/Y')}}
                </div>
            </div>
            @endif
            @if(!empty($sale_agreement->sale_agreement_item[0]->booking_line_item->information[1]))
            <!-- <div style="clear: both"></div> -->
            <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left;">
                <div>Occupant 2</div>
            </div>
            <div style="float:left;width: 40%;">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->sale_agreement_item[0]->booking_line_item->information[1]->full_name}}</span>
                <br />
                <div style="font-size: 11px; line-height: 1.5">
                    @if($sale_agreement->sale_agreement_item[0]->booking_line_item->information[1]->is_relationship_to_applicant)
                    {{$sale_agreement->sale_agreement_item[0]->booking_line_item->information[1]->is_relationship_to_applicant->reference_value_text}} -
                    @endif
                    {{Carbon::parse($sale_agreement->sale_agreement_item[0]->booking_line_item->information[1]->death_anniversary)->format('d/m/Y')}}
                </div>
            </div>
            @endif
            <div style="clear: both"></div>
            @endif
        </div>


        <div style="margin: 22px 0px; font-size: 11px; line-height: 16px;">Services</div>
        <div class="px-20" style="width: 100%"></div>
        <table style="width: 100%; float: none">
            <tr>
                <th style="width: 10%">#</th>
                <th style="width: 12%">Niche ID</th>
                <th style="width: 8%">Type</th>
                <th style="width: 35%">Location</th>
                <th style="width: 10%">Amount</th>
                <th style="width: 10%">GST</th>
                <th style="width: 10%">Total</th>
            </tr>
            <tbody>
            @foreach($sale_agreement->sale_agreement_item as $key => $data)
                <tr>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                        {{$data->booking_line_item->booking_no}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                        {{$data->booking_line_item->niche->reference_no}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                        {{$data->booking_line_item->niche->category->reference_value_text}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                        {{$data->booking_line_item->niche->full_location}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                        {{$data->booking_line_item->amount_format}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                        {{number_format($data->booking_line_item->tax_amount, 2, '.', ',')}}
                        @endif
                    </td>
                    <td>
                        @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                        {{$data->booking_line_item->total_amount}}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tbody>
        </table>
        <!-- <div class="px-20" style="margin-top: 30px;font-size: 9px;">I confirm that the above information is correct</div> -->
        
        <div style="margin-top: 30px;font-size: 11px;">I confirm that above information is correct</div>
        <div style="width: 100%;">
            <div style="float: left; width: 40%; margin-top: 15px">
                <div style="font-size: 11px;">Customer<br>name and signature:</div>
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
            <div style="float: left; width: 40%; margin-left: 3px;">
                <div style="font-size: 11px;">For Christian Columbarium Pte Ltd,<br>name and signature:</div>
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

    </div>
</body>

</html>