<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f3f5f8;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f5f8;padding:24px 12px;">
  <tr><td align="center">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #d9dfe7;border-radius:10px;overflow:hidden;">
      <tr><td style="background:#f3e2a0;padding:16px 24px;border-bottom:1px solid #dcc877;font-family:Georgia,'Times New Roman',serif;color:#1c2430;">
        <div style="font-size:18px;font-weight:bold;letter-spacing:.4px;">PROPAGATION OF ORNAMENTAL PLANTS</div>
        <div style="font-size:13px;">Online Manuscript Submission, Review and Tracking System</div>
      </td></tr>
      <tr><td style="padding:24px;font-family:-apple-system,'Segoe UI',Roboto,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1c2430;">
        @yield('body')
      </td></tr>
      <tr><td style="background:#f3e2a0;padding:12px 24px;border-top:1px solid #dcc877;font-family:Arial,sans-serif;font-size:12px;color:#1c2430;text-align:center;">
        Journal-POP &copy; 2011 - {{ date('Y') }} &middot; <a href="{{ url('/') }}" style="color:#244d82;">{{ parse_url(url('/'), PHP_URL_HOST) }}</a>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
