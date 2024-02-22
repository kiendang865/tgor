<?php use Carbon\Carbon;?>
<table>
  <thead style="border: 2px solid black;">
    <tr>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Date</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Official Receipt No.</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Particulars</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Receipt Details</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Total Amount</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Old Niches</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Premium Niches</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Renewal Niches</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Marble/ Urns</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Parlour rental</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Exhumation</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Photo</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Preserved Flowers</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Others</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">GST</th>
      <th style="font-size: 12px; font-weight: bold; background: #d9d9d9">Discount</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="background: #d9d9d9"></td>
      <td style="background: #d9d9d9"></td>
      <td style="font-style: italic; background: #d9d9d9">Accounts Code</td>
      <td style="background: #d9d9d9"></td>
      <td style="background: #d9d9d9">$</td>
      <td style="font-style: italic; background: #d9d9d9">3500</td>
      <td style="font-style: italic; background: #d9d9d9">3502</td>
      <td style="font-style: italic; background: #d9d9d9">3501</td>
      <td style="font-style: italic; background: #d9d9d9">3600</td>
      <td style="font-style: italic; background: #d9d9d9">3150</td>
      <td style="font-style: italic; background: #d9d9d9">3700</td>
      <td style="font-style: italic; background: #d9d9d9">3800</td>
      <td style="font-style: italic; background: #d9d9d9">2410</td>
      <td style="background: #d9d9d9"></td>
      <td style="font-style: italic; background: #d9d9d9">2422</td>
      <td style="background: #d9d9d9"></td>
    </tr>
    <?php
      $sum_total = 0;
      $sum_total_amount_1 = 0;
      $sum_total_amount_2 = 0;
      $sum_total_amount_3 = 0;
      $sum_total_amount_4 = 0;
      $sum_total_amount_5 = 0;
      $sum_total_amount_6 = 0;
      $sum_total_amount_7 = 0;
      $sum_total_amount_8 = 0;
      $sum_total_amount_9 = 0;
      $sum_total_discount = 0;
      $sum_total_gst = 0;
    ?>
    <?php foreach($collections as $items): ?>
    <tr>
      <td><?php echo Carbon::parse($items["payment_date"])->format('d-M'); ?></td>
      <td><?php echo $items["payment_no"] ?></td>
      <td><?php echo $items["client"]["display_name"]." ".$items["client"]["salutation"] ?></td>
      <td><?php echo $items["payment_mode"]["reference_value_text"] ?></td>
      <td style="text-align: left;"><?php
        $total = $items["total_amount"] + $items["total_tax_amount"];
        echo number_format($items["total_amount"] + $items["total_tax_amount"], 2, '.', ',');
      ?></td>
      <td style="text-align: left;">
        <?php
          $total_amount_1 = 0;
        ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Niches"): ?>
              <?php if(empty($invoice_item["sale_agreement_line_item"]["booking_line_item"]["renewal_from_id"]) && $invoice_item["sale_agreement_line_item"]["booking_line_item"]["niche"]["category"]["reference_value_text"] == "Standard"): ?>
                <?php
                  $total_amount_1 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
                ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php 
            if($total_amount_1 > 0){
              echo number_format($total_amount_1, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_2 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Niches"): ?>
              <?php if(empty($invoice_item["sale_agreement_line_item"]["booking_line_item"]["renewal_from_id"]) && $invoice_item["sale_agreement_line_item"]["booking_line_item"]["niche"]["category"]["reference_value_text"] == "Premium"): ?>
                <?php
                  $total_amount_2 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
                ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php 
            if($total_amount_2 > 0){
              echo number_format($total_amount_2, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_3 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Niches"): ?>
              <?php if(isset($invoice_item["sale_agreement_line_item"]["booking_line_item"]["renewal_from_id"])): ?>
                <?php
                  $total_amount_3 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
                ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php 
            if($total_amount_3 > 0){
              echo number_format($total_amount_3, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_4 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Additional Services"): ?>
              <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["other"]["service_name"] == "Urn"): ?>
                <?php
                  $total_amount_4 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
                ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_amount_4 > 0){
              echo number_format($total_amount_4, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_5 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Memorial Rooms"): ?>
              <?php
                $total_amount_5 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
              ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_amount_5 > 0){
              echo number_format($total_amount_5, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_6 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php 
            $arr_value = [
              'Exhumation Christian & Chinese Cemetery - Single Case',
              'Exhumation Lawn Cementry - Single Case'
            ]
          ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Additional Services"): ?>

              <?php if(in_array($invoice_item["sale_agreement_line_item"]["booking_line_item"]["serviceType"]["service_name"], $arr_value)): ?>
                <?php
                  $total_amount_6 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
                ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_amount_6 > 0){
              echo (string)number_format($total_amount_6, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_7 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php 
            $arr_value = [
              'Photo Local Colour 3" Oval',
              'Photo Local Colour 4" Oval',
              'Photo Local Couple Colour 4"',
              'Photo Local Couple Black & White 4"',
              'Photo Local Couple Black & White 5"',
              'Photo Local Colour 4.25" x 5.5"',
              'Photo Italian Black & White 3"',
              'Photo Italian Colour 3"',
              'Photo Italian Colour 4"',
              'Photo Italian Colour Rectangular 3.25" x 4"',
              'Photo Italian Couple Black & White 4.25" x 5.5"',
              'Photo Italian Couple Colour 3.25" x 4"',
              'Photo Italian Colour 3.5" x 4.75"',
              'Photo Italian Couple Black & White Postcard Size 5" x 7.5"',
              'Photo Italian Couple Colour Postcard Size 5" x 7.5"',
              'Photo Italian Oval Porcelain Colour 6" x 7.5"',
              'Photo Italian Black & White 6" x 8"',
              'Photo Italian Colour 6" x 8"',
              'Photo Italian Black & White 9.5" x 12"',
              'Photo Italian Oval Colour 9.5" x 12"'
            ]
          ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Additional Services"): ?>
              <?php if(in_array($invoice_item["sale_agreement_line_item"]["booking_line_item"]["serviceType"]["service_name"], $arr_value)): ?>
                <?php
                  $total_amount_7 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
                ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_amount_7 > 0){
              echo number_format($total_amount_7, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_8 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Additional Services"): ?>

              <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["serviceType"]["service_name"] == 'Flower Holder'): ?>
                <?php
                  $total_amount_8 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
                ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_amount_8 > 0){
              echo number_format($total_amount_8, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td style="text-align: left;">
        <?php $total_amount_9 = 0; ?>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php 
            $arr_value = [
              'Photo Local Colour 3" Oval',
              'Photo Local Colour 4" Oval',
              'Photo Local Couple Colour 4"',
              'Photo Local Couple Black & White 4"',
              'Photo Local Couple Black & White 5"',
              'Photo Local Colour 4.25" x 5.5"',
              'Photo Italian Black & White 3"',
              'Photo Italian Colour 3"',
              'Photo Italian Colour 4"',
              'Photo Italian Colour Rectangular 3.25" x 4"',
              'Photo Italian Couple Black & White 4.25" x 5.5"',
              'Photo Italian Couple Colour 3.25" x 4"',
              'Photo Italian Colour 3.5" x 4.75"',
              'Photo Italian Couple Black & White Postcard Size 5" x 7.5"',
              'Photo Italian Couple Colour Postcard Size 5" x 7.5"',
              'Photo Italian Oval Porcelain Colour 6" x 7.5"',
              'Photo Italian Black & White 6" x 8"',
              'Photo Italian Colour 6" x 8"',
              'Photo Italian Black & White 9.5" x 12"',
              'Photo Italian Oval Colour 9.5" x 12"',
              'Flower Holder',
              'Exhumation Christian & Chinese Cemetery - Single Case',
              'Exhumation Lawn Cementry - Single Case'
            ]
          ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]['reference_value_text'] == "Additional Services"): ?>
              <?php if(in_array($invoice_item["sale_agreement_line_item"]["booking_line_item"]["serviceType"]["service_name"], $arr_value)): ?>
                <?php $total_amount_9 = 0; ?>
              <?php else: ?>
                <?php $total_amount_9 += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] - (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]); ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_amount_9 > 0){
              echo number_format($total_amount_9, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td>
        <?php $total_gst = $items["total_tax_amount"]; ?>
        <?php
          if($items["total_tax_amount"] > 0){
            echo number_format((float)$items["total_tax_amount"], 2, '.', ',');
          }else{
            echo "";
          }
        ?>
      </td>
      <td>
      <?php $total_discount = 0; ?>
      <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php
              $total_discount += (float)($invoice_item["sale_agreement_line_item"]["booking_line_item"]["discount_amount"]);
            ?>
          <?php endforeach; ?>
          <?php
            if($total_discount > 0){
              echo number_format($total_discount, 2, '.', ',');
            }else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <?php
        $sum_total += $total;
        $sum_total_amount_1 += $total_amount_1;
        $sum_total_amount_2 += $total_amount_2;
        $sum_total_amount_3 += $total_amount_3;
        $sum_total_amount_4 += $total_amount_4;
        $sum_total_amount_5 += $total_amount_5;
        $sum_total_amount_6 += $total_amount_6;
        $sum_total_amount_7 += $total_amount_7;
        $sum_total_amount_8 += $total_amount_8;
        $sum_total_amount_9 += $total_amount_9;
        $sum_total_gst += $total_gst;
        $sum_total_discount += $total_discount;
      ?>
    </tr>
    <?php endforeach;?>
    <tr>
      <td style="background: #d9d9d9">Total</td>
      <td style="background: #d9d9d9"></td>
      <td style="background: #d9d9d9"></td>
      <td style="background: #d9d9d9"></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_1, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_2, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_3, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_4, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_5, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_6, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_7, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_8, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_amount_9, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_gst, 2, '.', ','); ?></td>
      <td style="background: #d9d9d9"><?php echo number_format($sum_total_discount, 2, '.', ','); ?></td>
    </tr>
  </tbody>
</table>