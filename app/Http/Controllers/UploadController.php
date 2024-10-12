<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Arquivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    /**
     * Upload a file.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(['data' => false ], 403);
    }
}
