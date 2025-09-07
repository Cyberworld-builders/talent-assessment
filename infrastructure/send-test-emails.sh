#!/bin/bash

# Send test emails to verify mailbox delivery
# This script will send test emails to all verified SES addresses

set -e

echo "📧 Sending Test Emails to Verified SES Addresses"
echo "================================================"

# Function to send test email
send_test_email() {
    local to_email="$1"
    local from_email="$2"
    local subject="$3"
    local body="$4"
    
    echo "📤 Sending test email to: $to_email"
    
    aws ses send-email \
        --from "$from_email" \
        --to "$to_email" \
        --subject "$subject" \
        --text "$body" \
        --region us-east-2 \
        --output table
    
    if [ $? -eq 0 ]; then
        echo "✅ Email sent successfully to $to_email"
    else
        echo "❌ Failed to send email to $to_email"
    fi
    echo ""
}

# Test production emails
echo "🧪 Testing Production Email Addresses:"
echo "======================================"

# Send from noreply@involvedtalent.com to admin@involvedtalent.com
send_test_email \
    "admin@involvedtalent.com" \
    "noreply@involvedtalent.com" \
    "Test Email - Mailbox Verification" \
    "This is a test email to verify that the admin@involvedtalent.com mailbox is active and can receive emails. If you receive this email, your mailbox is working correctly."

# Send from noreply@involvedtalent.com to support@involvedtalent.com
send_test_email \
    "support@involvedtalent.com" \
    "noreply@involvedtalent.com" \
    "Test Email - Mailbox Verification" \
    "This is a test email to verify that the support@involvedtalent.com mailbox is active and can receive emails. If you receive this email, your mailbox is working correctly."

echo "🧪 Testing Development Email Addresses:"
echo "======================================="

# Send from admin-goreman@cyberworldbuilders.com to user-apone@cyberworldbuilders.com
send_test_email \
    "user-apone@cyberworldbuilders.com" \
    "admin-goreman@cyberworldbuilders.com" \
    "Test Email - Mailbox Verification" \
    "This is a test email to verify that the user-apone@cyberworldbuilders.com mailbox is active and can receive emails. If you receive this email, your mailbox is working correctly."

echo "📊 Monitoring Instructions:"
echo "=========================="
echo "1. Check your email inboxes for the test emails"
echo "2. Monitor AWS SES console for delivery status:"
echo "   https://console.aws.amazon.com/ses/home?region=us-east-2#/sending"
echo "3. Check SNS topics for any bounce/complaint notifications:"
echo "   - Production Bounces: $(terraform output -raw ses_production_bounces_topic_arn)"
echo "   - Production Complaints: $(terraform output -raw ses_production_complaints_topic_arn)"
echo "   - Development Bounces: $(terraform output -raw ses_bounces_topic_arn)"
echo "   - Development Complaints: $(terraform output -raw ses_complaints_topic_arn)"
echo ""
echo "⚠️  If emails bounce, check:"
echo "   - Email addresses exist and are active"
echo "   - Mailboxes are not full"
echo "   - DNS records are properly configured"
echo "   - Receiving server is not blocking emails"
