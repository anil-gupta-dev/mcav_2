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

// use App\Http\Controllers\Artisan;
use Artisan;

class EmailController extends Controller
{
    //

    public function dashboardView()
    {
    	return view('email.dashboard');
    }
     public function createEmailView()
    {
    	// $get_description = ImportedExcelData::get();
    	$get_description = \DB::table('imported_excel_data')->distinct()->whereNotNull('activity_description')->get(['activity_description']);
    	$get_category    = \DB::table('imported_excel_data')->distinct()->whereNotNull('category')->get(['category']);
    	
    	return view('email.create_new',compact('get_category','get_description'));
    }
    public function storeEmailTemplate(Request $request)
    {
    	$data = $request->all();
    	$original_path = storage_path('email/');
    	// dd($original_path);
    	if (file_exists($original_path.$data['attachment_path']))
    	{
    		echo $request->activity_description;

    		$get_privious_template =EmailTemplate::where('activity_description','=',$request->activity_description)->get();
    		if (count($get_privious_template)==1) 
    		{
    			return redirect(route('create.email.template'))->with('error','The template already available for this  Category Description <br> <b> '.$data['activity_description'] .'</b> ');
    		}else
    		{
    			$template = new EmailTemplate;
		    	$template->template_name  		 = $data['template_name'];
		    	// $template->category  			 = $data['category'];
		    	$template->activity_description  = $data['activity_description'];
		    	$template->subject  			 = $data['subject'];
		    	$template->message  			 = $data['message'];
		    	$template->attachment_path  	 = $data['attachment_path'];
		    	$template->status  				 = 1;
		    	$template->save();
		    	return redirect(route('view.email.template'))->with('success','Created successfully');
    		}
    		
    	}
    	else
    	{
    		return redirect(route('create.email.template'))->with('error','The Attachment Path does not contain file');
    	}

    	

    	
    	
    }
    public function viewTemplates()
    {
    	$get_template = EmailTemplate::get();
    	// dd($get_template);
    		return view('email.view_templates',compact('get_template'));
    }
      public function editEmailTemplate($id)
    {
    		
    	$get_template = EmailTemplate::where('id',$id)->get();
    	$get_description = \DB::table('imported_excel_data')->distinct()->whereNotNull('activity_description')->get(['activity_description']);
    	$get_category    = \DB::table('imported_excel_data')->distinct()->whereNotNull('category')->get(['category']);

    	return view('email.edit',compact('get_template','get_description','get_category','id'));

    }
    public function updateEmailTemplate(Request $request)
    {
    	// dd($request->all());
    	$update_array['template_name']=$request->template_name;
    	// $update_array['category']=$request->category;
    	$update_array['activity_description']=$request->activity_description;
    	$update_array['subject']=$request->subject;
    	$update_array['message']=$request->message;
    	$update_array['attachment_path']=$request->attachment_path;
    	// $update_array['status']=1;
    	   $update_email=EmailTemplate::where('id',$request->id)->update($update_array);

    	   	return redirect(route('view.email.template'))->with('success','Updated successfully');

    }
    public function deleteEmailTemplate($id)
    {
    	EmailTemplate::where('id',$id)->delete();

    	return redirect(route('view.email.template'))->with('success','Deleted successfully');

    }




    // functions for send mail via admin interface
    public function sendEmailView()
    {
    	// $get_state = \DB::table('imported_excel_data')->distinct()->whereNotNull('state')->get(['state']);
    	$get_state = \DB::table('imported_excel_data')->distinct()->whereNotNull('state')->pluck('state');
    	// dd($get_state);
    	 Session::put('email_state',null);
         Session::save();
         Session::put('email_roc',null);
         Session::save();
         Session::put('email_doi',null);
         Session::save();
    	
    	return view('email.send_view',compact('get_state'));
    }
    public function getRocEmail($state)
    {
    	 $get_roc = \DB::table("imported_excel_data")->distinct()->whereNotNull('roc')->where('state',$state)->pluck("roc");

    	 Session::put('email_state', $state);
         Session::save();


        return json_encode($get_roc);

    }

    public function getDoiEmail($roc)
    {
    	 $get_doi = \DB::table("imported_excel_data")->distinct()->whereNotNull('doi')->where('roc','LIKE',$roc)->orderBy('doi')->pluck("doi");
    	  Session::put('email_roc', $roc);
          Session::save();

        return json_encode($get_doi);
    }

  
    public function getActivityDescriptionEmail($doi)
    {
    	// dd($obj);
    	$session_roc = Session::get('email_roc');
    	Session::put('email_doi', $doi);
        Session::save();

    	$get_des = \DB::table("imported_excel_data")->distinct()->whereNotNull('activity_description')->where('roc','LIKE',$session_roc)->where('doi','=',$doi)->orderBy('activity_description')->pluck("activity_description");
    	return json_encode($get_des);
    }

