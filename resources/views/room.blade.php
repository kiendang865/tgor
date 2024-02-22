<?php use Carbon\Carbon; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Room</title>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
  <style>
    @font-face {
      font-family:'stea';
      src: url('{!! asset('/fonts/STEA_FONT.ttf') !!}');
      font-style: normal;
      font-weight: normal;
    }
    body {
      /* font-family: "calibrib"; */
      margin: 0;
    }
    .mySlides {display: none;}
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
      color: #FFFFFF;
      font-size: 36px;
      text-transform: capitalize;
      line-height: 1.6;
      font-weight: bold;
    }
    .flex {
      display: flex;
      /* justify-content: center; */
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
    .slideshow-container {
      position: relative;
      margin: auto;
      width: 100%;
    }

    /* Caption text */
    .text {
      color: #f2f2f2;
      font-size: 15px;
      padding: 8px 12px;
      position: absolute;
      bottom: 8px;
      width: 100%;
      text-align: center;
    }

    /* Number text (1/3 etc) */
    .numbertext {
      color: #f2f2f2;
      font-size: 12px;
      padding: 8px 12px;
      position: absolute;
      top: 0;
    }

    .active {
      background-color: #717171;
    }

    /* Fading animation */
    .fade {
      -webkit-animation-name: fade;
      -webkit-animation-duration: 1.5s;
      animation-name: fade;
      animation-duration: 1.5s;
    }

    @-webkit-keyframes fade {
      from {opacity: .5} 
      to {opacity: 1}
    }

    @keyframes fade {
      from {opacity: .5} 
      to {opacity: 1}
    }

    /* On smaller screens, decrease text size */
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
    @media only screen and (max-width: 300px) {
      .text {font-size: 11px}
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
        @if(count($booking_room))
        <div class="slideshow-container">
            @foreach($booking_room as $item)
            <div class="mySlides fade">
                <div class="wrap">
                    <div class="title">Celebrating</div>
                    <div class="subtitle">The life of</div>
                    <div class="name" style="letter-spacing: 8px;">
                        {{ $item->departed_full_name }}
                    </div>
                    <div class="text-normal">
                        who was called home to be with The Lord on
                    </div>
                    <div class="text-large">
                        @if($item->date_of_death)
                        {{Carbon::parse($item->date_of_death)->format('d F Y')}}
                        @endif
                    </div>
                    <div class="name">
                        {{ $item->event->reference_value_text }} @ {{ $item->room->room_no }}
                    </div>
                    <div class="info-wrap">
                        <div class="mr-5">
                        @if($item->tv_wake_service)
                        <div class="text-normal text-left mt-20px">Wake Service</div>
                        @endif
                        @if($item->tv_encoffin_service)
                        <div class="text-normal text-left mt-20px">Encoffin Service</div>
                        @endif
                        @if($item->tv_cottage_leaves)
                        <div class="text-normal text-left mt-20px">Cortege Leaves</div>
                        @endif
                        @if($item->book_funeral_director == 'Yes')
                            @if($item->funeral_director)
                            <div class="text-normal text-left mt-20px">Funeral Director</div>
                            @endif
                        @endif
                        </div>
                        <div class="text-right-width">
                        @if($item->tv_wake_service)
                        <div class="text-normal text-left mt-20px">{{ $item->tv_wake_service }}</div>
                        @endif
                        @if($item->tv_encoffin_service)
                        <div class="text-normal text-left mt-20px">{{ $item->tv_encoffin_service }}</div>
                        @endif
                        @if($item->tv_cottage_leaves)
                        <div class="text-normal text-left mt-20px">{{ $item->tv_cottage_leaves }}</div>
                        @endif
                        @if($item->book_funeral_director == 'Yes')
                            @if($item->funeral_director)
                            <div class="text-normal text-left mt-20px">
                            {{ $item->funeral_director->company_name }}
                            </div>
                            @endif
                        @endif
                        </div>
                    </div>
                    <div class="avatar">
                        @if($item->tv_photo_of_departed)
                        <img src="{{$item->tv_photo_of_departed}}" class="img-size"/>
                        @else
                        <img src="{{ url('images/no-image-available.jpg') }}" class="img-size"/>
                        @endif
                        <div class="text-normal" style="margin-top: 10px;">
                        @if(isset($item->tv_life_years))
                            {{ $item->tv_life_years }}
                        @endif
                        </div>
                    </div>.
                </div>
            </div>
            @endforeach
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
  <script>
    var slideIndex = 0;
    showSlides();

    function showSlides() {
    var i;
    var slides = document.getElementsByClassName("mySlides");
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1}
    slides[slideIndex-1].style.display = "block";  
    setTimeout(showSlides, 4000);
    }
  </script>
</body>
</html>