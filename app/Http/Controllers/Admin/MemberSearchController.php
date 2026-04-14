<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('card_number') ?? $request->get('q');

        if (!$query) {
            return response()->json(null);
        }

        // Clean query: trim and remove spaces for better matching
        $cleanQuery = trim(str_replace(' ', '', $query));

        $member = User::where('is_admin', false)
            ->where(function($q) use ($cleanQuery) {
                $q->whereRaw("REPLACE(card_number, ' ', '') LIKE ?", ["%{$cleanQuery}%"])
                  ->orWhere('name', 'LIKE', "%{$cleanQuery}%")
                  ->orWhere('phone', 'LIKE', "%{$cleanQuery}%")
                  ->orWhere('email', 'LIKE', "%{$cleanQuery}%");
            })
            ->first();

        return response()->json($member);
    }
}
