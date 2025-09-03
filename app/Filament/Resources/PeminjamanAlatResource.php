<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanAlatResource\Pages;
use App\Models\PeminjamanAlat;
use App\Models\Peralatan;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action as TableAction;

class PeminjamanAlatResource extends \Filament\Resources\Resource
{
    protected static ?string $model = PeminjamanAlat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Aktifitas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Admin memilih peminjam lain
            Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Nama Peminjam')
                ->searchable()
                ->preload()
                ->getOptionLabelFromRecordUsing(fn($record) => trim(($record->pangkat ?? '') . ' ' . ($record->name ?? '')))
                ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->required(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->dehydrated(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false),

            Hidden::make('user_id')
                ->default(fn() => auth()->id())
                ->visible(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false))
                ->dehydrated(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)),


            Forms\Components\DatePicker::make('tanggal_pinjam')
                ->label('Tanggal Pinjam')
                ->required()
                ->default(now()->toDateString())
                ->native(false)
                ->closeOnDateSelection(),

            // Approval (admin only)
            Select::make('approval')
                ->label('Status Approval')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->required()
                ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)
                ->helperText('Hanya admin yang dapat mengubah status approval.')
                ->dehydrated(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false),

            Hidden::make('approval')
                ->default('pending')
                ->visible(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false))
                ->dehydrated(fn() => !(auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false)),


            Repeater::make('items')
                ->relationship('items')
                ->label('Daftar Alat yang Dipinjam')
                ->minItems(1)
                ->collapsed(false)
                ->columns(2)
                ->schema([
                    Select::make('peralatan_id')
                        ->label('Alat')
                        ->relationship('peralatan', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                    Placeholder::make('stok_info')
                        ->label('Stok Tersedia')
                        ->content(function (Get $get) {
                            $id = $get('peralatan_id');
                            if (!$id) return 'Pilih alat untuk melihat stok.';
                            $p = Peralatan::find($id);
                            return $p ? "Tersedia: {$p->stock} unit" : 'Alat tidak ditemukan.';
                        }),

                    TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->rule(function (Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $id = $get('peralatan_id');
                                if (!$id || !$value) return;
                                $p = Peralatan::find($id);
                                if (!$p) return $fail('Peralatan tidak ditemukan.');
                                if ((int)$value > $p->stock) $fail("Jumlah melebihi stok tersedia ({$p->stock}).");
                            };
                        }),
                ])
                // Kunci items jika sudah approved (hindari mismatch stok)
                ->disabled(fn($record) => filled($record) && $record->approval === 'approved')
                ->createItemButtonLabel('Tambah Alat')
                ->helperText('Pilih beberapa alat dan tentukan jumlah per alat.'),

            Forms\Components\Textarea::make('keterangan')
                ->label('Keterangan')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Peminjam')
                    ->formatStateUsing(function ($state, $record) {
                        $pangkat = $record->user->pangkat ?? '';
                        $nama = $record->user->name ?? '-';
                        return trim($pangkat . ' ' . $nama);
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('equipment_names')
                    ->label('Alat')
                    ->wrap(),

                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jenis Alat')
                    ->counts('items')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Qty')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('approval')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval')
                    ->label('Filter Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn($record) => auth()->check() && (
                        auth()->user()->hasAnyRole(['admin', 'super_admin']) || $record->user_id === auth()->id()
                    )),

                // ⬇️⬇️ Tambahkan aksi download invoice PDF
                // ✅ pakai TableAction (bukan Filament\Actions\Action)
                TableAction::make('invoice')
                    ->label('Invoice')
                    ->tooltip('Download invoice PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('peminjaman-alats.invoice', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => auth()->check() && (
                        auth()->user()->hasAnyRole(['admin', 'super_admin']) || $record->user_id === auth()->id()
                    )),

                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->check() && (
                        auth()->user()->hasAnyRole(['admin', 'super_admin']) || $record->user_id === auth()->id()
                    )),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => auth()->check() && (
                        auth()->user()->hasAnyRole(['admin', 'super_admin']) || $record->user_id === auth()->id()
                    )),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->hasAnyRole(['admin', 'super_admin']) ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPeminjamanAlats::route('/'),
            'create' => Pages\CreatePeminjamanAlat::route('/create'),
            'edit'   => Pages\EditPeminjamanAlat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['items.peralatan', 'user']); // eager load untuk tabel

        $user = auth()->user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }
}
