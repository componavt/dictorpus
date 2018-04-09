<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class DumpDownloadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

	$dir="db/";

	$files = scandir($dir,SCANDIR_SORT_DESCENDING);

	$dumps=array();

	foreach ($files as $file) if ($file!="."&&$file!="..") {
		$dumps[]=array("filename"=>$file,
				"date"=>date("d-m-Y H:i:s",filemtime($dir.$file)),
				"size"=>round(filesize($dir.$file)/(1024*1024),1),
				"href"=>env('APP_URL').$dir.$file
				);
	}

        return view('dumpindex')->with(array('dumps' => $dumps));
    }

}
