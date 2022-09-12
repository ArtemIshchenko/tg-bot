@php
    use App\Models\Requisition;
    use App\Models\User1;
@endphp
@extends('layouts.admin_layout')

@section('title', 'Главная')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Заявки</h1>
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
                    <a class="nav-link @if ($status == -1) active @endif" href="{{ route('requisitions', ['status' => -1]) }}">Все@if ($allCount > 0) ({{$allCount}}) @endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($status == Requisition::STATUS['waiting']) active @endif" href="{{ route('requisitions', ['status' => Requisition::STATUS['waiting']]) }}">В ожидании@if ($waitingCount > 0) ({{$waitingCount}}) @endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($status == Requisition::STATUS['done']) active @endif" href="{{ route('requisitions', ['status' => Requisition::STATUS['done']]) }}">Проведено@if ($doneCount > 0) ({{$doneCount}}) @endif</a>
                </li>
            </ul>
            @php
                $gridData = [
                    'dataProvider' => $dataProvider,
                    'paginatorOptions' => [ // Here you can set some options of paginator Illuminate\Pagination\LengthAwarePaginator, used in a package.
                        'pageName' => 'p'
                    ],
                    'rowsPerPage' => 15,
                    'title' => 'Каналы',
                    'useFilters' => true,
                    'columnFields' => [
                        [
                            'label' => 'ID',
                            'attribute' => 'id',
                            'htmlAttributes' => [
                                'style' => 'width: 5%',
                            ]
                        ],
                        [
                            'label' => 'ID пользователя',
                            'attribute' => 'user_id',
                        ],
                        [
                            'label' => 'ФИО',
                            'value' => function ($row) {
                                    return 'Псевдоним: ' . $row->username . '<br>' .
                                            'ФИО: ' . $row->first_name . ' ' . $row->last_name;
                            },
                            'format' => 'html',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Система оплаты',
                            'value' => function ($row) {
                                return Requisition::getPaymentSistemList()[$row->payment_system];
                            },
                            'sort' => 'payment_system',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Кашелек',
                            'attribute' => 'wallet',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Сумма для вывода',
                            'attribute' => 'amount_to_output',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Статус',
                            'value' => function ($row) {
                                return Requisition::getStatusList()[$row->status];
                            },
                            'sort' => 'status',
                            'filter' => false,
                        ],
                        [
                            'label' => 'Дата добавления',
                            'value' => function ($row) {
                                return date('d.m.Y, H:i', strtotime($row->created_at));
                            },
                            'sort' => 'created_at',
                            'filter' => false,
                        ],
                        [
                            'label' => '',
                            'class' => Itstructure\GridView\Columns\ActionColumn::class,
                            'actionTypes' => [
                                [
                                    'class' => Itstructure\GridView\Actions\Edit::class, // REQUIRED
                                    'url' => function ($data) { // Optional
                                        return route('requisitionChangeStatus', ['id' => $data['id'], 'status' =>  Requisition::STATUS['done']]);
                                    },
                                    'htmlAttributes' => [ // Optional
                                        'style' => 'color: yellow; font-size: 16px;',
                                    ]
                                ]
                            ],
                            'filter' => false,
                            'htmlAttributes' => [ // Optional
                                'style' => 'width: 7%',
                            ]
                        ]
                    ]
                ];
            @endphp
            @gridView($gridData)
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
