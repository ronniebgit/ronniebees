<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        if (! $user || ! $user->is_admin) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => ['This account does not have admin access.'],
            ]);
        }

        $token = $user->createToken('admin-api-token')->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'is_admin' => $request->user()->is_admin,
            ],
        ]);
    }

    public function dashboard(Request $request)
    {
        $orders = Order::latest()->take(20)->get();

        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
            'stats' => [
                'total_orders' => Order::count(),
                'revenue' => (float) Order::sum('total'),
                'pending' => Order::where('status', 'pending')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
            ],
            'orders' => $orders->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'status' => $order->status,
                    'total' => (float) $order->total,
                    'created_at' => $order->created_at?->toISOString(),
                ];
            })->values(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
