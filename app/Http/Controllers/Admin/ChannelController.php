<?php

namespace App\Http\Controllers\Admin;

use App\Models\TgChannel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Itstructure\GridView\DataProviders\EloquentDataProvider;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status = -1)
    {

        $query = TgChannel::query()->orderBy('created_at', 'DESC');
        if ($status > -1) {
            $query->where('status', $status);
        }

        $dataProvider = new EloquentDataProvider($query);

        return view('admin.channel.index', [
            'status' => $status,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.channel.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $request->validate([
            'alias' => 'string|max:256|unique:tg_channels',
            'url' => 'required_without:alias|string|max:256|unique:tg_channels',
            'name' => 'string|max:256',
            'description' => 'string|max:512',
            'status' => 'integer',
            'limit' => 'integer',
        ]);

        $channel = new TgChannel;
        $channel->alias = $request->alias;
        $channel->url = $request->url;
        $channel->name = $request->name;
        $channel->description = $request->description;
        $channel->status = TgChannel::STATUS['enabled'];
        $channel->limit = intval($request->limit);

        if ($channel->save()) {
            return redirect()->route('channels.edit', $channel->id)->withSuccess('Канал успешно добавлен');
        }

        return redirect()->back()->with('errMsg', 'Ошибка добавления канала');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     *  @param  \App\Models\TgСhannel $channel
     * @return \Illuminate\Http\Response
     */
    public function edit(TgChannel $channel)
    {

        return view('admin.channel.edit', [
            'channel' => $channel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\TgChannel $channel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TgChannel $channel)
    {

        $request->validate([
            'alias' => [
                'string',
                'max:256',
                Rule::unique('tg_channels')->ignore($channel->id),
            ],
            'url' => [
                'required_without:alias',
                'string',
                'max:256',
                Rule::unique('tg_channels')->ignore($channel->id),
            ],
            'name' => 'string|max:256',
            'description' => 'string|max:512',
            'status' => 'integer',
            'limit' => 'integer',
        ]);

        $channel->alias = $request->alias;
        $channel->url = $request->url;
        $channel->name = $request->name;
        $channel->description = $request->description;
        $channel->status = $request->status;
        $channel->limit = intval($request->limit);

        if ($channel->save()) {
            return redirect()->back()->withSuccess('Канал успешно обновлен');
        }

        return redirect()->back()->with('errMsg', 'Ошибка обновления канала');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TgChannel $channel
     * @return \Illuminate\Http\Response
     */
    public function destroy(TgChannel $channel)
    {
        $channel->delete();

        return redirect()->back()->withSuccess('Канал успешно удален');
    }

    /**
     * @property-description Изменение статуса канала
     * @param $id
     * @param $status
     */
    public function changeStatus($id, $status) {
        $id = intval($id);
        $status = intval($status);

        $model = TgChannel::find($id);
        if (!is_null($model)) {
            $model->status = $status;
            $model->save();
        };

        return redirect()->route('channels.index')->withSuccess('Статус канала успешно изменен');
    }
}
