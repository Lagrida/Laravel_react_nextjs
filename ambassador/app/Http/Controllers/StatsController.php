<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Ordere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $links = Link::where('user_id', $user_id)->with(['orderes' => fn($query) => $query->where('is_completed', 1)])->get();
        $links = $links->map(function(Link $link){
            return [
                'code' => $link->code,
                'count' => $link->orderes->count(),
                'revenue' => $link->orderes->sum(fn(Ordere $ordere) => $ordere->ambassador_revenue)
            ];
        });
        return response($links, Response::HTTP_OK);
    }
    public function rankings(Request $request)
    {
        /*$ambassadors = User::ambassadors()->get();
        $ambassadors = $ambassadors->map(fn(User $user) => [
            'name' => $user->name,
            'revenue' => $user->revenue
        ]);
        $ambassadors = $ambassadors->sortByDesc('revenue')->values();
        return response($ambassadors, Response::HTTP_OK);*/
        /*$rankings = Redis::zrevrange('rankings', 0, -1, 'WITHSCORES');
        return response($rankings, Response::HTTP_OK);*/
        return Redis::zrevrange('rankings', 0, -1, ['withscores' => true]);
    }
}
