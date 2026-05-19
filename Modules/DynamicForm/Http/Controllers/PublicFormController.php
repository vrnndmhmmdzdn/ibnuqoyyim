<?php

namespace Modules\DynamicForm\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DynamicForm\Models\Form;
use Modules\DynamicForm\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    public function show(string $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if form is expired
        if ($form->is_expired) {
            abort(410, 'This form has expired.');
        }

        // Check if login required
        if ($form->require_login && !auth()->check()) {
            // Redirect to Filament admin login
            return redirect()->route('filament.admin.auth.login')->with('error', 'Please login to access this form.');
        }

        return view('dynamic-form::public.form', [
            'form' => $form,
        ]);
    }

    public function submit(Request $request, string $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if form is expired
        if ($form->is_expired) {
            return response()->json(['error' => 'This form has expired.'], 410);
        }

        // Check if login required
        if ($form->require_login && !auth()->check()) {
            return response()->json(['error' => 'Please login to access this form.'], 401);
        }

        // Check if multiple submissions allowed
        if (!$form->allow_multiple_submissions) {
            $existingSubmission = FormSubmission::where('form_id', $form->id)
                ->where(function ($query) {
                    if (auth()->check()) {
                        $query->where('user_id', auth()->id());
                    } else {
                        $query->where('ip_address', $request->ip());
                    }
                })
                ->first();

            if ($existingSubmission) {
                return response()->json(['error' => 'You have already submitted this form.'], 422);
            }
        }

        // Validate and store submission
        $validationRules = [
            'data' => 'required|array',
        ];

        // If collect_email is enabled, validate email
        if ($form->collect_email) {
            $validationRules['data.email'] = 'required|email';
        }

        $request->validate($validationRules);

        // Extract email and name from submission data
        $submissionData = $request->input('data', []);
        if (!is_array($submissionData)) {
            $submissionData = [];
        }
        $responderEmail = null;
        $responderName = null;

        // Get email - check in data first, then from authenticated user
        if ($form->collect_email) {
            $responderEmail = $submissionData['email'] ?? null;
            
            // If email not in data and user is logged in, use user's email
            if (!$responderEmail && auth()->check() && auth()->user()->email) {
                $responderEmail = auth()->user()->email;
            }
        }

        // Get name - check in data first, then from authenticated user
        $responderName = $submissionData['name'] ?? null;
        if (!$responderName && auth()->check() && auth()->user()->name) {
            $responderName = auth()->user()->name;
        }

        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'submission_token' => Str::random(32),
            'data' => $submissionData,
            'responder_email' => $responderEmail,
            'responder_name' => $responderName,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'schema_snapshot' => $form->schema,
                'schema_updated_at' => $form->updated_at?->toISOString(),
            ],
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Form submitted successfully!',
            'submission_id' => $submission->id,
        ]);
    }
}
