@php
    use App\Models\User1;
@endphp
@extends('layouts.admin_layout')

@section('title', 'Пользователи')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Все пользователи</h1>
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
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link @if ($status == -1) active @endif" href="{{ route('users', ['status' => -1]) }}">Все@if ($allUserCount > 0) ({{$allUserCount}}) @endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($status == User1::STATUS['enabled']) active @endif" href="{{ route('users', ['status' => User1::STATUS['enabled']]) }}">Активные@if ($enabledUserCount > 0) ({{$enabledUserCount}}) @endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($status == User1::STATUS['disabled']) active @endif" href="{{ route('users', ['status' => User1::STATUS['disabled']]) }}">Неактивные@if ($disabledUserCount > 0) ({{$disabledUserCount}}) @endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($status == User1::STATUS['blocked']) active @endif" href="{{ route('users', ['status' => User1::STATUS['blocked']]) }}">Заблокированные@if ($blockedUserCount > 0) ({{$blockedUserCount}}) @endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($status == User1::STATUS['deleted']) active @endif" href="{{ route('users', ['status' => User1::STATUS['deleted']]) }}">Удаленные@if ($deletedUserCount > 0) ({{$deletedUserCount}}) @endif</a>
                </li>
            </ul>
            @php
                $gridData = [
                    'dataProvider' => $dataProvider,
                    'paginatorOptions' => [ // Here you can set some options of paginator Illuminate\Pagination\LengthAwarePaginator, used in a package.
                        'pageName' => 'p'
                    ],
                    'rowsPerPage' => 15,
                    'title' => 'Пользователи',
                    'useFilters' => true,
                    'columnFields' => [
                         [
                            'label' => 'Данные',
                            'value' => function ($row) {
                                return nl2br(User1::getInfo($row)['userLabel']);
                            },
                            'format' => 'html',
                            'attribute' => 'user_id',
                            'sort' => 'user_id'
                        ],
                        [
                            'label' => 'Подписки на каналы',
                            'attribute' => 'subscribe_count',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Подписки на группы',
                            'attribute' => 'join_group_count',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Бонусы',
                            'attribute' => 'bonus_count',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Заработано с рефералов',
                            'attribute' => 'referrals_earned',
                            'filter' => false,
                        ],
                        [
                            'label' => 'К выплате',
                            'attribute' => 'expected_to_pay',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Выведено',
                            'attribute' => 'output_amount',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Заработано',
                            'attribute' => 'earned',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Баланс',
                            'attribute' => 'balance',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Статус',
                            'value' => function ($row) {
                                return User1::getStatusList()[$row->status];
                            },
                            'format' => 'html',
                            'sort' => 'user_id',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Дата добавления',
                            'value' => function ($row) {
                                return date('d.m.Y, H:i', strtotime($row->created_at));
                            },
                            'format' => 'html',
                            'sort' => 'created_at',
                            'filter' => false,
                        ],
                        [
                            'label' => '',
                            'class' => Itstructure\GridView\Columns\ActionColumn::class,
                            'actionTypes' => [
                                'edit' => function ($data) {
                                    return route('userEdit', $data);
                                },
                            ],
                            'filter' => false,
                        ]
                    ]
                ];
            @endphp
            @gridView($gridData)
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
