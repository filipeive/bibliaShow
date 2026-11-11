<?php

namespace App\Http\Controllers;

use App\Models\Igreja;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        $igrejas = Igreja::orderBy('nome')->get();
        return view('auth.login', compact('igrejas'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Garantir que o usuário administrador padrão exista apenas em ambiente local se ainda não configurado
        if (app()->environment('local') && $credentials['email'] === 'admin@igreja.com' && !Usuario::where('email', 'admin@igreja.com')->exists()) {
            $igreja = Igreja::firstOrCreate(
                ['slug' => 'igreja-exemplo'],
                ['nome' => 'Igreja Bíblia Show Central', 'plano' => 'premium']
            );
            Usuario::create([
                'igreja_id' => $igreja->id,
                'nome' => 'Administrador Geral',
                'email' => 'admin@igreja.com',
                'password' => Hash::make('password'),
                'papel' => 'admin_geral',
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->papel === 'apresentador') {
                return redirect()->intended(route('apresentador.painel'))
                    ->with('success', 'Bem-vindo ao seu Estúdio de Apresentador, ' . $user->nome . '!');
            } elseif (in_array($user->papel, ['admin_geral', 'admin_igreja', 'editor'])) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Bem-vindo ao Painel Administrativo, ' . $user->nome . '!');
            }

            return redirect()->intended(route('game.play'))
                ->with('success', 'Bem-vindo ao Bíblia Show, ' . $user->nome . '!');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas. Verifique seu e-mail e senha.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        $igrejas = Igreja::orderBy('nome')->get();
        return view('auth.register', compact('igrejas'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'igreja_nome' => 'nullable|string|max:150',
            'igreja_id' => 'nullable|exists:igrejas,id',
            'papel' => 'nullable|in:usuario,apresentador',
        ]);

        if (!empty($validated['igreja_nome'])) {
            $igreja = Igreja::firstOrCreate(
                ['slug' => Str::slug($validated['igreja_nome'])],
                ['nome' => trim($validated['igreja_nome']), 'plano' => 'gratuito']
            );
            $igrejaId = $igreja->id;
        } else {
            $igrejaId = $validated['igreja_id'] ?? (Igreja::value('id') ?: Igreja::create([
                'nome' => 'Igreja Bíblia Show Central',
                'slug' => 'igreja-biblia-show',
            ])->id);
        }

        $papelEscolhido = in_array($validated['papel'] ?? '', ['usuario', 'apresentador']) ? $validated['papel'] : 'usuario';

        $usuario = Usuario::create([
            'igreja_id' => $igrejaId,
            'nome' => trim($validated['nome']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'papel' => $papelEscolhido,
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        if ($usuario->papel === 'apresentador') {
            return redirect()->route('apresentador.painel')
                ->with('success', 'Conta de Apresentador criada com sucesso! Você já pode gerenciar suas salas.');
        }

        return redirect()->route('game.play')
            ->with('success', 'Conta criada com sucesso! Bem-vindo ao Bíblia Show.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sessão encerrada com sucesso.');
    }
}

