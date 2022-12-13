<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;

class testController extends Controller
{
    public function uploadfile(Request $request){
        $data = [];



        $files = $request->file('images');

        try {

            foreach ($files as $key => $file) {



                $fileName                                               = $key.time().'.'.$file->extension();
                //return $fileName;
                $insert = Test::create([
                    'r_path' => url('/').'/images/test/'.$fileName
                ]);

                $image                                      =$file->move(public_path('images/test'), $fileName);

                $data[] = $fileName;

            }

            return $data;

        } catch (\Throwable $th) {
            return $th->getMessage();
        }


    }

    public function decrypt(){

    }
}
