<?php

return [
    'password_confirm' => [
        'heading' => 'Confirm password',
        'description' => 'Please confirm your password to complete this action.',
        'current_password' => 'Current password',
    ],
    'two_factor' => [
        'heading' => 'Two Factor Challenge',
        'description' => 'Please confirm access to your account by entering the code provided by your authenticator application.',
        'code_placeholder' => 'XXX-XXX',
        'recovery' => [
            'heading' => 'Two Factor Challenge',
            'description' => 'Please confirm access to your account by entering one of your emergency recovery codes.',
        ],
        'recovery_code_placeholder' => 'abcdef-98765',
        'recovery_code_text' => 'Lost device?',
        'recovery_code_link' => 'Use a recovery code',
        'back_to_login_link' => 'Back to login',
    ],
    'profile' => [
        'account' => 'Account',
        'profile' => 'Profile',
        'my_profile' => 'My Profile',
        'subheading' => 'Manage your user profile here.',
        'personal_info' => [
            'heading' => 'Personal Information',
            'subheading' => 'Manage your personal information.',
            'submit' => [
                'label' => 'Update',
            ],
            'notify' => 'Profile updated successfully!',
        ],
        'password' => [
            'heading' => 'Password',
            'subheading' => 'Must be at least 8 characters long.',
            'submit' => [
                'label' => 'Update',
            ],
            'notify' => 'Password updated successfully!',
        ],
        '2fa' => [
            'title' => 'Two Factor Authentication',
            'description' => 'Manage 2 factor authentication for your account (recommended).',
            'actions' => [
                'enable' => 'Enable',
                'regenerate_codes' => 'Regenerate Recovery Codes',
                'disable' => 'Disable',
                'confirm_finish' => 'Confirm & finish',
                'cancel_setup' => 'Cancel setup',
            ],
            'setup_key' => 'Setup key',
            'must_enable' => 'You must enable Two Factor Authentication to use this application.',
            'not_enabled' => [
                'title' => 'You have not enabled two factor authentication.',
                'description' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You can use Authenticator apps on your smart phone such as Google Authenticator, Microsoft Authenticator etc to facilitate this',
            ],
            'finish_enabling' => [
                'title' => 'Finish enabling two factor authentication.',
                'description' => "To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.",
            ],
            'enabled' => [
                'notify' => 'Two factor authentication enabled.',
                'title' => 'You have enabled two factor authentication!',
                'description' => 'Two factor authentication is now enabled. This helps make your account more secure.',
                'store_codes' => 'These codes can be used to recover access to your account if your device is lost. Warning! These codes will only be shown once.',
            ],
            'disabling' => [
                'notify' => 'Two factor authentication has been disabled.',
            ],
            'regenerate_codes' => [
                'notify' => 'New recovery codes have been generated.',
            ],
            'confirmation' => [
                'success_notification' => 'Code verified. Two factor authentication enabled.',
                'invalid_code' => 'The code you have entered is invalid.',
            ],
        ],
        'sanctum' => [
            'title' => 'API Tokens',
            'description' => 'Manage API tokens that allow third-party services to access this application on your behalf.',
            'create' => [
                'notify' => 'Token created successfully!',
                'message' => 'Your token is only shown once upon creation. If you lose your token, you will need to delete it and create a new one.',
                'submit' => [
                    'label' => 'Create',
                ],
            ],
            'update' => [
                'notify' => 'Token updated successfully!',
                'submit' => [
                    'label' => 'Update',
                ],
            ],
            'copied' => [
                'label' => 'I have copied my token',
            ],
        ],
        'browser_sessions' => [
            'heading' => 'Browser Sessions',
            'subheading' => 'Manage your active sessions.',
            'label' => 'Browser Sessions',
            'content' => 'If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.',
            'device' => 'This device',
            'last_active' => 'Last active',
            'logout_other_sessions' => 'Log Out Other Browser Sessions',
            'logout_heading' => 'Log Out Other Browser Sessions',
            'logout_description' => 'Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.',
            'logout_action' => 'Log Out Other Browser Sessions',
            'incorrect_password' => 'The password you entered was incorrect. Please try again.',
            'logout_success' => 'All other browser sessions have been logged out successfully.',
        ],
    ],
    'clipboard' => [
        'link' => 'Copy to clipboard',
        'tooltip' => 'Copied!',
    ],
    'fields' => [
        'avatar' => 'Avatar',
        'email' => 'Email',
        'pangkat' => 'Pangkat',
        'nrp' => 'NRP',
        'jabatan' => 'Jabatan',
        'login' => 'Login',
        'name' => 'Name',
        'password' => 'Password',
        'password_confirm' => 'Password confirm',
        'new_password' => 'New password',
        'new_password_confirmation' => 'Confirm password',
        'token_name' => 'Token name',
        'token_expiry' => 'Token expiry',
        'abilities' => 'Abilities',
        '2fa_code' => 'Code',
        '2fa_recovery_code' => 'Recovery Code',
        'created' => 'Created',
        'expires' => 'Expires',
    ],
    'or' => 'Or',
    'cancel' => 'Cancel',
];
