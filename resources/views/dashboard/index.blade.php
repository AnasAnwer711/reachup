@extends('layouts.app')

@section('css')

    <style>
        .selectedPeriod{
            border: 1px solid #6f6f6f;
            font-weight: 600;
        }
        .comp1,.comp2,.comp3{
          background-color: #eb740e;
              opacity : 0.5
        }
        .cstActive1,.cstActive2,.cstActive3{
              color: #fff;
              background-color: #eb740e;
              background-image: none;
              border-color: #01549b;
              opacity : 1 !important;
              font-weight: 600;
        }
        

    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1>Dashboard</h1>
            <ol class="breadcrumb" style="margin-top: -13px;">
                <li><a href="https://console.firebase.google.com/" target="_blank" class="btn btn-outline-warning" style="color: #000"> <img src="{{ asset('firebase-png.png') }}" alt="firebase" height="25" width="20"> Firebase Console</a></li>
                {{-- <li>Dashboard</li> --}}
                {{-- <li><i class="fa fa-angle-right"></i> Dashboard</li> --}}
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-darkblue"> <span class="info-box-icon bg-transparent"><i
                                class="ti-stats-up text-white"></i></span>
                        <div class="info-box-content">
                            <h6 class="info-box-text text-white">Reachups</h6>
                            <h1 class="text-white">{{ $reachup_count }}</h1>
                            {{-- <span class="progress-description text-white"> 70% Increase in 30 Days </span> --}}
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-green text-white"> <span class="info-box-icon bg-transparent"><i
                                class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <h6 class="info-box-text text-white">User</h6>
                            <h1 class="text-white">{{ $user_count }}</h1>
                            {{-- <span class="progress-description text-white"> 45% Increase in 30 Days </span> --}}
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-aqua"> <span class="info-box-icon bg-transparent"><i
                                class="fa fa-user-secret"></i></span>
                        <div class="info-box-content">
                            <h6 class="info-box-text text-white">Active Professionals</h6>
                            <h1 class="text-white">{{ $professional_count }}</h1>
                            {{-- <span class="progress-description text-white"> 50% Increase in 30 Days </span> --}}
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-primary"> <span class="info-box-icon bg-transparent"><i
                                class="fa fa-money" style="color: white"></i></span>
                        <div class="info-box-content">
                            <h6 class="info-box-text text-white">Total Earned Money</h6>
                            <h1 class="text-white">{{ number_format($total_earned, 2) }}</h1>
                            {{-- <span class="progress-description text-white"> 50% Increase in 30 Days </span> --}}
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="info-box">
                        <div class="col-12">
                            <div class="d-flex flex-wrap">
                                <div>
                                    <h5>Total ReachUp Earning</h5>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="ct-line-chart" style="height:350px;"></div> --}}
                        <div class="row text-center d-block" style="height:350px;">
                          <div class="chartreport1">
                            <canvas id="earningChart" class="rounded shadow"></canvas>
                          </div>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp1" data-period="Month" title="This Month">M</button>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp1" data-period="Quarter" title="This Quarter">Q</button>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp1 cstActive1" data-period="Year" title="This Year">Y</button>
                        </div>
                      </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="info-box">
                        <div class="col-12">
                            <div class="d-flex flex-wrap">
                                <div>
                                    <h5>Number of Reachups</h5>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center d-block" style="height:350px;">
                          <div class="chartreport3">
                            <canvas id="reachupChart" class="rounded shadow"></canvas>
                          </div>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp3" data-period="Month" title="This Month">M</button>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp3" data-period="Quarter" title="This Quarter">Q</button>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp3 cstActive3" data-period="Year" title="This Year">Y</button>
                        </div>
                    </div>
                </div>
            <div class="col-lg-4">
                    <div class="info-box">
                        <div class="col-12">
                            <div class="d-flex flex-wrap">
                                <div>
                                    <h5>Reachup Stats</h5>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center d-block" style="height:350px;">
                          <div class="chartreport2">
                            <canvas id="reachupStatsChart" class="rounded shadow"></canvas>
                          </div>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp2" data-period="Month" title="This Month">M</button>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp2" data-period="Quarter" title="This Quarter">Q</button>
                            <button type="button" class="btn btn-primary btn-xs btn-rounded comp2 cstActive2" data-period="Year" title="This Year">Y</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- /.content -->
    </div>

@endsection


