<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LivrosExport extends StringValueBinder implements FromCollection, ShouldAutoSize, WithHeadings, WithCustomValueBinder, WithColumnFormatting
{
    public function __construct(
        private readonly Collection $livros,
    ) {
    }

    public function collection(): Collection
    {
        return $this->livros->map(fn($livro) => [
            $livro->id,
            (string) $livro->isbn,
            $livro->nome,
            optional($livro->editora)->nome,
            $livro->bibliografia,
            $livro->imagem_capa,
            (float) $livro->preco,
            optional($livro->created_at)?->format('Y-m-d H:i:s'),
            optional($livro->updated_at)?->format('Y-m-d H:i:s'),
        ]);
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'B') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'ISBN',
            'Nome',
            'Editora',
            'Bibliografia',
            'Capa',
            'Preco',
            'Criado em',
            'Atualizado em',
        ];
    }
}