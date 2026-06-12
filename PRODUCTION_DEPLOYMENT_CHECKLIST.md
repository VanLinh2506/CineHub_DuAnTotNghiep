# 🚀 Production Deployment Checklist - VNPay

## ⚠️ PRE-DEPLOYMENT

### 1. VNPay Account Setup

- [ ] Đăng ký tài khoản VNPay Production
- [ ] Hoàn tất KYC/Business verification
- [ ] Nhận VNPAY_TMN_CODE production
- [ ] Nhận VNPAY_HASH_SECRET production
- [ ] Cấu hình Return URL whitelist trên VNPay dashboard
- [ ] Cấu hình IPN URL (nếu dùng webhook)
- [ ] Test thử trên sandbox với production credentials (nếu có)

### 2. Environment Configuration

- [ ] Cập nhật `.env` với production values:
  ```env
  VNPAY_URL=https://vnpayment.vn/paymentv2/vpcpay.html
  VNPAY_TMN_CODE=YOUR_PRODUCTION_TMN_CODE
  VNPAY_HASH_SECRET=YOUR_PRODUCTION_HASH_SECRET
  ```
- [ ] Đảm bảo không commit `.env` vào git
- [ ] Backup `.env` vào nơi an toàn
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Tạo `APP_KEY` mới cho production

### 3. Code Review

- [ ] Review tất cả VNPay related code
- [ ] Kiểm tra không có hardcoded test values
- [ ] Kiểm tra error handling đầy đủ
- [ ] Kiểm tra logging đầy đủ (không log sensitive data)
- [ ] Review security measures (signature, locking, validation)
- [ ] Code đã pass tất cả tests

### 4. Database

- [ ] Backup database trước khi deploy
- [ ] Run migrations on production
- [ ] Kiểm tra indexes trên các bảng:
  - `booking_pending`: index on `vnp_txn_ref`, `status`
  - `tickets`: index on `showtime_id`, `seat`, `status`
  - `transactions`: index on `user_id`, `type`, `related_id`
- [ ] Test database connection
- [ ] Kiểm tra disk space đủ

### 5. Server Setup

- [ ] PHP >= 8.2 installed
- [ ] Required PHP extensions enabled:
  - `openssl`
  - `pdo`
  - `mbstring`
  - `json`
  - `curl`
- [ ] Composer installed
- [ ] Web server configured (Nginx/Apache)
- [ ] SSL certificate installed (HTTPS required for VNPay)
- [ ] Firewall configured (allow VNPay IPs if needed)
- [ ] Cron jobs setup (for cleanup commands)

### 6. Cache & Queue

- [ ] Cấu hình Redis/Memcached cho production cache
- [ ] Test cache connection
- [ ] Setup queue worker (supervisor/systemd)
- [ ] Test queue working

### 7. Monitoring & Logging

- [ ] Setup error monitoring (Sentry, Bugsnag, etc.)
- [ ] Setup application monitoring (New Relic, DataDog, etc.)
- [ ] Configure log rotation
- [ ] Setup log aggregation (ELK, CloudWatch, etc.)
- [ ] Setup alerts for critical errors
- [ ] Test logging working

---

## 🚀 DEPLOYMENT STEPS

### Step 1: Backup Everything

```bash
# Backup database
php artisan db:backup  # or manual mysqldump

# Backup .env
cp .env .env.backup

# Backup storage
tar -czf storage_backup.tar.gz storage/

# Tag current version in git
git tag -a v1.0.0-pre-vnpay -m "Before VNPay deployment"
```

### Step 2: Deploy Code

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Build assets
npm install
npm run build
```

### Step 3: Configure Environment

```bash
# Update .env with production values
nano .env

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Run Migrations

```bash
# Backup first!
# Then run migrations
php artisan migrate --force
```

### Step 5: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Then cache again
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Set Permissions

```bash
# Storage và cache writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 7: Restart Services

```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart web server
sudo systemctl restart nginx

