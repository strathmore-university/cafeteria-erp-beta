<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\DepartmentResource\Pages\CreateDepartment;
use App\Filament\Clusters\Core\Resources\DepartmentResource\Pages\EditDepartment;
use App\Filament\Clusters\Core\Resources\DepartmentResource\Pages\ListDepartments;
use App\Filament\Clusters\Core\Resources\DepartmentResource\Pages\ViewDepartment;
use App\Models\Core\Department;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $model = Department::class;
    protected static ?string $cluster = Core::class;
    protected static ?string $slug = 'departments';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->string()->maxValue(255),
            TextInput::make('code')->required()->string()->maxValue(255),
            Select::make('head_user_id')->required()
                ->relationship('head', 'name')->searchable()->preload(),
            Select::make('parent_department_id')->required()
                ->relationship('parent', 'name')->searchable()->preload(),
            TextInput::make('chart_code')->required()->string()
                ->maxValue(255),
            TextInput::make('account_number')->required()->string()
                ->maxValue(255),
            TextInput::make('object_code')->required()->string()
                ->maxValue(255),
            TextInput::make('revenue_account_number')->required()
                ->string()->maxValue(255),
            TextInput::make('revenue_object_code')->required()
                ->string()->maxValue(255),
            TextInput::make('sync_id')->nullable()
                ->string()->maxValue(255),
            Toggle::make('is_active')->required()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('chart_code')->searchable()->sortable(),
            TextColumn::make('object_code')->searchable()->sortable(),
            TextColumn::make('account_number')->searchable()->sortable(),
            IconColumn::make('is_active')->boolean(),
        ])->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit' => EditDepartment::route('/{record}/edit'),
            'view' => ViewDepartment::route('/{record}/view'),
        ];
    }
}
