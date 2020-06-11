@extends('layouts.app')


@section('content')
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
 <link href=" https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">


<div class="container">
      <div class="row">
    <div class="col-md-3"></div>
    <div align="center" class="col-md-6">
         @if($errors->first('found'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
      {!! $errors->first('found') !!}
    </div>
     @endif
     @if( $errors->first('notfound') )
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
         {!! $errors->first('notfound') !!}
    </div>
     @endif
       @if(session()->has('error'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session()->get('error') }}
    </div>
     @endif
   
     <div class="col-md-3"></div>
     </div>
   
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Send Email To Newly Registered Companies</div>
                <div class="card-body">
                     

                     {!! Form::open(['route' => 'send.email.admin','method'=>'post']) !!}
                         @php
                         // print_r($get_state);
                         // exit;
                        @endphp
                    <div class="form-group col-sm-12">
                        {!! Form::label('state', 'Select State:') !!}<br>
                        {{'No Of Available States is: '.count($get_state)}}
                        <select name="state" class="form-control" required="">
                            <option>Select</option>
                            @php
                              $i=1;
                              foreach ($get_state as $value) {
                                       echo '<option  value="'.$value.'">'.$value.'</option>';
                                       $i=$i+1;
                              }
                            @endphp
                        </select>
                      
                    </div> 
                     <div style="margin-left: 3px;" class="row">
                        <div class="form-group col-sm-8">
                            {!! Form::label('roc', 'Select ROC:') !!}<br>
                        {{-- {{'No Of Available RO is: '.count($get_state)}} --}}
                        <select name="roc" class="form-control" required="">
                                     
                        </select>
                        </div>
                          <div class="col-sm-4">
                            <span id="loader"><i class="fa fa-spinner fa-3x fa-spin"></i></span>
                         </div>
                    </div>
                     <div style="margin-left: 3px;" class="row">
                        <div class="form-group col-sm-8">
                        {!! Form::label('doi', 'Select Doi:') !!}<br>
                        {{-- {{'No Of Available RO is: '.count($get_state)}} --}}
                        <select name="doi" class="form-control" required="">
                        
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <span id="loader-doi"><i class="fa fa-spinner fa-3x fa-spin"></i></span>
                    </div>
                     </div>
                      <div style="margin-left: 3px;" class="row">
                        <div class="form-group col-sm-8">
                        {!! Form::label('description', 'Select Activity:') !!}<br>
                        {{-- {{'No Of Available RO is: '.count($get_state)}} --}}
                        <select name="description" class="form-control" required="">
                        
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <span id="loader-des"><i class="fa fa-spinner fa-3x fa-spin"></i></span>
                    </div>
                     </div>
                     {{--  <div style="margin-left: 3px;" class="row">
                        <div class="form-group col-sm-8">
                        {!! Form::label('category', 'Select Category:') !!}<br>
                     
                        <select name="category" class="form-control"  required="">
                        
                        </select>
                          <div id="cat-error"></div>
                    </div>
                    <div class="col-sm-4">
                        <span id="loader-cat"><i class="fa fa-spinner fa-3x fa-spin"></i></span>
                    </div>
                     </div> --}}

                     <div class="row">
                         <div class=" col-sm-6" align="center">
                            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                            <a href="{!! route('home') !!}" class="btn btn-primary">Cancel</a>
                        </div>
                     </div>

                    
                
                    {!! Form::close() !!}
              
             
                </div>

            </div>
                   

</div>
        </div>
    </div>
</div>
<style type="text/css">
    #loader,#loader-doi,#loader-des,#loader-cat
    {
        display: none;
    }
</style>
<script type="text/javascript">
     $(document).ready(function() {
        $('#loader').css("display", "none");
        $('#loader-doi').css("display", "none");

    $('select[name="state"]').on('change', function(){
        var state = $(this).val();
        const url ='{{url('/')}}';
        if(state) {
            $.ajax({
                url: url+'/roc/get/'+state,
                {{-- url: {{)}}, --}}
                type:"GET",
                dataType:"json",
                beforeSend: function(){
                    $('#loader').css("display", "block");
                },

                success:function(data) {

                    $('select[name="roc"]').empty();
                     $('select[name="roc"]').append('<option>Select</option>');

                    $.each(data, function(key, value){

                        $('select[name="roc"]').append('<option value="'+ value +'">' + value + '</option>');

                    });
                },
                complete: function(){
                    $('#loader').css("display", "none");
                }
            });
        } else {
            $('select[name="roc"]').empty();
        }

    });
     $('select[name="roc"]').on('change', function(){
         
        var roc = $(this).val();
        const url ='{{url('/')}}';
        if(roc) {
            $.ajax({
                url: url+'/doi/get/'+roc,
                {{-- url: {{)}}, --}}
                type:"GET",
                dataType:"json",
                beforeSend: function(){
                    $('#loader-doi').css("display", "block");
                },

                success:function(data) {

                    $('select[name="doi"]').empty();
                    $('select[name="doi"]').append('<option>Select</option>');


                    $.each(data, function(key, value){

                        $('select[name="doi"]').append('<option value="'+ value +'">' + value + '</option>');

                    });
                },
                complete: function(){
                    $('#loader-doi').css("display", "none");
                }
            });
        } else {
            $('select[name="doi"]').empty();
        }

    });

       $('select[name="doi"]').on('change', function(){
         
        var doi = $(this).val();
        // var roc = $('roc').val();
        // var obj = { key1: doi,key2: roc};
        const url ='{{url('/')}}';
        if(doi) {
            $.ajax({
                url: url+'/des/get/'+doi,
                {{-- url: {{)}}, --}}
                type:"GET",
                dataType:"json",
                beforeSend: function(){
                    $('#loader-des').css("display", "block");
                },

                success:function(data) {
                    console.log(data);

                    $('select[name="description"]').empty();
                    $('select[name="description"]').append('<option>Select</option>');


                    $.each(data, function(key, value){

                        $('select[name="description"]').append('<option value="'+ value +'">' + value + '</option>');

                    });
                },
                complete: function(){
                    $('#loader-des').css("display", "none");
                }
            });
        } else {
            $('select[name="description"]').empty();
        }

    });


    //      $('select[name="description"]').on('change', function(){
         
    //     var des = $(this).val();
    //     // var roc = $('roc').val();
    //     // var obj = { key1: doi,key2: roc};
    //     const url =';
    //     $('#cat-error').html('');
    //     if(des) {
    //         $.ajax({
    //             url: url+'/cat/get/'+des,
    //             {{-- url: {{)}}, --}}
    //             type:"GET",
    //             dataType:"json",
    //             beforeSend: function(){
    //                 $('#loader-cat').css("display", "block");
    //             },

    //             success:function(data) {
    //                 console.log(data);

    //                 $('select[name="category"]').empty();
    //                 $('select[name="category"]').append('<option>Select</option>');

    //                 if (data=="") {
    //                     $('#cat-error').html('<span style="color:red;">No Category Found For the selected Description,<br> Please try with anouther one..</span>');
    //                 }else
    //                 {
    //                     $('#cat-error').html('');
    //                 }
    //                 $.each(data, function(key, value){

    //                     $('select[name="category"]').append('<option value="'+ value +'">' + value + '</option>');

    //                 });
    //             },
    //             complete: function(){
    //                 $('#loader-cat').css("display", "none");
    //             }
    //         });
    //     } else {
    //         $('select[name="category"]').empty();
    //     }

    // });


});
</script>



@endsection
