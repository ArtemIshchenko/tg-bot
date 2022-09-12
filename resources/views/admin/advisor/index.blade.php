@php
    use App\Models\User;
@endphp
@extends('layouts.admin_layout')

@section('title', 'Рекламодатели')

@section('content')

@push('scripts')
@endpush
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Все рекламодатели</h1>
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
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                        <tr>
                            <th style="width: 5%">
                                ID
                            </th>
                            <th>
                                Имя
                            </th>
                            <th>
                                E-mail
                            </th>
                            <th>
                                Дата добавления
                            </th>
                            <th style="width: 10%">
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($advisors as $advisor)
                            <tr>
                                <td>
                                    {{ $advisor->id }}
                                </td>
                                <td>
                                    {{ $advisor->name }}
                                </td>
                                <td>
                                    {{ $advisor->email }}
                                </td>
                                <td>
                                    {{ date('d.m.Y, H:i', strtotime($advisor->created_at)) }}
                                </td>
                                <td class="project-actions text-right">
                                    <a class="btn btn-info btn-xs" href="{{ route('advisors.edit', $advisor['id']) }}">
                                        <i class="fas fa-pencil-alt fa-xs">
                                        </i>
                                        Редактировать
                                    </a>
                                    <form action = "{{ route('advisors.destroy', $advisor['id']) }}" method="POST" style="display: inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs delete-btn" href="#">
                                            <i class="fas fa-trash fa-xs">
                                            </i>
                                            Удалить
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
