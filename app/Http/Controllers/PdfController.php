<?php

//https://github.com/barryvdh/laravel-dompdf
//https://github.com/elibyy/tcpdf-laravel
//https://github.com/danielboendergaard/phantom-pdf

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use PDF;
USe Storage;

//for testing
use App\TermsConsent;
use App\Client;
use App\User;

class PdfController extends Controller
{

	public function testGeneratePDF()
    {

		$record = TermsConsent::findOrFail(1);

		//send emails
		//Email Data
		$fields = [
			'client' => Client::findOrFail($record->client->id),
			'adviser' => User::findOrFail($record->user->id),
			'record' => $record
		];
  
		//DOMPDF
		//$pdf = PDF::loadView('admin.terms.pdf.promise_completed', $fields);
		//Storage::disk('documents')->put('cat/promise_completed.pdf', $pdf->stream());
		//$pdf->download('promise_completed.pdf');
		return view('admin.terms.pdf.promise_completed', $fields);

		//TCPDF
		//$view = \View::make('admin.terms.pdf.privacy', $data);
        //$html_content = $view->render();
        //PDF::SetTitle("TESTING");
        //PDF::AddPage();
		//PDF::writeHTML($html_content, true, false, true, false, '');
		//PDF::Output('userlist.pdf');

		//Phantom PDF
		//$pdf = PDF::createFromView(view('admin.terms.pdf.privacy', $data),'test.pdf',true);
		//Storage::disk('documents')->put('cat/test.pdf', $pdf);
		//return Storage::disk('documents')->download('cat/test.pdf');
		
	}
    /**

     * Generate PDF
     *
     * @return various, default: file stream
     */
	public static function generatePDF($data, $view, $filename, $mode)
	{

		if (!empty($view) && !empty($filename) && !empty($mode)) {
	        $pdf = PDF::loadView($view, $data);

	        switch ($mode) {
	        	case 'download':
	        		return $pdf->download($filename);
	        		break;

	        	case 'save':
					Storage::disk('documents')->put($filename, $pdf->stream());
					return 'saved';
	        		break;

	        	case 'stream':
	        		return $pdf->stream();
	        		break;
	        	
	        	default:
	        		return $pdf->stream();
	        		break;
	        }
	    } else {
	    	return false;
	    }
        
    }
}
