# Security Investigation Report - October 5, 2025

**Date:** October 15, 2025  
**Investigator:** AI Assistant  
**Subject:** Unexplained modification to `/home/ubuntu/.bashrc` file  
**Severity:** Medium - Configuration tampering without audit trail

## 🚨 **Incident Summary**

On October 5, 2025, at 19:18, the `/home/ubuntu/.bashrc` file was modified to include a problematic alias that overrides the `ls` command:

```bash
alias ls='docker-compose exec app bash'
```

This alias causes the `ls` command to execute `docker-compose exec app bash` instead of listing files, effectively dropping users into a Docker container's root shell.

## 🔍 **Investigation Timeline**

### **File Modification Details:**
- **File:** `/home/ubuntu/.bashrc`
- **Modification Time:** October 5, 2025, 19:18
- **File Size:** 9,289 bytes
- **Owner:** ubuntu:ubuntu
- **Permissions:** 644 (rw-r--r--)

### **System Activity Around Modification Time:**
- **19:18:10** - `sudo apt update` (ubuntu user)
- **19:18:17** - `sudo apt install -y python3-pip` (ubuntu user)
- **19:29:21** - SSH login from 174.85.247.60 (normal user activity)

### **Login History Analysis:**
- **Last login:** August 29, 2025 (system reboot on September 21)
- **No suspicious login activity** detected
- **Only ubuntu user** with sudo privileges
- **IP 174.85.247.60** - Known user IP address

## 🚨 **Key Findings**

### **Critical Issues:**
1. **No Audit Trail** - No logged commands explain the .bashrc modification
2. **Dangerous Alias** - Overrides fundamental `ls` command
3. **Unexplained Activity** - Modification occurred without documented cause
4. **Security Risk** - Could be used for privilege escalation or system access

### **Suspicious Aspects:**
- **Timing Coincidence** - Modification occurred during package installation
- **No Documentation** - No record of who made the change or why
- **Poor Alias Choice** - Using `ls` instead of `lsh` or `lshell` suggests error
- **Missing Logs** - No shell-related commands logged around modification time

### **System State:**
- **No evidence of compromise** in system logs
- **No unauthorized users** detected
- **No suspicious network activity** beyond normal SSH
- **Package installation** appears legitimate (python3-pip)

## 🔧 **Immediate Actions Taken**

### **Investigation Steps:**
1. ✅ **File integrity check** - Verified .bashrc file exists and is readable
2. ✅ **System log analysis** - Reviewed journalctl logs for October 5th
3. ✅ **Login history review** - Checked last command and login records
4. ✅ **User activity analysis** - Examined sudo commands and user sessions
5. ✅ **Network activity review** - Analyzed SSH connections and IP addresses

### **Evidence Collected:**
- **System logs** from October 5th (19:15-19:25)
- **Login history** from last command
- **File modification timestamps**
- **User permission analysis**
- **Command execution logs**

## 🚨 **Risk Assessment**

### **Current Risk Level:** MEDIUM
- **Impact:** High - Overrides fundamental system command
- **Likelihood:** Low - No evidence of malicious intent
- **Exposure:** Limited - Only affects ubuntu user shell

### **Potential Threats:**
1. **Accidental System Access** - Users dropped into Docker containers
2. **Command Confusion** - `ls` command behavior changed unexpectedly
3. **Privilege Escalation** - Access to Docker container root shell
4. **System Instability** - Unexpected command behavior

## 📋 **Recommended Actions**

### **Immediate (Completed):**
- ✅ **Document incident** - This report
- ✅ **Preserve evidence** - System logs and file timestamps
- ✅ **Assess impact** - Determine scope of modification

### **Short Term:**
- 🔄 **Remove problematic alias** - Fix the .bashrc configuration
- 🔄 **Verify system integrity** - Check for other unauthorized changes
- 🔄 **Review AWS CloudTrail logs** - Check for API-level activity
- 🔄 **Audit user permissions** - Verify sudo access and user accounts

### **Long Term:**
- 📋 **Implement file monitoring** - Monitor critical configuration files
- 📋 **Enhance logging** - Ensure all shell modifications are logged
- 📋 **Review access controls** - Limit who can modify system configurations
- 📋 **Create backup procedures** - Regular backups of configuration files

## 🔍 **Next Investigation Steps**

### **AWS CloudTrail Analysis:**
- Check for API calls around October 5th, 19:18
- Look for EC2 instance modifications
- Review IAM user activity
- Check for Systems Manager or SSM activity

### **Additional Forensics:**
- Review Docker container logs
- Check for other modified configuration files
- Analyze git history for project changes
- Review application logs for suspicious activity

## 📊 **Evidence Summary**

| Evidence Type | Status | Details |
|---------------|--------|---------|
| System Logs | ✅ Collected | journalctl logs from October 5th |
| File Timestamps | ✅ Collected | .bashrc modification time |
| Login History | ✅ Collected | User access records |
| Network Activity | ✅ Collected | SSH connection logs |
| AWS Logs | 🔄 Pending | CloudTrail analysis needed |
| File Integrity | ✅ Verified | No corruption detected |

## 🎯 **Conclusion**

The modification to `.bashrc` appears to be an **accidental configuration error** rather than a malicious attack. The timing coincides with legitimate package installation, and there's no evidence of unauthorized access or malicious intent.

However, the **lack of audit trail** is concerning and suggests either:
1. **Insufficient logging** of shell configuration changes
2. **Manual editing** that bypassed normal logging
3. **Undocumented process** that modified the file

**Recommendation:** Fix the immediate issue, then investigate AWS logs to determine if this was part of a larger automated process or manual error.

---

**Report Generated:** October 15, 2025  
**Next Review:** After AWS CloudTrail analysis  
**Status:** Investigation ongoing
