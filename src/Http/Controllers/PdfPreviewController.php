<?php

namespace SmartBit\TemplateMaker\Http\Controllers;

use Illuminate\Http\Request;
use SmartBit\Models\TemplateMaker;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use stdClass;
use Illuminate\Support\Facades\View;

class PdfPreviewController extends Controller
{
    /**
     * @param Request $request
     * @param DocumentTemplate $documentTemplate
     * 
     * @return PDF
     */
    public function show(Request $request, DocumentTemplate $documentTemplate)
    {
        dump($request);
        dd($documentTemplate);
        $html = $documentTemplate->data[fallback_locale()];
        $css = $documentTemplate->style[fallback_locale()];
	
        $pdf = PDF::loadView('components.template-maker.base-layout', compact('css', 'html'));

        return $pdf->stream($documentTemplate->name.'.pdf');
	}
}
