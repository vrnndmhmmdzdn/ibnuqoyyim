<style>
    /* Custom styles untuk memastikan tampilan yang baik */
.calendar-nav-button {
    @apply px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-200;
}

.calendar-selector {
    @apply px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100;
}

.calendar-container {
    @apply bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm;
}

.detail-section {
    @apply mt-8 pt-6 border-t border-gray-200 dark:border-gray-700;
}

.detail-card {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600;
}

.detail-card-blue {
    @apply p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700;
}

.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e5e7eb;
    z-index: 0;
}

.dark .step-indicator::before {
    background: #374151;
}

.step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #9ca3af;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.dark .step-circle {
    background: #374151;
    color: #6b7280;
}

.step.active .step-circle {
    background: #6b7280;
    color: white;
    box-shadow: 0 4px 6px rgba(107, 114, 128, 0.3);
}

.step.completed .step-circle {
    background: #10b981;
    color: white;
}

.step-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.dark .step-label {
    color: #9ca3af;
}

.step.active .step-label {
    color: #1f2937;
    font-weight: 600;
}

.dark .step.active .step-label {
    color: #f9fafb;
}

.duration-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.dark .duration-card {
    border-color: #374151;
    background: #1f2937;
}

.duration-card:hover {
    border-color: #6b7280;
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.2);
}

.duration-card.selected {
    border-color: #6b7280;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.dark .duration-card.selected {
    background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
}

.time-slot-btn {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    font-weight: 500;
}

.dark .time-slot-btn {
    border-color: #374151;
    background: #1f2937;
    color: #f9fafb;
}

.time-slot-btn:hover {
    border-color: #6b7280;
    background: #f9fafb;
}

.dark .time-slot-btn:hover {
    border-color: #6b7280;
    background: #374151;
}

.time-slot-btn.selected {
    border-color: #6b7280;
    background: #6b7280;
    color: white;
}

.court-section {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    background: white;
}

.dark .court-section {
    border-color: #374151;
    background: #1f2937;
}

</style>