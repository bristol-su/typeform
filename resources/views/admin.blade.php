@extends('typeform::layouts.app')

@section('title', settings('title', 'Typeform'))

@section('module-content')
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12" style="text-align: center;">

                    <h2 class="">{{settings('title')}}</h2>
                    <p class="">{!! settings('description') !!}</p>

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
                                query-string="{{url()->getAuthQueryString()}}"
                                :show-activity-instance-by="true"></responses>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection