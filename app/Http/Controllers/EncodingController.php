<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
//use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EncodingController extends Controller
{
    /**
     * Convert a string into a QR image
     *
     * @param  Request  $request
     *   @param  string   ?data
     *   @param  integer  ?dotsize             (default=3)
     *   @param  string   ?ecc                 (default=L {LMQH} )
     *   @param  integer  ?marginsize          (default=4 (2 for Micro))
     *   @param  integer  ?dpi                 (default=72)
     *   @param  string   ?output              (default=PNG {PNG,PNG32,EPS,SVG,XPM,ANSI,ANSI256,ASCII,ASCIIi,UTF8,ANSIUTF8})
     *
     * @return Response
     */
    public static function EncodeString( Request $request )
    {
        # Set fields validation constraints
        $validator = Validator::make($request->all(), [
            'data'          => 'required|string',
            'dotsize'       => 'integer|between:1,5',
            'ecc'           => ['regex:/[L|M|Q|H]{1}/'],
            'marginsize'    => 'integer|between:1,5',
            'dpi'           => 'integer|between:50,100',
            'output'        => ['regex:/[PNG|EPS|SVG]{1}/'],
        ]);

        # Check if fields are right
        if ( $validator->fails() ) {
            return response()->json([
                'status'  => 'error',
                'message' => 'some input field is missing or wrong'
            ], 400)->send();
        }
        
        # Build the whole command
        $cmd = collect([
            'dotsize'    => 3,
            'ecc'        => 'L',
            'marginsize' => 4,
            'dpi'        => 72,
            'output'     => 'PNG',
        ]);
        
        if ($request->has('dotsize')) {
            $cmd->put('dotsize', $request->input('dotsize'));
        }
        
        if ($request->has('ecc')) {
            $cmd->put('ecc', $request->input('ecc'));
        }
        
        if ($request->has('marginsize')) {
            $cmd->put('marginsize', $request->input('marginsize'));
        }
        
        if ($request->has('dpi')) {
            $cmd->put('dpi', $request->input('dpi'));
        }
        
        if ($request->has('output')) {
            $cmd->put('output', $request->input('output'));
        }
        
        # Building a random temporary path
        $tmpPath = '/tmp/' . Str::random(40);
        
        # Execute decoding
        $cmd = [
            'qrencode', 
            '-s', $cmd['dotsize'],
            '-l', $cmd['ecc'],
            '-m', $cmd['marginsize'],
            '-d', $cmd['dpi'],
            '-t', $cmd['output'],
            '-o', $tmpPath,
            $request->input('data')
        ];
        
        $process = new Process($cmd);
        $process->run();
        
        //try {
        //    $process->mustRun();
        //    echo $process->getOutput();
        //} catch (ProcessFailedException $e) {
        //    echo $e->getMessage();
        //    return;
        //}

		# Check looking for failures
		if ( !$process->isSuccessful() ) {
		    //throw new ProcessFailedException($process);
		    return response()->json([
                'status'  => 'error',
                'message' => 'code generation failed'
            ], 400)->send();
		}
		
		if ($request->has('download')) {
            return response()
                ->download( $tmpPath, Str::random(40) .'.'. $request->input('output') );
        }
        
		# Return QR image
		return response()->file( $tmpPath );

    
    
    }
    
}
