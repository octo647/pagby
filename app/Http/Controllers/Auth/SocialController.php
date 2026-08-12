<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirectToFacebook(Request $request)
    {
        // Aceita tenant como parâmetro na query string
        $tenantHost = $request->query('tenant');
        if (!$tenantHost) {
            return redirect('/login')->with('error', 'Tenant não identificado.');
        }

        // Salva tenant na sessão para uso no callback
        session(['social_login_tenant' => $tenantHost]);

        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(Request $request)
    {
        // Obtenha os dados do usuário autenticado pelo Facebook
        $socialUser = Socialite::driver('facebook')->user();

        // Recupera o tenant salvo na sessão
        $tenantHost = session('social_login_tenant');
        if (!$tenantHost) {
            return redirect('/login')->with('error', 'Tenant não identificado no callback.');
        }

        // Redireciona para o tenant com token seguro
        return $this->redirectToTenant($socialUser, 'facebook', $tenantHost);
    }
    public function redirectToGoogle(Request $request)
    {
        // Aceita tenant como parâmetro na query string
        $tenantHost = $request->query('tenant');
        if (!$tenantHost) {
            return redirect('/login')->with('error', 'Tenant não identificado.');
        }

        // Salva tenant na sessão para uso no callback
        session(['social_login_tenant' => $tenantHost]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // Obtenha os dados do usuário autenticado pelo Google
        $socialUser = Socialite::driver('google')->user();

        // Recupera o tenant salvo na sessão
        $tenantHost = session('social_login_tenant');
        if (!$tenantHost) {
            return redirect('/login')->with('error', 'Tenant não identificado no callback.');
        }

        // Redireciona para o tenant com token seguro
        return $this->redirectToTenant($socialUser, 'google', $tenantHost);
    }

    /**
     * Redirecionar para o tenant com os dados do usuário
     */
    private function redirectToTenant($socialUser, $provider, $tenantHost)
{
    $token = Str::random(60);
    
    // Salva os dados no CACHE (compartilhado entre domínios)
    \Cache::put('social_auth_' . $token, [
        'user' => [
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
        ],
        'created_at' => now(),
    ], now()->addMinutes(10)); // Expira em 10 minutos

    \Log::info("🎫 Token criado: " . $token);
    \Log::info("📍 Redirecionando para tenant: " . $tenantHost);

    // Monta domínio completo do tenant
    $tenantDomainSuffix = config('app.tenant_domain_suffix', '.pagby.com.br');
    // Remove qualquer protocolo ou sufixo existente, pega só o slug do tenant
    $tenant = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $tenantHost);
    $tenant = preg_replace('/\..*/', '', $tenant); // Remove tudo após o primeiro ponto
    
    // Monta URL completa: {tenant}.pagby.com.br
    $tenantUrl = "https://{$tenant}{$tenantDomainSuffix}/auth/social-callback?token={$token}";
    
    \Log::info("🌐 URL de redirecionamento: " . $tenantUrl);

    return redirect()->away($tenantUrl);
}


    /**
     * Rota de teste simples
     */
    public function test()
    {
        return response()->json([
            'status' => 'working',
            'message' => 'SocialController está funcionando!',
            'timestamp' => now()
        ]);
    }
    /**
     * Recebe o token do central, busca dados do usuário e autentica/cria localmente
     */
    public function handleCentralSocialCallback(Request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            \Log::error('❌ Token de autenticação social ausente');
            return redirect('/login')->with('error', 'Token de autenticação social ausente.');
        }

        \Log::info('🔑 Recebido token para validação: ' . $token);

        // Buscar dados do usuário na central via HTTP
        $centralUrl = config('app.central_url', 'https://pagby.com.br');
        $apiUrl = $centralUrl . "/api/social-auth/" . $token;
        
        \Log::info('🌐 Fazendo requisição para: ' . $apiUrl);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($apiUrl);
            
            if (!$response->successful()) {
                \Log::error('❌ Erro na requisição ao central', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return redirect('/login')->with('error', 'Não foi possível validar o token social.');
            }

            $data = $response->json();
            
            if (!$data || empty($data['user'])) {
                \Log::error('❌ Dados de usuário inválidos ou ausentes', ['data' => $data]);
                return redirect('/login')->with('error', 'Dados de usuário inválidos.');
            }

            $userData = $data['user'];
            \Log::info('✅ Dados do usuário recebidos', ['email' => $userData['email']]);

            $userModel = config('auth.providers.users.model');
            $user = $userModel::where('email', $userData['email'])->first();
            
            if (!$user) {
                \Log::info('👤 Criando novo usuário: ' . $userData['email']);
                $user = $userModel::create([
                    'name' => $userData['name'] ?? $userData['email'],
                    'email' => $userData['email'],
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'photo' => $userData['avatar'] ?? null,
                ]);
                
                // Atribuir papel "Cliente" ao novo usuário
                try {
                    $clienteRole = \App\Models\Role::where('role', 'Cliente')->first();
                    \Log::info('🔍 Buscando papel Cliente', ['found' => $clienteRole ? 'sim' : 'não']);
                    
                    if ($clienteRole) {
                        $user->roles()->attach($clienteRole->id);
                        \Log::info('✅ Papel "Cliente" atribuído ao usuário', [
                            'user_id' => $user->id,
                            'role_id' => $clienteRole->id
                        ]);
                    } else {
                        \Log::warning('⚠️ Papel "Cliente" não encontrado no banco do tenant');
                    }
                } catch (\Exception $roleEx) {
                    \Log::error('❌ Erro ao atribuir papel Cliente', [
                        'message' => $roleEx->getMessage()
                    ]);
                }
            } else {
                \Log::info('👤 Usuário já existe: ' . $userData['email']);
                
                // Verificar se o usuário tem papéis, se não tiver, atribuir Cliente
                $rolesCount = $user->roles()->count();
                \Log::info('🔍 Verificando papéis do usuário', [
                    'user_id' => $user->id,
                    'roles_count' => $rolesCount
                ]);
                
                if ($rolesCount === 0) {
                    \Log::info('⚠️ Usuário sem papel, tentando atribuir Cliente');
                    try {
                        $clienteRole = \App\Models\Role::where('role', 'Cliente')->first();
                        \Log::info('🔍 Papel Cliente encontrado?', ['found' => $clienteRole ? 'sim' : 'não']);
                        
                        if ($clienteRole) {
                            $user->roles()->attach($clienteRole->id);
                            \Log::info('✅ Papel "Cliente" atribuído a usuário existente', [
                                'user_id' => $user->id,
                                'role_id' => $clienteRole->id
                            ]);
                        } else {
                            \Log::warning('⚠️ Papel "Cliente" não encontrado no banco do tenant');
                        }
                    } catch (\Exception $roleEx) {
                        \Log::error('❌ Erro ao atribuir papel Cliente a usuário existente', [
                            'message' => $roleEx->getMessage(),
                            'trace' => $roleEx->getTraceAsString()
                        ]);
                    }
                } else {
                    \Log::info('ℹ️ Usuário já possui papéis', ['count' => $rolesCount]);
                }
            }

            Auth::guard('web')->login($user, true);
            \Log::info('✅ Login realizado com sucesso para: ' . $user->email);
            
            // Verificar se há dados de agendamento pendentes na sessão
            if (session()->has('requires_login_for_booking') && session()->has('booking_data')) {
                \Log::info('📅 Usuário tem agendamento pendente, redirecionando para /agendar');
                return redirect('/agendar');
            }
            
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            \Log::error('❌ Exceção ao processar callback social', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Erro ao processar autenticação social.');
        }
    }
}