@extends('layouts.absensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-success text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Absensi MW9</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <style>
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
        }

        #map { height: 200px; }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <input type="hidden" id="lokasi">
            <div class="webcam-capture">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @if ($cek > 0)
            <button id="takeabsen" class="btn btn-danger btn-block">
                <ion-icon name="camera"></ion-icon>
                Absen Pulang
            </button>
            @else
            <button id="takeabsen" class="btn btn-success btn-block">
                <ion-icon name="camera"></ion-icon>
                Absen Masuk
            </button>
            @endif 
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>

    <audio id="notifikasi_in">
        <source src="{{ asset('assets/audio/tuturu.mp3') }}" type="audio/mpeg">
    </audio>
@endsection

@push('myscript')
    <script>
        var notifikasi_in = document.getElementById('notifikasi_in');
        var notifikasi_out = document.getElementById('notifikasi_out');
        Webcam.set({
            height:488,
            width:640,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        Webcam.attach('.webcam-capture');

        var lokasi = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position){
            lokasi.value = position.coords.latitude + "," + position.coords.longitude;
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
            var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
            var circle = L.circle([position.coords.latitude, position.coords.longitude], {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 20
}).addTo(map);
        }

        function errorCallback() {

        }

        $("#takeabsen").click(function(e) {
            Webcam.snap(function(uri){
                image = uri;
            });
            var lokasi = $("#lokasi").val();
            $.ajax({
                type:'POST',
                url:'/absensi/store',
                data:{
                    _token:"{{ csrf_token() }}",
                    image:image,
                    lokasi:lokasi
                },
                cache:false,
                success:function(respond) {
                    var status = respond.split("|");
                    if(status[0] == "success") {
                        if(status[2] == "in") {
                            notifikasi_in.play();
                        }else{
                            notifikasi_in.play();
                        }
                        Swal.fire({
                            title: 'Berhasil!',
                            text: status[1],
                            icon: 'success',
                        });
                        setTimeout("location.href='/dashboard'", 3000);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal Absen, Silahkan Hubungi Operator!',
                            icon: 'error',
                        });
                    }
                }
            })
        });

    </script>
@endpush