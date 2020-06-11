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
                <div class="card-header">Create New Templates</div>

                <div class="card-body">
                   
                     {!! Form::open(['route' => 'store.email.template','method'=>'post']) !!}

                        @include('email.fields')

                    {!! Form::close() !!}
                @php
              
                @endphp
             
                </div>

            </div>
                   

</div>
        </div>
    </div>
</div>
@endsection
