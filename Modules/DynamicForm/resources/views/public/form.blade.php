<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Source+Serif+4:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/survey-core/survey-core.min.css" type="text/css" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f1ea;
            --ink: #151515;
            --muted: #6a6a6a;
            --card: #ffffff;
            --accent: #ef6f1b;
            --accent-dark: #c6550c;
            --ring: rgba(239, 111, 27, 0.18);
            --shadow: 0 20px 45px rgba(16, 12, 8, 0.12);
        }
        body {
            font-family: "Space Grotesk", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(1200px 600px at 10% 10%, #fff7ef 0%, rgba(255, 247, 239, 0) 60%),
                        radial-gradient(900px 500px at 90% 0%, #fde9d3 0%, rgba(253, 233, 211, 0) 65%),
                        var(--bg);
            color: var(--ink);
            margin: 0;
            padding: 32px 16px 48px;
        }
        .page-shell {
            max-width: 980px;
            margin: 0 auto;
            position: relative;
        }
        .form-container {
            background: var(--card);
            padding: 36px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(21, 21, 21, 0.06);
            backdrop-filter: blur(8px);
        }
        .form-header {
            margin-bottom: 30px;
        }
        .form-header h1 {
            margin: 0 0 10px 0;
            font-size: clamp(1.6rem, 1.2rem + 1.8vw, 2.4rem);
            letter-spacing: -0.02em;
        }
        .form-header p {
            color: var(--muted);
            margin: 0;
            font-family: "Source Serif 4", "Times New Roman", serif;
            font-size: 1.02rem;
        }
        .form-meta {
            display: inline-flex;
            gap: 10px;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(239, 111, 27, 0.08);
            color: var(--accent-dark);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
        }
        #surveyContainer {
            margin-top: 20px;
        }
        .thank-you-message {
            text-align: center;
            padding: 40px;
        }
        .thank-you-message h2 {
            color: var(--accent);
            margin-bottom: 10px;
        }
        .page-footer {
            text-align: center;
            color: var(--muted);
            margin-top: 24px;
            font-size: 0.9rem;
        }
        .page-footer strong {
            color: var(--ink);
        }
        .survey-container {
            border-radius: 16px;
            border: 1px dashed rgba(21, 21, 21, 0.08);
            padding: 12px 16px;
            background: #fffdfa;
        }
        .survey-question__title {
            font-weight: 600;
        }
        .sv-root-modern .sv-btn {
            background: var(--accent);
            border-color: var(--accent);
            box-shadow: 0 10px 20px rgba(239, 111, 27, 0.2);
        }
        .sv-root-modern .sv-btn:hover {
            background: var(--accent-dark);
            border-color: var(--accent-dark);
        }
        @media (max-width: 768px) {
            body {
                padding: 24px 12px 36px;
            }
            .form-container {
                padding: 24px;
            }
            .survey-container {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="form-container">
            <div class="form-header">
                <div class="form-meta">Dewafilament Dynamic Form</div>
            <h1>{{ $form->title }}</h1>
            @if($form->description)
                <p>{{ $form->description }}</p>
            @endif
            </div>

            <div class="survey-container">
                <div id="surveyContainer"></div>
                <div id="thankYouMessage" class="thank-you-message" style="display: none;">
                    <h2>Thank You!</h2>
                    <p>Your response has been recorded successfully.</p>
                </div>
            </div>
        </div>
        <div class="page-footer">
            
            <div>
                To view response results, visit
                <a href="{{ url('/admin/forms') }}" target="_blank" rel="noopener noreferrer">
                    {{ url('/admin/forms') }}
                </a>
            </div>
            by <strong>dewafilament</strong> &amp; <strong>survey js</strong>
        </div>
    </div>

    <script type="text/javascript" src="https://unpkg.com/survey-core/survey.core.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/survey-js-ui/survey-js-ui.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            try {
                const surveyJson = @json($form->schema ?? []);
                
                console.log('Survey JSON:', surveyJson);
                
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

                // If collect_email is enabled, add email field if not exists
                @if($form->collect_email)
                const hasEmailField = surveyJson.pages?.some(page => 
                    page.elements?.some(element => 
                        element.name === 'email' || (element.type === 'text' && element.inputType === 'email')
                    )
                );

                if (!hasEmailField) {
                    // Add email field to first page if it doesn't exist
                    if (surveyJson.pages && surveyJson.pages.length > 0) {
                        if (!surveyJson.pages[0].elements) {
                            surveyJson.pages[0].elements = [];
                        }
                        surveyJson.pages[0].elements.unshift({
                            type: 'text',
                            name: 'email',
                            title: 'Email Address',
                            inputType: 'email',
                            isRequired: true,
                            validators: [{
                                type: 'email'
                            }]
                        });
                    }
                }
                @endif

                // Use Survey.Model as per official documentation
                const survey = new Survey.Model(surveyJson);

                survey.onComplete.add(function (sender) {
                    // Submit data to server
                    fetch('{{ route("dynamic-form.submit", $form->slug) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            data: sender.data
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hide survey, show thank you message
                            document.getElementById('surveyContainer').style.display = 'none';
                            document.getElementById('thankYouMessage').style.display = 'block';
                            
                            // Redirect if configured
                            @if($form->settings && isset($form->settings['redirect_url']))
                                setTimeout(() => {
                                    window.location.href = '{{ $form->settings["redirect_url"] }}';
                                }, 2000);
                            @endif
                        } else {
                            alert('Error: ' + (data.message || 'Failed to submit form'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while submitting the form. Please try again.');
                    });
                });

                // Render survey as per official documentation
                survey.render(document.getElementById("surveyContainer"));
            } catch (error) {
                console.error('Error initializing survey:', error);
                document.getElementById('surveyContainer').innerHTML = '<p style="color: red;">Error loading form: ' + error.message + '</p>';
            }
        });
    </script>
</body>
</html>
