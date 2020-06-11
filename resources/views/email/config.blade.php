@extends('layouts.app')


@section('content')
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<div class="container">
      <div class="row">
    <div class="col-md-3"></div>
    <div align="center" class="col-md-6">
         @if(session()->has('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session()->get('success') }}

    </div>
     @endif
       @if(session()->has('error'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {!! session()->get('error') !!}
    </div>
     @endif
     <div class="col-md-3"></div>
     </div>
   
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Email Configuration Wizard</div>

                <div class="card-body">
                   
                     {!! Form::open(['route' => 'store.email.config','method'=>'post']) !!}
                {{--      @php
                      foreach ($get_config as $key => $value) {
                        echo '<div class="form-group col-sm-12">';
                        echo '<label>'.str_replace('_',' ',strtoupper($value->option)).'</label>';
                        echo '<input type="text" class="form-control" name="'.$value->option.'" value="'.$value->value.'">';
                        echo '</div>';
                    }
                     @endphp --}}
                  
                     <div class="form-group col-sm-12">
                        {!! Form::label('limit', 'Set Limit Email Per-Hour:') !!}<br>
                         {!! Form::text('limit', $get_config_limit, ['class' => 'form-control','required'=>'required']) !!}
                           
                    </div> 
                      <div class="form-group col-sm-12">
                        {!! Form::label('email_from', 'Send Email From :') !!}<br>
                         {!! Form::text('email_from', $get_email_from, ['class' => 'form-control','required'=>'required']) !!}
                           
                    </div> 
                      <div class="form-group col-sm-12">
                        {!! Form::label('from_name', 'Send Email By Name Of:') !!}<br>
                         {!!Form::text('from_name', $get_from_name, ['class' => 'form-control','required'=>'required']) !!}
                           
                    </div> 
                     <div class="form-group col-sm-12">
                        {!! Form::label('cron_email_doi', 'Select Doi:') !!}<br>
                        <select name="cron_email_doi" class="form-control" required="">
                            <option <?php if ($get_cron_email_doi=='Yesterday') echo 'selected'; ?> value="Yesterday">Yesterday</option>
                            <option <?php if ($get_cron_email_doi=='Month') echo 'selected'; ?> value="Month">Month</option>
                        </select>
                      
                    </div> 
                       <div class="form-group col-sm-12">
                        {!! Form::label('cron_email_state', 'Select State:') !!}<br>
                        <select name="cron_email_state" class="form-control" required="">
                            <option>Select</option>
                            @php
                              $i=1;
                              foreach ($get_state as $value) {
                                if ($value==$get_cron_email_state) { $selected="selected"; }
                                else{ $selected=""; }    
                                       echo '<option '.$selected.' value="'.$value.'">'.$value.'</option>';
                                       $i=$i+1;
                              }
                            @endphp
                        </select>
                      
                    </div> 
                   
                     <br><br>
                     <div class=" col-sm-12" align="center">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                        <a href="{!! route('home') !!}" class="btn btn-primary">Cancel</a>
                    </div>
                    
                        {{-- @include('email.fields') --}}

                    {!! Form::close() !!}
             
                </div>

            </div>
                   

</div>
        </div>
    </div>
</div>
@endsection
