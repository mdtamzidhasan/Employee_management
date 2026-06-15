<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">

                    {{-- Header --}}
                    <tr>
                        <td style="background: #4f46e5; padding: 28px 36px;">
                            <h1 style="margin:0; color:#ffffff; font-size: 20px; letter-spacing: 1px;">EMS</h1>
                            <p style="margin: 4px 0 0; color: rgba(255,255,255,0.8); font-size: 12px;">Employee Management System</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 36px;">
                            <p style="font-size: 15px; color:#1e293b; margin: 0 0 8px;">Hello {{ $userName }},</p>
                            <p style="font-size: 14px; color:#64748b; margin: 0 0 24px; line-height: 1.6;">
                                Use the verification code below to complete your login. This code will expire in 5 minutes.
                            </p>

                            {{-- OTP Code Box --}}
                            <div style="background:#f1f5f9; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 20px; text-align:center; margin-bottom: 24px;">
                                <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color:#4f46e5;">{{ $code }}</span>
                            </div>

                            <p style="font-size: 13px; color:#94a3b8; margin: 0; line-height: 1.6;">
                                If you did not request this code, please ignore this email or contact support immediately. Never share this code with anyone.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 36px; border-top: 1px solid #f1f5f9; text-align:center;">
                            <p style="font-size: 11px; color:#cbd5e1; margin: 0;">© {{ date('Y') }} EMS — Employee Management System</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>