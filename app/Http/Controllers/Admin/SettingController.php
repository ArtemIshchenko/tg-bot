<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $input = [];
        $rules = [];
        $messages = [];
        if (isset($data['\\'])) {
            foreach ($data['\\'] as $number => $value) {
                $model = Setting::where('number', intval($number))->first();
                if (!is_null($model)) {
                    $model->value = is_null($value) ? '' : $value;
                    $input["value_$number"] = $model->value;
                    $rules["value_$number"] = 'required|string|max:1024';
                    $messages["value_$number.required"] = "The value {$model->description} field is required.";
                }
            }

            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                return redirect('settings')
                    ->withErrors($validator)
                    ->withInput();
            }
            foreach ($data['\\'] as $number => $value) {
                $model = Setting::where('number', intval($number))->first();
                if (!is_null($model)) {
                    $model->value = is_null($value) ? '' : $value;
                    $model->save();
                }
            }
        }

        return view('admin.setting.index', [
            'settings' => Setting::getSettings(),
        ]);
    }

}
