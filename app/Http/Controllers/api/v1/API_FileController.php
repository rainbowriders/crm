<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use File;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;

class API_FileController extends Controller
{
    public function getGuide() {

        $file = File::get(public_path() . '/files/rainbow_crm_guide.pdf');
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');

        return $response;
    }
}
