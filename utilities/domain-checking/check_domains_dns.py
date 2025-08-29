#!/usr/bin/env python3

import socket
import json
from typing import List, Dict

def check_domain_availability(domain: str) -> str:
    """
    Check domain availability using DNS lookup
    """
    try:
        # Try to resolve the domain
        socket.gethostbyname(domain)
        return "UNAVAILABLE"  # Domain resolves, so it's taken
    except socket.gaierror:
        return "AVAILABLE"    # Domain doesn't resolve, likely available
    except Exception as e:
        return f"ERROR: {str(e)}"

def check_domains_bulk(domains: List[str]) -> Dict[str, str]:
    """
    Check availability for multiple domains
    """
    results = {}
    
    for domain in domains:
        print(f"Checking {domain}... ", end="", flush=True)
        
        result = check_domain_availability(domain)
        results[domain] = result
        
        if result == "AVAILABLE":
            print("✅ AVAILABLE")
        elif result == "UNAVAILABLE":
            print("❌ UNAVAILABLE")
        else:
            print(f"⚠️  {result}")
    
    return results

def main():
    # List of potential domains for talent assessment staging
    domains = [
        # Core talent assessment domains
        "talent-assessment.com",
        "talent-assessment.net",
        "talent-assessment.org",
        "talent-assessment.io",
        "talent-assessment.co",
        
        # Staging specific
        "talent-assessment-staging.com",
        "talent-assessment-staging.net",
        "talent-assessment-staging.org",
        "talent-assessment-staging.io",
        "talent-assessment-staging.co",
        
        # Alternative naming
        "talentassess.com",
        "talentassess.net",
        "talentassess.org",
        "talentassess.io",
        "talentassess.co",
        
        # Staging alternatives
        "talentassess-staging.com",
        "talentassess-staging.net",
        "talentassess-staging.org",
        "talentassess-staging.io",
        "talentassess-staging.co",
        
        # With hyphens
        "talent-assess.com",
        "talent-assess.net",
        "talent-assess.org",
        "talent-assess.io",
        "talent-assess.co",
        
        # Staging with hyphens
        "talent-assess-staging.com",
        "talent-assess-staging.net",
        "talent-assess-staging.org",
        "talent-assess-staging.io",
        "talent-assess-staging.co",
        
        # Alternative keywords
        "assessment-talent.com",
        "assessment-talent.net",
        "assessment-talent.org",
        "assessment-talent.io",
        "assessment-talent.co",
        
        # Staging alternatives
        "assessment-talent-staging.com",
        "assessment-talent-staging.net",
        "assessment-talent-staging.org",
        "assessment-talent-staging.io",
        "assessment-talent-staging.co",
        
        # Tech-focused
        "talenttech.com",
        "talenttech.net",
        "talenttech.org",
        "talenttech.io",
        "talenttech.co",
        
        # Staging tech
        "talenttech-staging.com",
        "talenttech-staging.net",
        "talenttech-staging.org",
        "talenttech-staging.io",
        "talenttech-staging.co",
        
        # Assessment focused
        "assesspro.com",
        "assesspro.net",
        "assesspro.org",
        "assesspro.io",
        "assesspro.co",
        
        # Staging assesspro
        "assesspro-staging.com",
        "assesspro-staging.net",
        "assesspro-staging.org",
        "assesspro-staging.io",
        "assesspro-staging.co",
        
        # Modern alternatives
        "talentai.com",
        "talentai.net",
        "talentai.org",
        "talentai.io",
        "talentai.co",
        
        # Staging talentai
        "talentai-staging.com",
        "talentai-staging.net",
        "talentai-staging.org",
        "talentai-staging.io",
        "talentai-staging.co",
        
        # Professional alternatives
        "talentpro.com",
        "talentpro.net",
        "talentpro.org",
        "talentpro.io",
        "talentpro.co",
        
        # Staging talentpro
        "talentpro-staging.com",
        "talentpro-staging.net",
        "talentpro-staging.org",
        "talentpro-staging.io",
        "talentpro-staging.co",
        
        # Assessment platform
        "assessplatform.com",
        "assessplatform.net",
        "assessplatform.org",
        "assessplatform.io",
        "assessplatform.co",
        
        # Staging assessplatform
        "assessplatform-staging.com",
        "assessplatform-staging.net",
        "assessplatform-staging.org",
        "assessplatform-staging.io",
        "assessplatform-staging.co",
        
        # Talent platform
        "talentplatform.com",
        "talentplatform.net",
        "talentplatform.org",
        "talentplatform.io",
        "talentplatform.co",
        
        # Staging talentplatform
        "talentplatform-staging.com",
        "talentplatform-staging.net",
        "talentplatform-staging.org",
        "talentplatform-staging.io",
        "talentplatform-staging.co",
        
        # Modern tech names
        "talentlabs.com",
        "talentlabs.net",
        "talentlabs.org",
        "talentlabs.io",
        "talentlabs.co",
        
        # Staging talentlabs
        "talentlabs-staging.com",
        "talentlabs-staging.net",
        "talentlabs-staging.org",
        "talentlabs-staging.io",
        "talentlabs-staging.co",
        
        # Assessment labs
        "assesslabs.com",
        "assesslabs.net",
        "assesslabs.org",
        "assesslabs.io",
        "assesslabs.co",
        
        # Staging assesslabs
        "assesslabs-staging.com",
        "assesslabs-staging.net",
        "assesslabs-staging.org",
        "assesslabs-staging.io",
        "assesslabs-staging.co",
        
        # Professional assessment
        "proassess.com",
        "proassess.net",
        "proassess.org",
        "proassess.io",
        "proassess.co",
        
        # Staging proassess
        "proassess-staging.com",
        "proassess-staging.net",
        "proassess-staging.org",
        "proassess-staging.io",
        "proassess-staging.co",
        
        # Talent evaluation
        "talenteval.com",
        "talenteval.net",
        "talenteval.org",
        "talenteval.io",
        "talenteval.co",
        
        # Staging talenteval
        "talenteval-staging.com",
        "talenteval-staging.net",
        "talenteval-staging.org",
        "talenteval-staging.io",
        "talenteval-staging.co",
        
        # Assessment hub
        "assesshub.com",
        "assesshub.net",
        "assesshub.org",
        "assesshub.io",
        "assesshub.co",
        
        # Staging assesshub
        "assesshub-staging.com",
        "assesshub-staging.net",
        "assesshub-staging.org",
        "assesshub-staging.io",
        "assesshub-staging.co",
        
        # Talent hub
        "talenthub.com",
        "talenthub.net",
        "talenthub.org",
        "talenthub.io",
        "talenthub.co",
        
        # Staging talenthub
        "talenthub-staging.com",
        "talenthub-staging.net",
        "talenthub-staging.org",
        "talenthub-staging.io",
        "talenthub-staging.co",
    ]
    
    print("🔍 Checking domain availability for talent assessment staging...")
    print("=" * 60)
    
    results = check_domains_bulk(domains)
    
    print("\n" + "=" * 60)
    print("📊 SUMMARY")
    print("=" * 60)
    
    # Group by availability
    available = []
    unavailable = []
    errors = []
    
    for domain, status in results.items():
        if status == "AVAILABLE":
            available.append(domain)
        elif status == "UNAVAILABLE":
            unavailable.append(domain)
        else:
            errors.append(domain)
    
    print(f"✅ AVAILABLE ({len(available)}):")
    for domain in available:
        print(f"   • {domain}")
    
    print(f"\n❌ UNAVAILABLE ({len(unavailable)}):")
    for domain in unavailable[:10]:  # Show first 10
        print(f"   • {domain}")
    if len(unavailable) > 10:
        print(f"   ... and {len(unavailable) - 10} more")
    
    if errors:
        print(f"\n⚠️  ERRORS ({len(errors)}):")
        for domain in errors:
            print(f"   • {domain}: {results[domain]}")
    
    # Save results to file
    with open('domain_availability_results.json', 'w') as f:
        json.dump(results, f, indent=2)
    
    print(f"\n💾 Results saved to domain_availability_results.json")
    
    # Recommend top choices
    if available:
        print(f"\n🎯 RECOMMENDED CHOICES:")
        print("=" * 60)
        
        # Prioritize .com domains first, then .io, then others
        priority_domains = []
        for domain in available:
            if domain.endswith('.com'):
                priority_domains.append(domain)
        
        for domain in available:
            if domain.endswith('.io') and domain not in priority_domains:
                priority_domains.append(domain)
        
        for domain in available:
            if domain not in priority_domains:
                priority_domains.append(domain)
        
        # Show top 10 recommendations
        for i, domain in enumerate(priority_domains[:10], 1):
            print(f"   {i}. {domain}")

if __name__ == "__main__":
    main()
