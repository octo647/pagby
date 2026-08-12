<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AsaasService;

class TestAsaasController extends Controller
{
    public function testCreateCustomer(Request $request)
    {
        $asaas = new AsaasService();
        $customerData = [
            'name' => 'Cliente Teste Pagby',
            'email' => 'cliente.teste.pagby+' . uniqid() . '@example.com',
            'cpfCnpj' => '12345678909',
            'phone' => '11999999999',
        ];
        $result = $asaas->criarCliente($customerData);
        return response()->json($result);
    }
}