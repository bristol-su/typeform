@extends('typeform::layouts.app')

@section('title', settings('title', 'Typeform'))

@section('module-content')
    <p-page-content title="{{settings('title')}}" subtitle="{{settings('description')}}">
        <p-tabs>
            <p-tab title="New Submission">
                @if(strlen(settings('form_url', '')) > 0 && \BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.view-form'))
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
                @elseif(!\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.view-form'))
                    You don't have permission to submit the form
                @else
                    No form found
                @endif
            </p-tab>
            @if(settings('collect_responses') && count($responses) > 0 && \BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.view-responses'))
                <p-tab title="Submissions">
                    <responses
                        :responses="{{$responses}}"
                        :show-approved-status="{{(settings('approval', false)?'true':'false')}}"
                        :can-add-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.comment.store')?'true':'false')}}"
                        :can-see-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.comment.index')?'true':'false')}}"
                        :can-delete-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.comment.destroy')?'true':'false')}}"
                        :can-update-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.comment.update')?'true':'false')}}"></responses>
                </p-tab>
            @endif
        </p-tabs>
    </p-page-content>
@endsection
