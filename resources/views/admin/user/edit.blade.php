@php
    use App\Models\User1;
@endphp
@extends('layouts.admin_layout')

@section('title', 'Редактировать пользователя')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Редактировать пользователя</h1>
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
            <form action="{{ route('userUpdate', $user['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="input-first-name">Имя</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" id="input-first-name" placeholder="Введите имя пользователя" value="{{ $user['first_name'] }}">
                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-last-name">Фамилия</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" id="input-last-name" placeholder="Введите фамилию пользователя" value="{{ $user['last_name'] }}">
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-username">Псевдоним</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="input-username" placeholder="Введите псевдоним пользователя" value="{{ $user['username'] }}">
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-subscribe-count">Подписки (каналы)</label>
                            <input type="text" name="subscribe_count" class="form-control @error('subscribe_count') is-invalid @enderror" id="input-subscribe-count" placeholder="" value="{{ $user['subscribe_count'] }}">
                            @error('subscribe_count')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-join-group-count">Подписки (группы)</label>
                            <input type="text" name="join_group_count" class="form-control @error('join_group_count') is-invalid @enderror" id="input-join-group-count" placeholder="" value="{{ $user['join_group_count'] }}">
                            @error('join_group_count')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-bonus-count">Бонусы</label>
                            <input type="text" name="bonus_count" class="form-control @error('bonus_count') is-invalid @enderror" id="input-bonus-count" placeholder="" value="{{ $user['bonus_count'] }}">
                            @error('bonus_count')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                            <div class="form-group">
                            <label for="input-referrals-earned">Заработано с рефералов</label>
                            <input type="text" name="referrals_earned" class="form-control @error('referrals_earned') is-invalid @enderror" id="input-referrals-earned" placeholder="" value="{{ $user['referrals_earned'] }}">
                            @error('referrals_earned')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-expected-to-pay">Ожидается к выплате</label>
                            <input type="text" name="expected_to_pay" class="form-control @error('expected_to_pay') is-invalid @enderror" id="input-expected-to-pay" placeholder="" value="{{ $user['expected_to_pay'] }}">
                            @error('expected_to_pay')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-output-amount">Ожидается к выплате</label>
                            <input type="text" name="output_amount" class="form-control @error('output_amount') is-invalid @enderror" id="input-output-amount" placeholder="" value="{{ $user['output_amount'] }}">
                            @error('output_amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-earned">Заработано всего</label>
                            <input type="text" name="earned" class="form-control @error('earned') is-invalid @enderror" id="input-earned" placeholder="" value="{{ $user['earned'] }}">
                            @error('earned')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="input-balance">Основной баланс</label>
                            <input type="text" name="balance" class="form-control @error('balance') is-invalid @enderror" id="input-balance" placeholder="" value="{{ $user['balance'] }}">
                            @error('balance')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Статус</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="input-disabled" value="{{ User1::STATUS['disabled'] }}" @if ($user['status'] == User1::STATUS['disabled']) checked @endif>
                                <label class="form-check-label" for="input-disabled">Неактивный</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="input-enabled" value="{{ User1::STATUS['enabled'] }}" @if ($user['status'] == User1::STATUS['enabled']) checked @endif>
                                <label class="form-check-label" for="input-enabled">Активный</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="input-blocked" value="{{ User1::STATUS['blocked'] }}" @if ($user['status'] == User1::STATUS['blocked']) checked @endif>
                                <label class="form-check-label" for="input-blocked">Заблокированный</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="input-deleted" value="{{ User1::STATUS['deleted'] }}" @if ($user['status'] == User1::STATUS['deleted']) checked @endif>
                                <label class="form-check-label" for="input-deleted">Удаленный</label>
                            </div>
                        </div>
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
