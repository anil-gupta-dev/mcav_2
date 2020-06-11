<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Collection;



use App\Models\CurlFetchExcelLog;
use App\Models\ImportedExcelData;
use App\Models\CurlFetchedByCin;
use App\Models\CronLog;
// use Illuminate\Http\Request;
use Illuminate\Http\Response;

// use App\Http\Controllers\File;
use File;
use Storage;
use Session;


// use Excel;
// use Request;

class HomeController extends Controller
{



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->import_in_progress = false;
        $this->import_message ='';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
      $get_limit = \DB::table('curl_setting')->where('options', 'curl_fetch_limit')->first();
      $get_curled_data = ImportedExcelData::orderBy('id','asc')->where('status',0)->get();
      $curled_count= count($get_curled_data);
      
      // $get_val =$get_limit->value;
        return view('home', compact('get_limit','curled_count'));
    }
    public function ViewImport()

    {
       $get_url = \DB::table('curl_setting')->where('options', 'curl_url')->first();

       $get_limit = \DB::table('curl_setting')->where('options', 'curl_fetch_limit')->first();
       $get_curled_data = ImportedExcelData::orderBy('id','asc')->where('status',0)->get();
        $curled_count= count($get_curled_data);
        return view('import_page', compact('get_url','get_limit','curled_count'));
    }


//function for process import 

    public function import(Request $request,$id="")
    {
        
              
              $url =  \DB::table('curl_setting')->where('options', $id)->first();
              $url = $url->value;   

              $errors = [];
              date_default_timezone_set('Asia/Kolkata');
              $file_name  = date("Y-m-d").'.xlsx' ;
              $time_stamp = date("Y/m/d") ;
              // $file_path  = 'FetchedExcel/'.$time_stamp.'/';
              $file_exists = storage_path('app/FetchedExcel/'.$time_stamp.'/'.$file_name);
              // check if file exist through error mesage 
               if (file_exists($file_exists))
               {
              
                  
                      $fetched_excel_log = new CurlFetchExcelLog ();
              

                      $fetched_excel_log->file_name = $file_name;
                      $fetched_excel_log->file_path = $file_exists;
                      $fetched_excel_log->fetched_on = $time_stamp;
                      $fetched_excel_log->logs = 'File already avialable';
                      $fetched_excel_log->status = '-1';
                      $fetched_excel_log->save ();
                      $errors['fetch_error']="File already avialable for more information see the log";
                      Session::put('import_status', false);
                      Session::put('import_message', '<span style="color:red;">File already avialable for more information Please see the Import log</span>');
                      Session::save();

                      $response =array();
                      $response['file_status']=true;
                      echo json_encode($response);
                      exit;

               }
               else
               {
                      $response['file_status']=false;
                      echo json_encode($response);
               }

            
               
              Session::put('import_status', true);
              Session::put('import_message', 'Import Process Starting now');
              Session::save();

        $this->DevOps_Curl_Fetch_Excel($url );
    }



