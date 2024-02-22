<php ?
<!DOCTYPE html>
<html lang="en">
  <head>
      <style>
      .container{
        padding: 50px 45px;
        /* size: 21cm 29.7cm;
        margin: 30mm 45mm 30mm 45mm; */
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
      .py-10{
        padding-top: 10px;
        padding-bottom: 10px;
      }
      .ml-20{
        margin-left: 20px;
      }
    
    </style>
  </head>
  <body>
    <div class="container">
      <div>
        <img style="padding-left: 10px; float:left" src="{{$logo}}" width="150" />
        <div style="width: 1px; height: 22px; background: rgba(0, 0, 0, 0.5); float:left; margin-top: 8px"></div>
        <div style="font-size: 10px; line-height: 1.5; margin-left: 15px; margin-top: 3px; float:left">
          Christian Columbarium Pte Ltd<br/>
          920 Old Choa Chu Kang Rd, Singapore 699815
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="ml-20" style="margin-top: 25px;width:100%;margin-bottom: 10px;">
          <div style="font-size: 15px; line-height: 18px; float: left;width:80%;">
          Payment Receipt
        </div>
        <div style="float: left; width:18%;">
          <div style="width:100%;">
              <div style="font-size: 10px;float: left;white-space: nowrap; line-height: 1.8;width:55%;">Payment no</div>
              <div style="font-size: 11px; float: right;width:35%;white-space: nowrap; line-height: 1.8">{{$payment_partial->payment->payment_no}}</div>
              <div style="clear: both"></div>
          </div>
          <div style="width:100%;">
              <div style="font-size: 10px;float: left;width:65%;">Date</div>
              <div style="font-size: 11px; float: right;width:35%;">{{$payment_partial->payment->payment_date}}</div>
              <div style="clear: both"></div>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div style="width: 100%; height: 4px; background: #EFEFEF; margin-top: 10px"></div>
      <div class="px-20" style="margin-top: 25px">
          <div style="width: 90px; font-size: 11px; padding-top: 8px; float: left">
          Received from
        </div>
        <div style="float: left">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">
              @if($payment_partial->salutation)
                {{$payment_partial->salutation}}.
              @endif
              {{$payment_partial->client->display_name}}</span>
              <br/>
              <div style="font-size: 11px; line-height: 1.5">
              {{$payment_partial->client->phone}} - {{$payment_partial->client->email}}
              <br/>
              {{$payment_partial->client->display_address}}
            </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="margin-top: 16px">
          <div style="width: 90px; font-size: 11px; padding-top: 8px; float: left">
          For invoice no
        </div>
        <div style="float: left">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">
            @if(!empty($payment_partial->invoice))
              {{$payment_partial->invoice->invoice_no}}
            @endif
            @if(!empty($payment_partial->payment->invoice))
              {{$payment_partial->payment->invoice->invoice_no}}
            @endif
            </span>
            <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      @if($payment_partial->payment_mode->reference_value_text === "Cheque")
      <div class="px-20" style="margin-top: 8px">
          <div style="width: 90px; font-size: 11px; padding-top: 8px; float: left">
          Payment Mode
        </div>
        <div style="float: left">
          <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$payment_partial->payment_mode->reference_value_text}}</span>
          <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="float: left;width: 180px; margin-top: 8px">
        <div style="width: 50%; font-size: 11px; padding-top: 8px; float: left">
          Bank
        </div>
        <div style="float: right;width: 50%; text-align: left">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$payment_partial->cheque_bank}}</span>
            <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20">
          <div style="width: 50px; font-size: 10px; padding-top: 8px; float: left">
          Cheque #
        </div>
        <div style="float: left">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$payment_partial->cheque}}</span>
            <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      @endif
      @if($payment_partial->payment_mode->reference_value_text === "Bank Transfer")
      <div class="px-20" style="margin-top: 8px">
          <div style="width: 90px; font-size: 10px; padding-top: 8px; float: left">
          Payment Mode
        </div>
        <div style="float: left">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$payment_partial->payment_mode->reference_value_text}}</span>
            <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="margin-top: 8px;">
          <div style="width: 90px; font-size: 11px; padding-top: 8px; float: left">
          Transaction #
        </div>
        <div style="float: left">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$payment_partial->transaction}}</span>
            <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      <!-- <div class="px-20">
          <div style="width: 50px; font-size: 9px; padding-top: 8px; float: left">
        </div>
        <div style="float: left">
            <br/>
        </div>
        <div style="clear: both"></div>
      </div> -->
      @endif
      @if($payment_partial->payment_mode->reference_value_text === "Cash")
      <div class="px-20" style="margin-top: 8px">
          <div style="width: 90px; font-size: 10px; padding-top: 8px; float: left">
          Payment Mode
        </div>
        <div style="float: left">
          <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$payment_partial->payment_mode->reference_value_text}}</span>
          <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      @endif
      @if($payment_partial->remarks)
      <div class="px-20" style="margin-top: 8px">
          <div style="width: 90px; font-size: 10px; padding-top: 8px; float: left">
          Being payment of
        </div>
        <div style="float: left">
          <span style="font-size: 12px; font-weight: bold; line-height: 2">{{$payment_partial->remarks}}</span>
          <br/>
        </div>
        <div style="clear: both"></div>
      </div>
      @endif
      <div class="px-20" style="width:100%;margin-bottom: 10px;">
          <div style="width: 150px; font-size: 10px; padding-top: 8px; float: left">
          Received with thanks the sum of
        </div>
        <div style="float: left;">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">${{$payment_partial->amount}}</span>
            <br/>
            <span style="font-size: 12px; font-weight: bold; line-height: 2">{{ str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($payment_partial->total_string)))) }} Dollars And Zero Cent</span>
        </div>
        <div style="clear: both"></div>
      </div>
      @if($payment_partial->payment->partial_amount != null && count($payment_partial->payment->partial_amount) > 0)
      <div class="px-20" style="width:100%;margin-bottom: 10px;">
          <div style="width: 150px; font-size: 10px; padding-top: 8px; float: left">
          Amount Payable Remaining
        </div>
        <div style="float: left;">
            <span style="font-size: 12px; font-weight: bold; line-height: 2">${{$payment_partial->amount_payable}}</span>
            <br/>
            <span style="font-size: 12px; font-weight: bold; line-height: 2">{{ str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($payment_partial->amount_payable_string)))) }} Dollars And Zero Cent</span>
        </div>
        <div style="clear: both"></div>
      </div>
      @endif
      <div class="px-20" style="margin-top: 20px">
          <div style="width: 150px; font-size: 11px; padding-top: 8px;">
          Authorized Signature
        </div>
        <div style="width: 145px;height: 50px;">
            <img src="{{$payment_partial->signature_tgor_officer}}" alt="#signature_client" width="104" height="50">
            <br/>
        </div>
        @if(!empty($payment_partial->admin))
          <div class="py-20" style="font-size: 12px; text-align: left">
            {{ $payment_partial->admin->display_name }}
          </div>
        @endif
        <div style="clear: both"></div>
      </div>
    </div>
  </body>
</html>
