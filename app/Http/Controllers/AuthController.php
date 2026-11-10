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

        // Garantir que o usuário administrador padrão exista caso o banco tenha sido recém-criado
        if ($credentials['email'] === 'admin@igreja.com') {
            $igreja = Igreja::firstOrCreate(
                ['slug' => 'igreja-exemplo'],
                ['nome' => 'Igreja Bíblia Show Central', 'plano' => 'premium']
            );
            Usuario::firstOrCreate(
                ['email' => 'admin@igreja.com'],
                [
                    'igreja_id' => $igreja->id,
                    'nome' => 'Administrador Geral',
                    'password' => Hash::make('password'),
                    'papel' => 'admin_geral',
                ]
            );
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Bem-vindo(a), ' . Auth::user()->nome . '!');
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
            'papel' => 'nullable|in:admin_igreja,apresentador,editor',
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

        $usuario = Usuario::create([
            'igreja_id' => $igrejaId,
            'nome' => trim($validated['nome']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'papel' => $validated['papel'] ?? 'apresentador',
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Conta criada com sucesso! Bem-vindo ao Painel Bíblia Show.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sessão encerrada com sucesso.');
    }
}

