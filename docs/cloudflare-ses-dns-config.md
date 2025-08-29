# Cloudflare DNS Configuration for SES (cyberworldbuilders.dev)

## 🎯 **SES Domain Setup Complete**

**Domain**: `cyberworldbuilders.dev`  
**Mail From**: `mail.cyberworldbuilders.dev`  
**From Address**: `noreply@cyberworldbuilders.dev`

## 📋 **Cloudflare DNS Records to Add**

Log into your Cloudflare dashboard for `cyberworldbuilders.dev` and add these DNS records:

### **1. Domain Verification Record**

```
Type: TXT
Name: _amazonses
Content: reqxRA1JgBM5KOmsA+IbVL5dLoHE9MvHvTn9HHIGiVY=
TTL: Auto
Proxy Status: DNS only (gray cloud)
```

### **2. DKIM Records (3 CNAME records)**

```
Type: CNAME
Name: q7fugutnqryjakzne4knmixwovbddfgm._domainkey
Content: q7fugutnqryjakzne4knmixwovbddfgm.dkim.amazonses.com
TTL: Auto
Proxy Status: DNS only (gray cloud)

Type: CNAME
Name: guydiu7qjk62se4o635jfdmpgvxaccie._domainkey
Content: guydiu7qjk62se4o635jfdmpgvxaccie.dkim.amazonses.com
TTL: Auto
Proxy Status: DNS only (gray cloud)

Type: CNAME
Name: n4nfaconch4i7op76tfcwkvpxjowtce5._domainkey
Content: n4nfaconch4i7op76tfcwkvpxjowtce5.dkim.amazonses.com
TTL: Auto
Proxy Status: DNS only (gray cloud)
```

### **3. Mail From Domain Records**

```
Type: MX
Name: mail
Content: 10 feedback-smtp.us-east-2.amazonses.com
TTL: Auto
Proxy Status: DNS only (gray cloud)

Type: TXT
Name: mail
Content: v=spf1 include:amazonses.com ~all
TTL: Auto
Proxy Status: DNS only (gray cloud)
```

## 🔧 **Laravel Configuration**

Update your Laravel environment variables:

```bash
# Mail Configuration
MAIL_DRIVER=ses
MAIL_FROM_ADDRESS=noreply@cyberworldbuilders.dev
MAIL_FROM_NAME="Talent Assessment"

# AWS SES Credentials
AWS_ACCESS_KEY_ID=[from terraform output]
AWS_SECRET_ACCESS_KEY=[from terraform output]
AWS_DEFAULT_REGION=us-east-2
```

## 📊 **SES Infrastructure Details**

### **Created Resources**
- ✅ **SES Domain**: `cyberworldbuilders.dev`
- ✅ **Mail From Domain**: `mail.cyberworldbuilders.dev`
- ✅ **Configuration Set**: `ses-config-set`
- ✅ **IAM User**: `ses-email-user`
- ✅ **SNS Topics**: Bounces, Complaints, Deliveries

### **SNS Topic ARNs**
- **Bounces**: `[from terraform output]`
- **Complaints**: `[from terraform output]`
- **Deliveries**: `[from terraform output]`

## 🚀 **Step-by-Step Cloudflare Setup**

### **Step 1: Access Cloudflare Dashboard**
1. Go to [cloudflare.com](https://cloudflare.com)
2. Log into your account
3. Select `cyberworldbuilders.dev` domain
4. Go to **DNS** section

### **Step 2: Add Domain Verification Record**
1. Click **Add record**
2. Set **Type** to `TXT`
3. Set **Name** to `_amazonses`
4. Set **Content** to `reqxRA1JgBM5KOmsA+IbVL5dLoHE9MvHvTn9HHIGiVY=`
5. Set **TTL** to `Auto`
6. Ensure **Proxy status** is **DNS only** (gray cloud)
7. Click **Save**

### **Step 3: Add DKIM Records**
Add 3 CNAME records for DKIM:

**Record 1:**
- **Type**: `CNAME`
- **Name**: `q7fugutnqryjakzne4knmixwovbddfgm._domainkey`
- **Content**: `q7fugutnqryjakzne4knmixwovbddfgm.dkim.amazonses.com`
- **TTL**: `Auto`
- **Proxy status**: DNS only (gray cloud)

**Record 2:**
- **Type**: `CNAME`
- **Name**: `guydiu7qjk62se4o635jfdmpgvxaccie._domainkey`
- **Content**: `guydiu7qjk62se4o635jfdmpgvxaccie.dkim.amazonses.com`
- **TTL**: `Auto`
- **Proxy status**: DNS only (gray cloud)

**Record 3:**
- **Type**: `CNAME`
- **Name**: `n4nfaconch4i7op76tfcwkvpxjowtce5._domainkey`
- **Content**: `n4nfaconch4i7op76tfcwkvpxjowtce5.dkim.amazonses.com`
- **TTL**: `Auto`
- **Proxy status**: DNS only (gray cloud)

### **Step 4: Add Mail From Domain Records**
Add 2 records for mail from domain:

**MX Record:**
- **Type**: `MX`
- **Name**: `mail`
- **Content**: `10 feedback-smtp.us-east-2.amazonses.com`
- **TTL**: `Auto`
- **Proxy status**: DNS only (gray cloud)

**SPF Record:**
- **Type**: `TXT`
- **Name**: `mail`
- **Content**: `v=spf1 include:amazonses.com ~all`
- **TTL**: `Auto`
- **Proxy status**: DNS only (gray cloud)

## 🔍 **Verification Commands**

Once DNS is configured, verify the setup:

```bash
# Check domain verification status
aws ses get-identity-verification-attributes \
  --identities cyberworldbuilders.dev \
  --region us-east-2

# Check DKIM status
aws ses get-identity-dkim-attributes \
  --identities cyberworldbuilders.dev \
  --region us-east-2

# Test email sending
aws ses send-email \
  --from noreply@cyberworldbuilders.dev \
  --destination ToAddresses=your-email@example.com \
  --message Subject={Data="SES Test"},Body={Text={Data="Test email from SES"}} \
  --region us-east-2
```

## ⚠️ **Important Notes**

1. **DNS Propagation**: Cloudflare typically propagates DNS changes within 5-10 minutes
2. **Proxy Status**: Keep all SES records as **DNS only** (gray cloud) - don't proxy them
3. **SES Sandbox**: New SES accounts start in sandbox mode (limited to verified email addresses)
4. **Production Access**: Request production access through AWS SES console when ready

## 🎉 **Success Indicators**

- Domain verification status shows "Success"
- DKIM status shows "Success" for all 3 tokens
- Test emails are delivered successfully
- No bounces or complaints in SNS topics

## 📞 **Next Steps**

1. **Add DNS Records**: Configure all records in Cloudflare
2. **Wait for Verification**: Usually 5-30 minutes
3. **Update Laravel**: Change mail configuration
4. **Test Email**: Send test emails to verify setup
5. **Monitor**: Watch bounce rates and delivery statistics

---

**Status**: SES infrastructure ready! 🚀  
**Domain**: `cyberworldbuilders.dev`  
**Next**: Add DNS records in Cloudflare.
