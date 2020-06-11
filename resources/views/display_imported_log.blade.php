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


<h1 align="center">Logs</h1>
   

<script>
$(document).ready(function() {
    $('#example').DataTable({ "order": [[ 0, "desc" ]]});
    // document.getElementById("example").DataTable();
} );
    </script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <!--SELECT `id`, `file_name`, `file_path`, `fetched_on`, `logs`, `status` FROM `curl_fetch_excel_log` WHERE 1 -->
            
           <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Date</th>
                <th>File Path</th>
                <th>File Name </th>
                <th>Log </th>
                <th>Status </th>
                
               
            </tr>
        </thead>
         <tbody>
             
               <?php $i = 1;?>
                @foreach($get_data as $edu)
                            <?php $i = $i + 1;?>
                            <tr >
                                <td>
                                    {{$edu->fetched_on}}
                                </td>
                                <td>
                                    {{$edu->file_path}}
                                </td>
                                 <td>
                                    {{$edu->file_name}}
                                </td>
                                 <td>
                                    {{$edu->logs}}
                                </td>
                                 <td>
                                     <?php $stat = $edu->status;
                                     if($stat==1)
                                     {
                                         echo "<span style='color:green'>Success</span>";
                                     }
                                     else
                                     {
                                        echo "<span style='color:red'>Failed</span>";
                                     }
                                     
                                     ?>
                                    <!--{{$edu->status}}-->
                                </td>
                                 
                                
                             
                            </tr>
                        @endforeach
           
        </tbody>
        </table>
        
        <br><br><br><br>
        
        <h3 align="center" style="color:green;">The Fetch Excel file has stored in our local Storage Drive For our reference </h3>
            
          
    
        </div>
    </div>
</div>
{{-- @endsection --}}
</body>