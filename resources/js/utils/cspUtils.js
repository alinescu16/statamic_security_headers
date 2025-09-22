/**
 * Merges new Content Security Policies with an existing policy string.
 *
 * @param {string} existingPolicy - The current CSP string.
 * @param {Array} newPolicies - An array of new policy objects to merge.
 * @returns {string} The merged CSP string.
 */
export function mergePolicies(existingPolicy, newPolicies) {
    const policyMap = new Map();
    const cspKeywords = new Set(['none', 'self', 'unsafe-eval', 'unsafe-hashes', 'unsafe-inline', 'strict-dynamic', 'report-sample']);

    if (existingPolicy) {
        const directives = existingPolicy.trim().split(';').filter(Boolean);
        directives.forEach(directiveStr => {
            const parts = directiveStr.trim().split(/\s+/);
            const directive = parts.shift();
            if (directive) {
                // Remove both single and double quotes from existing sources for normalization
                const cleanSources = parts.map(p => p.replace(/['"]/g, ''));
                policyMap.set(directive, new Set(cleanSources));
            }
        });
    }

    newPolicies.forEach(policy => {
        const directiveName = policy.directive || policy.directives;
        const source = policy.blocked_uri;

        if (!directiveName || !source) return;
        
        const directive = directiveName.trim();
        // **FIX:** Also remove both single and double quotes from the incoming source for normalization
        const cleanSource = source.replace(/['"]/g, '');

        const sources = policyMap.get(directive) || new Set();
        sources.add(cleanSource); // Add the normalized source
        policyMap.set(directive, sources);
    });

    const newPolicyParts = [];
    for (const [directive, sources] of policyMap.entries()) {
        const sourceList = Array.from(sources);
        
        const otherSources = sourceList.filter(s => s !== 'self');
        
        const orderedSourceList = ['self', ...otherSources];

        const finalSources = orderedSourceList.map(s => {
            return cspKeywords.has(s) ? `'${s}'` : s;
        });
        
        newPolicyParts.push(`${directive} ${finalSources.join(' ')}`);
    }

    let finalPolicy = newPolicyParts.join('; ');
    if (finalPolicy) {
        finalPolicy += ';';
    }

    return finalPolicy;
}

