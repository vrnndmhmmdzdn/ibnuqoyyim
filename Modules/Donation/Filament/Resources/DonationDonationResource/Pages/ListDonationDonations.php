<?php

namespace Modules\Donation\Filament\Resources\DonationDonationResource\Pages;

use Modules\Donation\Filament\Resources\DonationDonationResource;
use Modules\Donation\Models\DonationDonation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDonationDonations extends ListRecords
{
    protected static string $resource = DonationDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label('Export CSV')
                ->action(function () {
                    return response()->streamDownload(function () {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, [
                            'order_id',
                            'campaign',
                            'donor_name',
                            'donor_email',
                            'amount',
                            'status',
                            'created_at',
                            'paid_at',
                        ]);

                        DonationDonation::query()
                            ->with('campaign')
                            ->orderByDesc('created_at')
                            ->chunk(500, function ($rows) use ($handle) {
                                foreach ($rows as $row) {
                                    fputcsv($handle, [
                                        $row->provider_order_id,
                                        $row->campaign?->title,
                                        $row->donor_name,
                                        $row->donor_email,
                                        $row->amount,
                                        $row->status,
                                        optional($row->created_at)->toDateTimeString(),
                                        optional($row->paid_at)->toDateTimeString(),
                                    ]);
                                }
                            });

                        fclose($handle);
                    }, 'donations.csv');
                }),
        ];
    }
}
