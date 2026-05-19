<?php

namespace Modules\DynamicForm\Filament\Resources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.title')
                    ->label('Form')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('responder_name')
                    ->label('Responder')
                    ->searchable(),
                TextColumn::make('responder_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                // Exclude 'data' column from list query to prevent memory issues
                // The 'data' column can be very large (614KB+) due to file uploads
                // We only need it when viewing individual submission details
                $query->select([
                    'dynamic_form_submissions.id',
                    'dynamic_form_submissions.form_id',
                    'dynamic_form_submissions.submission_token',
                    'dynamic_form_submissions.responder_email',
                    'dynamic_form_submissions.responder_name',
                    'dynamic_form_submissions.user_id',
                    'dynamic_form_submissions.ip_address',
                    'dynamic_form_submissions.user_agent',
                    'dynamic_form_submissions.metadata',
                    'dynamic_form_submissions.submitted_at',
                    'dynamic_form_submissions.created_at',
                    'dynamic_form_submissions.updated_at',
                ]);
                
                // Use composite index for efficient sorting
                // This prevents "Out of sort memory" error
                return $query->reorder()
                    ->orderBy('submitted_at', 'desc')
                    ->orderBy('id', 'asc');
            });
    }
}
