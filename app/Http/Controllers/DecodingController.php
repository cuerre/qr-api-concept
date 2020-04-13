<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DecodingController extends Controller
{
    /**
     * Manage a request looking for QR codes on a photo
     *
     * @return JSON response
     */
    public static function DecodeFile( Request $request )
    {
        # Check if 'file' field is present
        if ( !$request->hasFile('photo') ) {
            return response()->json([
                'status'  => 'error',
                'message' => 'file not recieved'
            ], 400)->send();
        }
        
        # Check for corrupt file
        if ( !$request->file('photo')->isValid() ) {
            return response()->json([
                'status'  => 'error',
                'message' => 'file is malformed'
            ], 400)->send();
        }
        
        //return $request->photo->extension();
        
        # Get file extension (lower case)
        $extension = $request->photo->extension();
        
        # Define allowed extensions
        $allowedExt = collect(['png', 'jpeg', 'jpg']);
        
        # Check if extension is allowed
        if( !$allowedExt->contains($extension) ){
            return response()->json([
                'status'  => 'error',
                'message' => 'file extension not allowed'
            ], 400)->send();
        }
        
        # Execute decoding 
        $path = $request->photo->path();
        $process = new Process(['zbarimg', '--raw', '-q', $path]);
		$process->run();
		
		# Check looking for failures
		if ( !$process->isSuccessful() ) {
		    //throw new ProcessFailedException($process);
		    return response()->json([
                'status'  => 'error',
                'message' => 'file decoding failed'
            ], 400)->send();
		}
		
		# Clean the data a bit
		$data = Str::of($process->getOutput())->trim();
		
		# Return QR data
		return response()->json([
                'status'  => 'success',
                'data'    => $data->__toString()
            ], 200)->send();
    }
}
