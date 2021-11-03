@extends('template-maker::layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col-12">
                        <h3> Template Types </h3>
                        @foreach ($types as $type=>$value)
                            <b-col>
                                <h4>> {{$type}}</h4>
                                <b-row>
                                    <b-col>
                                        -> Templates
                                        <b-row>
                                            <b-col>
                                                Default(<a href="{{ route('pdf_preview', $type) }}">PDF</a>)
                                            </b-col>
                                        </b-row>
                                        @foreach ($value['stored_templates'] as $index=>$template)
                                            <b-row>
                                                <b-col>
                                                    <a href="{{ route('template.edit', $template) }}">
                                                        Edit
                                                    </a> || || ||
                                                    <a href="{{ route('pdf_preview', $template) }}">
                                                        Preview
                                                    </a> || || ||
                                                    {{$index}} -> 
                                                    <a href="{{ route('template.edit', $template) }}">
                                                        {{$template->name}}
                                                    </a>
                                                </b-col>
                                            </b-row>
                                        @endforeach
                                        <b-row>
                                            <b-col>
                                                <form method="POST" action="{{ route('template.store', $type) }}" aria-labelledby="triggerId">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" name="type" value="{{$type}}">
                                                        Add Variation
                                                    </button>
                                                </form>
                                            </b-col>
                                        </b-row>

                                    </b-col>
                                </b-row>
                            </b-col>
                            <br><br>
                        @endforeach

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection