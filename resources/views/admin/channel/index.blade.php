@php
    use App\Models\TgChannel;
    use App\Models\UserSubscribe;
@endphp
@extends('layouts.admin_layout')

@section('title', 'Главная')

@section('content')

@push('scripts')
    <script src="{{ asset('js/channels/index.js')}}"></script>
@endpush
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Все каналы</h1>
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
                            'label' => 'URL',
                            'attribute' => 'url',
                            'format' => 'url',
                        ],
                        [
                            'label' => 'Псевдоним',
                            'attribute' => 'alias',
                        ],
                        [
                            'label' => 'Наименование',
                            'attribute' => 'name',
                        ],
                        [
                            'label' => 'Кол. юзеров',
                            'value' => function ($row) {
                                return UserSubscribe::subscribeCount($row->id, UserSubscribe::TYPE['channel']);
                            },
                            'filter' => false,
                        ],
                        [
                            'label' => 'Лимит юзеров',
                            'attribute' => 'limit',
                        ],
                        [
                            'label' => 'Статус',
                            'value' => function ($row) {
                                return
                                    '<div class="custom-control custom-switch">
                                        <input type="checkbox"' . ($row->status == TgChannel::STATUS['enabled'] ? ' checked' : '') . ' class="custom-control-input" id="custom-status' . $row->id . '" data-url="' . route('channelChangeStatus', ['id' => $row->id, 'status' => $row->status == TgChannel::STATUS['disabled'] ? TgChannel::STATUS['enabled'] : TgChannel::STATUS['disabled']]) .'">
                                        <label class="custom-control-label" for="custom-status' . $row->id .'">' . TgChannel::getStatusList()[$row->status] . '</label>
                                    </div>';
                            },
                            'format' => 'html',
                            'sort' => 'status',
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
                                    return route('channels.edit', $data['id']);
                                },
                                [
                                    'class' => Itstructure\GridView\Actions\Delete::class, // Required
                                    'url' => function ($data) { // Optional
                                        return route('channelDestroy', $data['id']);
                                    },
                                    'htmlAttributes' => [ // Optional
                                        'target' => '_blank',
                                        'style' => 'color: yellow; font-size: 16px;',
                                        'onclick' => 'return window.confirm("Are you sure you want to delete?");'
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
