<?php use Carbon\Carbon;?>
<table>
  <thead>
    <tr>
      <th style="font-size: 12px; font-weight: bold;">Date</th>
      <th style="font-size: 12px; font-weight: bold;">Full name (Applicant)</th>
      <th style="font-size: 12px; font-weight: bold;">Additional Service reference number</th>
      <th style="font-size: 12px; font-weight: bold;">Payment type</th>
      <th style="font-size: 12px; font-weight: bold;">Receipt number</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice amount</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice GST</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice total</th>
      <th style="font-size: 12px; font-weight: bold;">Service Type</th>
      <th style="font-size: 12px; font-weight: bold;">Description</th>
      <th style="font-size: 12px; font-weight: bold;">Contractor</th>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Additional Services"): ?>
              <?php if(intval($key)+1 > 1): ?>
                <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_no"] ?>
              <?php else: ?>
                <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_no"] ?>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Additional Services"): ?>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Additional Services"): ?>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Additional Services"): ?>
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
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if(intval($key)+1 > 1): ?>
              <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["other"]["service_name"] ?>
            <?php else: ?>
              <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["other"]["service_name"] ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if(intval($key)+1 > 1): ?>
              <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["serviceType"]["service_name"] ?>
            <?php else: ?>
              <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["serviceType"]["service_name"] ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if(intval($key)+1 > 1): ?>
              <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["contractor"]["company_name"] ?>
            <?php else: ?>
              <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["contractor"]["company_name"] ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>