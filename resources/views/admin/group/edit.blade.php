@php
    use App\Models\TgGroup;
@endphp
@extends('layouts.admin_layout')

@section('title', 'Редактировать группу')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Редактировать группу</h1>
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
            <form action="{{ route('groups.update', $group['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="input-name">Название</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="input-name" placeholder="Введите название группы" value="{{ $group['name'] }}">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="input-description">Описание</label>
                        <textarea name="description" class="editor" id="input-description">{{ $group['description'] }}</textarea>
                        @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="inputUrl">URL</label>
                        <input type="text" name="url" class="form-control @error('url') is-invalid @enderror" id="inputUrl" placeholder="Введите URL" value="{{ $group['url'] }}">
                        @error('url')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="input-limit">Лимит</label>
                        <input type="text" name="limit" class="form-control @error('limit') is-invalid @enderror" id="input-limit" placeholder="Введите лимит" value="{{ $group['limit'] }}">
                        @error('limit')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Статус</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="input-enabled" value="{{ TgGroup::STATUS['enabled'] }}" @if ($group['status'] == TgGroup::STATUS['enabled']) checked @endif>
                            <label class="form-check-label" for="input-enabled">Активна</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="input-disabled" value="{{ TgGroup::STATUS['enabled'] }}" @if ($group['status'] == TgGroup::STATUS['disabled']) checked @endif>
                            <label class="form-check-label" for="input-disabled">Неактивна</label>
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