// Function Fetch Excel File From Server Via CURL 

    public function DevOps_Curl_Fetch_Excel($url )
    {
          $errors = [];

            set_time_limit(0); 
            $errors=[];
            $url  = 'http://www.mca.gov.in/mcafoportal/companiesRegReport.do';
           $curl = curl_init();
           $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';

          curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_USERAGENT => $ua,
          CURLOPT_COOKIE => 'NID=67=pdjIQN5CUKVn0bRgAlqitBk7WHVivLsbLcr7QOWMn35Pq03N1WMy6kxYBPORtaQUPQrfMK4Yo0vVz8tH97ejX3q7P2lNuPjTOhwqaI2bXCgPGSDKkdFoiYIqXubR0cTJ48hIAaKQqiQi_lpoe6edhMglvOO9ynw; PREF=ID=52aa671013493765:U=0cfb5c96530d04e3:FF=0:LD=en:TM=1370266105:LM=1370341612:GM=1:S=Kcc6KUnZwWfy3cOl; OTZ=1800625_34_34__34_; S=talkgadget=38GaRzFbruDPtFjrghEtRw; SID=DQAAALoAAADHyIbtG3J_u2hwNi4N6UQWgXlwOAQL58VRB_0xQYbDiL2HA5zvefboor5YVmHc8Zt5lcA0LCd2Riv4WsW53ZbNCv8Qu_THhIvtRgdEZfgk26LrKmObye1wU62jESQoNdbapFAfEH_IGHSIA0ZKsZrHiWLGVpujKyUvHHGsZc_XZm4Z4tb2bbYWWYAv02mw2njnf4jiKP2QTxnlnKFK77UvWn4FFcahe-XTk8Jlqblu66AlkTGMZpU0BDlYMValdnU; HSID=A6VT_ZJ0ZSm8NTdFf; SSID=A9_PWUXbZLazoEskE; APISID=RSS_BK5QSEmzBxlS/ApSt2fMy1g36vrYvk; SAPISID=ZIMOP9lJ_E8SLdkL/A32W20hPpwgd5Kg1J',
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "",
          CURLOPT_HTTPHEADER => array(
             "Content-Type: application/x-www-form-urlencoded",
             "cache-control: no-cache"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) 
        {
          // echo $err;
          // insert log if error
                $fetched_excel_log = new CurlFetchExcelLog();
                $fetched_excel_log->file_name = 'No filename';
                $fetched_excel_log->file_path = 'Not created';
                $fetched_excel_log->fetched_on = date("Y-m-d-h:i:s:a");
                $fetched_excel_log->logs = json_encode($err);
                $fetched_excel_log->status = '-1';
                $fetched_excel_log->save ();

                $errors['fetch_error']= "Faild To fetch Data";
                return redirect(route('import_process'))->withErrors($errors);

        } 
        else 
        {
         
             // declare file name and path  
              date_default_timezone_set('Asia/Kolkata');
              $file_name  = date("Y-m-d").'.xlsx' ;
              $time_stamp = date("Y/m/d") ;
              $file_path  = 'FetchedExcel/'.$time_stamp.'/';
              // echo "<pre>"; print_r($file_name); echo "</pre>"; die('end of code');
              // $file_exists = storage_path('app/FetchedExcel/'.$time_stamp.'/'.$file_name);
            
                    // create file in local storage path
                     $file_create = Storage::disk('local')->put($file_path.$file_name, $response);

                    // insert log to db  if success
                      $fetched_excel_log = new CurlFetchExcelLog ();
                      $fetched_excel_log->file_name = $file_name;
                      $fetched_excel_log->file_path = $file_path;
                      $fetched_excel_log->fetched_on = $time_stamp;
                      $fetched_excel_log->logs = 'Successfilly Fetched';
                      $fetched_excel_log->status = '1';
                      $fetched_excel_log->save ();
                      $errors['fetch_success']="Successfilly Fetch The Excel data ";

                      // response  log 
                      Session::put('import_status', true);
                      Session::put('import_message', 'Stage 1: The Excel File  Successfully  Fetched from server');
                      Session::save();

                      // test purpose
                      // $file_name='2019-03-29.xlsx';
                      // $file_path  = 'FetchedExcel/2019/03/29/';
                      $this->DevOps_Import_Fetched_Excel($file_name,$file_path);


        }
            
    }

    public function DevOps_Import_Fetched_Excel($file_name,$file_path )
    {
                  $errors =[];
                  Session::put('import_status', true);
                  Session::put('import_message', 'Processing Data From Excel to Local Database table  - Now process Excel First Sheet  # India Companies Registered');
                  Session::save();
            // read excel file by use phpspreadsheet library=>
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $storage = storage_path('app/'.$file_path.$file_name);
            $spreadsheet = $reader->load($storage);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

             $i=1;
            foreach ($sheetData as $key => $value)
            {
              if ($i>2) 
              {
                 // insert log to db  if success
                $imported_excel_data = new ImportedExcelData ();

                // SELECT `id`, `cin`, `compay_name`, `doi`, `state`, `roc`, `category`, `sub_category`, `class`, `authorized_capital`, `paid_capital`, `nof_members`, `activity_description`, `reg_office_address` FROM `imported_excel_data`

                 // $get_avialable = ImportedExcelData::whereEmail($email)->first();
                $cin_check=ImportedExcelData::where('cin',$value['A'])->count();

                // dd($cin_check);

                if ($cin_check)
                {
                    // for future process if data already available 
                }
                else
                {
                   $imported_excel_data->cin =$value['A'];
                   $imported_excel_data->compay_name =preg_replace("/[^a-zA-Z ]/","", $value['B']);
                   $imported_excel_data->doi =$value['C'];
                   $imported_excel_data->state =$value['D'];
                   $imported_excel_data->roc =$value['E'];
                   $imported_excel_data->category =$value['F'];
                   $imported_excel_data->sub_category =$value['G'];
                   $imported_excel_data->class =$value['H'];
                   $imported_excel_data->authorized_capital=$value['I'];
                   $imported_excel_data->paid_capital =$value['J'];
                   $imported_excel_data->nof_members =$value['K'];
                   $imported_excel_data->activity_description =preg_replace("/[^a-zA-Z ]/","", $value['L']);
                   $imported_excel_data->reg_office_address =preg_replace("/[^a-zA-Z ]/","", $value['M']);
                   $imported_excel_data->save ();
                }
                
                   
                  
              }

              $i=$i+1;

                 if (($i%100)==0)
                {
                  
                      //response log's
                      Session::put('import_status', true);
                      Session::put('import_message', $i.' Datas are Processed');
                      Session::save();
                }
            }
  
                      //response log's
                      Session::put('import_status', true);
                      Session::put('import_message', 'First Sheeet # Indian Companies Registered Datas are inserted Successfully');
                      Session::save();

    //insert LLP Sheet data here

                        //response log's
                       Session::put('import_status', true);
                       Session::put('import_message', 'Processing Data From Excel to Local Database table  - Now process Excel Second Sheet  # LLP  Registered');
                       Session::save();
            
               $reader1 = new \PhpOffice\PhpSpreadsheet\reader\Xlsx();
               $reader1->setReadDataOnly(true);
               $storage1 = storage_path('app/'.$file_path.$file_name);
               $reader1->setLoadSheetsOnly('LLP Registered');
               $spreadsheet1 = $reader1->load($storage1);

               $sheetData1 = $spreadsheet1->getActiveSheet()->toArray(null, true, true, true);

                 $j=1;
            foreach ($sheetData1 as $key1 => $value1)
            {
              if ($j>2) 
              {
                 // insert log to db  if success
                $imported_excel_data1 = new ImportedExcelData ();

                // SELECT `id`, `cin`, `compay_name`, `doi`, `state`, `roc`, `category`, `sub_category`, `class`, `authorized_capital`, `paid_capital`, `nof_members`, `activity_description`, `reg_office_address` FROM `imported_excel_data1`

                 // $get_avialable = ImportedExcelData::whereEmail($email)->first();
                $cin_check1=ImportedExcelData::where('llpin',$value1['A'])->count();


                if ($cin_check1)
                {
                    // for future process if data already available 
                }
                else
                {
                   $imported_excel_data1->llpin =$value1['A'];
                   $imported_excel_data1->compay_name =preg_replace("/[^a-zA-Z ]/","", $value1['B']);
                   $imported_excel_data1->doi =$value1['C'];
                   $imported_excel_data1->state =$value1['D'];
                   $imported_excel_data1->roc =$value1['E'];

                   $imported_excel_data1->nof_partner =$value1['F'];
                   $imported_excel_data1->nof_designed_partner =$value1['G'];
                   $imported_excel_data1->total_a_o_c =$value1['H'];
                   $imported_excel_data1->activity_description =preg_replace("/[^a-zA-Z ]/","", $value1['I']);
                   $imported_excel_data1->reg_office_address =preg_replace("/[^a-zA-Z ]/","", $value1['J']);
                   $imported_excel_data1->save ();
                }
                
                   
                  
              }

              $j=$j+1;

                 if (($j%100)==0)
                {
                  
                 
                      Session::put('import_status', true);
                      Session::put('import_message', $j.'  LLP Sheet Datas are Processed');
                      Session::save();
                }
            } 


                      Session::put('import_status', true);
                      Session::put('import_message', 'Second Sheeet # LLP  Registered Datas are inserted Successfully');
                      Session::save();

                      Session::put('import_status', false);
                      Session::put('import_message', 'The import process get completed now....');
                      Session::save();


// call curl function here
                      // $this->ProcessCurl();




    }



      public function ProcessCurl($id=0)
      {
        // echo $id;
        // exit;

            if ($id>0) 
            {
                $get_datas = ImportedExcelData::orderBy('id','asc')->where('status',0)
                          ->limit($id)
                          ->get();
            }
            else
            {
                 $get_datas = ImportedExcelData::orderBy('id','asc')->where('status',0)
                          ->limit(10)
                          ->get();
            } 

            // $get_datas = ImportedExcelData::orderBy('id','asc')->where('status',0)
            //               ->limit($id)
            //               ->get();  



                      Session::put('import_status',true);
                      Session::put('import_message', 'Curl process Start now.......');
                      Session::save();

//check case if all data are up-todate current records  sop the process and through the alrt message                       
            if (empty($get_datas)) 
            {

                      Session::put('import_status',false);
                      Session::put('import_message', 'All Details are Up-to-date, No need to run curl');
                      Session::save();

                      $response = array();
                      $response['success']='completed';
                      $response['success_message']='Curl process completed.!';
                      echo json_encode($response);
                          exit;
             
            }



          $ii=1;
          foreach ($get_datas as $data) 
          {
            try
            {

                $cin = $data->cin;
                $llpin =$data->llpin;
                $set_company=  $data->compay_name;

//here we find the which id comming CIN or LLPIN  
//idetify by the $cin_or_llpin if 0= cin if 1=llpin                

                $cin_or_llpin=0;

                if (empty($cin)) 
                {
                  $cin = $data->llpin;
                  $llpin =$data->llpin;
                  $cin_or_llpin=1;
                }

                // test purpose 2 directors 5   and 6 directors

                // $llpin ="AAO-6871";
                // $cin ="AAO-6871";
                // $llpin ="AAO-3762";
                // $cin ="AAO-3762"; 
                // $llpin ="AAO-5132";
                // $cin ="AAO-5132";
                 sleep(5); // if need configure timeout from curl_time_delay => curl_setting table
                $get_curl =$this->DevOps_Curl($cin);
                // echo "<pre>"; print_r($cin); echo "</pre>"; die('end of code');
// get response and parse by Use DOMDocument
                libxml_use_internal_errors(true);
                $dom = new \DOMDocument();
                // echo "<pre>"; print_r($get_curl[8]); echo "</pre>"; die('end of code');
                $get_html_table =$dom->loadHTML($get_curl[10]); 
                // print_r($get_html_table);
                // check case if giv site return empty data without table handle error log

    if (!empty($get_html_table)) 
    {
   
                
                $tables = $dom->getElementsByTagName('table');
// check the case if curl return empty html with out table 
      if ($tables->length>0){ 
              //get firt table from  responce html  table contains company llp details
                $table_company_llp = $tables->item(0)->getElementsByTagName('tr'); 
            if ($table_company_llp->length>0) 
            {

              
                    $Array_company_llp = array();
                foreach ($table_company_llp as $company_llp) 
                {



                    /*** get each column by tag name ***/ 
                    $cols = $company_llp->getElementsByTagName('td'); 

                    // echo $cols->item(0)->nodeValue.'-'.$cols->item(1)->nodeValue;
                    $key = strtolower( str_replace(" ", "", $cols->item(0)->nodeValue));

                    $key= preg_replace('/[^A-Za-z0-9\-]/', '', $key);

                    $Array_company_llp[$key]=$cols->item(1)->nodeValue;
                        // $Array_company_llp=array_merge($Array_company_llp,array($key=>$cols->item(1)->nodeValue));

                }                
                $set_cin =(!empty($llpin))? $Array_company_llp['llpin']:$Array_company_llp['cin'];
                $set_email=  $Array_company_llp['emailid'];
                $Array_company_llp = json_encode( $Array_company_llp,JSON_UNESCAPED_SLASHES);
                $Array_charges = array();
                $table_charges_th = $tables->item(1)->getElementsByTagName('th'); 
                $table_charges_td = $tables->item(1)->getElementsByTagName('td');
                for ($i = 0; $i < $table_charges_th->length; $i++)
                {
                   $Array_charges[strtolower(str_replace(" ", "", $table_charges_th->item($i)->nodeValue  ))] =  $table_charges_td->item(0)->nodeValue;
                }
                $Array_charges =  json_encode( $Array_charges,JSON_UNESCAPED_SLASHES);    
                //get third table form responce html  table contains directors details
                $table_director_details_th = $tables->item(2)->getElementsByTagName('th'); 
                // $table_director_details_tr = $tables->item(2)->getElementsByTagName('tr'); 
                $table_director_details_td = $tables->item(2)->getElementsByTagName('td'); 
                // if check case no director details in response
                if ($table_director_details_td->length>1) 
                {

                        $Array_directory_llp= array();
                        $Array_director = array();
                        $table_charges_th = $tables->item(2)->getElementsByTagName('th'); 
                        $table_charges_tr = $tables->item(2)->getElementsByTagName('tr'); 
                        $no_of_table_charges_tr = $table_charges_tr->length - 1;
                        $table_charges_td = $tables->item(2)->getElementsByTagName('td');
                        $d =0;
                        for ($r=1; $r <= $no_of_table_charges_tr ; $r++) { 
                          for ($i = 0; $i < $table_charges_th->length; $i++)
                          {
                           $Array_director[$r][strtolower(str_replace(" ", "", $table_charges_th->item($i)->nodeValue  ))] =  trim($table_charges_td->item($d)->nodeValue);
                           $d++;
                          }
                          $this->InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$Array_director[$r]);
                        }
                         
                              $Array_directory_main = json_encode($Array_director,JSON_UNESCAPED_SLASHES);
                               // update email and details in main table
                                    $update_main_table=[];
                                    $update_main_table['email'] =$set_email;
                                    $update_main_table['status'] =1;
                                    $update_main_table['updated_on'] =date("Y-m-d h:i:s");
                                    $update_main_table['director_details'] =$Array_directory_main;
                               if (!empty($llpin)) 
                               {
                                 
                               $update_cin_table=ImportedExcelData::where('llpin',$llpin)->update($update_main_table);
                               }
                              else
                               {
                                  $update_cin_table=ImportedExcelData::where('cin',$cin)->update($update_main_table);
                               }



                }//if end no directors details
                else
                {
                            $update_main_table_error=[];
                            $update_main_table_error['email'] =$set_email;
                            $update_main_table_error['status'] =-1;
                            $update_main_table_error['updated_on'] =date("Y-m-d h:i:s");
                            $update_main_table_error['director_details'] ="";
                       if (!empty($llpin)) 
                       {
                         
                       $update_cin_table_error=ImportedExcelData::where('llpin',$llpin)->update($update_main_table_error);
                       }
                      else
                       {
                          $update_cin_table_error=ImportedExcelData::where('cin',$cin)->update($update_main_table_error);
                       }

                        Session::put('import_status', true);
                        Session::put('import_message', 'No director Details Found for the record=> '.$cin);
                        Session::save();




                }   //else end no directors details


                                $ii=$ii+1;
                 
            }   // if table-tr end

        }//if table->length>0 end 
        if($tables->length == 0){
            $update_main_table_error=[];
                $update_main_table_error['email'] ='not@available';
                $update_main_table_error['status'] =-1;
                $update_main_table_error['updated_on'] =date("Y-m-d h:i:s");
                $update_main_table_error['director_details'] ="";
           if (!empty($llpin)) 
           {
             
           $update_cin_table_error=ImportedExcelData::where('llpin',$llpin)->update($update_main_table_error);
           }
          else
           {
              $update_cin_table_error=ImportedExcelData::where('cin',$cin)->update($update_main_table_error);
           }

            Session::put('import_status', true);
            Session::put('import_message', 'No Details Found for the record=> '.$cin);
            Session::save();
        }
                


          }//if get_html_table not empty case handle   



          }// try end 

            catch (Exception $e) 
            {
                    
                        Session::put('import_status', true);
                        Session::put('import_message', 'Unable to fetch record for '.$data->cin);
                        Session::save();                 
      
            }

          } // for each end here


          //response log's
                    
                    
                  $process_count = count($get_datas);
                  // on process
                  if ( $process_count > 0)
                  {
                    $response = array();
                    $response['success']='continue';
                    $response['message']='Curl Inprogress.......';
                    echo json_encode($response); exit;  
                  }
                  
                //  on success
                  $response = array();
                  $response['success']='completed';
                  $response['success_message']='Curl process completed.!';
                  $response['error_message']='';
                      echo json_encode($response); 
                      Session::put('import_status', false);
                      Session::put('import_message', 'Successfully curled all data');
                      Session::save();
                  exit;

          // return view('display_curled_by_cin');
      }




