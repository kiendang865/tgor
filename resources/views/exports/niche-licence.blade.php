<?php use Carbon\Carbon; ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <style>
      .container{
        padding: 35px 45px;
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
        font-size: 10px;
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
          font-size: 10px;
        color: #000000;
        font-weight: bold;
      }
      ul {
          list-style-type: lower-latin;
      }
      .px-20{
          padding-left: 25px;
        padding-right: 25px;
      }
      .ml-20{
          margin-left: 20px;
      }
    
    </style>
  </head>
  <body>
    <div class="container">
      <div>
        <!-- <img style="padding-left: 10px; float:left" src="{{$logo}}" width="150" /> -->
        <div style="width: 1px; height: 22px; background: rgba(0, 0, 0, 0.5); float:left; margin-top: 8px"></div>
        <div style="font-size: 9px; line-height: 1.5; margin-left: 15px; margin-top: 3px; float:left">
          Christian Columbarium Pte Ltd<br/>
          920 Old Choa Chu Kang Rd, Singapore 699815
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="ml-20" style="margin-top: 15px;width:100%;margin-bottom: 10px;">
        <div style="font-size: 15px; line-height: 18px; float: left;width:60%;">
          Niche Licence
        </div>
        <div style="float: left; width:40%;">
          <div style="width:100%;">
              <div style="font-size: 10px; float: left; line-height: 1.8;width:70%;">Licence agreement no</div>
              <div style="font-size: 10px; float: right;width:30%; line-height: 1.8"> {{$sale_agreement->sale_agreement_no}}</div>
              <div style="clear: both"></div>
          </div>
          <div style="width:100%;">
              <div style="font-size: 10px; float: left;width:70%;">Date</div>
              <div style="font-size: 10px; float: right;width:30%;">{{$sale_agreement->sale_agreement_date}}</div>
              <div style="clear: both"></div>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div style="width: 100%; height: 4px; background: #EFEFEF; margin-top: 10px"></div>
      <div class="px-20" style="margin-top: 25px">
          <div style="width: 100%; font-size: 12px;line-height: 2">
          THIS LICENCE is granted the <span style="font-weight: bold;">{{$sale_agreement->sale_agreement_date}}</span> by CHRISTIAN COLUMBARIUM PTE LTD (the "Licensor"), a company incorporated in Singapore (Company No. 199903800C) having its registered office at 70 Barker Road #04-02, Singapore 309936 to:
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="margin-top: 5px;width: 100%">
        <div style="width: 90%;">
          <div style="width: 14%; font-size: 12px; padding-top: 8px; float: left;">
            Name:
          </div>
          <div style="float: left; width: 79%">
            <div>
              <span style="padding: 5px;font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->client->display_name}}</span><span style="padding: 5px;font-size: 12px; line-height: 2">(the "Licensee")</span>
            </div>
          </div>
        </div>
        <!-- <div style="width: 16%;float:right">
          <span  style="font-size: 12px;">(the "Licensee")</span>
        </div> -->
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="margin-top: 5px;width: 100%">
        <div style="width: 50%;float: left;">
          <div style="width: 20%; font-size: 12px; padding-top: 8px; float: left;">
            Citizenship
          </div>
          <div style="float: right; width: 75%">
            <div>
            @if($sale_agreement->client->nationality === null)
                <span style="padding: 5px;font-size: 12px; font-weight: bold; line-height: 2">Singaporean</span>
            @else
                <span style="padding: 5px;font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->client->nationality}}</span>
            @endif
            </div>
          </div>
        </div>
        <div style="width: 44%; float: left;">
          <div style="width: 46%; font-size: 12px; padding-top: 8px; float:left;">
            NRIC/Passport No.:
          </div>
          <div style=" width:46%; float:right;">
            <div>
            @if($sale_agreement->client->passport === null)
                <span style="padding: 5px;font-size: 12px; font-weight: bold; line-height: 2">No{{$sale_agreement->client->passport}}</span>
            @else
                <span style="padding: 5px;font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->client->passport}}</span>
            @endif
            </div>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>

      <div class="px-20" style="margin-top: 5px;width: 100%">
        <div style="width: 100%;">
          <div style="width: 12%; font-size: 12px; padding-top: 8px; float: left;">
          Address
          </div>
          <div style="float: left; width: 88%">
            <div>
            @if($sale_agreement->client->display_address === null)
                <span style="padding: 5px;font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->client->street_no}} {{$sale_agreement->client->street_name}} {{$sale_agreement->client->building_name }}</span>
            @else
              <span style="padding: 5px;font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->client->display_address}}</span>
            @endif
            </div>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
     
      <div class="px-20" style="margin-top: 5px;width: 100%">
        <div style="width: 100%;">
          <div style="width: 15%; font-size: 12px; padding-top: 8px; float: left;">
          Postal Code
          </div>
          <div style="float: left; width: 88%">
            <div>
                <span style="padding:5px;font-size: 12px; font-weight: bold; line-height: 2">{{$sale_agreement->client->postal_code}}</span>
            </div>
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
    
      <div class="px-20" style="width:100%;margin-bottom: 10px;margin-top: 12px;">
        <div style="line-height: 2;font-size: 12px;">
          Whereas:
        </div>
        <div style=" width:100%;line-height: 2;font-size: 12px;">
        The Licensor was established for the purpose of setting up a Christian Columbarium managed according to Christian principles, practices and rites, and is the lessee of the Property at LOT 1257A MK12 Old Choa Chu Kang Road Singapore
        (the "Columbarium") for ninety-nine ({{$sale_agreement->countYear}}) years commencing on <span style="font-weight: bold;">{{$sale_agreement->start_date}}</span>  and 
        expiring on <span style="font-weight: bold;">{{$sale_agreement->expiry_date}}</span>.	
        </div>
        <div style="width:100%;line-height: 2;margin-top: 5px;font-size: 12px;">
        The Licensee wishes to obtain, and the Licensor wishes to grant to the Licensee, a licence for the use of a Columbarium Niche, on the terms and conditions of this Licence.	
        </div>
        <div style="width:97%;margin-top: 5px;margin-bottom: 5px;font-size: 12px;">
          <label for="">It is agreed as follows:</label>
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="width:95%;margin-bottom: 10px;font-size: 12px;">
        <div style="margin-bottom: 10px;">
          <label style="font-weight:bold">1. GRANT OF LICENCE AND OPTION TO RENEW</label> 
        </div>
        <div style="line-height: 2;margin-bottom: 10px;">
        In consideration of the non-refundable payment by the Licensee to the Licensor of the sum of 
        (<span style="font-weight: bold;">${{$sale_agreement->amount}}</span> price of niche excl GST) plus <span style="font-weight: bold;">${{$sale_agreement->gst_amount}}</span> (7% GST) borne by the licensee 
        (receipt of which is acknowledged by the Licensor), the Licensor hereby grants to the 
        Licensee an irrevocable licence to use the Columbarium Niche described in the Niche Licence Cover Page, 
        Licence No. <span style="font-weight: bold;">{{$sale_agreement->sale_agreement_no}}</span> ("Cover Page") hereto (the "Niche") for a period commencing from 
        the effective date specified in the Cover Page ("Effective Date") to {{$sale_agreement->expiry_date}} ("Expiry Date"), 
        subject to the terms and conditions in the Licence.
        </div>
        <div style="line-height: 2;margin-bottom: 10px;">
        The Licensor, at its sole discretion, may prescribe rules ("Rules") regulating the use of the grounds of the Columbarium ("Grounds") by persons visiting the Niche.		
        </div>
        <div style="line-height: 2;margin-bottom: 10px;">
        Upon expiry, the Licensee shall have an option to renew the term of 
        the Licence for a further period until <span style="font-weight: bold;">{{ Carbon::parse($sale_agreement->expiry_date)->subMonths(12)->format('j F Y') }}</span>, subject to payment of a 
        sum on terms and conditions to be decided by a succeeding 
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="px-20" style="width:95%;margin-bottom: 10px;font-size: 12px;">
        <div style="float: right;width: 19%; height:50px; text-align:center;border: 1px solid #D5D5D5;box-sizing: border-box; position: relative;">
          <span style="position: absolute; width:100%; top:31%; left:0;font-size: 9px;">Licensor's Initial</span>
        </div>
        <div style="float: right;width: 19%; height:50px; text-align:center;border: 1px solid #D5D5D5;box-sizing: border-box; position: relative;">
          <span style="position: absolute; width:100%; top:31%; left:0;font-size: 9px;">Licensee's Initial</span>
        </div>
        <div style="clear: both"></div>
      </div>
    </div>
    <div style="page-break-after: always;"></div>
  </body>
</html>
