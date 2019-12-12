@extends('typeform::layouts.app')

@section('title', settings('title'))

@section('module-content')
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12" style="text-align: center;">
                    
                    @if(settings('collect_responses'))
                        <webhook-setup-alert>
                            
                        </webhook-setup-alert>
                    @endif
                    
                    <h2 class="">{{settings('title')}}</h2>
                    <p class="">{!! settings('description') !!}</p>

                </div>
            </div>
        </div>
    </div>
@endsection