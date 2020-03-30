@extends('typeform::layouts.app')

@section('title', settings('title', 'Typeform'))

@section('module-content')
    <div class="py-5">
        <div class="container">
            <div class="row" style="margin: 80px;">
                <div class="col-md-12" style="text-align: center;">
                    <h2 class="">{{settings('title')}}</h2>
                    <p class="">{!! settings('description') !!}</p>

                    @if(strlen(settings('form_url', '')) > 0)
                        @if(settings('embed_type', 'widget') === 'widget')
                            <typeform-embed-widget
                                    form-url="{{settings('form_url')}}"
                                    :hide-headers="{{(settings('hide_headers', true)?'true':'false')}}"
                                    :hide-footer="{{(settings('hide_footer', true)?'true':'false')}}">

                            </typeform-embed-widget>
                        @else
                            <typeform-embed-popup
                                    form-url="{{settings('form_url')}}"
                                    :hide-headers="{{(settings('hide_headers', true)?'true':'false')}}"
                                    :hide-footer="{{(settings('hide_footer', true)?'true':'false')}}"
                                    mode="{{settings('embed_type')}}">
                            </typeform-embed-popup>
                        @endif
                    @else
                        No form found
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if(settings('collect_responses') && count($responses) > 0)
                        <h4>Previous Responses</h4>
                        <responses
                                :responses="{{$responses}}"
                                :show-approved-status="{{(settings('approval', false)?'true':'false')}}"
                                query-string="{{url()->getAuthQueryString()}}"></responses>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection