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

	$dir = public_path('db') . '/';

	$files = scandir($dir,SCANDIR_SORT_DESCENDING);

	$dumps=array();

	foreach ($files as $file) {
           // List only regular, non-symlink VepKar dump files.
           if (preg_match('/\Avepkardb-[0-9]{8}\.gz\z/', $file) !== 1
               || !is_file($dir.$file)
               || is_link($dir.$file)) {
               continue;
           }
		$dumps[]=array("filename"=>$file,
				"date"=>date("d-m-Y H:i:s",filemtime($dir.$file)),
				"size"=>round(filesize($dir.$file)/(1024*1024),1),
//				"href"=>env('APP_URL').$dir.$file
				"href"=>"db/".$file
				);
	}

        return view('dumpindex')->with(array('dumps' => $dumps));
    }

}
