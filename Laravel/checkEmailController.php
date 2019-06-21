<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class checkEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $hash = $request->hash;
		$message = DB::table('parsed_emails')->where('mail_to', 'like' ,"%".$hash."@example.email%")->first();
		
		if (!$message) {
			//return redirect()->route('index');
			return view('check',['hash' => array('fail',$hash."@example.email")]);
		} else {
			return view('check',['hash' => array('done',$hash."@example.email")]);
		}
		
		$information = array
		(
		"from" => explode("@",$message->mail_from),
		"to" => $message->mail_to,
		"text" => $message->text,
		"html" => $message->html
		);
		
		//Lets check if the mail server is reachable
		$check = dns_get_record($information['from'][1],DNS_MX);
		
		$p25 = @fsockopen($information['from'][1],25,$p25_errno,$p25_errstr,5);
		$p465 = @fsockopen($information['from'][1],465,$p465_errno,$p465_errstr,5);
		$p587 = @fsockopen($information['from'][1],587,$p587_errno,$p587_errstr,5);

		if ($p25 OR $p465 OR $p587) {
			$reachcheck=true;
		}
		
		//Lets get the SPF Record
		$check = dns_get_record($information['from'][1],DNS_TXT);
		//$spf = preg_grep("/\b\$spf\b/i",$check[0]['entries']);	
		
		//$spf = preg_grep("/\b\spf", $check[0]['entries']);
		
		/*
		foreach($check[0]['entries'] as $key => $val) {
			$x[]['record'] = $val;
			$x[]['result'] = strpos("spf",$val);
		}
		*/
		//strpos

		
		return view('check',['hash' => $check]);
    }
}
