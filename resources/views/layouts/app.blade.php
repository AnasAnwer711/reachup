<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ReachUp Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />

    <!-- v4.0.0-alpha.6 -->
    <link rel="stylesheet" href="{{ asset('dist/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/bootstrap/css/bootstrap-tagsinput.css') }}">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/et-line-font/et-line-font.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" href="{{asset('dist/css/intlTelInput.css')}}">

    <!-- FAV ICON -->
    <link rel="shortcut icon" type="image/x-icon" href="dist/img/fav-icon.png">

    <!-- Chartist CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('dist/plugins/chartist-js/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/plugins/chartist-js/chartist-init.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/plugins/chartist-js/chartist-plugin-tooltip.css') }}"> --}}

    <link rel="stylesheet" href="{{ asset('dist/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/plugins/datatables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/plugins/datatables/css/buttons.dataTables.min.css') }}">
{{-- 
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> --}}
{{-- <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
    integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"> --}}
{{-- <link href="{{ asset('dist/css/bstreeview.min.css') }}" rel="stylesheet"> --}}

    {{-- <link rel="stylesheet" href="{{ asset('dist/css/bstreeview.min.css') }}"> --}}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
    <style>
        .defaultBtn {

            background-color: #f39c12 !important;
            border-color: #4857d0 !important;
            margin-right: 5px !important;
            color: #FFF !important;
            display: inline-block !important;
            font-weight: 400 !important;
            line-height: 1.25 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            border: 1px solid transparent !important;
            padding: .5rem 1rem !important;
            font-size: 1rem !important;
            border-radius: .25rem !important;
            -webkit-transition: all .2s ease-in-out !important;
            -o-transition: all .2s ease-in-out !important;
            transition: all .2s ease-in-out !important;
        }

    </style>
    @yield('css')

</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper boxed-wrapper">
        @include('layouts.top-menu')
        @include('layouts.sidebar')


        @yield('content')

        @include('layouts.footer')

        <!-- /.content-wrapper -->

    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="{{ asset('dist/js/jquery.min.js') }}"></script>

    <!-- v4.0.0-alpha.6 -->
    <script src="{{ asset('dist/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/bootstrap/js/bootstrap-tagsinput.js') }}"></script>
    {{-- <script src="{{ asset('dist/bootstrap/js/bootstrap-tagsinput-angular.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="{{asset('dist/js/jquery.mask.min.js')}}"></script>

    <!-- template -->
    <script src="{{ asset('dist/js/niche.js') }}"></script>
    <script src="{{asset('dist/js/intlTelInput.js')}}"></script>
    <script src="{{asset('dist/js/utils.js')}}"></script>

    <!-- treeview -->
    {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
			integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
			crossorigin="anonymous"></script> --}}


    {{-- <script src="{{ asset('dist/js/bstreeview.min.js') }}"></script> --}}

    <!-- Chartjs JavaScript -->
    <script src="{{ asset('dist/plugins/chartjs/chart.min.js') }}"></script>
    {{-- <script src="{{ asset('dist/plugins/chartjs/chart-int.js') }}"></script> --}}

    <!-- Chartist JavaScript -->
    {{-- <script src="{{ asset('dist/plugins/chartist-js/chartist.min.js') }}"></script>
    <script src="{{ asset('dist/plugins/chartist-js/chartist-plugin-tooltip.js') }}"></script>
    <script src="{{ asset('dist/plugins/functions/chartist-init.js') }}"></script> --}}

    <!-- DataTable -->
    <script src="{{ asset('dist/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dist/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    
    {{-- <script src="{{ asset('dist/plugins/ckeditor/ckeditor.js') }}"></script> --}}
    <script src="{{ asset('dist/plugins/ckeditor5-build-classic/ckeditor.js') }}"></script>
    <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>



    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>


    <script>
        $(function() {
            // $('#example1').DataTable();
            $('#example2').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true,
                "pageLength": 50,
            });
            $('#example3 , #example4, #example5').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 50,
                'autoWidth': true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: $(this).find('.report').attr('data-name') ?? 'Export Data',
                        className: 'defaultBtn',
                        text: 'Export to PDF'
                    },

                    {
                        extend: 'excelHtml5',
                        title: $(this).find('.report').attr('data-name') ?? 'Export Data',
                        className: 'defaultBtn',
                        text: 'Export to Excel'
                    }
                ]
            })
        })

    </script>

    {{-- <script src="{{ asset('dist/plugins/table-expo/filesaver.min.js') }}"></script>
    <script src="{{ asset('dist/plugins/table-expo/xls.core.min.js') }}"></script>
    <script src="{{ asset('dist/plugins/table-expo/tableexport.js') }}"></script> --}}
    <script src="{{ asset('dist/js/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let old_count = 0;
        $( document ).ready(function() {
            old_count = parseInt($('#old_count').val());
            ajax_call();
        });
        var ajax_call = function() {
          //your jQuery ajax code
          $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: "/get_admin_notificaitons/"+old_count,
            dataType: 'json',
            success: function (response) {
                // console.log(response.data);
                // console.log(response.count);
                let container = $('#not-container');
                if(old_count == 0 && response.count == 0){
                  container.html('');
                  container.append('<li><a href="javascript:void(0)" style="cursor:default; white-space:unset;"><p>No notification exist</p></a></li>');
                } else if(old_count != response.count){
                  console.log('change in div');
                  
                  $.each(response.data, function(index, item) {
                    container.html(''); //clears container for new data
                    $.each(response.data, function(i, item) {
                      if(item.resolved == '1'){
      
                        container.append('<li><div class="pull-left icon-circle green" style="margin: 10px;"><i class="icon-lightbulb"></i></div><a href="javascript:void(0)" style="white-space:unset; cursor:default"><p>'+item.message+'</p></a></li>');
                      } else {
                        container.append('<li><div class="pull-left icon-circle red" style="margin: 10px;"><i class="icon-lightbulb"></i></div><a href="/admin_notification/'+item.id+'/edit" style="white-space:unset;"><p>'+item.message+'</p></a></li>');
      
                      }
                          // container.append('<div class="row"><div class="ten columns"><div class="editbuttoncont"><button class="btntimestampnameedit" data-seek="' + item.timestamp_time + '">' +  new Date(item.timestamp_time * 1000).toUTCString().match(/(\d\d:\d\d:\d\d)/)[0] +' - '+ item.timestamp_name +'</button></div></div> <div class="one columns"><form action="'+ item.timestamp_id +'/deletetimestamp" method="POST">' + '{!! csrf_field() !!}' +'<input type="hidden" name="_method" value="DELETE"><button class="btntimestampdelete"><i aria-hidden="true" class="fa fa-trash buttonicon"></i></button></form></div></div>');
                    });
                        container.append('<br>');
                  });
                }
                $('#old_count').val(response.count);
                if(response.count > 0){
                  $('.old_count').text(response.count);
                }
            setTimeout(() => {
               ajax_call()
            }, 2500);
      
            },error:function(){ 
                console.log('error');
            }
          });
        };
      
        // var interval = 1000 * 60 * 2; // where X is your every X minutes
        
        //setInterval(ajax_call, 5000);
    </script>
    <script>
        // $(".table-responsive .report").tableExport({
        //     formats: ["xlsx"],
        // });
        $('.app-menu__item').click(function() {
            $('.treeview-indicator').toggleClass("fa-chevron-down fa-chevron-right");
        });
        $(".deleteData").click(function(e) {
            var id = $(this).data("id");
            var source = $(this).data("source");
            var title = $(this).data("title");
            var description = $(this).data("description");
            e.preventDefault();

            Swal.fire({
                title: title ?? '',
                html: '<b>Note:</b> ' +
                    description ? description : '',
                showCancelButton: true,
                confirmButtonText: `Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        // url: '/' + source + '/' + id,
                        url: source + '/' + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}',
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.success) {

                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: data.message ?? '',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'danger',
                                    title: data.message ?? '',
                                    showConfirmButton: true,
                                })
                            }

                        },
                        error: function() {
                            Swal.fire({
                                position: 'center',
                                icon: 'danger',
                                title: '',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }

                    });
                }
            })


        });

        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 45  && charCode > 31 && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }

    </script>

    @yield('javascript')

</body>

</html>
