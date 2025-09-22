import { ref } from 'vue';
import { mergePolicies } from '../utils/cspUtils';

export function useSecurityHeaders() {
    const settings = ref({
        xFrameOptions: { enabled: true, value: null },
        xContentTypeOptions: { enabled: true },
        strictTransportSecurity: { enabled: true, maxAge: 1, includeSubDomains: false, preload: false },
        referrerPolicy: { enabled: true, value: 'no-referrer-when-downgrade' },
        contentSecurityPolicy: {
            enabled: false,
            policy: "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';",
            reportOnly: false,
            reportingEnabled: false,
            reportingPlatform: {
                reportingPlatformName: null,
                reportingUrl: '',
                reportingApiKey: '',
                reportingOrganization: '',
                reportingProject: '',
            }
        },
        permissionsPolicy: { enabled: false, policy: "geolocation=(), microphone=()" }
    });

    const saveSettings = () => {
        Statamic.$axios.post(cp_url('security_headers'), settings.value)
            .then(() => {
                Statamic.$toast.success('Settings saved successfully!');
            })
            .catch(error => {
                Statamic.$toast.error('An error occurred while saving.');
                console.error('Error saving settings:', error);
            });
    };

    const generatePolicies = () => {
        Statamic.$axios.post(cp_url('security_headers/generate_csp'))
            .then(response => {
                if (response.status === 200 && !response.data.original) {
                    settings.value.contentSecurityPolicy.policy = mergePolicies(
                        settings.value.contentSecurityPolicy.policy,
                        response.data
                    );
                    Statamic.$toast.success('Content Security Policies generated and merged!');
                } else {
                    throw response;
                }
            })
            .catch(error => {
                let errorMessage = 'An unknown error occurred.';
                if (error.response) {
                    errorMessage = error.response.data.message || 'The server returned an error.';
                } else if (error.data) {
                    errorMessage = error.data.original?.message || 'The application returned an error.';
                } else {
                    errorMessage = error.message || errorMessage;
                }
                Statamic.$toast.error(errorMessage);
                console.error('Error generating policies:', error);
            });
    };

    return {
        settings,
        saveSettings,
        generatePolicies
    };
}
