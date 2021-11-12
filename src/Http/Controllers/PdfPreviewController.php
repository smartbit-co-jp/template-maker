<?php

namespace SmartBit\TemplateMaker\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use SmartBit\TemplateMaker\Models\TemplateMaker;
use SmartBit\TemplateMaker\Models\Template;
use App\Models\Company;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use stdClass;
use Illuminate\Support\Facades\View;

class PdfPreviewController extends BaseController
{
    /**
     * @param Request $request
     * @param DocumentTemplate $documentTemplate
     * 
     * @return PDF
     */
    public function show(Request $request, $type)
    {
        $t = new TemplateMaker($type, ['en']);

        $html = file_get_contents($t->layout_path);
        $css = file_get_contents($t->style_path);
        $pdf = PDF::loadView('template-maker::base-layout', compact('css', 'html'));

        return $pdf->stream($type . '.pdf');
	}
}