function DevOps_Curl($cin)
    {
          $curl = curl_init();
              $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://www.mca.gov.in/mcafoportal/companyLLPMasterData.do",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $ua,
            CURLOPT_COOKIE => 'HttpOnly; HttpOnly; JSESSIONID=0000kyt5ZCBdl8AevScIVlstsTU:1ckvd911h',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST =>true,
            CURLOPT_POSTFIELDS => "companyName=&companyID=".$cin."&displayCaptcha=false&userEnteredCaptcha=",
          ));
          $headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: HttpOnly; HttpOnly; JSESSIONID=0000NNVTYYHAcyCuqSQ7fKS_Wql:1ckvd911h',
                'Referer: http://www.mca.gov.in/mcafoportal/companyLLPMasterData.do',
                'Upgrade-Insecure-Requests: 1',
            ];
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $response = preg_split( "/([\r\n][\r\n])\\1/",curl_exec($curl));
            $err = curl_error($curl);
            curl_close($curl);
                if ($err) {
                  return $err;
                } else {
                  return  $response;
                }
    }


    public function ViewImportData()
        {
            $get_data = ImportedExcelData::orderBy('id','asc')->get();
            // $get_data = ImportedExcelData::orderBy('id','asc')->where('llpin',null)->get();
            // $get_data_llpin = ImportedExcelData::orderBy('id','asc')->where('cin',null)->get();
            // $get_data =DB::table('imported_excel_data');
            // $get_data =DB::table('imported_excel_data')->paginate(10);
            
            // dd($get_data);
            // echo "<pre>"; print_r($get_data); echo "</pre>"; die('end of code');
            return view('display_imported', compact('get_data'));
            
            
        }
         public function ViewImportLog()
        {
            $get_data = CurlFetchExcelLog::orderBy('fetched_on','DESC')->get();
            
            // dd($get_data);

            // echo json_encode($get_data);

     
            
            return view('display_imported_log', compact('get_data'));
            
            
        }

           public function ViewCurledData()
        {
            $get_data = CurlFetchedByCin::get();
            
            // dd($get_data);
            
            return view('display_curled_by_cin', compact('get_data'));
            
            
        }

          public function Test()
        {
date_default_timezone_set('Asia/Kolkata');
              $file_name  = date("Y-m-d").'.xlsx' ;
              $time_stamp = date("Y/m/d") ;

              echo $time_stamp;
              $file_exists = storage_path('app/'.$time_stamp.'/'.$file_name);

              if (file_exists($file_exists)) 
              {
                
                echo "yes";
              }
              else
              {
                echo "no";
              }

          // exit;

               $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
               $reader->setReadDataOnly(true);
               // $storage = storage_path('app/'.$file_path.$file_name);
               $storage = storage_path('app/FetchedExcel/2019/03/29/2019-03-29.xlsx');
               $reader->setLoadSheetsOnly('LLP Registered');
               $spreadsheet = $reader->load($storage);
               // $sheetnames = ['Data Sheet #1', 'Data Sheet #3'];

               $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

               print_r($sheetData);
               exit;


                $errors=[];
                 $i=1;
            foreach ($sheetData as $key => $value)
            {
              if ($i>2) 
              {
                 // insert log to db  if success
                $imported_excel_data = new ImportedExcelData ();

                // SELECT `id`, `cin`, `compay_name`, `doi`, `state`, `roc`, `category`, `sub_category`, `class`, `authorized_capital`, `paid_capital`, `nof_members`, `activity_description`, `reg_office_address` FROM `imported_excel_data`

                 // $get_avialable = ImportedExcelData::whereEmail($email)->first();
                $cin_check=ImportedExcelData::where('cin',$value['A'])->count();

                // dd($cin_check);

                if ($cin_check)
                {
                    
                }
                else
                {
                   $imported_excel_data->cin =$value['A'];
                   $imported_excel_data->compay_name =$value['B'];
                   $imported_excel_data->doi =$value['C'];
                   $imported_excel_data->state =$value['D'];
                   $imported_excel_data->roc =$value['E'];
                   $imported_excel_data->category ="";
                   $imported_excel_data->sub_category ="";
                   $imported_excel_data->class ="";
                   $imported_excel_data->authorized_capital="";
                   $imported_excel_data->paid_capital ="";
                   $imported_excel_data->nof_members =0;
                   $imported_excel_data->nof_partner =$value['F'];
                   $imported_excel_data->nof_designed_partner =$value['G'];
                   $imported_excel_data->total_a_o_c =$value['H'];
                   $imported_excel_data->activity_description =$value['I'];
                   $imported_excel_data->reg_office_address =$value['J'];
                   $imported_excel_data->save ();
                }
                
                   
                  
              }

              $i=$i+1;
            }
              $errors['import_success']= "Imported Successfully";
                return redirect(route('import_process'))->withErrors($errors);

        }

        public function ExportAsExcel()
        {
          // echo "Hai buddy";
          $get_data = ImportedExcelData::where('llpin',null)->get();
          $get_data_llpin = ImportedExcelData::where('cin',null)->get();
          $get_data_directors = CurlFetchedByCin::get();

          // echo "Processing the data please wait......";
          $spreadsheet = new Spreadsheet();

          // Set document properties
          $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
              ->setLastModifiedBy('Maarten Balliauw')
              ->setTitle('Office 2007 XLSX Test Document')
              ->setSubject('Office 2007 XLSX Test Document')
              ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
              ->setKeywords('office 2007 openxml php')
              ->setCategory('Test result file');


               $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'CIN')
              ->setCellValue('C1', 'COMPANY NAME')
              ->setCellValue('D1', 'EMAIL')
              ->setCellValue('E1','DIRECTOR DETAILS')
              ->setCellValue('F1', 'DOI')
              ->setCellValue('G1', 'STATE')
              ->setCellValue('H1', 'ROC')
              ->setCellValue('I1', 'CATEGORY')
              ->setCellValue('J1', 'SUB CATEGORY')
              ->setCellValue('K1', 'CLASS')
              ->setCellValue('L1', 'AUTHARIXED CAPITAL')
              ->setCellValue('M1', 'PAID CAPITAL')
              ->setCellValue('N1', 'NO OF MEMBERS')
              ->setCellValue('O1', 'ACTIVITY DESCRIPTION')
              ->setCellValue('P1', 'REGISTERED OFFICE ADDRESS');

              // dd($get_data);

              $i=2;

           foreach ($get_data as  $value) 
          {

            $directors ="";
            $director_array= json_decode($value->director_details, true);
            $di=1;
              foreach ($director_array as $key1 => $value1) 
              {
                foreach ($value1 as $k => $v) 
                {
                    $directors .=' '.$k.':'.$v.' ';
                }
                 $directors .= "                   ";
                 $di=$di+1;
                                              
              }
              // dd($directors);
                                  
             $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $value->id)
              ->setCellValue('B'.$i, $value->cin)
              ->setCellValue('C'.$i, $value->company_name)
              ->setCellValue('D'.$i, $value->email)
              ->setCellValue('E'.$i, $directors)
              ->setCellValue('F'.$i, $value->doi)
              ->setCellValue('G'.$i, $value->state)
              ->setCellValue('H'.$i, $value->roc)
              ->setCellValue('I'.$i, $value->category)
              ->setCellValue('J'.$i, $value->sub_category)
              ->setCellValue('K'.$i, $value->class)
              ->setCellValue('L'.$i, $value->authorized_capital)
              ->setCellValue('M'.$i, $value->paid_capital)
              ->setCellValue('N'.$i, $value->nof_members)
              ->setCellValue('O'.$i, $value->activity_description)
              ->setCellValue('P'.$i, $value->reg_office_address);
             

            $i=$i+1; 
          }
// echo "hai";

// echo json_encode($spreadsheet);
// exit;



          // Add some data
          // $spreadsheet->setActiveSheetIndex(0)
          //     ->setCellValue('A1', 'Hello')
          //     ->setCellValue('B2', 'world!')
          //     ->setCellValue('C1', 'Hello')
          //     ->setCellValue('D2', 'world!');

          // // Miscellaneous glyphs, UTF-8
          // $spreadsheet->setActiveSheetIndex(0)
          //     ->setCellValue('A4', 'Miscellaneous glyphs')
          //     ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

          $date=Date('Y-m-d');

          // Rename worksheet
          $spreadsheet->getActiveSheet()->setTitle('Indian Companies Registered');

          // // Set active sheet index to the first sheet, so Excel opens this as the first sheet
          // $spreadsheet->setActiveSheetIndex(0);

          $spreadsheet->createSheet();
          // // Add some data to the second sheet, resembling some different data types
          $spreadsheet->setActiveSheetIndex(1);
          // $spreadsheet->getActiveSheet()->setCellValue('A1', 'More data');
           $spreadsheet->setActiveSheetIndex(1)
              ->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'LLPIN')
              ->setCellValue('C1', 'COMPANY NAME')
              ->setCellValue('D1', 'EMAIL')
              ->setCellValue('E1','DIRECTOR DETAILS')
              ->setCellValue('F1', 'DOI')
              ->setCellValue('G1', 'STATE')
              ->setCellValue('H1', 'ROC')
              ->setCellValue('I1', 'NO OF PRTNER')
              ->setCellValue('J1', 'NO OF DESIGNED PRTNER')
              ->setCellValue('K1', 'ACTIVITY DESCRIPTION')
              ->setCellValue('L1', 'REGISTERED OFFICE ADDRESS');

          $j=1;
          foreach ($get_data_llpin as  $llpin) 
          {
             $spreadsheet->getActiveSheet()
              ->setCellValue('A'.$j, $llpin->id)
              ->setCellValue('B'.$j, $llpin->llpin)
              ->setCellValue('C'.$j, $llpin->company_name)
              ->setCellValue('D'.$j, $llpin->email)
              ->setCellValue('E'.$j, $llpin->director_details)
              ->setCellValue('F'.$j, $llpin->doi)
              ->setCellValue('G'.$j, $llpin->state)
              ->setCellValue('H'.$j, $llpin->roc)
              ->setCellValue('I'.$j, $llpin->nof_partner)
              ->setCellValue('J'.$j, $llpin->nof_designed_partner)
              ->setCellValue('K'.$j, $llpin->activity_description)
              ->setCellValue('L'.$j, $llpin->reg_office_address);

            $j=$j+1; 
          }

          // // Rename 2nd sheet
          $spreadsheet->getActiveSheet()->setTitle('LLP Registered');



                $spreadsheet->createSheet();
          // // Add some data to the second sheet, resembling some different data types
          $spreadsheet->setActiveSheetIndex(2);
          // $spreadsheet->getActiveSheet()->setCellValue('A1', 'More data');
           $spreadsheet->setActiveSheetIndex(2)
              ->setCellValue('A1', 'CIN')
              ->setCellValue('B1', 'LLPIN')
              ->setCellValue('C1', 'COMPANY NAME')
              ->setCellValue('D1','DIN/PAN')
              ->setCellValue('E1', 'NAME')
              ->setCellValue('F1', 'BEGIN DATE')
              ->setCellValue('G1', 'END DATE')
              ->setCellValue('H1', 'SURRENDER DIN')
              ->setCellValue('I1', 'UPDATED ON');

          $k=1;
          foreach ($get_data_directors as  $direct) 
          {
             $spreadsheet->getActiveSheet()
              ->setCellValue('A'.$k, $direct->cin)
              ->setCellValue('B'.$k, $direct->llpin)
              ->setCellValue('C'.$k, $direct->company_name)
              ->setCellValue('D'.$k, $direct->din_pan)
              ->setCellValue('E'.$k, $direct->name)
              ->setCellValue('F'.$k, $direct->begindate)
              ->setCellValue('G'.$k, $direct->enddate)
              ->setCellValue('H'.$k, $direct->surrendereddin)
              ->setCellValue('I'.$k, $direct->updated_on);

            $k=$k+1; 
          }

          // // Rename 3nd sheet
          $spreadsheet->getActiveSheet()->setTitle('Directors Details');





          // Redirect output to a client’s web browser (Xlsx)
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename="Exported-data-'.$date.'.xlsx"');
          header('Cache-Control: max-age=0');
          // If you're serving to IE 9, then the following may be needed
          header('Cache-Control: max-age=1');

          // If you're serving to IE over SSL, then the following may be needed
          header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
          header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
          header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
          header('Pragma: public'); // HTTP/1.0

          $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
          $writer->save('php://output');
          exit;

          
        }


        public function ImportStatus()
        {

          // // $g = Session::all();


          //     echo Session::get('import_message');

          //     // print_r($g);

          //           exit;
          $response = array();
          // $response['status']=$this->import_in_progress;
          // $response['message']=$this->import_message;
          $response['status']=Session::get('import_status');
          $response['message']=Session::get('import_message');


          echo json_encode($response);
          exit;
        }

        public function InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$director)
        {

                        $insert_curl_data = new CurlFetchedByCin();

                        // {"din/pan":"08391245","name":"suneetha chejerla","begindate":"15/03/2019","enddate":"-","surrendereddin":""}
                        date_default_timezone_set('Asia/Kolkata');

                        foreach ($director as $key => $value) 
                        {
                           $set_din =$director['din/pan'];
                           $set_name =$director['name'];
                           $set_begin =$director['begindate'];
                           $set_end =$director['enddate'];
                           $set_surrender =$director['surrendereddin'];
                        }


                       if ($cin_or_llpin==1) 
                       {
                          $cin_check=CurlFetchedByCin::where('llpin',$cin)->where('din_pan',$set_din)->count();
                       }
                       else
                       {
                          $cin_check=CurlFetchedByCin::where('cin',$cin)->where('din_pan',$set_din)->count();
                       }

                       $update_cin=[];
                      

                       if ($cin_check) 
                       {
                            // $update_cin['email_id']=$set_email;
                            // // $update_cin['din_pan']=$set_din;
                            // $update_cin['array_company_llp']=$Array_company_llp;
                            // $update_cin['array_directory_llp']=$Array_directory_main;
                            // $update_cin['array_charges']=$Array_charges;
                            $update_cin['status'] =1;
                            $update_cin['updated_on'] =date("Y-m-d");

                            if ($cin_or_llpin==1) 
                            {
                              $update_cin_curl=CurlFetchedByCin::where('llpin',$cin)->where('din_pan',$set_din)->update($update_cin);
                            }
                            else
                            {
                              $update_cin_curl=CurlFetchedByCin::where('cin',$cin)->where('din_pan',$set_din)->update($update_cin);
                            }


                              Session::put('import_status', true);
                              Session::put('import_message', 'Item -> '.$cin.' Updated by curl');
                              Session::save();





                       }
                       else
                       {
                              if ($cin_or_llpin==1) 
                             {
                               $insert_curl_data->llpin =$cin;

                             }
                             else
                             {
                               $insert_curl_data->cin =$cin;
                             }
                               // $insert_curl_data->email_id =$set_email;
                               $insert_curl_data->din_pan =$set_din;
                               $insert_curl_data->company_name =$set_company;
                               $insert_curl_data->name =$set_name;
                               $insert_curl_data->begindate =$set_begin;
                               $insert_curl_data->enddate =$set_end;
                               $insert_curl_data->surrendereddin =$set_surrender;
                               // $insert_curl_data->status =1;
                               $insert_curl_data->updated_on =date("Y-m-d h:i:s");
                               // $insert_curl_data->array_company_llp =$Array_company_llp;
                               // $insert_curl_data->array_directory_llp =$Array_directory_main;
                               // $insert_curl_data->array_charges =$Array_charges;
                               $insert_curl_data->save ();

                                  Session::put('import_status', true);
                                  Session::put('import_message', 'Item -> '.$cin.' Processed by curl');
                                  Session::save();
                       }

        }

        public function StopCurl()
        {

                      Session::put('import_status', false);
                      Session::put('import_message', 'Curl Fully Stoped Now.......');
                      Session::save();

                      $response=array();
                      $response['status']='stopped';

                      echo json_encode($response);
                      exit;

        }



