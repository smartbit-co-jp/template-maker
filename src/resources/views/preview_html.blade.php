@extends('spark::layouts.app',['body_class'=>'bg-dark'])

@section('content')
<home :user="user" inline-template>
    <div class="container-fluid">
        <div class="container">
            @include('inc.messages')
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <h1 class="text-light">{{ __('document_template.edit.title') }}</h1>
            </div>
        </div>
        <sb-form 
        {{-- @save="data.update($event)"  --}}
        :fields='{{ $documentTemplate->toJson() }}'
        id="contract-template-edit"
        title="{{__('document_template.edit.title')}}"
        method="put"
        action="{{ action('Vue\DocumentTemplateController@update', $documentTemplate) }}"
        >
        test
            <template v-slot:body="{form}">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-xl-8">
                        <editor 
                            api-key="{{ config('app.tinymce_api_key') }}" 
                            v-model="form.data"
                            name="data"
                            @onChange="form.send()"
                            :init="{
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
                                language_url : '/vendor/tinymce/langs/{{locale()}}.js'
                            }"
                        ></editor>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-primary mb-2">
                            <div class="card-header py-2 text-white">
                                {{ $documentTemplate->name }}
                            </div>
                            <div class="card-body bg-light py-2">
                                <b-button block variant="success" @click="form.send()">
                                    {{ __('global.save') }}
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </sb-form>
    </div>
</home>
@endsection