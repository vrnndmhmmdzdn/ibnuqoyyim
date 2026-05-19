<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">{{ $this->record->form->title }}</h2>
            @if($this->record->form->description)
                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $this->record->form->description }}</p>
            @endif

            <div class="mt-4">
                <div id="surveyContainer"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-md font-semibold mb-4">Submission Details</h3>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted At</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->record->submitted_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</dd>
                </div>
                @if($this->record->responder_name)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Responder Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->record->responder_name }}</dd>
                </div>
                @endif
                @if($this->record->responder_email)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Responder Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->record->responder_email }}</dd>
                </div>
                @endif
                @if($this->record->ip_address)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->record->ip_address }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <link href="https://unpkg.com/survey-core/survey-core.min.css" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/survey-core/survey.core.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/survey-js-ui/survey-js-ui.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            try {
                const surveyJson = @json($this->record->form->schema ?? []);
                const submissionData = @json($this->record->data ?? []);

                console.log('Survey JSON:', surveyJson);
                console.log('Submission Data:', submissionData);

                if (!surveyJson || !surveyJson.pages || surveyJson.pages.length === 0) {
                    console.error('Invalid survey schema');
                    document.getElementById('surveyContainer').innerHTML = '<p style="color: red;">Form schema is empty or invalid.</p>';
                    return;
                }

                // Check if Survey is loaded
                if (typeof Survey === 'undefined') {
                    console.error('SurveyJS library not loaded');
                    document.getElementById('surveyContainer').innerHTML = '<p style="color: red;">SurveyJS library failed to load. Please refresh the page.</p>';
                    return;
                }

                // Create survey model
                const survey = new Survey.Model(surveyJson);

                // Set mode to display (read-only)
                survey.mode = 'display';

                // Load submission data
                survey.data = submissionData;

                // Render survey
                survey.render(document.getElementById("surveyContainer"));
            } catch (error) {
                console.error('Error initializing survey:', error);
                document.getElementById('surveyContainer').innerHTML = '<p style="color: red;">Error loading form: ' + error.message + '</p>';
            }
        });
    </script>
</x-filament-panels::page>

