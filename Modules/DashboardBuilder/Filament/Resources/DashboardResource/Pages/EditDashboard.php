<?php

namespace Modules\DashboardBuilder\Filament\Resources\DashboardResource\Pages;

class EditDashboard extends ManageDashboardLayout
{
    public function getTitle(): string
    {
        return $this->record?->name ?: 'Dashboard Builder';
    }
}
