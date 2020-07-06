<?php

namespace App\Http\Controllers;

// use Auth;
use Illuminate\Support\Facades\Auth;
use App\Item;
use App\CartItem;
use Illuminate\Http\Request;

class BuyController extends Controller
{
    public function index()
    {
        $cartitems = CartItem::select('cart_items.*', 'items.name', 'items.amount')
            ->where('user_id', Auth::id())
            ->join('items', 'items.id','=','cart_items.item_id')
            ->get();
            
        $subtotal = 0;
        foreach($cartitems as $cartitem) {
            $subtotal += $cartitem->amount * $cartitem->quantity;
        }
        return view('buy/index', ['cartitems' => $cartitems, 'subtotal' => $subtotal]);
    }

    public function store(Request $request)
    {
        if( $request->has('post') ) { //フォームからのリクエストパラメータにpostという値が含まれているかどうかを判定
            CartItem::where('user_id', Auth::id())->delete(); //ユーザーが持っているカート情報を削除し、同じ注文を何度も行ってしまわないようにします
            return view('buy/complete'); //購入完了へ進みます。
        }
        $request->flash(); //フォームのリクエスト情報をセッションに記録。
        return $this->index(); //購入画面のビューを再度表示しています。
    }
}