      public function getCategoryEmail($des)
    {
    	$session_roc = Session::get('email_roc');
    	$session_dio = Session::get('email_doi');
    	Session::put('email_des', $des);
        Session::save();

        $get_cat = \DB::table("imported_excel_data")->distinct()->whereNotNull('category')->where('roc','LIKE',$session_roc)->where('doi','=',$session_dio)->where('activity_description','LIKE',$des)->orderBy('category')->pluck("category");

        return json_encode($get_cat);

    }

    public function sendEmailAdmin(Request $request)
    {
    	// dd($request->all());

    	$state 	 	 =$request->state;
    	$roc 	 	 =$request->roc;
    	$doi 		 =$request->doi;
    	$description =$request->description;
    	// $category 	 =$request->category;

    	// $get_email_ids =\DB::table("imported_excel_data")->distinct()->whereNotNull('email')->where('state','=',$state)->where('roc','LIKE',$roc)->where('doi','=',$doi)->where('activity_description','LIKE',$description)->where('category','=',$category)->orderBy('email')->pluck("email"); 

    	if (!empty($state) && !empty($roc)  && !empty($doi) && !empty($description) ) {
    	

    		$get_email_template =\DB::table("email_templates")->where('activity_description','LIKE',$description)->get();
    		$get_state = \DB::table('imported_excel_data')->distinct()->whereNotNull('state')->pluck('state');
    		if (count($get_email_template)==1) 
    		{
 
    		 dispatch(new SendEmailByAdminJob($state, $roc, $doi, $description));

    		 return view('email.send_view',compact('get_state'))->withErrors(['found'=>'The action added  in Queue system it wil perform soon.']);

    	    		// return redirect(route('send.email'))->with('success','The action added  in Queue system it wil perform soon.');
    		}
    		else
    		{
    			// return redirect(route('send.email'))->with('error','The selectd Activity template '.$description.' not found');
    			
    			// dd($get_state);
    			// return view('email.send_view',compact('get_state'))->with('error','The selectd Activity template '.$description.' not found');
    			return view('email.send_view',compact('get_state'))->withErrors(['notfound'=>'The selected Activity template <b>'.$description.'</b> not found']);

    			// return redirect()->back()->with('success', 'your message here'); 

    		}

    		// Artisan::call('queue:work');

    		// dd($get_email_template);
    		// return redirect()->back()->with('success','The action perform in Queus system');
    	}
    	else
    	{
    		return redirect()->back()->with('error','Some fields are not select please select all fields then try again');
    	}


			// $sample=[];
   //  	foreach ($get_email_ids as $value) {
   //  		 $value =strtolower($value);
   //  		   // \Notification::send($value, new SendEmailByAdmin($sample));
   //  		 \Notification::route('mail', $value)->notify(new SendEmailByAdmin($sample));

   //  	}

    	dd($get_email_ids);


    }

    public function configEmail()
    {
        $get_config_limit =  EmailSettings::where('option','limit')->pluck('value')->first();
        $get_email_from =  EmailSettings::where('option','email_from')->pluck('value')->first();
        $get_from_name =  EmailSettings::where('option','from_name')->pluck('value')->first();
        $get_cron_email_doi =  EmailSettings::where('option','cron_email_doi')->pluck('value')->first();
        $get_cron_email_state =  EmailSettings::where('option','cron_email_state')->pluck('value')->first();
        $get_state = \DB::table('imported_excel_data')->distinct()->whereNotNull('state')->pluck('state');
        // foreach ($get_config as $key => $value) {
        //     echo $value->option;
        //     echo $value->value;
        // }
        // dd('das');
        
        return view('email.config', compact('get_state','get_config_limit', 'get_email_from', 'get_from_name', 'get_cron_email_doi', 'get_cron_email_state'));       
    }

    public function storeconfigEmail(Request  $request)
    {

        $update_array_limit=[];
        $update_array_email_from=[];
        $update_array_from_name=[];
        $update_array_cron_email_doi=[];
        $update_array_cron_email_state=[];
        $update_array_limit['value']=$request->limit;
        $update_array_email_from['value']=$request->email_from;
        $update_array_from_name['value']=$request->from_name;
        $update_array_cron_email_doi['value']=$request->cron_email_doi;
        $update_array_cron_email_state['value']=$request->cron_email_state;

        EmailSettings::where('option','limit')->update($update_array_limit);
        EmailSettings::where('option','email_from')->update($update_array_email_from);
        EmailSettings::where('option','from_name')->update($update_array_from_name);
        EmailSettings::where('option','cron_email_doi')->update($update_array_cron_email_doi);
        EmailSettings::where('option','cron_email_state')->update($update_array_cron_email_state);
        // // // \DB::table('email_settings')->update($update_array);

            return redirect(route('email.config'))->with('success','Configuration saved successfuly');
    }


    public function testemail()
    {
    	// $get = EmailSettings::get();

    	$from_name = EmailSettings::where('option','from_name')->pluck('value');
        $email_from = EmailSettings::where('option','email_from')->pluck('value');
        echo $from_name[0];
        echo $email_from[0];
            dd($from_name);


    	// dd($get);
    }


}
