<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequisicaoController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $livrosDisponiveis = Livro::query()
            ->with('editora')
            ->leftJoin('requisicoes', function ($join) {
                $join->on('livros.id', '=', 'requisicoes.livro_id')
                    ->whereNull('requisicoes.devolvido_em');
            })
            ->whereNull('requisicoes.id')
            ->distinct()
            ->select('livros.*')
            ->orderBy('livros.nome')
            ->get();

        return view('requisicoes.index', [
            'livrosDisponiveis' => $livrosDisponiveis,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
            // Verificar se o utilizador já tem 3 livros requisitados em simultâneo
            $ativos = Requisicao::query()
                ->where('user_id', $user->id)
                ->whereNull('devolvido_em')
                ->count();
            if ($ativos >= 3) {
                return back()
                    ->with('status', 'Só pode ter até 3 livros requisitados em simultâneo.')
                    ->withInput();
            }
        $user = $request->user();

        $data = $request->validate([
            'livro_id' => ['required', 'integer', 'exists:livros,id'],
        ]);

        $livroId = (int) $data['livro_id'];

        $ocupado = Requisicao::query()
            ->where('livro_id', $livroId)
            ->whereNull('devolvido_em')
            ->exists();

        if ($ocupado) {
            return back()->withErrors([
                'livro_id' => 'Este livro não está disponível para requisição neste momento.',
            ])->withInput();
        }


        $requisitadoEm = now();
        $fimPrevistoEm = $requisitadoEm->copy()->addDays(5);

        // Gerar o próximo número sequencial
        $ultimoNumero = Requisicao::max('numero');
        $proximoNumero = $ultimoNumero ? ((int)$ultimoNumero + 1) : 1;


        $requisicao = Requisicao::query()->create([
            'numero' => $proximoNumero,
            'user_id' => $user->id,
            'livro_id' => $livroId,
            'requisitado_em' => $requisitadoEm,
            'fim_previsto_em' => $fimPrevistoEm,
        ]);

        // Enviar email para todos os admins e para o cidadão
        // Só envia para o utilizador se estiver em ambiente de desenvolvimento/teste
        if (app()->environment(['local', 'testing'])) {
            \Mail::to($user->email)->send(new \App\Mail\NovaRequisicaoMail($requisicao));
        } else {
            // Em produção, envia para ambos
            \Mail::to($user->email)->send(new \App\Mail\NovaRequisicaoMail($requisicao));
            $adminEmail = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->value('email');
            if ($adminEmail) {
                \Mail::to($adminEmail)->send(new \App\Mail\NovaRequisicaoMail($requisicao));
            }
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.livros')
                ->with('status', 'Requisição criada com sucesso.');
        } else {
            return redirect()
                ->route('livros')
                ->with('status', 'Requisição criada com sucesso.');
        }
    }

    public function devolver(Request $request, Requisicao $requisicao): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && (int) $requisicao->user_id !== (int) $user->id) {
            abort(403, 'Não tem permissão para devolver esta requisição.');
        }

        if ($requisicao->devolvido_em !== null) {
            return redirect()
                ->route('requisicoes.index')
                ->with('status', 'Esta requisição já estava devolvida.');
        }

        $requisicao->update([
            'devolvido_em' => now(),
        ]);

        return redirect()
            ->route('requisicoes.index')
            ->with('status', 'Livro devolvido com sucesso.');
    }
}
