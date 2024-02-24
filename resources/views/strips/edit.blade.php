@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Strip Light
@endsection

@section('styles')
    <link href="{{ asset('assets/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sweetalert2.bundle.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Edit Strip Light</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('strips.update') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="id" value="{{ $data->id }}">
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ @old('name', $data->name) }}" placeholder="Plese enter name" />
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="text" name="quantity" id="quantity" class="form-control" value="{{ @old('price', $data->quantity) }}" placeholder="Plese enter quantity"/>
                                    <span class="kt-form__help error quantity"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="unit">Unit <span class="text-danger"></span></label>
                                    <select name="unit" id="unit" class="form-control">
                                        <option value="inch" <?= ($data->unit == 'inch' ? 'selected' :'') ?> >Inch</option>
                                        <option value="feet" <?= ($data->unit == 'feet' ? 'selected' :'') ?> >Feet</option>
                                        <option value="meter" <?= ($data->unit == 'meter' ? 'selected' :'') ?> >Meter</option>
                                    </select>
                                    <span class="kt-form__help error unit"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="choke">Choke per Unit <span class="text-danger"></span></label>
                                    <input type="text" name="choke" id="choke" class="form-control" value="{{ @old('price', $data->choke) }}" placeholder="Plese enter choke per unit" />
                                    <span class="kt-form__help error choke"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <input type="text" name="price" id="price" class="form-control digits" value="{{ @old('price', $data->price) }}" placeholder="Plese enter price" />
                                    <span class="kt-form__help error price"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="note">Note <span class="text-danger"></span></label>
                                    <input type="text" name="note" id="note" class="form-control" value="{{ @old('note', $data->note) }}" placeholder="Plese enter note" />
                                    <span class="kt-form__help error note"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="ampere">Ampere <span class="text-danger"></span></label>
                                    <input type="text" name="amp" id="ampere" class="form-control" value="{{ @old('amp', $data->amp) }}" placeholder="Plese enter ampere" />
                                    <span class="kt-form__help error file"></span>
                                </div>
                                <div class="form-group col-sm-12">
                                    @if(isset($data->file) && !empty($data->file))
                                        @php $file = url('/uploads/strips/').'/'.$data->file; @endphp
                                    @else
                                    @php $file = url('/uploads/strips/default.png'); @endphp
                                    @endif
                                    <label for="file">Attechment <span class="text-danger"></span></label>
                                    <input type="file" name="file" id="file" class="form-control dropify" placeholder="Plese select attachment" data-default-file="{{ $file }}" data-show-remove="false" />
                                    <span class="kt-form__help error file"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('strips') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/promise.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.bundle.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.digits').keyup(function(e){
                if (/\D/g.test(this.value)){
                    this.value = this.value.replace(/\D/g, '');
                }
            });

            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop file here or click',
                    'remove':  'Remove',
                    'error':   'Ooops, something wrong happended.'
                }
            });

            var drEvent = $('.dropify').dropify();
        });
    </script>

    <script>
        $(document).ready(function () {
            var form = $('#form');
            $('.kt-form__help').html('');
            form.submit(function(e) {
                $('.help-block').html('');
                $('.m-form__help').html('');
                $.ajax({
                    url : form.attr('action'),
                    type : form.attr('method'),
                    data : form.serialize(),
                    dataType: 'json',
                    async:false,
                    success : function(json){
                        return true;
                    },
                    error: function(json){
                        if(json.status === 422) {
                            e.preventDefault();
                            var errors_ = json.responseJSON;
                            $('.kt-form__help').html('');
                            $.each(errors_.errors, function (key, value) {
                                $('.'+key).html(value);
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

