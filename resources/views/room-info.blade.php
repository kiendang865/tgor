<?php use Carbon\Carbon; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Room</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
  <style>
    @font-face {
      font-family:'stea';
      src: url('{!! asset('/fonts/STEA_FONT.ttf') !!}');
      font-style: normal;
      font-weight: normal;
    }
    body {
      margin: 0;
    }
    .title{
      font-family: "stea", serif;
      font-size: 90px;
      color: #FFFFFF;
    }
    .subtitle{
      font-family: 'EB Garamond', serif;
      font-size: 32px;
      color: #FFFFFF;
      text-transform: uppercase;
      margin: 10px 0px;
      letter-spacing: 8px;
    }
    .wrapper {
      width: 100%;
      height: 100vh;
    }
    .wrapper .background {
      background-image: url('/images/Templkate_for_Obit.png');
      height: 100%;
      background-repeat: no-repeat;
      background-size: cover;
    }
    .wrapper .center {
      height: 100%;
      text-align: center;
      position: relative;
    }
    .wrapper .name {
      font-family: 'EB Garamond', serif;
      color: #FFFFFF;
      font-size: 48px;
      text-transform: capitalize;
      line-height: 2;
    }
    .wrapper .text-normal {
      font-family: 'EB Garamond', serif;
      font-size: 28px;
      color: #FFFFFF;
    }
    .wrapper .text-large {
      font-family: 'EB Garamond', serif;
      color: #FFFFFF;
      font-size: 36px;
      text-transform: capitalize;
      line-height: 1.6;
    }
    .flex {
      display: flex;
      font-size: 23px;
      color: white;
      font-weight: bold;
      margin: 20px 0;
    }
    .d-flex{
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .info-wrap {
      display: flex;
      justify-content: center;
    }
    .text-left{
      text-align: left;
    }
    .mr-5{
      margin-right: 5%;
    }
    .mt-20px{
      margin-top: 20px;
    }
    .avatar{
      position: absolute;
      right: 10%;
      top: 30%;
    }
    .wrap{
      margin-right: 60px;
    }
    .img-size{
      width: 220px;
      height: 277px;
    }
    .text-right-width{
      max-width: 650px;
    }
    @media (min-width: 3839px){
      .title{
        font-size: 200px;
      }
      .subtitle{
        font-size: 80px;
        margin: 40px 0px;
        letter-spacing: 4px;
      }
      .img-size{
        width: 440px;
        height: 510px;
      }
      .wrapper .text-normal {
        font-size: 60px;
        max-width: 1200px;
        margin: auto;
      }
      .wrapper .name {
        font-size: 80px;
      }
    }
    @media (max-width: 1440px){
      .avatar{
        right: 5%;
      }
    }
    @media (max-width: 1280px){
      .wrapper .name {
        font-size: 42px;
      }
      .wrapper .text-normal {
        font-size: 22px;
      }
      .wrap{
        margin-right: 100px;
      }
    }
    @media (max-width: 1080px){
      .title{
        font-family: "stea", serif;
        font-size: 70px;
        color: #FFFFFF;
      }
      .subtitle{
        font-family: 'EB Garamond', serif;
        font-size: 22px;
        color: #FFFFFF;
        text-transform: uppercase;
        margin: 10px 0px;
        letter-spacing: 8px;
      }
      .wrapper .name {
        font-size: 35px;
      }
      .img-size {
        width: 185px;
        height: 225px;
      }
      .text-right-width{
        max-width: 450px;
      }
    }
    @media (max-width: 520px){
      .title{
        font-size: 24px;
      }
      .subtitle{
        font-size: 14px;
        margin: 4px 0px;
        letter-spacing: 4px;
      }
      .wrapper .name {
        font-size: 12px;
      }
      .wrapper .text-normal {
        font-size: 10px;
        max-width: 175px;
        margin: auto;
      }
      .wrap{
        margin-right: 20px;
      }
      .img-size{
        width: 42px;
        height: 52px;
      }
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="background">
      <div class="center d-flex">
        @if(!empty($booking_room))
        <div class="wrap">
          <div class="title">Celebrating</div>
          <div class="subtitle">The life of</div>
          <div class="name" style="letter-spacing: 8px;">
            {{ $booking_room->departed_full_name }}
          </div>
          <div class="text-normal">
            who was called home to be with The Lord on
          </div>
          <div class="text-large">
            @if($booking_room->date_of_death)
              {{Carbon::parse($booking_room->date_of_death)->format('d F Y')}}
            @endif
          </div>
          <div class="name">
            {{ $booking_room->event->reference_value_text }} @ {{ $booking_room->room->room_no }}
          </div>
          <div class="info-wrap">
            <div class="mr-5">
              @if($booking_room->tv_wake_service)
              <div class="text-normal text-left mt-20px">Wake Service</div>
              @endif
              @if($booking_room->tv_encoffin_service)
              <div class="text-normal text-left mt-20px">Encoffin Service</div>
              @endif
              @if($booking_room->tv_cottage_leaves)
              <div class="text-normal text-left mt-20px">Cortege Leaves</div>
              @endif
              @if($booking_room->book_funeral_director == 'Yes')
                @if($booking_room->funeral_director)
                <div class="text-normal text-left mt-20px">Funeral Director</div>
                @endif
              @endif
            </div>
            <div class="text-right-width">
              @if($booking_room->tv_wake_service)
              <div class="text-normal text-left mt-20px">{{ $booking_room->tv_wake_service }}</div>
              @endif
              @if($booking_room->tv_encoffin_service)
              <div class="text-normal text-left mt-20px">{{ $booking_room->tv_encoffin_service }}</div>
              @endif
              @if($booking_room->tv_cottage_leaves)
              <div class="text-normal text-left mt-20px">{{ $booking_room->tv_cottage_leaves }}</div>
              @endif
              @if($booking_room->book_funeral_director == 'Yes')
                @if($booking_room->funeral_director)
                <div class="text-normal text-left mt-20px">
                  {{ $booking_room->funeral_director->company_name }}
                </div>
                @endif
              @endif
            </div>
          </div>
          <div class="avatar">
            @if($booking_room->tv_photo_of_departed)
            <img src="{{$booking_room->tv_photo_of_departed}}" class="img-size"/>
            @else
            <img src="{{ url('images/no-image-available.jpg') }}" class="img-size"/>
            @endif
            <div class="text-normal" style="margin-top: 10px;">
              @if(isset($booking_room->tv_life_years))
                {{ $booking_room->tv_life_years }}
              @endif
            </div>
          </div>
        </div>
        @else
        <div class="wrap">
          <div class="title">
            Currently, no event
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</body>

</html>