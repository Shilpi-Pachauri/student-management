<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Models\Sections;
use App\Models\Classes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Collection;
use App\Exports\StudentsExport;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = "Academic Management";

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')
                    ->live()
                    ->relationship(name: 'class', titleAttribute: 'name'),
                    
                Select::make('section_id')
                ->label('Sections')
                ->options(function (Get $get) {
                    $classId = $get('class_id');
                    if ($classId) {
                       return Sections::where('class_id', $classId)->pluck('name', 'id')->toArray();
                    }
                }),

                TextInput::make('name')
                ->autofocus()
                ->required(),
                TextInput::make('email')
                    ->unique()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('name')
               ->searchable()
               ->sortable(),
               TextColumn::make('email')
               ->searchable()
               ->sortable(),
               TextColumn::make('class.name')->badge()
               ->searchable(),
               TextColumn::make('section.name')->badge()
               ->searchable(),

            ])
            ->filters([
                Filter::make('class-filter')
                ->form([
                    Select::make('class_id')
                    ->label('Filter By Class')
                    ->placeholder('Select a Class')
                    ->options(
                        Classes::pluck('name' , 'id')->toArray(),
                    ),

                    Select::make('section_id')
                    ->label('Filter By Section')
                    ->placeholder('Select a Section')
                    ->options(function (Get $get) {
                        $classId = $get('class_id');
                        if ($classId) {
                            return Sections::where('class_id', $classId)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                    }),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['class_id'], function ($query) use ($data) {
                        return $query->where('class_id', $data['class_id']);
                    })->when($data['section_id'], function ($query) use ($data) {
                        return $query->where('section_id', $data['section_id']);
                    });
                }),
            ])
            ->actions([
                Action::make('downloadPdf')->url(function (Student $student) {
                    return route('student.pdf.generate', $student);
                }),
                Action::make('qrCode')
                ->url(function (Student $record) {
                   return static::getUrl('qrCode', ['record' => $record]);
                }),
                Tables\Actions\EditAction::make(),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                    ->label('Export Records')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Collection $records) {
                        return (new StudentsExport($records))
                            ->download('students.xlsx');
                    })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'qrCode' => Pages\GenerateQrCode::route('/{record}/qrcode'),
        ];
    }
}
