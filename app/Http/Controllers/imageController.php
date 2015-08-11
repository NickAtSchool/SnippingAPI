<?php
/**
 * Created by PhpStorm.
 * User: nick.werle
 * Date: 8/10/2015
 * Time: 9:40 AM
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function getImage($id){
        $result = app('db')->table('images')->select(['id', 'filename'])->where('id', '=', $id)->get();
        if(count($result) > 0)
            return "<img src='".url('/uploads/'.$result[0]->filename)."' />";
        else
            return response(view('errors.404'), 404);
    }

    public function postImage(Request $request){
//        TODO: Validate input is image, better abstraction.
        $image = $request->file('imgToUpload');
        $extension = $image->getClientOriginalExtension();
        $id = str_random();
        $dbStoreSuccessful = false;
        $data = [
            'id' => $id,
            'uploader' => $request->ip(),
            'filename' => $id .".". $extension
        ];
        while(!$dbStoreSuccessful){
            try{
                app('db')->table('images')->insert($data);
                $dbStoreSuccessful = true;
            } catch (\Exception $e){
                $id = str_random();
                $data = [
                    'id' => $id,
                    'uploader' => $request->ip(),
                    'filename' => $id .".". $extension
                ];
            }
        }

        $image->move('uploads/', $id .".". $extension);
        return response()->json($data);
    }

}