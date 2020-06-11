@extends('layouts.app')


@section('content')
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create New Templates</div>

                <div class="card-body">
                     {!! Form::open(['route'=>'update.email.template','methos'=>'post']) !!}
                     {{-- <form action="{{ route('update.email.template', $id) }}" method="POST" > --}}

                        @include('email.edit_fields')

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