@section('javascript')
    <script>
        $(document).ready(function() {
            callEarningChart("{{ route('earningchart') }}", "Year");
            callReachupStatsChart("{{ route('reachupstatschart') }}", "Year");
            callReachupChart("{{ route('reachupchart') }}", "Year");
        });

        $("#drawing button").click(function(e) {
          var isActive = $(this).hasClass('active');
          $('.active').removeClass('active');
          if (!isActive) {
            $(this).addClass('active');
          }
        });

        $(".comp1").click(function() {
          var isActive = $(this).hasClass('cstActive1');
          $('.cstActive1').removeClass('cstActive1');
          $(this).addClass('cstActive1');


          var period = $(this).data('period');
          console.log(period);

          if(period == 'Month'){
            callEarningChart("{{ route('earningchart','period=Month') }}");
          } else if (period == 'Quarter') {

            callEarningChart("{{ route('earningchart','period=Quarter') }}");
          } else {
          callEarningChart("{{ route('earningchart','period=Year') }}");

          }

        });
         $(".comp2").click(function() {
          var isActive = $(this).hasClass('cstActive2');
          $('.cstActive2').removeClass('cstActive2');
          $(this).addClass('cstActive2');
          var period = $(this).data('period');
          console.log(period);

          if(period == 'Month'){
            callReachupStatsChart("{{ route('reachupstatschart','period=Month') }}");
          } else if (period == 'Quarter') {

            callReachupStatsChart("{{ route('reachupstatschart','period=Quarter') }}");
          } else {
          callReachupStatsChart("{{ route('reachupstatschart','period=Year') }}");

          }

        });

        $(".comp3").click(function() {
          var isActive = $(this).hasClass('cstActive3');
          $('.cstActive3').removeClass('cstActive3');
          $(this).addClass('cstActive3');
          var period = $(this).data('period');
          console.log(period);

          if(period == 'Month'){
            callReachupChart("{{ route('reachupchart','period=Month') }}");
          } else if (period == 'Quarter') {

            callReachupChart("{{ route('reachupchart','period=Quarter') }}");
          } else {
          callReachupChart("{{ route('reachupchart','period=Year') }}");

          }

        });

        function abbreviateNumber(value) {
            let newValue = value;
            const suffixes = ["", "K", "M", "B","T"];
            let suffixNum = 0;
            while (newValue >= 1000) {
                newValue /= 1000;
                // suffixNum++;
            }

            // newValue = newValue.toPrecision(3);

            // newValue += suffixes[suffixNum];
            return newValue;
        }


        function callEarningChart(url,period) {
            console.log(url);
            $.get(url, function(response) {
                var labels = response.labels;
                var current = response.current;
                var previous = response.previous;
                
                $("canvas#earningChart").remove();
                $("div.chartreport1").append('<canvas id="earningChart" class="animated fadeIn" height="300px"></canvas>');
                // var ctx = document.getElementById("chartreport").getContext("2d");

                var ctx = document.getElementById('earningChart').getContext('2d');

                // myChart = new Chart(grapharea, { type: 'radar', data: barData, options: barOptions });
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'bar',
                    // The data for our dataset
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Current Year',
                                backgroundColor: '#f39c12',
                                data: current,
                            },
                            {
                                label: 'Previous Year',
                                backgroundColor: '#2f3742',
                                data: previous,
                            },
                        ]
                    },
                    // Configuration options go here
                    options: {
                      maintainAspectRatio: false,
                      responsive: true,
                        scales: {
                          y: {
                            title: {
                              display: true,
                              text: 'Earnings'
                            },
                            ticks: {
                              // forces step size to be 50 units
                            //   stepSize: 1
                              precision: 0
                            },
                            callback: function(value) {
                                        if (value % 1 === 0) {
                                            return value;
                                        }
                                    }
                          }
                            // yAxes: [{
                            //     ticks: {
                            //         beginAtZero: true,
                            //         precision:0,
                            //         stepSize: 1,
                            //         callback: function(value) {
                            //             if (value % 1 === 0) {
                            //                 return value;
                            //             }
                            //         }
                            //     },
                            //     scaleLabel: {
                            //         display: true
                            //     }
                            // }],
                        },
                        legend: {
                            labels: {
                                // This more specific font property overrides the global property
                                fontColor: '#122C4B',
                                fontFamily: "'Muli', sans-serif",
                                padding: 25,
                                boxWidth: 25,
                                fontSize: 14,
                            }
                        },
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 0,
                                bottom: 10
                            }
                        }
                    }
                });
                //  chart.render();
            });
        }

        function getRandomRgb() {
      var num = Math.round(0xffffff * Math.random());
      var r = num >> 16;
      var g = num >> 8 & 255;
      var b = num & 255;
      return 'rgb(' + r + ', ' + g + ', ' + b + ')';
   }

        function callReachupStatsChart(url,period) {
            console.log(url);
            $.get(url, function(response) {
                var labels = response.labels;
                var current = response.current;
                var previous = response.previous;


                // if(chart != undefined){

               
                // }
                $("canvas#reachupStatsChart").remove();
                $("div.chartreport2").append('<canvas id="reachupStatsChart" class="animated fadeIn" height="300px"></canvas>');
                // var ctx = document.getElementById("chartreport").getContext("2d");

                var ctx = document.getElementById('reachupStatsChart').getContext('2d');
                var backgroundColor = [];
                var newColor0 = [];
                var newColor1 = [];
                for (var i = 0; i < labels.length; i++) {
                    backgroundColor.push(getRandomRgb());
                }
                for (let index = 0; index < backgroundColor.length; index++) {
                    const element = backgroundColor[index];
                    // console.log(element.length - 1);
                    last_length = element.length - 5;
                    var newColor = backgroundColor[index].substr(4,last_length);
                    newColor0.push('rgb(' + newColor+ ', 0.8)');
                    newColor1.push('rgb(' + newColor+ ', 0.5)');
                }
                current.backgroundColor = newColor0;
                previous.backgroundColor = newColor1;
                // myChart = new Chart(grapharea, { type: 'radar', data: barData, options: barOptions });
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'doughnut',
                    // The data for our dataset
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Current Year',
                                backgroundColor: newColor0,
                                data: current,
                            },
                            {
                                label: 'Previous Year',
                                backgroundColor: newColor1,
                                data: previous,
                            },
                        ]
                    },
                    // Configuration options go here
                    options: {
                      maintainAspectRatio: false,
                      responsive: true,
                        scales: {
                          // y: {
                          //   title: {
                          //     display: true,
                          //     text: 'Reachups'
                          //   },
                          //   ticks: {
                          //     // forces step size to be 50 units
                          //     stepSize: 1
                          //   }
                          // }
                            // yAxes: [{
                            //     ticks: {
                            //         beginAtZero: true,
                            //         precision:0,
                            //         stepSize: 1,
                            //         callback: function(value) {
                            //             if (value % 1 === 0) {
                            //                 return value;
                            //             }
                            //         }
                            //     },
                            //     scaleLabel: {
                            //         display: true
                            //     }
                            // }],
                        },
                        legend: {
                            labels: {
                                // This more specific font property overrides the global property
                                fontColor: '#122C4B',
                                fontFamily: "'Muli', sans-serif",
                                padding: 25,
                                boxWidth: 25,
                                fontSize: 14,
                            }
                        },
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 0,
                                bottom: 10
                            }
                        }
                    }
                });
                //  chart.render();
            });
        }

        function callReachupChart(url,period) {
            console.log(url);
            $.get(url, function(response) {
                var labels = response.labels;
                var current = response.current;
                var previous = response.previous;


                // if(chart != undefined){

               
                // }
                $("canvas#reachupChart").remove();
                $("div.chartreport3").append('<canvas id="reachupChart" class="animated fadeIn" height="300px"></canvas>');
                // var ctx = document.getElementById("chartreport").getContext("2d");

                var ctx = document.getElementById('reachupChart').getContext('2d');

                // myChart = new Chart(grapharea, { type: 'radar', data: barData, options: barOptions });
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'bar',
                    // The data for our dataset
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Current Year',
                                backgroundColor: '#f39c12',
                                data: current,
                            },
                            {
                                label: 'Previous Year',
                                backgroundColor: '#2f3742',
                                data: previous,
                            },
                        ]
                    },
                    // Configuration options go here
                    options: {
                      maintainAspectRatio: false,
                      responsive: true,
                        scales: {
                          y: {
                            title: {
                              display: true,
                              text: 'Reachups'
                            },
                            ticks: {
                              // forces step size to be 50 units
                            //   stepSize: 1
                              precision: 0
                            }
                          }
                            // yAxes: [{
                            //     ticks: {
                            //         beginAtZero: true,
                            //         precision:0,
                            //         stepSize: 1,
                            //         callback: function(value) {
                            //             if (value % 1 === 0) {
                            //                 return value;
                            //             }
                            //         }
                            //     },
                            //     scaleLabel: {
                            //         display: true
                            //     }
                            // }],
                        },
                        legend: {
                            labels: {
                                // This more specific font property overrides the global property
                                fontColor: '#122C4B',
                                fontFamily: "'Muli', sans-serif",
                                padding: 25,
                                boxWidth: 25,
                                fontSize: 14,
                            }
                        },
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 0,
                                bottom: 10
                            }
                        }
                    }
                });
                //  chart.render();
            });
        }

    </script>
@endsection
