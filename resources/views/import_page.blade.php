@extends('layouts.app')

@section('content')


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

{{-- Import Process has handle by  ajax  request --}}

<script type="text/javascript">
        $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }

    });

    function manual_sync_cb_to_j2(id)
    {
        
        console.log(id);
        const btn = document.getElementById('manual_sync_cb_j2_btn');
        btn.disabled = true;
        // send an ajax request to queue all the items

         doSync();
         doStatus();
          // setTimeout( function() { doStatus(); },2000);
        

    }

    function doSync() 
    {
        (function ($) 
        {

            $('#manual-sync-notes-main').html(' <span style="color:red">The Import Process Going On By AJAX Call</span><br> ');
            $('#manual-sync-notes').html(' <br> Import in progress... ');

                console.log('{{route('import', ['id' => 'curl_url'])}}');

                $.ajax({
                    url :'{{route('import', ['id' => 'curl_url'])}}',
                    type: 'get',
                    cache: false,
                    async: true,
                    contentType: 'application/json; charset=utf-8',
                    dataType: 'json',
                    success: function(json) {
                            console.log(json);
                             if(!json.file_status)
                            {
                                doStatus();
                            }
                            else
                            {
                                  $('#manual-sync-notes').html('<span style="color:red;">File already avialable for more information Please see the Import log</span>');
                            }


                    }
                });
        })(jQuery);
    }


        function doStatus() 
        {
        (function ($) 
        {
            $.ajax({
                url :'{{route('import_status')}}',
                type: 'get',
                cache: false,
                async: true,
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                success: function(json) 
                {
                        console.log(json);
                        if(!json.status)
                        {

                            // const btn = document.getElementById('manual_sync_cb_j2_btn');
                            // btn.disabled = false;

                            console.log('import completed');
                             $('#manual-sync-notes').html(json.message);
                             $('#manual-sync-notes-main').html('<span style="color:green;">Success...!</span>');
                              const btn1 = document.getElementById('manual_stop_curl');
                              btn1.disabled=false;

                              manual_sync_cb_to_curl();

            
                        } 
                        else
                        {
                            $('#manual-sync-notes').html(json.message);

                             setTimeout( function() { doStatus(); },5000);
                            // doStatus();

                        }

                }
            });
        })(jQuery);
    }
//function for run curl


    function manual_sync_cb_to_curl()
    {
        
     
        // send an ajax request to queue all the items

         doCurl();
         // doStatus();
         // doCurlStatus();
          setTimeout( function() { doCurlStatus(); },3000);
        

    }


function doCurl() 
    {
        (function ($) 
        {

            $('#manual-sync-notes-main').html(' <span style="color:red">The Curl Process Going On By AJAX Call</span><br> ');
            $('#manual-sync-notes').html(' <br> Curl in progress... ');
             const btn1 = document.getElementById('manual_stop_curl');
                             setTimeout( function() {  btn1.disabled = false; },3100);

                

             $doCurl =  $.ajax({
                    url :'{{route('process_curl', ['id' => $get_limit->value])}}',
                    type: 'get',
                    cache: false,
                    async: true,
                    contentType: 'application/json; charset=utf-8',
                    dataType: 'json',
                    success: function(json) {
                            console.log(json);
                            if(json.success == 'completed'){
                            // $('#manual-sync-notes').append('<br>');
                            $('#manual-sync-notes-main').html(json.success_message);
                            const btn = document.getElementById('manual_sync_cb_j2_btn');
                            btn.disabled = false;

                            // window.location.reload();
                        } else{
                            $('#manual-sync-notes-main').html(json.message);
                            doCurl();
                            // doCurlStatus();
                             // setTimeout( function() { doCurlStatus(); },5000);



                        }

                    }
                });
        })(jQuery);
    }

