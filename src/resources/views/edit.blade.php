<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="{{ mix('/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-dark">
    <br><br><br>
    <div class="container">
                    






<home :user="user" inline-template>
    <div class="container-fluid">
        <div class="container">
            {{-- @include('inc.messages') --}}
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <nav class="breadcrumb bg-dark p-0" style="margin-top: -10px;">
                    <span class="breadcrumb-item active">
                        {{-- {{$documentTemplate->name}} --}}
                    </span>
                </nav>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-10">
                {{-- <sb-form :fields='{{ $documentTemplate->toJson() }}' id="contract-template-edit" watch-changes
                    title="{{__('document_template.edit.title')}}" method="put"
                    action="{{ action('Vue\DocumentTemplateController@update', $documentTemplate) }}"> --}}
                    <template v-slot:body="{form}">
                        <div class="row justify-content-center">
                            <div class="col-md-9 col-xl-9 mb-3">
                                <div class="card mb-2">
                                    <div class="card-body px-3 pt-2 pb-3">
                                        <div class="card-title">
                                            {{ __('document_template.edit.title') }}
                                            {{-- {!!locale_img($lang)!!} --}}
                                        </div>
                                        <div class="form pb-2">
                                            <label for="">{{__('document_template.name')}}</label>
                                            {{-- @if ($lang==fallback_locale())
                                            <b-form-input v-model="form.name"></b-form-input>
                                            @else
                                            <b-form-input readonly value="{{$documentTemplate->name.'-'.__($lang)}}">
                                            </b-form-input>
                                            @endif --}}
                                        </div>
                                        <div class="text-right">
                                            {{-- <b-button variant="success" size="sm" class="mr-2" @click="form.send()">
                                                {{ __('global.save') }}
                                            </b-button> --}}
                                            {{-- TODO habilitar esses botões quando estiver funcionando o editor de template --}}
                                            {{-- @if (request()->has('html'))
                                                <a href="{{action('DocumentTemplateController@edit',[$documentTemplate->parent??$documentTemplate,'lang'=>$lang])}}"
                                            class="btn btn-sm btn-primary ">
                                            {{  __('document_template.basic_editor') }}
                                            </a>
                                            @else
                                            <a href="{{action('DocumentTemplateController@edit',[$documentTemplate->parent??$documentTemplate,'lang'=>$lang,'html'=>'true'])}}"
                                                class="btn btn-sm btn-primary ">
                                                {{  __('document_template.advanced_editor') }}
                                            </a>

                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                                {{-- TODO habilitar essa condicional quando estiver funcionando o editor de template --}}
                                @if (!request()->has('html') && false)
                                <basic-editor api-key="{{ config('app.tinymce_api_key') }}" v-if="form.data"
                                    v-model="form.data.{{ $lang }}" name="data" @onChange="form.send()" :init="{
                                            height: 800,
                                            skin: 'oxide-dark',
                                            plugins: 'table template',
                                            menubar: 'edit insert format table',
                                            toolbar: 'undo redo | template table | styleselect | fontselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent',
                                            templates: [
                                                {title: '3-3-3-3', description: 'adds a row with four columns grid layout', content: '<div style=&quot;margin: 0 -5px; &quot;><div style=&quot;margin:0;padding:5px;float:left;width:25%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;margin:0;padding:5px;float:left;width:25%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;margin:0;padding:5px;float:left;width:25%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;margin:0;padding:5px;float:left;width:25%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;clear:both;&quot;></div></div>'},
                                                {title: '4-4-4', description: 'adds a row with four columns grid layout', content: '<div style=&quot;margin: 0 -5px; &quot;><div style=&quot;margin:0;padding:5px;float:left;width:33.33%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;margin:0;padding:5px;float:left;width:33.33%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;margin:0;padding:5px;float:left;width:33.33%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;clear:both;&quot;></div></div>'},
                                                {title: '2-6-4', description: 'adds a row with four columns grid layout', content: '<div style=&quot;margin: 0 -5px; &quot;><div style=&quot;margin:0;padding:5px;float:left;width:16.6667%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;margin:0;padding:5px;float:left;width:50%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;margin:0;padding:5px;float:left;width:33.33%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; &quot;>My content</div> <div style=&quot;clear:both;&quot;></div></div>'},
                                                {title: 'Some title 2', description: 'Some desc 2', content: '<div>My content 2</div>'}
                                            ],
                                            language: '{{locale()}}',
                                            language_url : '/vendor/tinymce/langs/{{locale()}}.js',
                                            content_style: form.style.{{ $lang }}
                                        }"></basic-editor>
                                @else
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="card mb-2">
                                            {{-- CSS --}}
                                            <div class="card-header px-2 py-1"> css
                                                <div class="float-right">
                                                    @if ('aaa' == 'bbbbb')
                                                        <export-document-template type="css" lang="{{ $lang }}"
                                                            :document-template-id="{{ $documentTemplate->id }}">
                                                        </export-document-template>
                                                        <import-document-template type="css" lang="{{ $lang }}"
                                                            :document-template-id="{{ $documentTemplate->id }}">
                                                        </import-document-template> --}}
                                                    @endif
                                                </div>
                                                <div class="card-body pl-0">
                                                    <code-editor v-model="form.style.{{ $lang }}" line-numbers
                                                        style="max-height: 400px;" language="css">
                                                    </code-editor>
                                                </div>
                                            </div>
                                            <div class="card">
                                                {{-- HTML --}}
                                                <div class="card-header px-2 py-1">html
                                                    <div class="float-right">
                                                         @if ('aaaa' === 'bb')
                                                            <export-document-template type="html" lang="{{ $lang }}"
                                                                :document-template-id="{{ $documentTemplate->id }}">
                                                            </export-document-template>
                                                            <import-document-template type="html" lang="{{ $lang }}"
                                                                :document-template-id="{{ $documentTemplate->id }}">
                                                            </import-document-template>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-body pl-0">
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-3">
                                        <div>
                                            {{-- {!! $templater->makeKeyList() !!} --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                {{-- </sb-form> --}}
            </div>
        </div>
    </div>
</home>








    </div>
</body>

</html>


