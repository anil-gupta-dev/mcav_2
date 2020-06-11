<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;


use App\Models\CurlFetchExcelLog;
use App\Models\ImportedExcelData;
use App\Models\CurlFetchedByCin;
use App\Models\CronLog;
use App\Models\EmailSettings;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use File;
use Storage;
use Session;

use App\Notifications\SendEmailByAdmin;
use App\Jobs\SendEmailByAdminJob;
use App\Jobs\SendEmailByCronJob;



// use App\Http\Controllers\Artisan;
use Artisan;
class CronEmailController extends Controller
{
    public function sendEmailCron1()
    {
    	$state 	 	 ='Maharashtra';
    	$roc 	 	 =$request->roc;
    	$doi 		 =$request->doi;
    	$description =$request->description;
    	$category 	 =$request->category;

    }
    public function sendEmailCron()
    {
    	// echo now();
    	/*    • The automated mails will be sent only to the following selections:
        ◦ State > Maharashtra
        ◦ ROC > All within Maharashtra
        ◦ DOI > Yesterday  / Month
        ◦ Activity > All 
            ▪ All respective template per activity will be applied accordingly. */

        $get_doi_option =EmailSettings::where('option','cron_email_doi')->pluck('value');
        if (!empty($get_doi_option)) {

        	$get_doi =$get_doi_option[0];

        	if ($get_doi=='Yesterday') 
        	{
        		$yesterday_date = date('Y-m-d', strtotime('-7 days'));
                $first_day_this_month =null;
                $last_day_this_month =null;
        		dispatch(new SendEmailByCronJob($yesterday_date, $first_day_this_month, $last_day_this_month));

        	}
        	elseif ($get_doi=='Month') 
        	{
                $yesterday_date = date('Y-m-d', strtotime('-5 days'));
        		$time=strtotime($yesterday_date);
				$month=date("m",$time);
				$year=date("Y",$time);

				$first_day_this_month = date('Y-'.$month.'-01'); // hard-coded '01' for first day
				$last_day_this_month  = date('Y-'.$month.'-t');
                $yesterday_date=null;

                // $get_data =\DB::table("imported_excel_data")->distinct()->whereNotNull('email')->where('state','=','Maharashtra')->whereBetween('doi',[$first_day_this_month, $last_day_this_month])->get();

                // dd($get_data);
                dispatch(new SendEmailByCronJob($yesterday_date, $first_day_this_month, $last_day_this_month));

        	}
        	
        }
        else{
        	 $insert_log = new CronLog();
             $insert_log->log = 'Email Sent By Cron Failed Not found Option=> cron_email_doi';
             $insert_log->updated_on = date("Y-m-d h:i:s:a");
             $insert_log->save ();
        }
    	

		


     
        dd($get_doi);

    

    }
}
