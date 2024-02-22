
<!DOCTYPE html>
<html lang="en">
  <head>
      <style>
      .container{
        padding: 50px 45px;
      }
      body{
        font-family: Roboto, sans-serif;
      }
      table{
          border-collapse: collapse !important;
          width: 100%;
      }
      th{
          border-bottom: 2px solid #EFEFEF;
        font-size: 9px;
        color: #999999;
        text-align: left;
        padding: 0 7px 10px 7px;
      }
      td{
          font-size: 8px;
        text-align: left;
        padding: 8px;
      }
      tfoot > tr > td{
          border-top: 2px solid #DFE0EB;
          padding-top: 10px;
          font-size: 9px;
        color: #000000;
        font-weight: bold;
      }
      ul {
          list-style-type: lower-latin;
      }
      .px-20{
          padding-left: 20px;
        padding-right: 20px;
      }
      .ml-20{
          margin-left: 20px;
      }
    
    </style>
  </head>
  <body>
    <div class="container">
      <div>
        <img style="padding-left: 10px; float:left" src="{{$logo}}" width="150"/>
        <div style="width: 1px; height: 22px; background: rgba(0, 0, 0, 0.5); float:left; margin-top: 8px"></div>
        <div style="font-size: 9px; line-height: 1.5; margin-left: 15px; margin-top: 3px; float:left">
          Christian Columbarium Pte Ltd<br/>
          920 Old Choa Chu Kang Rd, Singapore 699815
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="ml-20" style="margin-top: 25px;width:100%;margin-bottom: 10px;">
          <div style="font-size: 15px; line-height: 18px; float: left;width:70%;">
           Tax Invoice
          </div>
        <div style="float: left; width:30%;">
          <div style="width:100%;">
              <div style="font-size: 10px; float: left; line-height: 1.8;width:60%;">Invoice no</div>
              <div style="font-size: 11px; float: right;width:40%;white-space: nowrap;line-height: 1.8">{{$invoice->invoice_no}}</div>
              <div style="clear: both"></div>
          </div>
          <div style="width:100%;">
              <div style="font-size: 10px; float: left;width:60%; ">Date</div>
              <div style="font-size: 11px; float: right;width:40%;">{{$invoice->invoice_date}}</div>
              <div style="clear: both"></div>
          </div>
          <div style="width:100%; padding-top: 17px;">
            <div style="font-size: 10px; float: left;width:60%; ">GST Registration No. </div>
            <div style="font-size: 11px; float: right;width:40%;">{{$gst}}</div>
            <div style="clear: both"></div>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div style="width: 100%; height: 4px; background: #EFEFEF; margin-top: 40px"></div>
      <div class="px-20" style="margin-top: 25px">
          <div style="width: 50px; font-size: 11px; padding-top: 8px; float: left">
            Bill To
        </div>
        <div style="float: left">
            <span style="font-size: 14px; font-weight: bold; line-height: 2">Mr. {{$invoice->client->display_name}}</span>
            <br/>
            <div style="font-size: 12px; line-height: 1.5">
              {{$invoice->client->display_address}}
              <br>
              {{$invoice->client->postal_code}}
              <br>
              {{$invoice->client->phone}}
              <br>
              {{$invoice->client->email}}
              <br>
            </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="margin-top: 20px; font-size: 11px; line-height: 16px">Services</div>
      <div class="px-20" style="width: 100%">
          <table style="width: 100%; float: none">
          <tr>
              <th style="width: 5%">#</th>
              <th style="width: 15%">Service Type</th>
              <th style="width: 15%">Niche ID/Room</th>
              <th style="width: 35%">Remarks</th>
              <th style="width: 10%">Amount</th>
              <th style="width: 10%">Discount</th>
              <th style="width: 10%">GST</th>
              <th style="width: 10%">Total</th>
          </tr>
          <tbody>
            @foreach($invoice->invoice_line_item as $key => $data)
              <tr>
                <td>{{++$key}}</td>
                <td>
                  @if($data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text === "Additional Services")
                    {{$data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text}}
                  @elseif($data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text === "Niches")
                    {{$data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text}}
                  @elseif($data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                    {{$data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text}}
                  @endif
                </td>
                <td>
                  @if($data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text === "Additional Services")
                    {{$data->sale_agreement_line_item->booking_line_item->other->service_name}}
                  @elseif($data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text === "Niches")
                    {{$data->sale_agreement_line_item->booking_line_item->niche->reference_no}}
                  @elseif($data->sale_agreement_line_item->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                    {{$data->sale_agreement_line_item->booking_line_item->room->room_no}}
                  @endif
                </td>
                <td>
                  {{$data->sale_agreement_line_item->booking_line_item->remarks}}
                </td>
                <td>${{$data->sale_agreement_line_item->booking_line_item->amount_format}}</td>
                <td>
                  @if(isset($data->sale_agreement_line_item->booking_line_item->getDiscount))
                  ${{number_format($data->sale_agreement_line_item->booking_line_item->getDiscount->amount, 2, '.', ',')}}
                  @else
                    --
                  @endif
                </td>
                <td>${{$data->sale_agreement_line_item->booking_line_item->tax_amount}}</td>
                <td> 
                  @if(isset($data->sale_agreement_line_item->booking_line_item->amount))
                    @if(isset($data->sale_agreement_line_item->booking_line_item->discount_amount))
                      ${{number_format((float)($data->sale_agreement_line_item->booking_line_item->amount) + (float)($data->sale_agreement_line_item->booking_line_item->tax_amount) - (float)($data->sale_agreement_line_item->booking_line_item->discount_amount), 2, '.', ',')}}
                    @else
                      ${{number_format((float)($data->sale_agreement_line_item->booking_line_item->amount) + (float)($data->sale_agreement_line_item->booking_line_item->tax_amount), 2, '.', ',')}}
                    @endif
                  @else
                    --
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colSpan="4">Total</td>
              <td>${{number_format((float)$invoice->total_amount, 2, '.', ',') }}</td>
              <td>
                @if($invoice->amount_discount > 0)
                ${{number_format($invoice->amount_discount, 2, '.', ',')}}
                @else
                  --
                @endif
              </td>
              <td>${{$invoice->total_tax_amount}}</td>
              <td>${{$invoice->total}}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="px-20" style="margin-top: 25px">
          <div style="width: 50px; font-size: 10px; float: left">
            Pay To
        </div>
        <div style="float: left">
          <div style="font-size: 10px; margin-bottom: 4px;">a) Bank Transfer to <strong>DBS Bank Account No. 100-0006-256</strong></div>
          <div style="font-size: 10px; margin-bottom: 4px;">b) Cheque crossed and make payable to <strong>Christian Columbarium Pte Ltd</strong></div>
          <div style="font-size: 10px; margin-bottom: 4px;">c) PayNow (enter <strong>UEN 199903800C</strong> in payee or scan QR Code)</div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div style="margin-top: 25px; padding-left: 20px">
        <div>
          <img src="{{$qr_code}}"  style="width: 125px; height: 120px"/>
        </div>
      </div>
    </div>
  </body>
</html>
