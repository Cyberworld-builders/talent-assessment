#!/bin/bash

# Domain availability checker for talent assessment staging
# Uses AWS CLI to check multiple domains efficiently

echo "🔍 Checking domain availability for talent assessment staging..."
echo "============================================================"

# Create a temporary file with domains to check
cat > domains_to_check.txt << 'EOF'
talent-assessment.com
talent-assessment.net
talent-assessment.org
talent-assessment.io
talent-assessment.co
talent-assessment-staging.com
talent-assessment-staging.net
talent-assessment-staging.org
talent-assessment-staging.io
talent-assessment-staging.co
talentassess.com
talentassess.net
talentassess.org
talentassess.io
talentassess.co
talentassess-staging.com
talentassess-staging.net
talentassess-staging.org
talentassess-staging.io
talentassess-staging.co
talent-assess.com
talent-assess.net
talent-assess.org
talent-assess.io
talent-assess.co
talent-assess-staging.com
talent-assess-staging.net
talent-assess-staging.org
talent-assess-staging.io
talent-assess-staging.co
assessment-talent.com
assessment-talent.net
assessment-talent.org
assessment-talent.io
assessment-talent.co
assessment-talent-staging.com
assessment-talent-staging.net
assessment-talent-staging.org
assessment-talent-staging.io
assessment-talent-staging.co
talenttech.com
talenttech.net
talenttech.org
talenttech.io
talenttech.co
talenttech-staging.com
talenttech-staging.net
talenttech-staging.org
talenttech-staging.io
talenttech-staging.co
assesspro.com
assesspro.net
assesspro.org
assesspro.io
assesspro.co
assesspro-staging.com
assesspro-staging.net
assesspro-staging.org
assesspro-staging.io
assesspro-staging.co
talentai.com
talentai.net
talentai.org
talentai.io
talentai.co
talentai-staging.com
talentai-staging.net
talentai-staging.org
talentai-staging.io
talentai-staging.co
talentpro.com
talentpro.net
talentpro.org
talentpro.io
talentpro.co
talentpro-staging.com
talentpro-staging.net
talentpro-staging.org
talentpro-staging.io
talentpro-staging.co
assessplatform.com
assessplatform.net
assessplatform.org
assessplatform.io
assessplatform.co
assessplatform-staging.com
assessplatform-staging.net
assessplatform-staging.org
assessplatform-staging.io
assessplatform-staging.co
talentplatform.com
talentplatform.net
talentplatform.org
talentplatform.io
talentplatform.co
talentplatform-staging.com
talentplatform-staging.net
talentplatform-staging.org
talentplatform-staging.io
talentplatform-staging.co
talentlabs.com
talentlabs.net
talentlabs.org
talentlabs.io
talentlabs.co
talentlabs-staging.com
talentlabs-staging.net
talentlabs-staging.org
talentlabs-staging.io
talentlabs-staging.co
assesslabs.com
assesslabs.net
assesslabs.org
assesslabs.io
assesslabs.co
assesslabs-staging.com
assesslabs-staging.net
assesslabs-staging.org
assesslabs-staging.io
assesslabs-staging.co
proassess.com
proassess.net
proassess.org
proassess.io
proassess.co
proassess-staging.com
proassess-staging.net
proassess-staging.org
proassess-staging.io
proassess-staging.co
talenteval.com
talenteval.net
talenteval.org
talenteval.io
talenteval.co
talenteval-staging.com
talenteval-staging.net
talenteval-staging.org
talenteval-staging.io
talenteval-staging.co
assesshub.com
assesshub.net
assesshub.org
assesshub.io
assesshub.co
assesshub-staging.com
assesshub-staging.net
assesshub-staging.org
assesshub-staging.io
assesshub-staging.co
talenthub.com
talenthub.net
talenthub.org
talenthub.io
talenthub.co
talenthub-staging.com
talenthub-staging.net
talenthub-staging.org
talenthub-staging.io
talenthub-staging.co
EOF

# Initialize counters
available_count=0
unavailable_count=0
error_count=0

# Create output files
> available_domains.txt
> unavailable_domains.txt
> error_domains.txt

echo "Checking domains..."

# Process each domain
while IFS= read -r domain; do
    if [[ -n "$domain" ]]; then
        echo -n "Checking $domain... "
        
        # Check domain availability
        result=$(aws route53domains check-domain-availability --domain-name "$domain" --query 'Availability' --output text 2>/dev/null)
        
        if [[ $? -eq 0 ]]; then
            if [[ "$result" == "AVAILABLE" ]]; then
                echo "✅ AVAILABLE"
                echo "$domain" >> available_domains.txt
                ((available_count++))
            else
                echo "❌ UNAVAILABLE"
                echo "$domain" >> unavailable_domains.txt
                ((unavailable_count++))
            fi
        else
            echo "⚠️  ERROR"
            echo "$domain" >> error_domains.txt
            ((error_count++))
        fi
    fi
done < domains_to_check.txt

echo ""
echo "============================================================"
echo "📊 SUMMARY"
echo "============================================================"
echo "✅ AVAILABLE: $available_count"
echo "❌ UNAVAILABLE: $unavailable_count"
echo "⚠️  ERRORS: $error_count"
echo ""

if [[ $available_count -gt 0 ]]; then
    echo "🎯 AVAILABLE DOMAINS:"
    echo "============================================================"
    
    # Prioritize .com domains first, then .io, then others
    echo "📋 .COM DOMAINS:"
    grep "\.com$" available_domains.txt | head -10
    
    echo ""
    echo "📋 .IO DOMAINS:"
    grep "\.io$" available_domains.txt | head -10
    
    echo ""
    echo "📋 OTHER DOMAINS:"
    grep -v "\.com$\|\.io$" available_domains.txt | head -10
    
    echo ""
    echo "🎯 TOP RECOMMENDATIONS:"
    echo "============================================================"
    
    # Show top recommendations (prioritizing .com and .io)
    {
        grep "\.com$" available_domains.txt
        grep "\.io$" available_domains.txt
        grep -v "\.com$\|\.io$" available_domains.txt
    } | head -10 | nl
fi

echo ""
echo "💾 Results saved to:"
echo "   - available_domains.txt"
echo "   - unavailable_domains.txt"
echo "   - error_domains.txt"

# Clean up
rm domains_to_check.txt

echo ""
echo "✅ Domain availability check complete!"
