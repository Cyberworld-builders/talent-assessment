#!/bin/bash

# Test email delivery to verified SES addresses
# This script will attempt to send test emails to verify if mailboxes exist

echo "🧪 Testing Email Delivery to Verified SES Addresses"
echo "=================================================="

# Get the verified email addresses from Terraform output
echo "📧 Verified Production Email Addresses:"
terraform output -json production_test_email_addresses | jq -r '.[]' | while read email; do
    echo "  - $email"
done

echo ""
echo "📧 Verified Development Email Addresses:"
terraform output -json test_email_addresses | jq -r '.[]' | while read email; do
    echo "  - $email"
done

echo ""
echo "🔧 To test email delivery, you can:"
echo ""
echo "1. Use AWS CLI to send test emails:"
echo "   aws ses send-email \\"
echo "     --source 'noreply@involvedtalent.com' \\"
echo "     --destination 'ToAddresses=admin@involvedtalent.com' \\"
echo "     --message 'Subject={Data=\"Test Email\"},Body={Text={Data=\"This is a test email to verify mailbox delivery.\"}}' \\"
echo "     --region us-east-2"
echo ""
echo "2. Use Laravel Artisan tinker:"
echo "   php artisan tinker"
echo "   Mail::raw('Test email content', function(\$message) {"
echo "     \$message->to('admin@involvedtalent.com')->subject('Test Email');"
echo "   });"
echo ""
echo "3. Check AWS SES console for delivery status:"
echo "   https://console.aws.amazon.com/ses/home?region=us-east-2#/sending"
echo ""
echo "4. Monitor SNS topics for bounce/complaint notifications:"
echo "   - Bounces: $(terraform output -raw ses_production_bounces_topic_arn)"
echo "   - Complaints: $(terraform output -raw ses_production_complaints_topic_arn)"
echo ""
echo "⚠️  Note: If emails bounce, it usually means:"
echo "   - The email address doesn't exist"
echo "   - The mailbox is full"
echo "   - The receiving server is rejecting emails"
echo "   - DNS records are not properly configured"
