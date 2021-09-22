@extends('typeform::layouts.app')

@section('title', settings('title', 'Typeform'))

@section('module-content')
    <p-page-content title="{{settings('title')}}" subtitle="{{settings('description')}}">
        @if(settings('collect_responses'))
            <responses
                :responses="{{$responses}}"
                :can-refresh-responses="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.admin.refresh-form-responses')?'true':'false')}}"
                :show-approved-status="{{(settings('approval', false)?'true':'false')}}"
                :allow-approval="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.admin.approve')?'true':'false')}}"
                :can-add-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.admin.comment.store')?'true':'false')}}"
                :can-see-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.admin.comment.index')?'true':'false')}}"
                :can-delete-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.admin.comment.destroy')?'true':'false')}}"
                :can-update-comments="{{(\BristolSU\Support\Permissions\Facade\PermissionTester::evaluate('typeform.admin.comment.update')?'true':'false')}}"
                :show-activity-instance-by="true"></responses>
        @endif
    </p-page-content>
@endsection
