@php
    use App\Models\User;
@endphp

@extends('layouts.admin_layout')

@section('title', 'Добавить рекламодателя')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Добавить рекламодателя</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>{{ session('success') }}</h4>
            </div>
        @elseif (session('errMsg'))
              <div class="alert alert-warning" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h4><i class="icon fa fa-check"></i>{{ session('errMsg') }}</h4>
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
            <form action="{{ route('advisors.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                  <label for="input-name">Имя</label>
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="input-name" placeholder="Введите имя" value="{{ old('name') }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="input-email">E-mail</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="input-email" placeholder="Введите E-mail" value="{{ old('email') }}">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="input-password">Пароль</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="input-password" placeholder="Введите пароль" required autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="input-password-confirm">Подтвердите пароль</label>
                    <input type="password" name="password_confirmation" class="form-control" id="input-password-confirm" placeholder="Введите пароль"  required autocomplete="new-password">
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Добавить</button>
            </div>
            </form>
        </div>
    </div>
    </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
