
{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"  crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>


    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>

<body>
    
<br>
{{-- check current user  logged  in or not --}}

@if (!Auth::user())
 <script type="text/javascript">
    window.location = "{{ url('/login') }}";//here double curly bracket
</script>
@endif


<h1 align="center"> Email Templates</h1>
   

  

<script>
$(document).ready(function() {
    $('#example').DataTable({ "order": [[ 0, "desc" ]]});
    // document.getElementById("example").DataTable();
} );
    </script>
<div class="container">
    <div class="row">
    <div class="col-md-3"></div>
    <div align="center" class="col-md-6">
         @if(session()->has('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session()->get('success') }}
    </div>
     <div class="col-md-3"></div>
     </div>
    @endif
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <!--SELECT `id`, `file_name`, `file_path`, `fetched_on`, `logs`, `status` FROM `curl_fetch_excel_log` WHERE 1 -->

             <a class="btn btn-info float-right" href="{{route('create.email.template')}}">Add New</a><br><br>
             <a class="btn btn-info float-right" href="{{route('home')}}">Dashboard</a><br><br>
            
            
                    <table  id="example" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Template Name</th>
                                <th> Category</th>
                                <th> Description</th>
                                <th> Subject</th>
                                <th> Message</th>
                                <th> Attachment</th>
                                <th> status</th>
                                <th> Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $file_path =url('');
                            $file_path =str_replace('/public','', $file_path);
                            $file_path =str_replace('/index.php','', $file_path);
                            $file_path =str_replace('_html','public_html', $file_path);
                            $file_path=$file_path.'/storage/email/';
                            // $file_path =Config::get('app.url');
                            
                            foreach ($get_template as $key => $value) {
                               echo '<tr>
                               <td>'.$value->id.'</td>
                               <td>'.$value->template_name.'</td>
                               <td>'.$value->category.'</td>
                               <td>'.$value->activity_description.'</td>
                               <td>'.$value->subject.'</td>
                               <td>'.$value->message.'</td>
                               <td><a href="'.$file_path.$value->attachment_path.'"  target="_blank">'.$value->attachment_path.'</a></td>';
                               if ($value->status==1) 
                               {
                                  echo '<td style="color:green">Active</td>';
                               }
                               else
                               {
                                    echo '<td style="color:red">In-Active</td>';
                               }
                               @endphp
                             <td>  <a href="{{route('edit.email.template',$value->id)}}">Edit</a> <br><br> <a href="{{route('delete.email.template',$value->id)}}">Delete</a></td>
                               @php
                               echo '</tr>';
                            }
                            @endphp
                         
                        </tbody>
                    </table>
        
        </div>
    </div>
</div>
{{-- @endsection --}}
</body>