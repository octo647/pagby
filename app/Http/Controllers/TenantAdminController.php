<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantAdminController extends Controller
{
    public function index() {
        $tenants = Tenant::all();
      
        return view('admin.tenants.index', compact('tenants'));
    }
}
