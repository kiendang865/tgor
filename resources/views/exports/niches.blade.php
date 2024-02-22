<table>
  <thead>
    <tr>
      <th style="font-size: 12px; font-weight: bold;">ID</th>
      <th style="font-size: 12px; font-weight: bold;">Type</th>
      <th style="font-size: 12px; font-weight: bold;">Location</th>
      <th style="font-size: 12px; font-weight: bold;">Status</th>
      <th style="font-size: 12px; font-weight: bold;">Occupant Name</th>
      <th style="font-size: 12px; font-weight: bold;">Duration Of Lease</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($niches as $items): ?>
    <tr>
      <td><?php echo $items["reference_no"] ?></td>
      <td><?php echo $items["type"]["reference_value_text"] ?></td>
      <td><?php echo $items["full_location"] ?></td>
      <td><?php echo $items["status"] ?></td>
      <?php if(isset($items["booking_line_item"]["information"]) && is_array($items["booking_line_item"]["information"])): ?>
        <?php if(!empty($items["booking_line_item"]["information"])): ?>
          <?php if(count($items["booking_line_item"]["information"]) > 1): ?>
            <td><?php echo $items["booking_line_item"]["information"][0]["full_name"].", ". $items["booking_line_item"]["information"][1]["full_name"] ?></td>
          <?php else: ?>
            <td><?php echo $items["booking_line_item"]["information"][0]["full_name"] ?></td>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif;?>
      <?php if(isset($items["booking_line_item"]["duration_of_lease"])): ?>
        <td><?php echo $items["booking_line_item"]["duration_of_lease"] ?></td>
      <?php endif;?>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>