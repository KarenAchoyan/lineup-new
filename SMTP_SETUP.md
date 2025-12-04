# SMTP Configuration for Password Reset

## Hostinger Email SMTP Settings

To enable password reset functionality, add the following settings to your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=info@lineup.ge
MAIL_PASSWORD=3745509XXXXlineup
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@lineup.ge
MAIL_FROM_NAME="Lineup"
```

## Alternative Configuration (TLS)

If SSL doesn't work, you can try TLS with port 587:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=info@lineup.ge
MAIL_PASSWORD=3745509XXXXlineup
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@lineup.ge
MAIL_FROM_NAME="Lineup"
```

## Testing

After adding these settings to your `.env` file, clear the config cache:

```bash
php artisan config:clear
```

Then test the password reset functionality by:
1. Going to the login page
2. Clicking "Forgot password?"
3. Entering an email address
4. Checking if the reset email is received

