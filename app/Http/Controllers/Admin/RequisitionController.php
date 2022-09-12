<?php

namespace App\Http\Controllers\Admin;

use App\Models\Requisition;
use App\Http\Controllers\Controller;
use App\Models\User1;
use Illuminate\Http\Request;
use Itstructure\GridView\DataProviders\EloquentDataProvider;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status = -1)
    {
        $status = intval($status);

        $query = Requisition::query()
                    ->join('user1s', 'requisitions.user_id', '=', 'user1s.user_id')
                    ->select('requisitions.*', 'username', 'first_name', 'last_name')
                    ->orderBy('requisitions.created_at', 'DESC');
        if ($status > -1) {
            $query->where('requisitions.status', $status);
        } else {
            $query->where('requisitions.status', '!=', Requisition::STATUS['nothing']);
        }

        $dataProvider = new EloquentDataProvider($query);

        return view('admin.requisition.index', [
            'status' => $status,
            'dataProvider' => $dataProvider,
            'allCount' => Requisition::where('status', '!=', Requisition::STATUS['nothing'])->count(),
            'waitingCount' => Requisition::where('status', Requisition::STATUS['waiting'])->count(),
            'doneCount' => Requisition::where('status', Requisition::STATUS['done'])->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, $id)
    {
         $request->validate([
            'status' => 'integer',
        ]);

        $requisition = Requisition::findOrFail($id);
        $requisition->status = $request->status;

        if ($requisition->save()) {
            return redirect()->route('requisitions')->withSuccess('Статус успешно изменен');
        }

        return redirect()->back()->withErrors('Ошибка изменения статуса');
    }
}
