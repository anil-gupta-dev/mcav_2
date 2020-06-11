@extends('layouts.app')


@section('content')
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>



<script type="text/javascript">
        $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });




    function manual_sync_cb_to_j2(id)
    {
        var limit={{$get_limit->value}}
        console.log(id);
        const btn = document.getElementById('manual_sync_cb_j2_btn');
        btn.disabled = true;
        // send an ajax request to queue all the items

         doSync();
        

    }

    function doSync() {
        (function ($) {

            $('#manual-sync-notes').append(' <br> Curl in progress... ');

            $.ajax({
                url :'{{route('process_curl', ['id' => $get_limit->value])}}',
                type: 'get',
                cache: false,
                async: true,
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                success: function(json) {
                        console.log(json);
                        if(json.success == 'completed'){
                            $('#manual-sync-notes').append('<br>');
                            $('#manual-sync-notes').append(json.success_message);
                             $('#manual-sync-notes').append('<br>');
                              $('#manual-sync-notes').append('Unable to process the following data');
                              $('#manual-sync-notes').append('<br>');
                            $('#manual-sync-notes').append(json.error_message);
                            $('#manual-sync-notes').append('<br> <hr>');
                            const btn = document.getElementById('manual_sync_cb_j2_btn');
                            btn.disabled = false;
                            // window.location.reload();
                        } else{
                            $('#manual-sync-notes').append(json.message);
                            doSync();
                        }

                }
            });
        })(jQuery);
    }
</script>



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                      <a  class="btn btn-primary" href="{{ route('import_process') }}"><i class="fa fa-upload" aria-hidden="true"></i>
 &nbsp; {{ __('Manually Import Data') }}</a>
                        <a target="_blank" class="btn btn-success" href="{{ route('view_import') }}"><i class="fa fa-eye" aria-hidden="true"></i>
 &nbsp; {{ __('View Imported MCA Data') }}</a>
 <a target="_blank" class="btn btn-warning" href="{{ route('view_import_log') }}"><i class="fa fa-tasks" aria-hidden="true"></i>
 &nbsp;{{ __('View Imported Log') }}</a>

  <br><br>

                               <a target="_blank" class="btn btn-info" href="{{ route('view_curled') }}"><i class="fa fa-address-card" aria-hidden="true"></i>
 &nbsp;{{ __('Browse Directors ') }}</a>

                              
                      
                </div>


                {{-- <div class="card-body">
                   <a  class="btn btn-danger" target="_blank" href="{{ route('view.email.template') }}">{{ __('Email Templates') }}</a>

                </div> --}}
            </div>
                    <div class="well" id="manual-sync-notes" style="width: 400px;">

                    

</div>
        </div>
    </div>
<br><br>
    <div class="row justify-content-center">
        <div class="col-md-8">
           <div class="card">
                <div class="card-header">E-mail Management</div>

                <div class="card-body">
                   <a  class="btn btn-success" target="_blank" href="{{ route('view.email.template') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i>
&nbsp;{{ __('View All Templates') }}</a>
                    <a  class="btn btn-info" target="_blank" href="{{ route('create.email.template') }}"><i class="fa fa-plus-circle" aria-hidden="true"></i>
 &nbsp;{{ __('Add New Templates') }}</a>

                </div>

                <div class="card-body">
                  
                    <a  class="btn btn-danger"  href="{{ route('email.config') }}"><i class="fa fa-cogs" aria-hidden="true"></i>
 &nbsp;{{ __('Email Configuration') }}</a>

                </div>

                  

                </div>
            </div> 
        </div>
      </div>


</div>
@endsection