// this function used for cron job only 

        public function CronImport()
        {


           $url =  \DB::table('curl_setting')->where('options', 'curl_url')->first();
              $url = $url->value;   

              $errors = [];
              date_default_timezone_set('Asia/Kolkata');
              $file_name  = date("Y-m-d").'.xlsx' ;
              $time_stamp = date("Y/m/d") ;
              // $file_path  = 'FetchedExcel/'.$time_stamp.'/';
              $file_exists = storage_path('app/FetchedExcel/'.$time_stamp.'/'.$file_name);

              // check if file exist through error mesage 
               if (file_exists($file_exists))
               {
              
                  
                      $fetched_excel_log = new CurlFetchExcelLog ();
                      $fetched_excel_log->file_name = $file_name;
                      $fetched_excel_log->file_path = $file_exists;
                      $fetched_excel_log->fetched_on = $time_stamp;
                      $fetched_excel_log->logs = 'File already avialable';
                      $fetched_excel_log->status = '-1';
                      $fetched_excel_log->save ();
                      // $errors['fetch_error']="File already avialable for more information see the log";

                    // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'File: '.$file_name.' already avialable';
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();
                        exit;

               }
               

              $this->DevOps_Curl_Fetch_Excel_Cron($url );


        }


          public function DevOps_Curl_Fetch_Excel_Cron($url )
    {
          $errors = [];

            set_time_limit(0); 
            $errors=[];
            // $url  = 'http://www.mca.gov.in/mcafoportal/companiesRegReport.do';
           $curl = curl_init();
           $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';

          curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_USERAGENT => $ua,
          CURLOPT_COOKIE => 'NID=67=pdjIQN5CUKVn0bRgAlqitBk7WHVivLsbLcr7QOWMn35Pq03N1WMy6kxYBPORtaQUPQrfMK4Yo0vVz8tH97ejX3q7P2lNuPjTOhwqaI2bXCgPGSDKkdFoiYIqXubR0cTJ48hIAaKQqiQi_lpoe6edhMglvOO9ynw; PREF=ID=52aa671013493765:U=0cfb5c96530d04e3:FF=0:LD=en:TM=1370266105:LM=1370341612:GM=1:S=Kcc6KUnZwWfy3cOl; OTZ=1800625_34_34__34_; S=talkgadget=38GaRzFbruDPtFjrghEtRw; SID=DQAAALoAAADHyIbtG3J_u2hwNi4N6UQWgXlwOAQL58VRB_0xQYbDiL2HA5zvefboor5YVmHc8Zt5lcA0LCd2Riv4WsW53ZbNCv8Qu_THhIvtRgdEZfgk26LrKmObye1wU62jESQoNdbapFAfEH_IGHSIA0ZKsZrHiWLGVpujKyUvHHGsZc_XZm4Z4tb2bbYWWYAv02mw2njnf4jiKP2QTxnlnKFK77UvWn4FFcahe-XTk8Jlqblu66AlkTGMZpU0BDlYMValdnU; HSID=A6VT_ZJ0ZSm8NTdFf; SSID=A9_PWUXbZLazoEskE; APISID=RSS_BK5QSEmzBxlS/ApSt2fMy1g36vrYvk; SAPISID=ZIMOP9lJ_E8SLdkL/A32W20hPpwgd5Kg1J',
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "",
          CURLOPT_HTTPHEADER => array(
             "Content-Type: application/x-www-form-urlencoded",
             "cache-control: no-cache"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) 
        {
          // echo $err;
          // insert log if error
                $fetched_excel_log = new CurlFetchExcelLog();
                $fetched_excel_log->file_name = 'No filename';
                $fetched_excel_log->file_path = 'Not created';
                $fetched_excel_log->fetched_on = date("Y-m-d-h:i:s:a");
                $fetched_excel_log->logs = json_encode($err);
                $fetched_excel_log->status = '-1';
                $fetched_excel_log->save ();


                 // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'No File in Requested url: '.$url;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();

               
                exit;

        } 
        else 
        {
         
             // declare file name and path  
              date_default_timezone_set('Asia/Kolkata');
              $file_name  = date("Y-m-d").'.xlsx' ;
              $time_stamp = date("Y/m/d") ;
              $file_path  = 'FetchedExcel/'.$time_stamp.'/';
              // $file_exists = storage_path('app/FetchedExcel/'.$time_stamp.'/'.$file_name);
            
                    // create file in local storage path
                     $file_create = Storage::disk('local')->put($file_path.$file_name, $response);

                    // insert log to db  if success
                      $fetched_excel_log = new CurlFetchExcelLog ();
                      $fetched_excel_log->file_name = $file_name;
                      $fetched_excel_log->file_path = $file_path;
                      $fetched_excel_log->fetched_on = $time_stamp;
                      $fetched_excel_log->logs = 'Seccessfully Fetched';
                      $fetched_excel_log->status = '1';
                      $fetched_excel_log->save ();



                          // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'Successfully Fetched the File From: '.$url;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();


                      $this->DevOps_Import_Fetched_Excel_Cron($file_name,$file_path);


        }
            
    }


    public function DevOps_Import_Fetched_Excel_Cron($file_name,$file_path )
    {

                  $errors =[];

// read excel file by use phpspreadsheet library=>

           $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
           $reader->setReadDataOnly(true);
           $storage = storage_path('app/'.$file_path.$file_name);
           $spreadsheet = $reader->load($storage);
           $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

      if (!empty($sheetData))
      {

   

             $i=1;
            foreach ($sheetData as $key => $value)
            {
              if ($i>2) 
              {
                 // insert log to db  if success
                $imported_excel_data = new ImportedExcelData ();

                // SELECT `id`, `cin`, `compay_name`, `doi`, `state`, `roc`, `category`, `sub_category`, `class`, `authorized_capital`, `paid_capital`, `nof_members`, `activity_description`, `reg_office_address` FROM `imported_excel_data`

                 // $get_avialable = ImportedExcelData::whereEmail($email)->first();
                $cin_check=ImportedExcelData::where('cin',$value['A'])->count();

                // dd($cin_check);

                if ($cin_check)
                {
                    // for future process if data already available 
                }
                else
                {
                   $imported_excel_data->cin =$value['A'];
                   $imported_excel_data->compay_name =preg_replace("/[^a-zA-Z ]/","", $value['B']);
                   $imported_excel_data->doi =$value['C'];
                   $imported_excel_data->state =$value['D'];
                   $imported_excel_data->roc =$value['E'];
                   $imported_excel_data->category =$value['F'];
                   $imported_excel_data->sub_category =$value['G'];
                   $imported_excel_data->class =$value['H'];
                   $imported_excel_data->authorized_capital=$value['I'];
                   $imported_excel_data->paid_capital =$value['J'];
                   $imported_excel_data->nof_members =$value['K'];
                   $imported_excel_data->activity_description =$value['L'];
                   $imported_excel_data->reg_office_address =$value['M'];
                   $imported_excel_data->save ();
                }
                
                   
                  
              }

              $i=$i+1;

            }
  
                          // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'First Sheeet # Indian Companies Registered Datas are inserted Successfully From File: '.$file_name;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();

        }//if check empty data or not
        else
        {

          // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'The Excel file does not have proper data to insert: '.$file_name;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();

        }



    //insert LLP Sheet data here
            
               $reader1 = new \PhpOffice\PhpSpreadsheet\reader\Xlsx();
               $reader1->setReadDataOnly(true);
               $storage1 = storage_path('app/'.$file_path.$file_name);
               $reader1->setLoadSheetsOnly('LLP Registered');
               $spreadsheet1 = $reader1->load($storage1);

               $sheetData1 = $spreadsheet1->getActiveSheet()->toArray(null, true, true, true);

                 $j=1;

      if (!empty($sheetData1)) 
      {
            foreach ($sheetData1 as $key1 => $value1)
            {
              if ($j>2) 
              {
                 // insert log to db  if success
                $imported_excel_data1 = new ImportedExcelData ();

                // SELECT `id`, `cin`, `compay_name`, `doi`, `state`, `roc`, `category`, `sub_category`, `class`, `authorized_capital`, `paid_capital`, `nof_members`, `activity_description`, `reg_office_address` FROM `imported_excel_data1`

                 // $get_avialable = ImportedExcelData::whereEmail($email)->first();
                $cin_check1=ImportedExcelData::where('llpin',$value1['A'])->count();


                if ($cin_check1)
                {
                    // for future process if data already available 
                }
                else
                {
                   $imported_excel_data1->llpin =$value1['A'];
                   $imported_excel_data1->compay_name =preg_replace("/[^a-zA-Z ]/","", $value1['B']);
                   $imported_excel_data1->doi =$value1['C'];
                   $imported_excel_data1->state =$value1['D'];
                   $imported_excel_data1->roc =$value1['E'];

                   $imported_excel_data1->nof_partner =$value1['F'];
                   $imported_excel_data1->nof_designed_partner =$value1['G'];
                   $imported_excel_data1->total_a_o_c =$value1['H'];
                   $imported_excel_data1->activity_description =$value1['I'];
                   $imported_excel_data1->reg_office_address =$value1['J'];
                   $imported_excel_data1->save ();
                }
                
                   
                  
              }

              $j=$j+1;

                
            } 
              // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'Second Sheeet # LLP  Registered Companies Datas are inserted Successfully From File: '.$file_name;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();

        }//if check empty data or not
        else
        {

          // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'The Excel file does not have proper data to insert: '.$file_name;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();

        }

// call curl function here
             $this->ProcessCurlCron();

      }


      public function ProcessCurlCron()
      {
 

                 $get_datas = ImportedExcelData::orderBy('id','asc')->where('status',0)
                               ->get();

//check case if all data are up-todate current records  sop the process and through the alrt message                       
            if (empty($get_datas)) 
            {

                      // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'All Details are Up-to-date, No need to run cur';
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();
                          exit;
             
            }



          $ii=1;
          foreach ($get_datas as $data) 
          {
            try
            {

                $cin = $data->cin;
                $llpin =$data->llpin;
                 $set_company=  $data->compay_name;

//here we find the which id comming CIN or LLPIN  
//idetify by the $cin_or_llpin if 0= cin if 1=llpin                

                $cin_or_llpin=0;

                if (empty($cin)) 
                {
                  $cin = $data->llpin;
                  $llpin =$data->llpin;
                  $cin_or_llpin=1;
                }

                 sleep(5); // if need configure timeout from curl_time_delay => curl_setting table
                $get_curl =$this->DevOps_Curl($cin);

// get response and parse by Use DOMDocument

                libxml_use_internal_errors(true);
                $dom = new \DOMDocument();
              $get_html_table =  $dom->loadHTML($get_curl[8]); 

   if (!empty($get_html_table)) 
    {          
                $tables = $dom->getElementsByTagName('table');

// check the case if curl return empty html with out table 
                if ($tables->length>0) 
            {


//get firt table from  responce html  table contains company llp details

                $table_company_llp = $tables->item(0)->getElementsByTagName('tr'); 

            if ($table_company_llp->length>0) 
            {

              
                    $Array_company_llp = array();

                foreach ($table_company_llp as $company_llp) 
                {



                    /*** get each column by tag name ***/ 
                    $cols = $company_llp->getElementsByTagName('td'); 

                    // echo $cols->item(0)->nodeValue.'-'.$cols->item(1)->nodeValue;
                    $key = strtolower( str_replace(" ", "", $cols->item(0)->nodeValue));

                    $key= preg_replace('/[^A-Za-z0-9\-]/', '', $key);

                    $Array_company_llp[$key]=$cols->item(1)->nodeValue;
                        // $Array_company_llp=array_merge($Array_company_llp,array($key=>$cols->item(1)->nodeValue));

                }                
               
                $set_cin =(!empty($llpin))? $Array_company_llp['llpin']:$Array_company_llp['cin'];

                // echo $set_cin;
                // exit;
                $set_email=  $Array_company_llp['emailid'];
 


    // convert json encoded result
                  $Array_company_llp = json_encode( $Array_company_llp,JSON_UNESCAPED_SLASHES);


//get second table form responce html  table contains charges details

                 $Array_charges = array();
                $table_charges_th = $tables->item(1)->getElementsByTagName('th'); 
                $table_charges_td = $tables->item(1)->getElementsByTagName('td');


                // print_r($table_charges_td);
                // exit;
                 for ($i = 0; $i < $table_charges_th->length; $i++)
                {
                      

                   $Array_charges[strtolower(str_replace(" ", "", $table_charges_th->item($i)->nodeValue  ))] =  $table_charges_td->item(0)->nodeValue;
                }
                // echo $Array_charges['dateofcreation'];

                $Array_charges =  json_encode( $Array_charges,JSON_UNESCAPED_SLASHES);    
             
//get third table form responce html  table contains directors details



                $table_director_details_th = $tables->item(2)->getElementsByTagName('th'); 
                // $table_director_details_tr = $tables->item(2)->getElementsByTagName('tr'); 
                $table_director_details_td = $tables->item(2)->getElementsByTagName('td'); 

// if check case no director details in response
             
        if ($table_director_details_td->length>1) 
        {

                $Array_directory_llp= array();

                $Array_directory_llp_key=array();

                $Array_directory_llp_val_1=array();
                $Array_directory_llp_val_2=array();
                $Array_directory_llp_val=array();

// initialize he array variable here

                $Array_directory_main=array();
                $Array_directory_sub_1=array();
                $Array_directory_sub_2=array();
                $Array_directory_sub_3=array();
                $Array_directory_sub_4=array();
                $Array_directory_sub_5=array();
                $Array_directory_sub_6=array();




// declare array key value pair

 //  first director 
                   for ($i = 0; $i < $table_director_details_th->length; $i++)
                  {
                        // $Array_directory_llp[strtolower(str_replace(" ", "", $table_director_details_th->item($i)->nodeValue ))] =  $table_director_details_td->item($i)->nodeValue;
                    
                      $Array_directory_sub_1[strtolower(str_replace(" ", "", $table_director_details_th->item($i)->nodeValue ))]= trim($table_director_details_td->item($i)->nodeValue);

                  }
                  // $Array_directory_main['1']=json_encode($Array_directory_sub_1,JSON_UNESCAPED_SLASHES);
                  $Array_directory_main['1']=$Array_directory_sub_1;

                  $this->InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$Array_directory_sub_1);


// if have second director
                     if ($table_director_details_td->length>5) 
                      {
                        $k=0;
                         for ($j=5; $j <10  ; $j++) 
                         { 

                            $Array_directory_sub_2[strtolower(str_replace(" ", "", $table_director_details_th->item($k)->nodeValue))]=trim($table_director_details_td->item($j)->nodeValue);
                            $k=$k+1;
                         }

                         // $Array_directory_main['2']=json_encode($Array_directory_sub_2,JSON_UNESCAPED_SLASHES);
                         $Array_directory_main['2']=$Array_directory_sub_2;
                         $this->InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$Array_directory_sub_2);


                      }
// if have third director
                      if ($table_director_details_td->length>10) 
                      {
                        $m=0;
                         for ($l=10; $l <15 ; $l++) 
                         { 

                            $Array_directory_sub_3[strtolower(str_replace(" ", "", $table_director_details_th->item($m)->nodeValue))]=trim($table_director_details_td->item($l)->nodeValue);
                            $m=$m+1;
                         }
                         // $Array_directory_main['3']=json_encode($Array_directory_sub_3,JSON_UNESCAPED_SLASHES);
                         $Array_directory_main['3']=$Array_directory_sub_3;
                         $this->InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$Array_directory_sub_3);
                      }
// if have fourth director
                       if ($table_director_details_td->length>15) 
                      {
                        $o=0;
                         for ($n=15; $n <20  ; $n++) 
                         { 

                            $Array_directory_sub_4[strtolower(str_replace(" ", "", $table_director_details_th->item($o)->nodeValue))]=trim($table_director_details_td->item($n)->nodeValue);
                            $o=$o+1;
                         }
                         // $Array_directory_main['4']=json_encode($Array_directory_sub_4,JSON_UNESCAPED_SLASHES);
                         $Array_directory_main['4']=$Array_directory_sub_4;
                         $this->InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$Array_directory_sub_4);
                      }
// if have fifth director
                       if ($table_director_details_td->length>20) 
                      {
                        $q=0;
                         for ($p=20; $p <25  ; $p++) 
                         { 

                            $Array_directory_sub_5[strtolower(str_replace(" ", "", $table_director_details_th->item($q)->nodeValue))]=trim($table_director_details_td->item($p)->nodeValue);
                            $q=$q+1;
                         }
                         // $Array_directory_main['5']=json_encode($Array_directory_sub_5,JSON_UNESCAPED_SLASHES);
                         $Array_directory_main['5']=$Array_directory_sub_5;
                         $this->InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$Array_directory_sub_5);
                      }

// if have six director
                       if ($table_director_details_td->length>25) 
                      {
                        $s=0;
                         for ($r=25; $r <30  ; $r++) 
                         { 

                            $Array_directory_sub_6[strtolower(str_replace(" ", "", $table_director_details_th->item($s)->nodeValue))]=trim($table_director_details_td->item($r)->nodeValue);
                            $s=$s+1;
                         }
                         // $Array_directory_main['6']=json_encode($Array_directory_sub_6,JSON_UNESCAPED_SLASHES);
                         $Array_directory_main['6']=$Array_directory_sub_6;
                         $this->InsertDirectorDetails($cin,$cin_or_llpin,$set_company,$Array_directory_sub_6);
                      }



                      $Array_directory_main = json_encode($Array_directory_main,JSON_UNESCAPED_SLASHES);
               
                   

                       // update email and details in main table
                            $update_main_table=[];
                            $update_main_table['email'] =$set_email;
                            $update_main_table['status'] =1;
                            $update_main_table['updated_on'] =date("Y-m-d h:i:s");
                            $update_main_table['director_details'] =$Array_directory_main;
                       if (!empty($llpin)) 
                       {
                         
                       $update_cin_table=ImportedExcelData::where('llpin',$llpin)->update($update_main_table);
                       }
                      else
                       {
                          $update_cin_table=ImportedExcelData::where('cin',$cin)->update($update_main_table);
                       }



                }//if end no directors details
                else
                {
                            $update_main_table_error=[];
                            $update_main_table_error['email'] =$set_email;
                            $update_main_table_error['status'] =-1;
                            $update_main_table_error['updated_on'] =date("Y-m-d h:i:s");
                            $update_main_table_error['director_details'] ="";
                       if (!empty($llpin)) 
                       {
                         
                       $update_cin_table_error=ImportedExcelData::where('llpin',$llpin)->update($update_main_table_error);
                       }
                      else
                       {
                          $update_cin_table_error=ImportedExcelData::where('cin',$cin)->update($update_main_table_error);
                       }

                        // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'No director Details Found for the record => '.$cin;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();




                }   //else end no directors details


                        $ii=$ii+1;




                 
            }   // if table-tr end

            }//if table->length>0 end 

        }//if end dom eampty or not
        else
        {

                        $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'No director Details Found for the record => '.$cin;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();


        }

          }// try end 

            catch (Exception $e) 
            {
                    
                        
                           // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'Unable to fetch record for '.$data->cin;
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save ();             
      
            }

          } // for each end here


          //response log's


                 // insert Log
                       $fetched_excel_log_cron = new CronLog();
                       $fetched_excel_log_cron->log = 'Cron Job Curl Process Successfully Completed';
                       $fetched_excel_log_cron->updated_on = date("Y-m-d h:i:s:a");
                       $fetched_excel_log_cron->save (); 
                    
                    
               
                  exit;

      }


      










}
