<?php

namespace App\Http\Controllers\Admin;

use App\Models\User1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Itstructure\GridView\DataProviders\EloquentDataProvider;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status = -1)
    {
        $status = intval($status);
        $query = User1::query()->orderBy('created_at', 'DESC');
        if ($status > -1) {
            $query->where('status', $status);
        }

        $dataProvider = new EloquentDataProvider($query);


        return view('admin.user.index', [
            'status' => $status,
            'dataProvider' => $dataProvider,
            'allUserCount' => User1::count(),
            'disabledUserCount' => User1::where('status', User1::STATUS['disabled'])->count(),
            'enabledUserCount' => User1::where('status', User1::STATUS['enabled'])->count(),
            'blockedUserCount' => User1::where('status', User1::STATUS['blocked'])->count(),
            'deletedUserCount' => User1::where('status', User1::STATUS['deleted'])->count(),
        ]);
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
     *  @param  \App\Models\User1 $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User1 $user)
    {

        return view('admin.user.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User1 $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User1 $user)
    {

        $request->validate([
            'first_name' => 'string|max:64',
            'last_name' => 'string|max:64',
            'username' => 'string|max:64',
            'subscribe_count' => 'numeric',
            'join_group_count' => 'numeric',
            'bonus_count' => 'numeric',
            'referrals_earned' => 'numeric',
            'expected_to_pay' => 'numeric',
            'output_amount' => 'numeric',
            'earned' => 'numeric',
            'balance' => 'numeric',
            'status' => 'integer',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->subscribe_count = $request->subscribe_count;
        $user->join_group_count = $request->join_group_count;
        $user->bonus_count = $request->bonus_count;
        $user->referrals_earned = $request->referrals_earned;
        $user->expected_to_pay = $request->expected_to_pay;
        $user->output_amount = $request->output_amount;
        $user->earned = $request->earned;
        $user->balance = $request->balance;
        $user->status = $request->status;

        $user->save();

        return redirect()->back()->withSuccess('Канал успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User1 $user)
    {
        $user->status = User1::STATUS['deleted'];
        $user->save();

        return redirect()->back()->withSuccess('Пользователь успешно удален');
    }
}
