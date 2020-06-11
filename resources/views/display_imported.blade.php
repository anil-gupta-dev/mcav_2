{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
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


<h1 align="center">Imported Data From Gov Site</h1>

<?php
 //$dataSet = $get_data;

//echo json_encode($get_data);
//exit;
 //echo json_encode($dataSet);

 //exit;

?>
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
            
            <!--SELECT `id`, `cin`, `compay_name`, `doi`,
            `state`, `roc`, `category`, `sub_category`, `class`, `authorized_capital`, 
            `paid_capital`, `nof_members`, `activity_description`,
            `reg_office_address` FROM `imported_excel_data` -->
            
           <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>CIN</th>
                <th>LLPIN</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Director Details</th>
                <th>DOI </th>
                <th>State </th>
                <th>ROC </th>
                <th>category </th>
                <th>SubCategory </th>
                <th>Class </th>
                <th>Authorized Capital </th>
                <th>Paid Capital </th>
                <th>Total AOC </th>
                <th>Nof members </th>
                <th>Nof partner </th>
                <th>Nof Designed partner </th>
                <th>Activity Description </th>
                <th>Reg Office Address </th>
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
                                    {{$edu->compay_name}}
                                </td>
                                <td>
                                    {{$edu->email}}
                                </td>
                                <td>
                                    {{-- {{$edu->director_details}} --}}
                                    <?php
                                    $director = $edu->director_details;
                                    
                                    if (!empty($director))
                                    {
                                    $a= json_decode($director, true);

                                    //echo $a;
                                    //print_r($a);
                                        foreach ($a as $key => $value) {
                                           foreach ($value as $k => $v) {
                                               
                                               echo $k.':'.$v;
                                               echo "<br>";
                                           }
                                           echo "<hr  style='border-color:black;'>";
                                        }
                                    }
                                    

                                     //$string = implode(';', $a);
                                     //echo $string;

                                    


                                    ?>
                                </td>
                                 <td>
                                    {{$edu->doi}}
                                </td>
                                 <td>
                                    {{$edu->state}}
                                </td>
                                 <td>
                                    {{$edu->roc}}
                                </td>
                                 <td>
                                    {{$edu->category}}
                                </td>
                                 <td>
                                    {{$edu->sub_category}}
                                </td>
                                 <td>
                                    {{$edu->class}}
                                </td>
                                 <td>
                                    {{$edu->authorized_capital}}
                                </td>
                                 <td>
                                    {{$edu->paid_capital}}
                                </td>
                                 <td>
                                    {{$edu->total_a_o_c}}
                                </td>

                                 <td>
                                    {{$edu->nof_members}}
                                </td>
                                <td>
                                    {{$edu->nof_partner}}
                                </td>
                                <td>
                                    {{$edu->nof_designed_partner}}
                                </td>
                                 <td>
                                    {{$edu->activity_description}}
                                </td>
                                 <td>
                                    {{$edu->reg_office_address}}
                                </td>
                                <td>
                                    {{$edu->updated_on}}
                                </td>
                                
                             
                            </tr>
                        @endforeach
              
           
        </tbody>
        </table>
        <br><br>

       
            
          
    
        </div>
    </div>
</div>
{{-- {{ $get_data->links() }} --}}

{{-- @endsection --}}
