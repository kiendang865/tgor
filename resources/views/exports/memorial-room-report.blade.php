<?php use Carbon\Carbon;?>
<table>
  <thead>
    <tr>
      <th style="font-size: 12px; font-weight: bold;">Date</th>
      <th style="font-size: 12px; font-weight: bold;">Full name (Applicant)</th>
      <th style="font-size: 12px; font-weight: bold;">Full name (Deceased)</th>
      <th style="font-size: 12px; font-weight: bold;">Memorial Rooms reference number</th>
      <th style="font-size: 12px; font-weight: bold;">Check in date</th>
      <th style="font-size: 12px; font-weight: bold;">Check out date</th>
      <th style="font-size: 12px; font-weight: bold;">Payment type</th>
      <th style="font-size: 12px; font-weight: bold;">Receipt number</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice amount</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice GST</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice total</th>
      <th style="font-size: 12px; font-weight: bold;">Funeral Director name</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($collections as $items): ?>
    <tr>
      <td><?php echo Carbon::parse($items["payment_date"])->format('d-M'); ?></td>
      <td><?php echo $items["client"]["display_name"] ?></td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if(isset($invoice_item["sale_agreement_line_item"]["booking_line_item"]["departed_full_name"])): ?>
              <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["departed_full_name"] ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Memorial Rooms"): ?>
              <?php if(intval($key)+1 > 1): ?>
                <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_no"] ?>
              <?php else: ?>
                <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_no"] ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) :?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Memorial Rooms"): ?>
              <?php if(intval($key)+1 > 1): ?>
                <?php echo ', '.Carbon::parse($invoice_item["sale_agreement_line_item"]["booking_line_item"]["check_in_date"])->format('d/m/Y'); ?>
              <?php else: ?>
                <?php echo Carbon::parse($invoice_item["sale_agreement_line_item"]["booking_line_item"]["check_in_date"])->format('d/m/Y'); ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td>
      <?php if(isset($items["invoice"]["invoice_line_item"])) :?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Memorial Rooms"): ?>
              <?php if(intval($key)+1 > 1): ?>
                <?php echo ', '.Carbon::parse($invoice_item["sale_agreement_line_item"]["booking_line_item"]["check_out_date"])->format('d/m/Y'); ?>
              <?php else: ?>
                <?php echo Carbon::parse($invoice_item["sale_agreement_line_item"]["booking_line_item"]["check_out_date"])->format('d/m/Y'); ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td><?php echo $items["payment_mode"]["reference_value_text"] ?></td>
      <td><?php echo $items["payment_no"] ?></td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) :?>
        <?php $total_amount = 0 ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Memorial Rooms"): ?>
              <?php $total_amount += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_amount > 0){
              echo number_format($total_amount, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) :?>
        <?php $total_tax_amount = 0 ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Memorial Rooms"): ?>
              <?php $total_tax_amount += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["tax_amount"] ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total_tax_amount > 0){
              echo number_format($total_tax_amount, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) :?>
        <?php $total = 0 ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Memorial Rooms"): ?>
              <?php $total += (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["amount"] + (float)$invoice_item["sale_agreement_line_item"]["booking_line_item"]["tax_amount"] ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php
            if($total > 0){
              echo number_format($total, 2, '.', ',');
            }
            else{
              echo "";
            }
          ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) :?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["funeral_director"]["company_name"] ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>