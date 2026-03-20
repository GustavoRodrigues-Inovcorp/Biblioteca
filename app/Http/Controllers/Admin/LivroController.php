<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LivroController extends Controller
{
	public function index(): View
	{
		return view('admin.livros');
	}

	public function create(): View
	{
		return view('admin.livros.create', [
			'editoras' => Editora::query()->orderBy('nome')->get(['id', 'nome']),
			'autores' => Autor::query()->orderBy('nome')->get(['id', 'nome']),
		]);
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $this->validateLivro($request);
		$autorFotoPath = $this->storeAutorFoto($request);
		$editoraLogoPath = $this->storeEditoraLogo($request);

		$livro = Livro::query()->create([
			'isbn' => $data['isbn'],
			'nome' => $data['nome'],
			'editora_id' => $this->resolveEditoraId($data, $editoraLogoPath),
			'bibliografia' => $data['bibliografia'] ?? null,
			'imagem_capa' => $this->storeCapaImage($request),
			'preco' => $this->normalizePrice($data['preco']),
		]);

		$livro->autores()->sync($this->resolveAutorIds($data, $autorFotoPath));

		return redirect()->route('admin.livros')->with('status', 'Livro criado com sucesso.');
	}

	public function edit(Livro $livro): View
	{
		$livro->load('autores:id,nome');

		return view('admin.livros.edit', [
			'livro' => $livro,
			'editoras' => Editora::query()->orderBy('nome')->get(['id', 'nome']),
			'autores' => Autor::query()->orderBy('nome')->get(['id', 'nome']),
		]);
	}

	public function update(Request $request, Livro $livro): RedirectResponse
	{
		$data = $this->validateLivro($request, $livro);
		$autorFotoPath = $this->storeAutorFoto($request);
		$editoraLogoPath = $this->storeEditoraLogo($request);
		$imagemCapa = $livro->imagem_capa;

		if ($request->hasFile('imagem_capa')) {
			$this->deleteStoredCapaIfLocal($imagemCapa);
			$imagemCapa = $this->storeCapaImage($request);
		}

		$livro->update([
			'isbn' => $data['isbn'],
			'nome' => $data['nome'],
			'editora_id' => $this->resolveEditoraId($data, $editoraLogoPath),
			'bibliografia' => $data['bibliografia'] ?? null,
			'imagem_capa' => $imagemCapa,
			'preco' => $this->normalizePrice($data['preco']),
		]);

		$livro->autores()->sync($this->resolveAutorIds($data, $autorFotoPath));

		return redirect()->route('admin.livros')->with('status', 'Livro atualizado com sucesso.');
	}

	public function destroy(Livro $livro): RedirectResponse
	{
		$livro->autores()->detach();
		$this->deleteStoredCapaIfLocal($livro->imagem_capa);
		$livro->delete();

		return redirect()->route('admin.livros')->with('status', 'Livro eliminado com sucesso.');
	}

	public function show(Livro $livro): View
	{
		$livro->load(['autores', 'editora', 'requisicoes.user']);
		return view('admin.livros.show', [
			'livro' => $livro,
		]);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function validateLivro(Request $request, ?Livro $livro = null): array
	{
		$uniqueRule = 'unique:livros,isbn';

		if ($livro) {
			$uniqueRule .= ',' . $livro->id;
		}

		$validator = Validator::make($request->all(), [
			'isbn' => ['required', 'string', 'size:13', $uniqueRule],
			'nome' => ['required', 'string', 'max:255'],
			'editora_input' => ['nullable', 'string', 'max:255'],
			'bibliografia' => ['nullable', 'string'],
			'imagem_capa' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
			'autor_foto' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
			'editora_logotipo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
			'preco' => ['required', 'numeric', 'min:0'],
			'autores_input' => ['nullable', 'string'],
			'autores' => ['nullable', 'array'],
			'autores.*' => ['exists:autores,id'],
		]);

		$validator->after(function ($validator) use ($request): void {
			if ($this->isNewEditoraInput($request) && ! $request->hasFile('editora_logotipo')) {
				$validator->errors()->add('editora_logotipo', 'Para uma editora nova, o logótipo é obrigatório.');
			}

			if ($this->hasNewAutoresInput($request) && ! $request->hasFile('autor_foto')) {
				$validator->errors()->add('autor_foto', 'Para autor(es) novo(s), a foto é obrigatória.');
			}
		});

		return $validator->validate();
	}

	/**
	 * @param array<string, mixed> $data
	 * @return array<int>
	 */
	private function resolveAutorIds(array $data, ?string $autorFotoPath = null): array
	{
		$input = trim((string) ($data['autores_input'] ?? ''));

		if ($input === '') {
			return array_map('intval', $data['autores'] ?? []);
		}

		$nomes = preg_split('/[\r\n,;]+/', $input) ?: [];
		$nomes = array_values(array_unique(array_filter(array_map(function (string $nome): string {
			return trim(preg_replace('/\s+/', ' ', $nome) ?? '');
		}, $nomes), fn (string $nome): bool => $nome !== '')));

		$autoresExistentes = Autor::query()->get(['id', 'nome']);
		$autoresPorNomeNormalizado = [];

		foreach ($autoresExistentes as $autorExistente) {
			$chave = $this->normalizeAutorNome((string) $autorExistente->nome);

			if ($chave !== '' && ! isset($autoresPorNomeNormalizado[$chave])) {
				$autoresPorNomeNormalizado[$chave] = $autorExistente;
			}
		}

		$autorIds = [];

		foreach ($nomes as $nome) {
			$chave = $this->normalizeAutorNome($nome);

			if ($chave === '') {
				continue;
			}

			$autor = $autoresPorNomeNormalizado[$chave] ?? null;

			if (! $autor) {
				$autor = Autor::query()->create([
					'nome' => $nome,
					'foto' => $autorFotoPath,
				]);
				$autoresPorNomeNormalizado[$chave] = $autor;
			}

			$autorIds[] = (int) $autor->id;
		}

		return array_values(array_unique($autorIds));
	}

	/**
	 * @param array<string, mixed> $data
	 */
	private function resolveEditoraId(array $data, ?string $editoraLogoPath = null): ?int
	{
		$input = trim((string) ($data['editora_input'] ?? ''));

		if ($input === '') {
			return null;
		}

		$editorasExistentes = Editora::query()->get(['id', 'nome']);

		foreach ($editorasExistentes as $editoraExistente) {
			if ($this->normalizeEntityName((string) $editoraExistente->nome) === $this->normalizeEntityName($input)) {
				return (int) $editoraExistente->id;
			}
		}

		$editora = Editora::query()->create([
			'nome' => $input,
			'logotipo' => $editoraLogoPath,
		]);

		return (int) $editora->id;
	}

	private function isNewEditoraInput(Request $request): bool
	{
		$input = trim((string) $request->input('editora_input', ''));

		if ($input === '') {
			return false;
		}

		$inputNormalizado = $this->normalizeEntityName($input);

		$editoraExistente = Editora::query()
			->get(['nome'])
			->first(fn (Editora $editora): bool => $this->normalizeEntityName((string) $editora->nome) === $inputNormalizado);

		return ! $editoraExistente;
	}

	private function hasNewAutoresInput(Request $request): bool
	{
		$input = trim((string) $request->input('autores_input', ''));

		if ($input === '') {
			return false;
		}

		$nomes = preg_split('/[\r\n,;]+/', $input) ?: [];
		$nomesNormalizados = array_values(array_filter(array_map(function (string $nome): string {
			return $this->normalizeAutorNome(trim((string) preg_replace('/\s+/', ' ', $nome)));
		}, $nomes)));

		if ($nomesNormalizados === []) {
			return false;
		}

		$existentes = Autor::query()
			->get(['nome'])
			->map(fn (Autor $autor): string => $this->normalizeAutorNome((string) $autor->nome))
			->filter()
			->unique()
			->values()
			->all();

		foreach ($nomesNormalizados as $nome) {
			if (! in_array($nome, $existentes, true)) {
				return true;
			}
		}

		return false;
	}

	private function normalizeAutorNome(string $nome): string
	{
		return $this->normalizeEntityName($nome);
	}

	private function normalizeEntityName(string $nome): string
	{
		$normalized = mb_strtolower(Str::ascii($nome));
		$normalized = preg_replace('/[^a-z0-9\s]/', ' ', $normalized) ?? '';
		$normalized = preg_replace('/\s+/', ' ', $normalized) ?? '';

		return trim($normalized);
	}

	private function normalizePrice(float|string $value): float
	{
		return (float) str_replace(',', '.', (string) $value);
	}

	private function storeCapaImage(Request $request): ?string
	{
		if (! $request->hasFile('imagem_capa')) {
			return null;
		}

		/** @var UploadedFile $file */
		$file = $request->file('imagem_capa');

		return $file->store('capas', 'public');
	}

	private function storeAutorFoto(Request $request): ?string
	{
		if (! $request->hasFile('autor_foto')) {
			return null;
		}

		/** @var UploadedFile $file */
		$file = $request->file('autor_foto');

		return $file->store('autores', 'public');
	}

	private function storeEditoraLogo(Request $request): ?string
	{
		if (! $request->hasFile('editora_logotipo')) {
			return null;
		}

		/** @var UploadedFile $file */
		$file = $request->file('editora_logotipo');

		return $file->store('editoras', 'public');
	}

	private function deleteStoredCapaIfLocal(?string $imagemCapa): void
	{
		if (! $imagemCapa || str_starts_with($imagemCapa, 'http')) {
			return;
		}

		if (Storage::disk('public')->exists($imagemCapa)) {
			Storage::disk('public')->delete($imagemCapa);
		}
	}
}
