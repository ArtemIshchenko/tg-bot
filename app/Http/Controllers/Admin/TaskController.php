<?php

namespace App\Http\Controllers\Admin;

use App\Models\TgTask;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Itstructure\GridView\DataProviders\EloquentDataProvider;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status = -1)
    {
        $query = TgTask::query()->orderBy('created_at', 'DESC');
        if ($status > -1) {
            $query->where('status', $status);
        }

        $dataProvider = new EloquentDataProvider($query);

        return view('admin.task.index', [
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
        return view('admin.task.create', [
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
            'name' => 'string|max:256',
            'description' => 'string|max:512',
            'status' => 'integer',
        ]);

        $task = new TgTask;
        $task->name = $request->name;
        if (is_null($request->description)) {
            $request->description = '';
        }
        $task->description = $request->description;
        $task->status = TgTask::STATUS['enabled'];

        if ($task->save()) {
            return redirect()->route('tasks.edit', $task->id)->withSuccess('Задача успешно добавлена');
        }

        return redirect()->back()->withErrors('Ошибка добавления задачи');
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
     *  @param  \App\Models\TgTask $task
     * @return \Illuminate\Http\Response
     */
    public function edit(TgTask $task)
    {

        return view('admin.task.edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\TgTask $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TgTask $task)
    {

        $request->validate([
            'name' => 'string|max:256',
            'description' => 'string|max:512',
            'status' => 'integer',
        ]);

        $task->name = $request->name;
        if (is_null($request->description)) {
            $request->description = '';
        }
        $task->description = $request->description;
        $task->status = $request->status;

        $task->save();

        return redirect()->back()->withSuccess('Задача успешно обновлена');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\TgGroup $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(TgTask $task)
    {
        $task->delete();

        return redirect()->back()->withSuccess('Задача успешно удалена');
    }

    /**
     * @property-description Изменение статуса задачи
     * @param $id
     * @param $status
     */
    public function changeStatus($id, $status) {
        $id = intval($id);
        $status = intval($status);

        $model = TgTask::find($id);
        if (!is_null($model)) {
            $model->status = $status;
            $model->save();
        };

        return redirect()->route('tasks.index')->withSuccess('Статус задачи успешно изменен');
    }
}
