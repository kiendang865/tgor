<?php use Carbon\Carbon; ?>
<table>
  <thead>
    <tr>
      <th style="font-size: 12px; font-weight: bold;">Booked By</th>
      <th style="font-size: 12px; font-weight: bold;">Checked In</th>
      <th style="font-size: 12px; font-weight: bold;">Checked Out</th>
      <th style="font-size: 12px; font-weight: bold;">Funeral Director</th>
    </tr>
  </thead>
  <tbody>
    @foreach($booking_line_items as $item)
    <tr>
      <td>{{ $item->client->display_name }}</td>
      @if($item->check_in_date)
        <td>
          {{ Carbon::parse($item->check_in_date)->format('d/m/Y') }}
          @if($item->check_in_time)
            {{Carbon::parse($item->check_in_time)->format('H:i')}}
          @endif
        </td>
      @else
        <td></td>
      @endif
      @if($item->check_out_date)
        <td>
          {{ Carbon::parse($item->check_out_date)->format('d/m/Y') }}
          @if($item->check_out_time)
            {{ Carbon::parse($item->check_out_time)->format('H:i') }}
          @endif
        </td>
      @else
        <td></td>
      @endif
      @if($item->funeral_director && $item->funeral_director->company_name)
        <td>{{ $item->funeral_director->company_name}}</td>
      @else
        <td></td>
      @endif
    </tr>
    @endforeach
  </tbody>
</table>