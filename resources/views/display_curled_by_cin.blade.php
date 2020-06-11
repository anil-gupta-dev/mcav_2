<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"  crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>


    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>

    <br>
{{-- check current user  logged  in or not --}}

@if (!Auth::user())
 <script type="text/javascript">
    window.location = "{{ url('/login') }}";//here double curly bracket
</script>
@endif

<style type="text/css">
    #loading {
position: fixed;
width: 100%;
height: 100vh;
background: #fff url('https://media.giphy.com/media/11FuEnXyGsXFba/giphy.gif') no-repeat center center;
z-index: 9999;
}
</style>

<script>    
$(document).ready(function() {
    // $('#loading').fadeOut(100000);

//var data = JSON.parse(json);
//console.log(json);
    $('#example').DataTable();
    // $('#example1').DataTable();
} );
$(window).on('load', function() {
    $('#loading').fadeOut();
})
    </script>

<h1 align="center">Directors Details</h1>
    <?php
//print_r($get_data);
//exit;

    ?>
    <div id="loading"></div>
<div class="container-fluid">
    <div class="row justify-content-center">

         <br><br>
        <center> 
            <a target="_blank" class="btn btn-success" href="{{ route('export_excel') }}">{{ __('Export as Excel') }}</a>
            <a  class="btn btn-info" href="{{ route('home') }}">{{ __('Back to Home') }}</a>
        </center> 
        <br><br>
        <div class="col-md-12">
            
            <!--SELECT `id`, `file_name`, `file_path`, `fetched_on`, `logs`, `status` FROM `curl_fetch_excel_log` WHERE 1 -->
            
           <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>CIN</th>
                <th>LLPIN</th>
                <th>Company  </th>
                <th>Din </th>
                <th>Name </th>
                <th>Begin Date </th>
                <th>End Date </th>
                <th>Surrender Din </th>
                <th>Updated On </th>
                
                
               
            </tr>
        </thead>
         <tbody>
             
               <?php $i = 1;?>
                @foreach($get_data as $edu)
                            <?php $i = $i + 1;?>
                            <tr >
                                <td>
                                    {{$edu->cin}}
                                </td>
                                 <td>
                                    {{$edu->llpin}}
                                </td>
                                <td>
                                     {{$edu->company_name}}
                                </td>
                                 <td>
                                     {{$edu->din_pan}}
                                </td>
                                 <td>
                                    {{$edu->name}}
                                </td> 
                                 <td>
                                    {{$edu->begindate}}
                                </td>
                                 <td>
                                    {{$edu->enddate}}
                                </td>
                                 <td>
                                    {{$edu->surrendereddin}}
                                </td>
                                <td>
                                    {{$edu->updated_on}}
                                </td>
                             
                                 
                                
                             
                            </tr>
                        @endforeach
           
        </tbody>
        </table>
        
        <br><br><br><br>
     {{--    
        <h3 align="center" style="color:green;">The Fetch Excel file has stored in our local Storage Drive For our reference </h3> --}}
            
          
    
        </div>
    </div>
</div>
{{-- @endsection --}}
