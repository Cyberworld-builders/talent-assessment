#!/bin/bash

# Check DNS records for mailbox configuration
# This script checks MX records and other DNS settings to verify mailbox setup

echo "🔍 Checking DNS Records for Mailbox Configuration"
echo "================================================="

# Function to check MX records
check_mx_records() {
    local domain="$1"
    echo "📧 Checking MX records for: $domain"
    
    if command -v dig &> /dev/null; then
        echo "MX Records:"
        dig MX "$domain" +short | while read priority mx_record; do
            echo "  Priority: $priority, Mail Server: $mx_record"
        done
    else
        echo "  dig command not found. Install dnsutils to check MX records."
    fi
    
    echo ""
}

# Function to check if domain has mail servers
check_mail_servers() {
    local domain="$1"
    echo "🌐 Checking mail servers for: $domain"
    
    if command -v nslookup &> /dev/null; then
        echo "Mail server lookup:"
        nslookup -type=MX "$domain" 2>/dev/null | grep -E "(mail|mx)" || echo "  No mail servers found"
    else
        echo "  nslookup command not found."
    fi
    
    echo ""
}

# Check production domain
echo "🏢 Production Domain: involvedtalent.com"
echo "========================================"
check_mx_records "involvedtalent.com"
check_mail_servers "involvedtalent.com"

# Check development domain
echo "🧪 Development Domain: cyberworldbuilders.com"
echo "============================================="
check_mx_records "cyberworldbuilders.com"
check_mail_servers "cyberworldbuilders.com"

echo "📋 Interpretation Guide:"
echo "======================="
echo "✅ If MX records exist: Domain has mail servers configured"
echo "❌ If no MX records: Domain likely doesn't have mailboxes set up"
echo "⚠️  If MX records point to external services: Check if those services are active"
echo ""
echo "🔧 Common Mail Server Patterns:"
echo "  - Google Workspace: aspmx.l.google.com"
echo "  - Microsoft 365: *.mail.protection.outlook.com"
echo "  - Custom mail servers: mail.domain.com, mx.domain.com"
echo "  - No MX records: Usually means no mail service configured"
