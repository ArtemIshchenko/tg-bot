@php
    use App\Models\Setting;
@endphp
@extends('layouts.admin_layout')

@section('title', 'Настройки')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Настройки</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i>{{ session('success') }}</h4>
                </div>
            @endif
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary">
                        <!-- form start -->
                        <form method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5">
                                    @foreach($settings as $number => $setting)
                                        <div class="form-group">
                                            <label for="input-s{{$number}}">{{$setting->description}}</label>
                                            @if (Setting::getTypeByName($setting->name) == Setting::TYPE['textarea'])
                                                <textarea name="{{ '\[' . $setting->number . ']value' }}" class="form-control @error("value_$number") is-invalid @enderror" id="input-s{{$number}}">{{ $setting->value }}</textarea>
                                            @else
                                            <input type="text" name="{{ '\[' . $setting->number . ']value' }}" class="form-control @error("value_$number") is-invalid @enderror" id="input-s{{$number}}" placeholder="" value="{{ $setting->value }}">
                                            @endif
                                            @error("value_$number")
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$errors->first("value_$number")}}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
