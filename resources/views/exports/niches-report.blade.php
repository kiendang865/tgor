<?php use Carbon\Carbon;?>
<table>
  <thead>
    <tr>
      <th style="font-size: 12px; font-weight: bold;">Date</th>
      <th style="font-size: 12px; font-weight: bold;">Full name (Applicant)</th>
      <th style="font-size: 12px; font-weight: bold;">Full name (Occupant)</th>
      <th style="font-size: 12px; font-weight: bold;">Niche reference number</th>
      <th style="font-size: 12px; font-weight: bold;">Application date</th>
      <th style="font-size: 12px; font-weight: bold;">Payment type</th>
      <th style="font-size: 12px; font-weight: bold;">Receipt number</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice amount</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice GST</th>
      <th style="font-size: 12px; font-weight: bold;">Invoice total</th>
      <th style="font-size: 12px; font-weight: bold;">Referred by</th>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Niches"): ?>
              <?php if(intval($key)+1 > 1): ?>
                <?php if(count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == $key && count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == 1): ?>
                  <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"][0]["full_name"] ?>
                <?php endif; ?>
                <?php if(count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == $key+1 && count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == 2 ): ?>
                  <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"][0]["full_name"].", ". $invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"][1]["full_name"] ?>
                <?php endif; ?>
              <?php else: ?>
                <?php if(count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == $key+1 && count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == 1): ?>
                  <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"][0]["full_name"] ?>
                <?php endif; ?>
                <?php if(count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == $key+2 && count($invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"]) == 2 ): ?>
                  <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"][0]["full_name"].", ". $invoice_item["sale_agreement_line_item"]["booking_line_item"]["information"][1]["full_name"] ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) : ?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Niches"): ?>
              <?php if(intval($key)+1 > 1): ?>
                <?php echo ", ".$invoice_item["sale_agreement_line_item"]["booking_line_item"]["niche"]["reference_no"] ?>
              <?php else: ?>
                <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["niche"]["reference_no"] ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if(isset($items["invoice"]["invoice_line_item"])) :?>
          <?php foreach($items["invoice"]["invoice_line_item"] as $key => $invoice_item): ?>
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Niches"): ?>
              <?php if(intval($key)+1 > 1): ?>
                <?php echo ', '.Carbon::parse($invoice_item["sale_agreement_line_item"]["booking_line_item"]["application_date"])->format('d/m/Y'); ?>
              <?php else: ?>
                <?php echo Carbon::parse($invoice_item["sale_agreement_line_item"]["booking_line_item"]["application_date"])->format('d/m/Y'); ?>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Niches"): ?>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Niches"): ?>
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
            <?php if($invoice_item["sale_agreement_line_item"]["booking_line_item"]["booking_type"]["reference_value_text"] == "Niches"): ?>
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
            <?php echo $invoice_item["sale_agreement_line_item"]["booking_line_item"]["referral_name"] ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>