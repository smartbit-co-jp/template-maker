@extends('template-maker::layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col-12">
                        <h3> Template Types </h3>
                        @foreach ($types as $type=>$value)
                            <div class="col-12">
                                <h4>> {{$type}}</h4>
                                <div class="row">
                                    <div class="col-12">
                                        -> Templates
                                        <div class="row">
                                            <div class="col-12">
                                                Default(<a href="{{ route('pdf_preview', $type) }}">PDF</a>)
                                            </div>
                                        </div>
                                        @foreach ($value['stored_templates'] as $index=>$template)
                                            <div class="row">
                                                <div class="col-12">
                                                    <a href="{{ route('template.edit', $template) }}">
                                                        Edit
                                                    </a> || || ||
                                                    <a href="{{ route('pdf_preview', $template) }}">
                                                        Preview
                                                    </a> || || ||
                                                    {{$template->id}} -> 
                                                    <a href="{{ route('template.edit', $template) }}">
                                                        {{$template->name}}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="row">
                                            <div class="col-12">
                                                <form method="POST" action="{{ route('template.store', $type) }}" aria-labelledby="triggerId">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" name="type" value="{{$type}}">
                                                        Add Variation
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <br><br>
                        @endforeach

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection