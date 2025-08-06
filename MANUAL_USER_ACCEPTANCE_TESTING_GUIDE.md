# 🧪 MANUAL USER ACCEPTANCE TESTING GUIDE

**Date:** August 4, 2025  
**System:** Astacala Rescue Cross-Platform Application  
**Testing Type:** User Acceptance Testing (UAT)  
**Testing Phase:** Final Validation  

---

## 🚀 **PRE-TESTING SETUP**

### **1. Server Requirements**
Ensure both servers are running before starting tests:

```powershell
# Backend API Server (Port 8000)
cd "D:\astacala_rescue_mobile\astacala_backend\astacala-rescue-api"
php artisan serve --port=8000

# Web Application Server (Port 8001) - IMPORTANT: Use CMD for proper routing
cmd /c "cd /d D:\astacala_rescue_mobile\astacala_resque-main\astacala_rescue_web && php -S 127.0.0.1:8001 -t public"
```

### **2. Verify Server Status**
Check that both servers are accessible:
- **Backend API:** http://127.0.0.1:8000/api/v1/health (should return JSON)
- **Web App Login:** http://127.0.0.1:8001/login (actual login page with authentication)
- **Web App Root:** http://127.0.0.1:8001/ (redirects to login)

### **3. Test Credentials**
Use these pre-configured test accounts:

**Admin User:**
- Username: `admin`
- Password: `password`

**Regular User:** 
- Username: `testuser`
- Password: `password`

---

## 📋 **USER ACCEPTANCE TEST SCENARIOS**

### **TEST SCENARIO 1: Authentication & Login**
**Objective:** Verify user can successfully log in and access the system

#### **Test Steps:**
1. Navigate to: http://127.0.0.1:8001/login (Note: /login lowercase, not /Login)
2. You should see the Laravel-based login form
3. Enter credentials:
   - Username: `admin`
   - Password: `password`
4. Click "Login" button
5. Verify you are redirected to dashboard at http://127.0.0.1:8001/dashboard

#### **Expected Results:**
- ✅ Login form displays correctly
- ✅ Login succeeds without errors
- ✅ Dashboard loads with user information
- ✅ Navigation menu is accessible

#### **Data to Record:**
- Login attempt time: ________
- Login success (Y/N): ________
- Dashboard load time: ________
- Any error messages: ________

---

### **TEST SCENARIO 2: Dashboard Functionality**
**Objective:** Verify dashboard displays system statistics and information

#### **Test Steps:**
1. From the dashboard/home page
2. Check for statistical cards/widgets
3. Verify data displays (user counts, reports, etc.)
4. Test navigation menu items
5. Check for any loading indicators

#### **Expected Results:**
- ✅ Statistics display correctly
- ✅ User count shows: 44 users
- ✅ Reports count shows: 35 reports  
- ✅ All dashboard widgets load without errors
- ✅ Navigation menu works properly

#### **Data to Record:**
- Statistics displayed correctly (Y/N): ________
- User count shown: ________
- Reports count shown: ________
- Page load time: ________
- Any visual issues: ________

---

### **TEST SCENARIO 3: User Management**
**Objective:** Test user data viewing and management features

#### **Test Steps:**
1. Navigate to "User Data" or "Datapengguna" page
2. Verify user list displays
3. Check for pagination if applicable
4. Test any search/filter functionality
5. Verify data accuracy

#### **Expected Results:**
- ✅ User list loads successfully
- ✅ User data displays correctly
- ✅ No errors or broken features
- ✅ Data matches expected format

#### **Data to Record:**
- User list loads (Y/N): ________
- Number of users shown: ________
- Search functionality works (Y/N): ________
- Any display issues: ________

---

### **TEST SCENARIO 4: Reports Section**
**Objective:** Test disaster reports viewing functionality

#### **Test Steps:**
1. Navigate to "Reports" or "Pelaporan" page
2. Check if page loads without 500 errors
3. Verify reports list displays
4. Test any filtering/sorting options
5. Check report details if clickable

#### **Expected Results:**
- ✅ Reports page loads without 500 error
- ✅ Reports list displays correctly
- ✅ Data shows disaster reports
- ✅ No critical errors

#### **Data to Record:**
- Reports page loads (Y/N): ________
- 500 error occurs (Y/N): ________
- Reports data displays (Y/N): ________
- Number of reports shown: ________

---

### **TEST SCENARIO 5: News/Publications**
**Objective:** Verify news and publications section works

#### **Test Steps:**
1. Navigate to "Publications" or "Publikasi" page
2. Check for news articles/publications
3. Test any article viewing functionality
4. Verify content displays properly

#### **Expected Results:**
- ✅ Publications page loads successfully
- ✅ News articles display correctly
- ✅ Content is readable and formatted
- ✅ No backend API errors

#### **Data to Record:**
- Publications page loads (Y/N): ________
- Articles display correctly (Y/N): ________
- Backend API errors (Y/N): ________
- Content quality acceptable (Y/N): ________

---

### **TEST SCENARIO 6: Session Management**
**Objective:** Test session persistence and logout functionality

#### **Test Steps:**
1. Navigate between different pages while logged in
2. Verify session maintains across page changes
3. Open a new browser tab to the same URL
4. Test logout functionality
5. Verify session termination

#### **Expected Results:**
- ✅ Session persists across page navigation
- ✅ User remains logged in on new tabs
- ✅ Logout works correctly
- ✅ Post-logout access is restricted

#### **Data to Record:**
- Session persists (Y/N): ________
- Multi-tab session works (Y/N): ________
- Logout successful (Y/N): ________
- Session security maintained (Y/N): ________

---

### **TEST SCENARIO 7: API Integration Validation**
**Objective:** Verify backend API integration works properly

#### **Test Steps:**
1. Monitor browser developer tools (F12)
2. Navigate through different pages
3. Check Network tab for API calls
4. Verify API responses are successful (200 status)
5. Check for any failed API requests

#### **Expected Results:**
- ✅ API calls return 200 status codes
- ✅ Response times under 200ms
- ✅ No 500 or 404 API errors
- ✅ Data loads consistently

#### **Data to Record:**
- Average API response time: ________
- Number of failed API calls: ________
- Any timeout issues (Y/N): ________
- API integration quality (1-10): ________

---

### **TEST SCENARIO 8: Cross-Browser Compatibility**
**Objective:** Test application works across different browsers

#### **Test Steps:**
1. Test in Chrome browser
2. Test in Firefox browser  
3. Test in Edge browser
4. Verify consistent functionality
5. Check for browser-specific issues

#### **Expected Results:**
- ✅ Works consistently in Chrome
- ✅ Works consistently in Firefox
- ✅ Works consistently in Edge
- ✅ No major browser-specific bugs

#### **Data to Record:**
- Chrome compatibility (Y/N): ________
- Firefox compatibility (Y/N): ________
- Edge compatibility (Y/N): ________
- Cross-browser issues noted: ________

---

## 📊 **UAT RESULTS SUMMARY TEMPLATE**

### **Overall System Performance Rating**
Rate each area from 1-10 (10 = Excellent, 1 = Poor):

- **Login/Authentication:** ___/10
- **Dashboard Functionality:** ___/10  
- **User Management:** ___/10
- **Reports Section:** ___/10
- **News/Publications:** ___/10
- **Session Management:** ___/10
- **API Performance:** ___/10
- **Browser Compatibility:** ___/10

### **Critical Issues Found**
List any issues that prevent normal system use:
1. ________________________________
2. ________________________________
3. ________________________________

### **Minor Issues Found**  
List any cosmetic or non-critical issues:
1. ________________________________
2. ________________________________
3. ________________________________

### **Positive Observations**
Note what works well:
1. ________________________________
2. ________________________________
3. ________________________________

### **Overall System Readiness**
Based on testing, rate the system's readiness for production:

- [ ] **Ready for Production** - All critical functionality works
- [ ] **Ready with Minor Fixes** - Works well with small issues
- [ ] **Needs Major Work** - Significant issues found
- [ ] **Not Ready** - Critical failures prevent use

### **Final Recommendation**
Your overall recommendation for this system:

```
[Write your overall assessment and recommendation here]
```

---

## 🔧 **TROUBLESHOOTING GUIDE**

### **Common Issues & Solutions**

#### **Issue: Login Page Not Loading**
- **Solution:** Verify web server is running on port 8001
- **Command:** `php -S 127.0.0.1:8001 -t public`

#### **Issue: 500 Errors on Pages**
- **Solution:** Check backend API server is running on port 8000
- **Command:** `php artisan serve --port=8000`

#### **Issue: API Timeouts**
- **Solution:** Restart both servers and clear browser cache

#### **Issue: Session Not Persisting**
- **Solution:** Clear browser cookies and try again

### **Contact Information**
For technical issues during testing:
- **AI Agent:** GitHub Copilot  
- **Session ID:** Cross-Platform Integration Review
- **Documentation:** See comprehensive documentation in `/documentation/` folder

---

**Testing Completed By:** ________________  
**Date/Time Completed:** ________________  
**Overall Grade Assigned:** ____%  
**System Recommendation:** ________________
