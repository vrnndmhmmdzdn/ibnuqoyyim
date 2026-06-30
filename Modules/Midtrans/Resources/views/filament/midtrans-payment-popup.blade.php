<div>
    <!-- Midtrans Snap Script -->
    <script type="text/javascript" src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

    <script type="text/javascript">
        // Function to update transaction status via API
        async function updateTransactionStatus(orderId) {
            try {
                const response = await fetch('/midtrans/check-status/' + orderId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                console.log('Status updated:', data);
                return data;
            } catch (error) {
                console.error('Failed to update status:', error);
                return null;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-trigger Midtrans Snap popup
            if (typeof window.snap !== 'undefined') {
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: async function(result) {
                        console.log('Payment success:', result);

                        // Update status from Midtrans API
                        await updateTransactionStatus('{{ $orderId }}');

                        // Show success notification
                        new FilamentNotification()
                            .title('Payment Successful')
                            .success()
                            .body('Payment has been completed successfully!')
                            .send();

                        // Reload page after 1 second
                        setTimeout(function() {
                            window.location.href = window.location.pathname;
                        }, 1000);
                    },
                    onPending: async function(result) {
                        console.log('Payment pending:', result);

                        // Update status from Midtrans API
                        await updateTransactionStatus('{{ $orderId }}');

                        new FilamentNotification()
                            .title('Payment Pending')
                            .warning()
                            .body('Your payment is being processed.')
                            .send();

                        // Reload page
                        setTimeout(function() {
                            window.location.href = window.location.pathname;
                        }, 1000);
                    },
                    onError: async function(result) {
                        console.log('Payment error:', result);

                        // Update status from Midtrans API
                        await updateTransactionStatus('{{ $orderId }}');

                        new FilamentNotification()
                            .title('Payment Error')
                            .danger()
                            .body('An error occurred during payment. Please try again.')
                            .send();
                    },
                    onClose: async function() {
                        console.log('Payment popup closed');

                        // Update status when popup is closed (in case payment was completed)
                        await updateTransactionStatus('{{ $orderId }}');

                        // Remove show_payment parameter from URL and reload
                        window.location.href = window.location.pathname;
                    }
                });
            } else {
                console.error('Midtrans Snap library not loaded');

                new FilamentNotification()
                    .title('Error')
                    .danger()
                    .body('Failed to load Midtrans payment gateway.')
                    .send();
            }
        });
    </script>

    <!-- Optional: Show a loading indicator while Snap is initializing -->
    <div id="midtrans-loading" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="text-align: center;">
            <svg class="animate-spin h-8 w-8 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p style="color: #666; font-size: 14px;">Loading Midtrans Payment...</p>
        </div>
    </div>

    <script>
        // Hide loading indicator when Snap is ready
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loading = document.getElementById('midtrans-loading');
                if (loading) {
                    loading.style.display = 'none';
                }
            }, 1000);
        });
    </script>
</div>
