@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Pre Defined Message
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View Pre Defined Message</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method("PATCH")
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="message">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" id="message" class="form-control" placeholder="Plese enter message" disabled>{{ $data->message ?? '' }}</textarea>
                                    <span class="kt-form__help error message"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <a href="{{ route('pre_defined_message') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

