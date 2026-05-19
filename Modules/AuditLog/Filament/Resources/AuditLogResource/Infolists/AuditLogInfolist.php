<?php

namespace Modules\AuditLog\Filament\Resources\AuditLogResource\Infolists;

use Filament\Infolists;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Audit Event')
                ->schema([
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Date')
                        ->dateTime(),
                    Infolists\Components\TextEntry::make('event')
                        ->badge(),
                    Infolists\Components\TextEntry::make('auditable_type')
                        ->label('Model')
                        ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-'),
                    Infolists\Components\TextEntry::make('auditable_id')->label('Record ID'),
                    Infolists\Components\TextEntry::make('actor_type')
                        ->label('Actor Type')
                        ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-'),
                    Infolists\Components\TextEntry::make('actor_id')->label('Actor ID')->default('-'),
                    Infolists\Components\TextEntry::make('method')->default('-'),
                    Infolists\Components\TextEntry::make('ip_address')->default('-'),
                    Infolists\Components\TextEntry::make('url')->default('-')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('user_agent')->default('-')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Diff Viewer')
                ->schema([
                    Infolists\Components\TextEntry::make('diff_html')
                        ->label('Changed Fields')
                        ->html()
                        ->columnSpanFull(),
                ]),
            Section::make('Before')
                ->schema([
                    Infolists\Components\TextEntry::make('old_values')
                        ->label('Old Values')
                        ->formatStateUsing(
                            fn ($state) => $state
                                ? '<pre style="margin:0;white-space:pre-wrap;">'
                                    . e((string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                                    . '</pre>'
                                : '-'
                        )
                        ->html()
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
            Section::make('After')
                ->schema([
                    Infolists\Components\TextEntry::make('new_values')
                        ->label('New Values')
                        ->formatStateUsing(
                            fn ($state) => $state
                                ? '<pre style="margin:0;white-space:pre-wrap;">'
                                    . e((string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                                    . '</pre>'
                                : '-'
                        )
                        ->html()
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }
}
