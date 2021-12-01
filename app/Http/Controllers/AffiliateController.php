<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index()
    {
        return User::affiliates()->get();
    }
}
