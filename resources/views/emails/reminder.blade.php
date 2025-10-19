<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Reminder</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .reminder-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .reminder-box.urgent {
            border-left-color: #e74c3c;
            background: #fff5f5;
        }
        .reminder-box h3 {
            margin-top: 0;
            color: #667eea;
            font-size: 18px;
        }
        .reminder-box.urgent h3 {
            color: #e74c3c;
        }
        .detail-row {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            margin-right: 8px;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
            text-align: center;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .warning-icon {
            font-size: 24px;
            margin-right: 8px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .content {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Assessment Reminder</h1>
        </div>
        
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            
            @if($days_remaining == 0)
                <div class="reminder-box urgent">
                    <h3><span class="warning-icon">⚠️</span> Urgent: Assessment Expires Today!</h3>
            @elseif($days_remaining < 0)
                <div class="reminder-box urgent">
                    <h3><span class="warning-icon">⚠️</span> Overdue Assessment</h3>
            @else
                <div class="reminder-box">
                    <h3>Pending Assessment</h3>
            @endif
            
                    <p>This is a friendly reminder that you have a pending assessment that needs to be completed.</p>
                    
                    <div class="detail-row">
                        <span class="detail-label">Assessment:</span>
                        <span>{{ $assessment->name }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Expires:</span>
                        <span>{{ $expires->format('l, F jS, Y \a\t g:i A') }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Time Remaining:</span>
                        @if($days_remaining > 1)
                            <span>{{ $days_remaining }} days</span>
                        @elseif($days_remaining == 1)
                            <span>1 day</span>
                        @elseif($days_remaining == 0)
                            <span style="color: #e74c3c; font-weight: bold;">Expires Today!</span>
                        @else
                            <span style="color: #e74c3c; font-weight: bold;">Overdue</span>
                        @endif
                    </div>
                </div>
            
            @if($days_remaining <= 3 && $days_remaining >= 0)
                <p style="color: #e67e22; font-weight: 600;">
                    ⏰ Time is running out! Please complete this assessment as soon as possible.
                </p>
            @endif
            
            <p>Click the button below to log in and complete your assessment:</p>
            
            <div style="text-align: center;">
                <a href="{{ $assignments_link }}" class="button">
                    Complete Assessment Now
                </a>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background: #e7f3ff; border-radius: 6px; border-left: 4px solid #2196F3;">
                <p style="margin: 0; font-size: 14px;">
                    <strong>Login Credentials:</strong><br>
                    Username: <strong>{{ $user->username }}</strong><br>
                    Password: <strong>{{ $user->generate_password_for_user() }}</strong>
                </p>
            </div>
            
            <p style="margin-top: 30px; font-size: 14px; color: #6c757d;">
                If you have any questions or need assistance, please don't hesitate to contact us.
            </p>
        </div>
        
        <div class="footer">
            <p>
                This is an automated reminder for your pending assessment.<br>
                Please do not reply to this email.
            </p>
            <p style="margin-top: 10px;">
                <a href="{{ $assignments_link }}" style="color: #667eea; text-decoration: none;">
                    View All Assignments
                </a>
            </p>
        </div>
    </div>
</body>
</html>

