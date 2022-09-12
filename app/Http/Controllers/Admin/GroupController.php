<?php

namespace App\Http\Controllers\Admin;

use App\Models\TgGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Itstructure\GridView\DataProviders\EloquentDataProvider;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status = -1)
    {
        $query = TgGroup::query()->orderBy('created_at', 'DESC');
        if ($status > -1) {
            $query->where('status', $status);
        }

        $dataProvider = new EloquentDataProvider($query);

        return view('admin.group.index', [
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
        return view('admin.group.create', [
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
            'url' => 'required|string|max:256|unique:tg_groups',
            'name' => 'string|max:256',
            'description' => 'string|max:512',
            'status' => 'integer',
            'limit' => 'integer',
        ]);

        $group = new TgGroup;
        $group->url = $request->url;
        $group->name = $request->name;
        $group->description = $request->description;
        $group->status = TgGroup::STATUS['enabled'];
        $group->limit = intval($request->limit);

        if ($group->save()) {
            return redirect()->route('groups.edit', $group->id)->withSuccess('Группа успешно добавлена');
        }

        return redirect()->back()->with('errMsg', 'Ошибка добавления группы');
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
     *  @param  \App\Models\TgGroup $group
     * @return \Illuminate\Http\Response
     */
    public function edit(TgGroup $group)
    {

        return view('admin.group.edit', [
            'group' => $group,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\TgGroup $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TgGroup $group)
    {
        $request->validate([
            'url' => [
                'required',
                'string',
                'max:256',
                Rule::unique('tg_groups')->ignore($group->id),
            ],
            'name' => 'string|max:256',
            'description' => 'string|max:512',
            'status' => 'integer',
            'limit' => 'integer',
        ]);

        $group->url = $request->url;
        $group->name = $request->name;
        $group->description = $request->description;
        $group->status = $request->status;
        $group->limit = intval($request->limit);

        if ($group->save()) {
            return redirect()->back()->withSuccess('Группа успешно обновлена');
        }

        return redirect()->back()->with('errMsg', 'Ошибка обновления группы');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\TgGroup $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(TgGroup $group)
    {
        $group->delete();

        return redirect()->back()->withSuccess('Группа успешно удалена');
    }

    /**
     * @property-description Изменение статуса группы
     * @param $id
     * @param $status
     */
    public function changeStatus($id, $status) {
        $id = intval($id);
        $status = intval($status);

        $model = TgGroup::find($id);
        if (!is_null($model)) {
            $model->status = $status;
            $model->save();
        };

        return redirect()->route('groups.index')->withSuccess('Статус группы успешно изменен');
    }
}