function doCurlStatus() 
        {
        (function ($) 
        {
            $doCurlStatus= $.ajax({
                url :'{{route('import_status')}}',
                type: 'get',
                cache: false,
                async: true,
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                success: function(json) 
                {
                        console.log(json);
                        if(!json.status)
                        {

                            const btn = document.getElementById('manual_sync_cb_j2_btn');
                            btn.disabled = false;

                            console.log('completed');
                             $('#manual-sync-notes').html(json.message);
                             $('#manual-sync-notes-main').html('<span style="color:green;">Success...!</span>');

            
                        } 
                        else
                        {
                            $('#manual-sync-notes').html(json.message);


                             setTimeout( function() { doCurlStatus(); },5000);
                            // doStatus();

                        }

                }
            });
        })(jQuery);
    }

   function manual_stop_curl()
   {
            $doCurl.abort();

            $doCurlStatus.abort();
            const btn1 = document.getElementById('manual_stop_curl');
                            btn1.disabled = true;

            (function ($) 
        {
             $.ajax({
                url :'{{route('stop_curl')}}',
                type: 'get',
                cache: false,
                async: true,
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                success: function(json) 
                {
                        console.log(json.status);
                            $doCurlStatus.abort();
                             $('#manual-sync-notes').html('<span style="color:red;">Stoped By Manually</span>');
                       
                }
            });
        })(jQuery);


               $('#manual-sync-notes-main').html('<span style="color:red;">All Curl Request get Stopped...! <br> Some pre proecessed datas are  processig it will end some seconds</span>');

                 $('#manual-sync-notes').css("color", "green");

   }



</script>






<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Import Process</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                       @if( $errors->has('file_exists') )
                          <div class="gvapp-field-error">
                           <span  style="color: red;text-align: center;">   {{ $errors->first('file_exists') }}</span>
                           <span  style="color: red;text-align: center;">   {{ $errors->first('file_exists') }}</span>
                       
                         
                          </div>
                      @endif
                       @if( $errors->has('fetch_error') )
                          <div class="gvapp-field-error">
                           <span  style="color: red;text-align: center;">   {{ $errors->first('fetch_error') }}</span>
                           {{-- <span  style="color: red;text-align: center;">   {{ $errors->first('file_exists') }}</span> --}}
                          </div>
                      @endif
                       @if( $errors->has('fetch_success') )
                          <div class="gvapp-field-error">
                           <span  style="color: red;text-align: center;">   {{ $errors->first('fetch_success') }}</span>
                           {{-- <span  style="color: red;text-align: center;">   {{ $errors->first('file_exists') }}</span> --}}
                          </div>
                      @endif
                       @if( $errors->has('import_success') )
                          <div class="gvapp-field-error">
                           <span  style="color: red;text-align: center;">   {{ $errors->first('import_success') }}</span>
                           {{-- <span  style="color: red;text-align: center;">   {{ $errors->first('file_exists') }}</span> --}}
                          </div>
                      @endif

{{-- Below code used  for testing purpose  --}}      

     {{--              {!! Form::open(['url'=>'import','target'=>'_blank']) !!}
   
  
      <input type="text" name="devops_path" class="form-control" placeholder="Enter the Path" value="http://www.mca.gov.in/mcafoportal/companiesRegReport.do" readonly="readonly" required="">


    <br>
    <label>Enter the xls, xlsx file path , Then press upload </label><br>
     {!! Form::submit('Upload / Import', ['class' => 'btn btn-success', 'id'=>'btn-import'])!!}
     {!! form::close() !!} --}}

     <?php  echo 'File will fetch from => '.$get_url->value.'<br>  ';

       ?>
             

                @if ($curled_count>0)
                               <button class="btn btn-info btn-large" onclick="manual_sync_cb_to_curl('{{ $get_limit->value }}')" id="manual_sync_cb_j2_btn"> Run Curl Manually</button>
                               <br><br>
                               <span style="color: red;">[Note]:  All data are Imported  but due do some  response Fails Some data's are not updated , So Please run curl by manually to fetch the directors details , Click Run Curl Manually </span>
                @else
                      <button class="btn btn-info btn-large" onclick="manual_sync_cb_to_j2('{{ $get_url->value }}')" id="manual_sync_cb_j2_btn">Start Import</button>
                @endif
                <br><br>

                 <button class="btn btn-danger btn-large" disabled="true" onclick="manual_stop_curl()" id="manual_stop_curl">Stop Curl</button>

             </div>
            </div>
        </div>
    </div>
</div>

<br><br>
 <div class="well" align="center" id="manual-sync-notes-main" style="width: 100%;"></div>
    <br>
 <div class="well" align="center" id="manual-sync-notes" style="width: 100%;">
@endsection