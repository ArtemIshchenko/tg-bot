<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TgChannel;
use App\Models\TgGroup;
use App\Models\TgTask;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {
        $channel_count = TgChannel::where('status', 1)->count();
        $group_count = TgGroup::where('status', 1)->count();
        $task_count = TgTask::where('status', 1)->count();

        return view('admin.home.index', [
            'channel_count' => $channel_count,
            'group_count' => $group_count,
            'task_count' => $task_count,
        ]);
    }
}
