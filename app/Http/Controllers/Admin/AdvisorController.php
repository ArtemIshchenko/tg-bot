<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdvisorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $advisors = Advisor::role('advisor')->orderBy('created_at', 'DESC')->get();

        return view('admin.advisor.index', [
            'advisors' => $advisors
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.advisor.create', [
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
             'name' => ['required', 'string', 'max:255'],
             'email' => ['required', 'string', 'email', 'max:255', 'unique:advisors'],
             'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Advisor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('advisor');

        return redirect()->route('advisors.edit', $user->id)->withSuccess('Рекламодатель успешно добавлен');
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
     *  @param  \App\Models\Advisor $advisor
     * @return \Illuminate\Http\Response
     */
    public function edit(Advisor $advisor)
    {

        return view('admin.advisor.edit', [
            'advisor' => $advisor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Advisor $advisor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Advisor $advisor)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('advisors')->ignore($advisor->id),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $advisor->name = $request->name;
        $advisor->email = $request->email;
        $advisor->password = $request->password;

        if ($advisor->save()) {
            return redirect()->back()->withSuccess('Рекламодатель успешно обновлен');
        }

        return redirect()->back()->with('errMsg', 'Ошибка обновления рекламодателя');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Advisor $advisor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Advisor $advisor)
    {
        $advisor->delete();

        return redirect()->back()->withSuccess('Рекламодатель успешно удален');
    }

}