# Restart queue workers
sudo supervisorctl restart laravel-worker:*
```

---

## ✅ POST-DEPLOYMENT TESTING

### 1. Basic Tests

- [ ] Website loads correctly
- [ ] HTTPS working
- [ ] Static assets loading
- [ ] Database connection working
- [ ] Cache working
- [ ] Queue working

### 2. VNPay Integration Tests

#### Test Booking Payment

- [ ] Visit booking page
- [ ] Select movie, theater, showtime
- [ ] Select seats (test validation)
- [ ] Select food items
- [ ] Click "Đặt vé"
- [ ] **Use real money!** (small amount for testing)
- [ ] Complete payment on VNPay
- [ ] Verify callback received
- [ ] Verify tickets created
- [ ] Verify QR codes generated
- [ ] Verify transaction saved
- [ ] Check email sent (if configured)

#### Test Deposit Points

- [ ] Login to account
- [ ] Go to Profile
- [ ] Enter deposit amount (minimum amount)
- [ ] **Use real money!** (small amount for testing)
- [ ] Complete payment on VNPay
- [ ] Verify callback received
- [ ] Verify points added
- [ ] Verify transaction saved
- [ ] Check balance updated

### 3. Error Scenarios

- [ ] Test payment failure (cancel on VNPay)
- [ ] Test timeout (wait > 15 mins)
- [ ] Test duplicate callback (refresh callback URL)
- [ ] Test concurrent bookings (2 users same seats)
- [ ] Monitor logs for errors

### 4. Performance Tests

- [ ] Check response times
- [ ] Check database query performance
- [ ] Check cache hit rates
- [ ] Check memory usage
- [ ] Check disk usage

### 5. Security Tests

- [ ] Test signature verification (modify vnp_SecureHash)
- [ ] Test amount validation (modify vnp_Amount)
- [ ] Test idempotent processing (replay callback)
- [ ] Test SQL injection (malicious input)
- [ ] Test XSS (malicious input in booking)

---

## 📊 MONITORING CHECKLIST

### Real-time Monitoring

- [ ] Setup uptime monitoring (Pingdom, UptimeRobot)
- [ ] Setup error rate monitoring
- [ ] Setup response time monitoring
- [ ] Setup server resource monitoring (CPU, RAM, Disk)
- [ ] Setup VNPay callback success rate monitoring

### Logs to Monitor

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Web server logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Metrics to Track

- [ ] Total bookings per day
- [ ] Total deposits per day
- [ ] VNPay success rate
- [ ] VNPay failure rate
- [ ] Average booking amount
- [ ] Average deposit amount
- [ ] Response time p50, p95, p99
- [ ] Error rate

### Alerts to Setup

- [ ] Alert when VNPay success rate < 95%
- [ ] Alert when error rate > 1%
- [ ] Alert when response time > 3s
- [ ] Alert when disk space < 20%
- [ ] Alert when memory usage > 80%
- [ ] Alert on critical errors (500 errors)

---

## 🔄 ROLLBACK PLAN

### If Something Goes Wrong

1. **Immediate Actions:**
   ```bash
   # Stop accepting new orders (maintenance mode)
   php artisan down --secret="secret-code"
   
   # Check logs
   tail -n 100 storage/logs/laravel.log
   ```

2. **Rollback Code:**
   ```bash
   # Revert to previous version
   git checkout v1.0.0-pre-vnpay
   
   # Reinstall dependencies
   composer install
   npm install
   npm run build
   ```

3. **Rollback Database (if needed):**
   ```bash
   # Restore from backup
   php artisan db:restore  # or manual mysql restore
   ```

4. **Rollback Config:**
   ```bash
   # Restore old .env
   cp .env.backup .env
   
   # Clear caches
   php artisan config:clear
   php artisan cache:clear
   ```

5. **Restart Services:**
   ```bash
   sudo systemctl restart php8.2-fpm
   sudo systemctl restart nginx
   ```

6. **Bring site back up:**
   ```bash
   php artisan up
   ```

---

## 📞 EMERGENCY CONTACTS

### VNPay Support

- **Hotline:** 1900 55 55 77
- **Email:** support@vnpay.vn
- **Portal:** https://sandbox.vnpayment.vn/merchantv2

### Team Contacts

- **Lead Developer:** [Your Name]
- **DevOps:** [DevOps Name]
- **Database Admin:** [DBA Name]
- **Project Manager:** [PM Name]

---

## 📝 POST-DEPLOYMENT DOCUMENTATION

### Update Documentation

- [ ] Update README with production URLs
- [ ] Document any production-specific configurations
- [ ] Document rollback procedures
- [ ] Update API documentation
- [ ] Create incident response playbook

### Knowledge Transfer

- [ ] Train support team on VNPay flow
- [ ] Document common issues and solutions
- [ ] Create troubleshooting guide for ops team
- [ ] Schedule knowledge transfer session

---

## 🎯 SUCCESS CRITERIA

Deployment is successful when:

- [x] All pre-deployment checks passed
- [x] Code deployed without errors
- [x] All services running normally
- [x] Booking payment working end-to-end
- [x] Deposit points working end-to-end
- [x] No critical errors in logs
- [x] Monitoring and alerts active
- [x] Performance metrics normal
- [x] Security tests passed
- [x] Team notified and trained

---

## 📊 FINAL CHECKLIST

Before signing off:

- [ ] ✅ VNPay integration working in production
- [ ] ✅ At least 10 successful test transactions
- [ ] ✅ Zero critical errors in 1 hour post-deployment
- [ ] ✅ Monitoring dashboards green
- [ ] ✅ Documentation updated
- [ ] ✅ Team notified
- [ ] ✅ Rollback plan documented
- [ ] ✅ Success criteria met

---

## 🎉 POST-DEPLOYMENT

### Announce Launch

- [ ] Internal announcement to team
- [ ] Update status page
- [ ] Notify users about new payment method
- [ ] Marketing announcement (if applicable)

### Week 1 Monitoring

- [ ] Daily log reviews
- [ ] Daily metrics reviews
- [ ] User feedback monitoring
- [ ] Performance tuning if needed

### Week 1 Optimization

- [ ] Analyze slow queries
- [ ] Optimize cache usage
- [ ] Review and adjust alerts
- [ ] Fine-tune monitoring

---

**🚀 GOOD LUCK WITH YOUR DEPLOYMENT!**

---

## Signature

**Deployed by:** _________________  
**Date:** _________________  
**Time:** _________________  
**Version:** v1.0.0  
**Status:** [ ] Success  [ ] Rollback

**Notes:**
_________________________________________
_________________________________________
_________________________________________
