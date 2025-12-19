@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="p-8 bg-gradient-to-br bg-white min-h-screen">
    <div class="max-w-4xl mx-auto">

        <div class="bg-white/90 backdrop-blur-sm shadow-md rounded-lg border border-slate-200">
            <!-- Header -->
            <div class="border-b border-slate-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-slate-700">Driver Profile</h2>
                <button id="editBtn" type="button" class="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition shadow-sm">
                    Edit Profile
                </button>
            </div>

            <!-- View Mode -->
            <div id="viewMode" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">First Name</label>
                        <p class="text-slate-700 font-medium" id="view-fname">{{ $driver->fname }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Middle Name</label>
                        <p class="text-slate-700 font-medium" id="view-mname">{{ $driver->mname ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Last Name</label>
                        <p class="text-slate-700 font-medium" id="view-lname">{{ $driver->lname }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Username</label>
                        <p class="text-slate-700 font-medium" id="view-username">{{ $driver->username }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Contact Number</label>
                        <p class="text-slate-700 font-medium" id="view-contact">{{ $driver->contact_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">License Number</label>
                        <p class="text-slate-700 font-medium" id="view-license">{{ $driver->license_no }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border
                            {{ $driver->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                            {{ ucfirst($driver->status) }}
                        </span>
                    </div>

                </div>
            </div>

            <!-- Edit Mode (Hidden by default) -->
            <div id="editMode" class="p-6 hidden">
                <form id="profileForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">First Name <span class="text-red-400">*</span></label>
                            <input type="text" name="fname" id="edit-fname" value="{{ old('fname', $driver->fname) }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Middle Name</label>
                            <input type="text" name="mname" id="edit-mname" value="{{ old('mname', $driver->mname) }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Last Name <span class="text-red-400">*</span></label>
                            <input type="text" name="lname" id="edit-lname" value="{{ old('lname', $driver->lname) }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Username <span class="text-red-400">*</span></label>
                            <input type="text" name="username" id="edit-username" value="{{ old('username', $driver->username) }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Contact Number <span class="text-red-400">*</span></label>
                            <input type="text" name="contact_number" id="edit-contact" value="{{ old('contact_number', $driver->contact_number) }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">License Number <span class="text-red-400">*</span></label>
                            <input type="text" name="license_no" id="edit-license" value="{{ old('license_no', $driver->license_no) }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white"
                                   required>
                        </div>

                        <!-- Change Password Section -->
                        <div class="md:col-span-2 border-t border-slate-200 pt-6 mt-4">
                            <h3 class="text-lg font-medium text-slate-700 mb-4">Change Password (Optional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1">Current Password</label>
                                    <input type="password" name="current_password" id="current_password"
                                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white">
                                </div>

                                <div></div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1">New Password</label>
                                    <input type="password" name="password" id="new_password"
                                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white">
                                    <p class="text-xs text-slate-500 mt-1">Minimum 6 characters. Leave blank to keep current password</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white">
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-200">
                        <button id="cancelBtn" type="button" 
                                class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition border border-slate-200">
                            Cancel
                        </button>
                        <button type="submit" id="saveBtn"
                                class="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition shadow-sm">
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>

            <!-- Footer -->
            <div class="border-t border-slate-200 px-6 py-4 flex justify-end">
                <a href="{{ route('driver.dashboard') }}" 
                   class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition border border-slate-200">
                   Back to Dashboard
                </a>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');
    const profileForm = document.getElementById('profileForm');

    // Check for success/error messages in URL
    const urlParams = new URLSearchParams(window.location.search);
    const successMsg = urlParams.get('success');
    const errorMsg = urlParams.get('error');
    
    if (successMsg) {
        showNotification(successMsg, 'success');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (errorMsg) {
        showNotification(errorMsg, 'error');
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Switch to edit mode
    editBtn.addEventListener('click', function() {
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
        editBtn.classList.add('hidden');
    });

    // Cancel editing
    cancelBtn.addEventListener('click', function() {
        editMode.classList.add('hidden');
        viewMode.classList.remove('hidden');
        editBtn.classList.remove('hidden');
        
        // Reset form
        profileForm.reset();
        
        // Clear password fields
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('password_confirmation').value = '';
    });

    // Submit form
    profileForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validate password fields if current password is provided
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (currentPassword && !newPassword) {
            showNotification('Please enter a new password', 'error');
            return;
        }

        if (newPassword && !currentPassword) {
            showNotification('Please enter your current password', 'error');
            return;
        }

        if (newPassword && newPassword !== confirmPassword) {
            showNotification('New passwords do not match', 'error');
            return;
        }

        if (newPassword && newPassword.length < 6) {
            showNotification('Password must be at least 6 characters', 'error');
            return;
        }

        // Disable submit button
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
            const formData = new FormData(profileForm);
            
            const response = await fetch('{{ route("driver.profile.update") }}', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const result = await response.json();

            if (result.success) {
                // Update view mode with new values
                document.getElementById('view-fname').textContent = formData.get('fname');
                document.getElementById('view-mname').textContent = formData.get('mname') || 'N/A';
                document.getElementById('view-lname').textContent = formData.get('lname');
                document.getElementById('view-username').textContent = formData.get('username');
                document.getElementById('view-contact').textContent = formData.get('contact_number');
                document.getElementById('view-license').textContent = formData.get('license_no');

                // Redirect with success message
                window.location.href = '{{ route("driver.profile") }}?success=' + encodeURIComponent(result.message);
            } else {
                showNotification(result.message || 'Error updating profile', 'error');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Changes';
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error updating profile', 'error');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
        }
    });
});

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-2xl text-white flex items-center space-x-3 transform transition-all duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    
    notification.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'}
        </svg>
        <span class="font-semibold">${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endsection