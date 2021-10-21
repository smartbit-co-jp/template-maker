<?php

namespace SmartBit\TemplateMaker\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use SmartBit\TemplateMaker\Models\TemplateMaker;
use SmartBit\TemplateMaker\Models\Template;
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
    public function show(Request $request, Template $template)
    {
        // todo didnt load dynamically from request's data -> $template
        $template = Template::first();

        // dd($template);
        dd($request);

        $html = $template->data['ja'];
        $css = $template->style['ja'];
    
        $pdf = PDF::loadView('template-maker::base-layout', compact('css', 'html'));

        return $pdf->stream($template->name.'.pdf');






        // $t = new TemplateMaker('sample', ['en']);

        // $html = file_get_contents($t->layout_path);
        // $css = file_get_contents($t->style_path);
        // $pdf = PDF::loadView('template-maker::base-layout', compact('css', 'html'));

        // $pdf = PDF::loadHtml($html);


        // return $pdf->stream($type . '.pdf');
	}
}
