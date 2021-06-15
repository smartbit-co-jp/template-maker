@extends('layouts.app')

@section('content')

    <br><br><br>
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <div class="card" style="min-height: 600px;">
                    <div class="card-body">
                        <div class="dropdown open float-right">
                            <button class="btn btn-success dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ "Register Template" }}
                            </button>
                            <form method="POST" action="{{ action('SmartBit\TemplateMaker\Http\Controllers\TemplateController@store') }}" class="dropdown-menu dropdown-menu-right" aria-labelledby="triggerId">
                                @csrf
                                @foreach ($template_types as $type)
                                    <button type="submit" class="dropdown-item" name="type" value="{{$type}}">
                                        {{$type}}
                                    </button>
                                @endforeach
                            </form>
                        </div>
                        <h4 class="card-title">
                            {{ __('Template Maker') }}
                        </h4>
                        <div class="clearfix"></div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @foreach ($template_types as $type)
                                <li class="nav-item">
                                    <a class="nav-link {{(($subtab==null&&$loop->first)||$subtab==$type)?'active':''}}" data-toggle="tab" href="#template_{{$type}}" role="tab" aria-controls="home" {{$loop->first?'aria-selected="true"':''}}>
                                        {{__($type)}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            @foreach ($template_types as $type)
                            <div class="tab-pane fade {{(($subtab==null&&$loop->first)||$subtab==$type)?'show active':''}}" id="doc_{{$type}}" role="tabpanel">
                                {{-- @if (haken()->documentTemplates->where('type',$type)->count() > 0) --}}
                                    <table class="table table-striped table-bordered table-inverse table-hover">
                                        <thead class="thead-inverse">
                                            <tr>
                                                <th>{{ __('document_template.name') }}</th>
                                                {{-- @if (haken()->languages->count()>1) --}}
                                                {{-- <th>
                                                    {{ __('global.translations') }}
                                                </th> --}}
                                                {{-- @endif --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stored_templates[$type] as $document_template)
                                                {{-- @dump($document_template) --}}
                                                {{-- @foreach(haken()->documentTemplates->where('type',$type) as $document_template) --}}
                                                <tr class="has-hover">
                                                    <td scope="row" style="font-size: 1.2em">
                                                        <div class="">
                                                            @if ($document_template->is_default)
                                                                <i class="fas fa-star text-primary"></i>
                                                            @endif
                                                            {{$document_template->name}}
                                                        </div>
                                                        <div class="show-on-hover small">
                                                            <b-link v-b-modal.ct-{{$document_template->id}} >
                                                                <i class="fas fa-eye    "></i>
                                                                {{__('Show')}}
                                                            </b-link>
                                                            {{-- <b-modal title="{{$document_template->name}}" size="xl" id="ct-{{$document_template->id}}">
                                                                <iframe width="100%" 
                                                                    height="1000" 
                                                                    src="{{ action('SmartBit\TemplateMaker\Http\Controllers\PdfPreviewController@show', $document_template) }}" 
                                                                    frameborder="0">
                                                                </iframe>
                                                            </b-modal> --}}
                                                            <form class="d-inline" onsubmit="return confirm('{{__('global.delete.confirmation_message')}}')" method="POST" action="{{ action('SmartBit\TemplateMaker\Http\Controllers\TemplateController@destroy', $document_template) }}">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="submit" class="text-danger btn-text-link">
                                                                    <i class="fas fa-trash"></i>
                                                                    {{ "Delete" }}
                                                                </button> 
                                                            </form>
                                                            @if ($document_template->is_default)
                                                                <span>
                                                                    <i class="fas fa-star"></i>
                                                                    {{ "Default" }}
                                                                </span>
                                                                @else
                                                                <form class="d-inline" onsubmit="return confirm('{{__('document_template.make_default_confirm_message')}}')" method="POST" action="{{ action('SmartBit\TemplateMaker\Http\Controllers\TemplateController@update', $document_template) }}">
                                                                    @method('put')
                                                                    @csrf
                                                                    <button type="submit" class="text-primary btn-text-link" name="make_default" value="true">
                                                                        <i class="fas fa-star"></i>
                                                                        {{ "Set as default" }}
                                                                    </button> 
                                                                </form>
                                                            @endif
                                                             <a class="text-warning" href="{{ action('SmartBit\TemplateMaker\Http\Controllers\TemplateController@edit',$document_template) }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                                {{ "Edit" }}
                                                            </a> 
                                                        </div>
                                                    </td>
                                                    {{-- @if (haken()->languages->count()>1) --}}
                                                    {{-- <td>
                                                        @foreach(haken()->languages as $locale)
                                                            @if ($locale != fallback_locale())
                                                                <a href="{{ action('DocumentTemplateController@edit',[$document_template,'lang'=>$locale]) }}">
                                                                    <span class="{{$document_template->hasLocale($locale)?:'no-lang'}}">
                                                                        {!!locale_img($locale)!!} 
                                                                    </span>
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    </td> --}}
                                                    {{-- @endif --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                {{-- @else --}}
                                    {{-- <div class="p-5">
                                        {{__('No entry found')}}

                                    </div> --}}
                                {{-- @endif --}}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection