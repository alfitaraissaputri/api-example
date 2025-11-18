<?php

use App\Models\Catatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes - CRUD CATATAN (TUGAS 2)
|--------------------------------------------------------------------------
|
| URL akan menjadi: /api/catatan
|
*/

Route::get('/catatan', function (Request $request) {
    // Tampilkan catatan beserta data pemiliknya
    $data = Catatan::with('user')->get();
    return response()->json($data);
});

Route::post('/catatan', function (Request $request) {
    // Validasi
    $request->validate([
        'judul' => 'required',
        'isi' => 'required',
        'user_id' => 'required|exists:users,id' // Memastikan user_id ada & valid
    ]);

    $catatan = new Catatan();
    $catatan->judul = $request->input('judul');
    $catatan->isi = $request->input('isi');
    $catatan->user_id = $request->input('user_id'); // Simpan ID pemilik
    $catatan->save();

    return response()->json($catatan, 201); // 201 = Created
});

Route::get('/catatan/{id}', function ($id) {
    // Tampilkan 1 catatan beserta data pemiliknya
    $catatan = Catatan::with('user')->find($id);

    if (!$catatan) {
        return response()->json(['message' => 'Catatan tidak ditemukan'], 404);
    }

    return response()->json($catatan);
});

Route::put('/catatan/{id}', function (Request $request, $id) {
    $catatan = Catatan::find($id);

    if ($catatan) {
        $catatan->judul = $request->input('judul');
        $catatan->isi = $request->input('isi');
        
        // Update user_id jika dikirim
        if ($request->has('user_id')) {
            $catatan->user_id = $request->input('user_id');
        }
        
        $catatan->save();
    } else {
        return response()->json([
            'message' => 'Catatan yang ingin di update tidak ada'
        ], 404);
    }

    return response()->json($catatan);
});

Route::delete('/catatan/{id}', function ($id) {
    $catatan = Catatan::find($id);

    if ($catatan) {
        $catatan->delete();
    } else {
        return response()->json([
            'message' => 'Catatan yang ingin dihapus tidak ada'
        ], 404);
    }

    return response()->json([
        'message' => 'Catatan berhasil dihapus'
    ]);
});

//Zona User

// Get semua user
Route::get('/users', function() {
    $users = User::all();
    return response()->json($users);
});

// Buat user baru
Route::post('/users', function(Request $request) {
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8'
    ]);

    $user = new User();
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->password = Hash::make($request->input('password')); // Hash password
    $user->save();

    return response()->json($user, 201);
});

// Get 1 user berdasarkan ID
Route::get('/users/{id}', function($id) {
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }
    return response()->json($user);
});

// Update user
Route::put('/users/{id}', function(Request $request, $id) {
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }

    $user->name = $request->input('name');
    $user->email = $request->input('email');
    // Update password hanya jika diisi
    if ($request->has('password') && $request->input('password') != '') {
        $user->password = Hash::make($request->input('password'));
    }
    $user->save();

    return response()->json($user);
});

// Hapus user
Route::delete('/users/{id}', function($id) {
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }
    $user->delete();
    return response()->json(['message' => 'User berhasil dihapus']);
});


//Mapping User dengan Catatan

// Rute untuk melihat semua catatan milik user tertentu
Route::get('/users/{id}/catatan', function($id) {
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }

    // Ambil user beserta relasi "catatans"
    $userWithCatatan = User::with('catatans')->find($id);

    return response()->json($userWithCatatan);
});