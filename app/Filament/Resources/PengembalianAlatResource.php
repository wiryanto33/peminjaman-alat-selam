<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianAlatResource\Pages;
use App\Models\PengembalianAlat;
use App\Models\PeminjamanItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengembalianAlatResource extends \Filament\Resources\Resource
{
    protected static ?string $model = PengembalianAlat::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationGroup = 'Aktifitas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Admin boleh pilih user; user biasa otomatis dirinya sendiri
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Pengembali')
                ->searchable()
                ->preload()
                // before: fn($r) =>
                ->getOptionLabelFromRecordUsing(fn($record) => trim(($record->pangkat ?? '') . ' ' . ($record->name ?? '')))
                ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->required(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->dehydrated(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->live()
                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                    $set('peminjaman_item_id', null);
                    $set('peminjaman_id', null);
                }),


            Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id())
                ->visible(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false))
                ->dehydrated(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)),

            // Pilih item yang dikembalikan
            Forms\Components\Select::make('peminjaman_item_id')
                ->label('Alat yang Dikembalikan')
                ->options(function (Get $get) {
                    $selectedUserId = $get('user_id') ?: auth()->id();

                    return \App\Models\PeminjamanItem::query()
                        ->with(['peralatan:id,name', 'peminjaman:id,user_id,approval'])
                        ->whereHas('peminjaman', function ($q) use ($selectedUserId) {
                            $q->where('user_id', $selectedUserId)
                                ->where('approval', 'approved');
                        })
                        ->get()
                        ->mapWithKeys(fn($item) => [
                            $item->id => ($item->peralatan?->name ?? 'Alat') . " — Dipinjam {$item->jumlah} unit",
                        ]);
                })
                ->searchable()
                ->preload()
                ->required()
                ->live()
                // set otomatis peminjaman_id dari item terpilih
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state) {
                        $item = \App\Models\PeminjamanItem::with('peminjaman')->find($state);
                        $set('peminjaman_id', $item?->peminjaman?->id);
                    } else {
                        $set('peminjaman_id', null);
                    }
                }),

            // Diset otomatis dari item terpilih (tak perlu tampil)
            Forms\Components\Hidden::make('peminjaman_id')
                ->dehydrated(true),

            Forms\Components\DatePicker::make('tanggal_kembali')
                ->label('Tanggal Pengembalian')
                ->default(now()->toDateString())
                ->native(false)
                ->closeOnDateSelection(),

            Forms\Components\TextInput::make('jumlah_dikembalikan')
                ->label('Jumlah Dikembalikan')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->required()
                ->rule(function (Get $get, \Livewire\Component $livewire) {
                    return function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                        $itemId = $get('peminjaman_item_id');
                        if (!$itemId || !$value) return;

                        $item = \App\Models\PeminjamanItem::with('peralatan')->find($itemId);
                        if (!$item) return $fail('Item peminjaman tidak ditemukan.');

                        // EXCLUDE record yang sedang diedit
                        $currentId = $livewire->record->id ?? null;

                        // Hanya hitung pending + approved
                        $sudah = \App\Models\PengembalianAlat::query()
                            ->where('peminjaman_item_id', $itemId)
                            ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                            ->whereIn('approval', ['pending', 'approved'])
                            ->sum('jumlah_dikembalikan');

                        $max = max(0, $item->jumlah - $sudah);

                        if ((int) $value > $max) {
                            $alat = $item->peralatan?->name ?? 'Alat';
                            $fail("Melebihi sisa yang boleh dikembalikan untuk {$alat}. Sisa: {$max}.");
                        }
                    };
                }),


            Forms\Components\Select::make('status_kondisi')
                ->label('Kondisi')
                ->options([
                    'Baik'   => 'Baik',
                    'Rusak'  => 'Rusak',
                    'Hilang' => 'Hilang',
                ])
                ->native(false),

            // Approval: admin saja yang bisa ubah
            Forms\Components\Select::make('approval')
                ->label('Status Approval')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->dehydrated(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->helperText('Hanya admin yang dapat mengubah status approval.'),

            // User biasa: approval hidden = pending
            Forms\Components\Hidden::make('approval')
                ->default('pending')
                ->visible(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false))
                ->dehydrated(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)),

            Forms\Components\Textarea::make('keterangan')
                ->label('Keterangan')
                ->columnSpanFull(),
        ])->columns(2); // ⬅️ TUTUP di sini. Tidak ada statePath/afterStateUpdated di level Form.
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengembali')
                    ->formatStateUsing(function ($state, $record) {
                        $pangkat = $record->user->pangkat ?? '';
                        return trim($pangkat . ' ' . ($state ?? '-'));
                    })
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('peminjamanItem.peralatan.name')
                    ->label('Alat')
                    ->searchable(),

                Tables\Columns\TextColumn::make('peminjamanItem.jumlah')
                    ->label('Qty Dipinjam')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_dikembalikan')
                    ->label('Qty Dikembalikan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->date()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('approval')
                    ->label('Approval')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPengembalianAlats::route('/'),
            'create' => Pages\CreatePengembalianAlat::route('/create'),
            'edit'   => Pages\EditPengembalianAlat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()
            ->with(['user', 'peminjamanItem.peralatan', 'peminjaman']);

        $u = auth()->user();
        if (! $u) {
            return $q->whereRaw('1=0');
        }

        // Admin melihat semua; user hanya lihat pengembalian miliknya
        if ($u->hasAnyRole(['super_admin', 'admin'])) {
            return $q;
        }

        return $q->where('user_id', $u->id);
    }
}
