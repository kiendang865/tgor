<php ?
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
        <img style="padding-left: 10px; float:left" src="{{$logo}}" width="150" height="40" />
        <div style="width: 1px; height: 20px; background: rgba(0, 0, 0, 0.5); float:left; margin-top: 6px"></div>
        <div style="font-size: 9px; line-height: 1.5; margin-left: 15px; float:left">
          Christian Columbarium Pte Ltd<br/>
          920 Old Choa Chu Kang Rd, Singapore 699815
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="ml-20" style="margin-top: 25px;width:100%;margin-bottom: 10px;">
          <div style="font-size: 15px; line-height: 18px; float: left;width:76%;">
          License Agreement
        </div>
        <div style="float: left; width:23%;">
          <div style="width:100%;">
              <div style="font-size: 10px; font-weight: 600; float: left; line-height: 1.8;width:70%;">License agreement no</div>
              <div style="font-size: 10px; float: right;width:30%; line-height: 1.8">{{$sale_agreement->sale_agreement_no}}</div>
              <div style="clear: both"></div>
          </div>
          <div style="width:100%;">
              <div style="font-size: 10px; font-weight: 600; float: left;width:70%;">Date</div>
              <div style="font-size: 10px; float: right;width:30%;">{{$sale_agreement->sale_agreement_date}}</div>
              <div style="clear: both"></div>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div style="width: 100%; height: 4px; background: #EFEFEF; margin-top: 10px"></div>
        <div class="px-20" style="margin-top: 25px;width:50%">
            <div style="width: 20%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                Licensee
            </div>
            <div style="float:left">
                <span style="font-size: 12px; font-weight: bold; line-height: 2">Mr. {{$sale_agreement->client->display_name}}</span>
                <br/>
                <div style="font-size: 11px; line-height: 1.5">
                {{$sale_agreement->client->phone}} - {{$sale_agreement->client->email}}
                <br/>
                {{$sale_agreement->client->display_address ? $sale_agreement->client->display_address : '--'}}
                </div>
            </div>
            <div style="clear: both"></div>
        </div>
        <!-- Co-Licensee -->
        @foreach($sale_agreement->sale_agreement_item as $key => $data)
        @if($data->booking_line_item->booking_type->reference_value_text =="Niches")
            <div class="px-20" style="margin-top: 25px;width:100%">
            @if($data->booking_line_item->co_license =="Yes")
                <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                    <div>Co-Licensee</div>
                    <div style="padding-left: 18px;">{{$data->booking_line_item->niche->reference_no}}</div>
                </div>
                <div style="float:left;width: 37%;">
                    <span style="font-size: 12px; font-weight: bold; line-height: 2">Mr. {{$data->booking_line_item->co_license_name}}</span>
                    <br/>
                    <div style="font-size: 11px; line-height: 1.5">
                    {{$data->booking_line_item->co_license_phone}} - {{$data->booking_line_item->co_license_email}}
                    <br/>
                    {{$data->booking_line_item->co_license_street_no}} {{$data->booking_line_item->co_license_street_name}}
                    </div>
                </div>
            @else
              <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                    <div>Co-Licensee</div>
                    <div style="padding-left: 18px;">{{$data->booking_line_item->niche->reference_no}}</div>
                </div>
                <div style="float:left;width: 37%;">
                    <span style="font-size: 12px; font-weight: bold; line-height: 2">--</span>
                    <br/>
                    <div style="font-size: 11px; line-height: 1.5">
                      --
                    <br/>
                      --
                    </div>
                </div>
            @endif
            @if(!empty($data->booking_line_item->information)&&count($data->booking_line_item->information) > 0)
            @if(!empty($data->booking_line_item->information[0]))
              <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                  <div>Occupant</div>
                  <div style="padding-left: 6px;">{{$data->booking_line_item->niche->reference_no}}</div>
              </div>
              <div style="float:left;width: 37%;">
                  <span style="font-size: 12px; font-weight: bold; line-height: 2">Mr. {{$data->booking_line_item->information[0]->first_name}} {{$data->booking_line_item->information[0]->last_name}}</span>
                  <br/>
                  <div style="font-size: 11px; line-height: 1.5">
                    {{$data->booking_line_item->information[0]->relationship_to_applicant}} {{$data->booking_line_item->information[0]->death_anniversary}}
                  </div>
              </div>
              <div style="clear: both"></div>
            @endif
        <!-- dsddsa -->
            @if(!empty($data->booking_line_item->information[1]))
                <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                  
                </div>
                <div style="float:left;width: 37%;">
              
                </div>
                <!-- <div style="clear: both"></div> -->
                <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left;margin-top: 25px;">
                    <div>Occupant</div>
                    <div style="padding-left: 18px;">A001</div>
                </div>
                <div style="float:left;width: 37%;margin-top: 25px;">
                    <span style="font-size: 12px; font-weight: bold; line-height: 2">Mr. {{$data->booking_line_item->information[1]->first_name}} {{$data->booking_line_item->information[1]->last_name}}</span>
                    <br/>
                    <div style="font-size: 11px; line-height: 1.5">
                    {{$data->booking_line_item->information[1]->death_anniversary}}
                    </div>
                </div>
                <div style="clear: both"></div>
            @endif
            @else
            <div style="width: 10%; font-size: 11px; padding-right:20px; padding-top: 8px; float:left">
                    <div>Occupant</div>
                    <div style="padding-left: 6px;">{{$data->booking_line_item->niche->reference_no}}</div>
                </div>
                <div style="float:left;width: 37%;">
                    <span style="font-size: 12px; font-weight: bold; line-height: 2">Mr.--</span>
                    <br/>
                    <div style="font-size: 11px; line-height: 1.5">
                     --
                    </div>
                </div>
                <div style="clear: both"></div>
          @endif 
            </div>
        @endif
        @endforeach
        
       

      <div class="px-20" style="margin-top: 20px; font-size: 11px; line-height: 16px;page-break-before: always;">Services</div>
      <div class="px-20" style="width: 100%">
          <table style="width: 100%; float: none">
          <tr>
              <th style="width: 5%">#</th>
              <th style="width: 15%">Service Type</th>
              <th style="width: 15%">Niche ID/Room</th>
              <th style="width: 35%">Remark</th>
              <th style="width: 10%">Amount</th>
              @if($sale_agreement->discount_id != null)
              <th style="width: 10%">Discount</th>
              @endif
              <th style="width: 10%">GST</th>
              <th style="width: 10%">Total</th>
          </tr>
          <tbody>
            @foreach($sale_agreement->sale_agreement_item as $key => $data)
              <tr>
                <td>{{++$key}}</td>
                <td>
                  @if($data->booking_line_item->booking_type->reference_value_text === "Additional Services")
                    {{$data->booking_line_item->booking_type->reference_value_text}}
                  @elseif($data->booking_line_item->booking_type->reference_value_text === "Niches")
                    {{$data->booking_line_item->booking_type->reference_value_text}}
                  @elseif($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                    {{$data->booking_line_item->booking_type->reference_value_text}}
                  @endif
                </td>
                <td>
                @if($data->booking_line_item->booking_type->reference_value_text === "Additional Services")
                    {{$data->booking_line_item->other->service_name}}
                  @elseif($data->booking_line_item->booking_type->reference_value_text === "Niches")
                    {{$data->booking_line_item->niche->reference_no}}
                  @elseif($data->booking_line_item->booking_type->reference_value_text === "Memorial Rooms")
                    {{$data->booking_line_item->room->room_no}}
                  @endif
                </td>
                <td>
                  {{$data->booking_line_item->remarks}}
                </td>
                <td>${{$data->booking_line_item->amount_format}}</td>
                @if($data->booking_line_item->booking_type->reference_value_text === "Niches")
                  @if($sale_agreement->discount_id != null)
                  <td>${{number_format($data->booking_line_item->discount_amount, 2, '.', ',')}}</td>
                  @endif
                @endif
                <td>${{$data->booking_line_item->gst_amount_format}}</td>
                <td>${{$data->booking_line_item->total_amount}}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colSpan="4">Total</td>
              <td>${{$sale_agreement->total_amount}}</td>
              @if($sale_agreement->discount_id != null)
              <td>${{number_format($amount_discount, 2, '.', ',')}}</td>
              @endif
              <td>${{$sale_agreement->total_tax_amount}}</td>
              <td>${{$sale_agreement->total}}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      
      <div class="px-20" style="margin-top: 30px;font-size: 11px;">I confirm that the above information is correct</div>
      <div class="px-20" style="margin-top: 15px;">
          <div style="max-width: 145px;float: left">
          <div style="width: 145px;height: 50px;">
            @if(!empty($sale_agreement->signature_tgor_officer))
              <img src="{{$sale_agreement->signature_tgor_officer}}" width="104" height="50"/>
            @endif
          </div>
              <p style="font-size: 11px">Name: <strong>{{$sale_agreement->admin ? $sale_agreement->admin->display_name : ' ' }}</strong>
              <br>
              for and on behalf of CHRISTIAN COLUMBARIUM PTE LTD
            </p>
          </div>
        </div>
          <div style="max-width: 145px;width: 145px;height: 50px; margin-left: 80px; float: left">
            <div style="width: 145px;height: 50px;">
            @if(!empty($sale_agreement->signature_client))
              <img src="{{$sale_agreement->signature_client}}" width="104" height="50"/>
            @endif
          </div>
          
          <div>
              <p style="font-size: 11px">Licensee: <strong>{{$sale_agreement->client->display_name}}</strong></p>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
      
    </div>
  </body>
</html>
