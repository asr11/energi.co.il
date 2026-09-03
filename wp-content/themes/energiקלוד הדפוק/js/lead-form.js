/**
 * 🔋 Energi Lead Form - JavaScript מקצועי ומודולרי
 * גרסה: 2.0
 * מטרה: יציבות מקסימלית + ביצועים מעולים
 */

(function() {
    'use strict';

    // ===== הגדרות ומשתנים גלובליים =====
    const CONFIG = {
        WEBHOOK_URL: 'https://hooks.zapier.com/hooks/catch/3000907/u4ehjb8/',
        MAX_STEPS: 4,
        SAVINGS_BASE: {
            solar: 2000,
            battery: 1500,
            smart_home: 800,
            led_lighting: 600,
            heat_pump: 1200,
            electric_vehicle: 1000
        },
        SIZE_MULTIPLIERS: {
            50: 1,
            100: 1.2,
            150: 1.5,
            200: 1.8,
            300: 2.2
        },
        BILL_MULTIPLIERS: {
            200: 1,
            400: 1.3,
            600: 1.6,
            800: 1.9,
            1000: 2.2
        }
    };

    let currentStep = 1;
    let formData = {};
    let isSubmitting = false;

    // ===== כלי עזר (Utilities) =====
    const Utils = {
        // בטוח querySelector
        $(selector) {
            const element = document.querySelector(selector);
            if (!element) {
                console.warn(`Element not found: ${selector}`);
            }
            return element;
        },

        // בטוח querySelectorAll
        $$(selector) {
            return document.querySelectorAll(selector);
        },

        // בדיקה אם אלמנט קיים
        exists(selector) {
            return document.querySelector(selector) !== null;
        },

        // הוספת class עם בדיקה
        addClass(element, className) {
            if (element && element.classList) {
                element.classList.add(className);
            }
        },

        // הסרת class עם בדיקה
        removeClass(element, className) {
            if (element && element.classList) {
                element.classList.remove(className);
            }
        },

        // בדיקה אם יש class
        hasClass(element, className) {
            return element && element.classList && element.classList.contains(className);
        },

        // הצגה/הסתרה בטוחה
        show(element) {
            if (element) element.style.display = 'block';
        },

        hide(element) {
            if (element) element.style.display = 'none';
        },

        // לוג מובנה עם timestamp
        log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            console[type](`[${timestamp}] Energi: ${message}`);
        },

        // בדיקת תקינות אימייל
        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        // בדיקת תקינות טלפון ישראלי
        isValidPhone(phone) {
            const phoneRegex = /^0\d{1,2}-?\d{7}$/;
            return phoneRegex.test(phone.replace(/\s/g, ''));
        },

        // עיגול מספרים
        formatNumber(num) {
            return new Intl.NumberFormat('he-IL').format(Math.round(num));
        },

        // Debounce function
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };

    // ===== ניהול שלבים (Steps Manager) =====
    const StepsManager = {
        init() {
            this.updateProgressBar();
            this.updateButtons();
            Utils.log('Steps Manager initialized');
        },

        next() {
            if (this.validateCurrentStep() && currentStep < CONFIG.MAX_STEPS) {
                this.hideCurrentStep();
                currentStep++;
                this.showCurrentStep();
                this.updateProgressBar();
                this.updateButtons();
                
                // Google Analytics tracking
                this.trackStepCompletion(currentStep - 1);
                
                Utils.log(`Moved to step ${currentStep}`);
            }
        },

        prev() {
            if (currentStep > 1) {
                this.hideCurrentStep();
                currentStep--;
                this.showCurrentStep();
                this.updateProgressBar();
                this.updateButtons();
                
                Utils.log(`Moved back to step ${currentStep}`);
            }
        },

        showCurrentStep() {
            const step = Utils.$(`#step${currentStep}`);
            if (step) {
                Utils.addClass(step, 'active');
                // Focus management לנגישות
                const firstInput = step.querySelector('input, select, button');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 300);
                }
            }
        },

        hideCurrentStep() {
            Utils.$$('.form-step').forEach(step => {
                Utils.removeClass(step, 'active');
            });
        },

        updateProgressBar() {
            Utils.$$('.progress-step').forEach((step, index) => {
                Utils.removeClass(step, 'active');
                Utils.removeClass(step, 'completed');
                
                if (index + 1 < currentStep) {
                    Utils.addClass(step, 'completed');
                } else if (index + 1 === currentStep) {
                    Utils.addClass(step, 'active');
                }
            });
        },

        updateButtons() {
            const prevBtn = Utils.$('#prevBtn');
            const nextBtn = Utils.$('#nextBtn');
            const submitBtn = Utils.$('#submitBtn');

            // כפתור חזרה
            if (prevBtn) {
                if (currentStep > 1) {
                    Utils.show(prevBtn);
                } else {
                    Utils.hide(prevBtn);
                }
            }

            // כפתורי המשך/שליחה
            if (nextBtn && submitBtn) {
                if (currentStep < CONFIG.MAX_STEPS) {
                    Utils.show(nextBtn);
                    Utils.hide(submitBtn);
                } else {
                    Utils.hide(nextBtn);
                    Utils.show(submitBtn);
                }
            }
        },

        validateCurrentStep() {
            const step = Utils.$(`#step${currentStep}`);
            if (!step) return false;

            // בדיקת שדות חובה
            const requiredInputs = step.querySelectorAll('input[required], select[required]');
            for (let input of requiredInputs) {
                if (!input.value.trim()) {
                    this.showError(input, 'שדה זה הוא חובה');
                    input.focus();
                    return false;
                }
            }

            // בדיקות מיוחדות לכל שלב
            switch (currentStep) {
                case 1:
                    return this.validateStep1();
                case 2:
                    return this.validateStep2();
                case 3:
                    return this.validateStep3();
                case 4:
                    return this.validateStep4();
            }

            return true;
        },

        validateStep1() {
            const propertyType = Utils.$('input[name="propertyType"]:checked');
            if (!propertyType) {
                this.showAlert('אנא בחר סוג נכס');
                return false;
            }
            return true;
        },

        validateStep2() {
            const solutions = Utils.$$('input[name="solutions[]"]:checked');
            if (solutions.length === 0) {
                this.showAlert('אנא בחר לפחות פתרון אחד');
                return false;
            }
            return true;
        },

        validateStep3() {
            // בדיקות בסיסיות כבר נעשו, כאן נוכל להוסיף בדיקות ספציפיות
            return true;
        },

        validateStep4() {
            const phone = Utils.$('input[name="phone"]');
            const email = Utils.$('input[name="email"]');

            if (phone && !Utils.isValidPhone(phone.value)) {
                this.showError(phone, 'אנא הכנס מספר טלפון תקין');
                return false;
            }

            if (email && email.value && !Utils.isValidEmail(email.value)) {
                this.showError(email, 'אנא הכנס כתובת אימייל תקינה');
                return false;
            }

            return true;
        },

        showError(element, message) {
            // הסרת שגיאות קודמות
            this.clearErrors();
            
            // הוספת class שגיאה
            if (element) {
                Utils.addClass(element, 'error');
                element.style.borderColor = '#dc3545';
                
                // יצירת הודעת שגיאה
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.cssText = 'color: #dc3545; font-size: 14px; margin-top: 5px; font-weight: 500;';
                errorDiv.textContent = message;
                
                // הוספה אחרי האלמנט
                element.parentNode.insertBefore(errorDiv, element.nextSibling);
                
                // הסרה אוטומטית של השגיאה כשמתקנים
                element.addEventListener('input', () => {
                    this.clearErrors();
                }, { once: true });
            }
        },

        clearErrors() {
            // הסרת כל הודעות השגיאה
            Utils.$$('.error-message').forEach(el => el.remove());
            
            // הסרת סטיילינג שגיאה
            Utils.$$('.error').forEach(el => {
                Utils.removeClass(el, 'error');
                el.style.borderColor = '';
            });
        },

        showAlert(message) {
            // ניצול alert מובנה עם styling טוב יותר אם אפשר
            if (window.Swal) {
                // אם יש SweetAlert2
                Swal.fire({
                    icon: 'warning',
                    title: 'שים לב',
                    text: message,
                    confirmButtonText: 'הבנתי',
                    confirmButtonColor: '#4CAF50'
                });
            } else {
                // Fallback רגיל
                alert(message);
            }
        },

        trackStepCompletion(step) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'form_step_completed', {
                    event_category: 'Lead Generation',
                    event_label: `Step ${step}`,
                    value: step
                });
            }
        }
    };

    // ===== מחשבון חיסכון (Savings Calculator) =====
    const SavingsCalculator = {
        init() {
            this.updateSavings();
            Utils.log('Savings Calculator initialized');
        },

        updateSavings: Utils.debounce(function() {
            let baseSavings = 0;
            
            // חישוב לפי פתרונות
            const solutions = Utils.$$('input[name="solutions[]"]:checked');
            solutions.forEach(solution => {
                baseSavings += CONFIG.SAVINGS_BASE[solution.value] || 0;
            });

            // מכפלה לפי גודל נכס
            const propertySize = Utils.$('select[name="propertySize"]')?.value;
            if (propertySize && CONFIG.SIZE_MULTIPLIERS[propertySize]) {
                baseSavings *= CONFIG.SIZE_MULTIPLIERS[propertySize];
            }

            // מכפלה לפי חשבון חודשי
            const monthlyBill = Utils.$('select[name="monthlyBill"]')?.value;
            if (monthlyBill && CONFIG.BILL_MULTIPLIERS[monthlyBill]) {
                baseSavings *= CONFIG.BILL_MULTIPLIERS[monthlyBill];
            }

            // עדכון התצוגה
            const savingsElement = Utils.$('#savingsEstimate');
            if (savingsElement) {
                const formattedSavings = `₪${Utils.formatNumber(baseSavings)}`;
                
                // אנימציה קלה
                savingsElement.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    savingsElement.textContent = formattedSavings;
                    savingsElement.style.transform = 'scale(1)';
                }, 150);
            }

            Utils.log(`Savings updated: ₪${Utils.formatNumber(baseSavings)}`);
        }, 300)
    };

    // ===== ניהול אפשרויות (Options Manager) =====
    const OptionsManager = {
        init() {
            this.attachEventListeners();
            Utils.log('Options Manager initialized');
        },

        attachEventListeners() {
            // מאזינים לאפשרויות רדיו
            Utils.$$('.checkbox-item').forEach(item => {
                item.addEventListener('click', this.handleOptionClick.bind(this));
                item.addEventListener('keydown', this.handleKeydown.bind(this));
            });
        },

        handleOptionClick(event) {
            const item = event.currentTarget;
            const input = item.querySelector('input');
            
            if (!input) return;

            if (input.type === 'radio') {
                this.selectRadioOption(item, input);
            } else if (input.type === 'checkbox') {
                this.toggleCheckboxOption(item, input);
            }

            // עדכון החיסכון אחרי כל שינוי
            SavingsCalculator.updateSavings();
        },

        handleKeydown(event) {
            // תמיכה בנגישות - Enter או Space
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                this.handleOptionClick(event);
            }
        },

        selectRadioOption(item, input) {
            // הסרה מכל האפשרויות באותה קבוצה
            const groupName = input.name;
            Utils.$$(`input[name="${groupName}"]`).forEach(radio => {
                const parentItem = radio.closest('.checkbox-item');
                if (parentItem) {
                    Utils.removeClass(parentItem, 'selected');
                }
                radio.checked = false;
            });

            // בחירת האפשרות הנוכחית
            Utils.addClass(item, 'selected');
            input.checked = true;
        },

        toggleCheckboxOption(item, input) {
            input.checked = !input.checked;
            
            if (input.checked) {
                Utils.addClass(item, 'selected');
            } else {
                Utils.removeClass(item, 'selected');
            }
        }
    };

    // ===== שליחת הטופס (Form Submission) =====
    const FormSubmission = {
        init() {
            const form = Utils.$('#leadForm');
            if (form) {
                form.addEventListener('submit', this.handleSubmit.bind(this));
                Utils.log('Form submission handler attached');
            }
        },

        async handleSubmit(event) {
            event.preventDefault();
            
            if (isSubmitting) {
                Utils.log('Form submission already in progress', 'warn');
                return;
            }

            try {
                isSubmitting = true;
                this.setLoadingState(true);
                
                const data = await this.collectFormData();
                await this.submitToZapier(data);
                this.showSuccessMessage();
                this.trackConversion();
                
            } catch (error) {
                Utils.log(`Form submission error: ${error.message}`, 'error');
                this.showErrorMessage(error.message);
            } finally {
                isSubmitting = false;
                this.setLoadingState(false);
            }
        },

        async collectFormData() {
            const form = Utils.$('#leadForm');
            const formData = new FormData(form);
            const data = {};

            // איסוף נתונים בסיסיים
            for (let [key, value] of formData.entries()) {
                if (data[key]) {
                    if (Array.isArray(data[key])) {
                        data[key].push(value);
                    } else {
                        data[key] = [data[key], value];
                    }
                } else {
                    data[key] = value;
                }
            }

            // איסוף נתונים מיוחדים
            data.solutions = Array.from(Utils.$$('input[name="solutions[]"]:checked')).map(cb => cb.value);
            data.propertyType = Utils.$('input[name="propertyType"]:checked')?.value || '';
            data.estimatedSavings = Utils.$('#savingsEstimate')?.textContent || '₪0';
            
            // מטא-דאטה
            data.submissionDate = new Date().toISOString();
            data.source = 'Energi.co.il Calculator v2.0';
            data.pageUrl = window.location.href;
            data.userAgent = navigator.userAgent;
            data.screenResolution = `${screen.width}x${screen.height}`;
            data.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            Utils.log('Form data collected successfully');
            return data;
        },

        async submitToZapier(data) {
            const response = await fetch(CONFIG.WEBHOOK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            Utils.log('Data sent to Zapier successfully');
            return response;
        },

        setLoadingState(loading) {
            const submitBtn = Utils.$('#submitBtn');
            if (submitBtn) {
                if (loading) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'שולח...';
                    Utils.addClass(submitBtn, 'loading');
                } else {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'קבל הצעות מחיר 🚀';
                    Utils.removeClass(submitBtn, 'loading');
                }
            }
        },

        showSuccessMessage() {
            const formContainer = Utils.$('.form-container');
            const successMessage = Utils.$('#successMessage');
            
            if (formContainer && successMessage) {
                Utils.hide(formContainer);
                Utils.addClass(successMessage, 'show');
                
                // גלילה לתחילת הדף
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                Utils.log('Success message displayed');
            }
        },

        showErrorMessage(errorMessage) {
            const userMessage = 'אירעה שגיאה בשליחת הטופס. אנא נסה שוב או צור קשר טלפונית.';
            
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'שגיאה בשליחה',
                    text: userMessage,
                    confirmButtonText: 'נסה שוב',
                    confirmButtonColor: '#4CAF50'
                });
            } else {
                alert(userMessage);
            }
        },

        trackConversion() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'conversion', {
                    send_to: 'AW-CONVERSION_ID/CONVERSION_LABEL',
                    event_category: 'Lead Generation',
                    event_label: 'Form Completed',
                    value: 1
                });
                
                gtag('event', 'lead_submitted', {
                    event_category: 'Lead Generation',
                    event_label: 'Calculator Form',
                    custom_map: { metric1: 'lead_value' }
                });
            }
            
            Utils.log('Conversion tracked successfully');
        }
    };

    // ===== פונקציות גלובליות (Global Functions) =====
    window.nextStep = () => StepsManager.next();
    window.prevStep = () => StepsManager.prev();
    window.selectOption = (element, name, value) => {
        const input = element.querySelector('input');
        if (input) {
            OptionsManager.selectRadioOption(element, input);
            SavingsCalculator.updateSavings();
        }
    };
    window.toggleCheckbox = (element, value) => {
        const input = element.querySelector('input');
        if (input) {
            OptionsManager.toggleCheckboxOption(element, input);
            SavingsCalculator.updateSavings();
        }
    };
    window.updateSavings = () => SavingsCalculator.updateSavings();

    // ===== אתחול המערכת (System Initialization) =====
    function initializeSystem() {
        Utils.log('🔋 Energi Lead Form v2.0 - Initializing...');

        // בדיקה שכל האלמנטים הנדרשים קיימים
        const requiredElements = ['#leadForm', '.form-step', '#savingsEstimate'];
        const missingElements = requiredElements.filter(selector => !Utils.exists(selector));
        
        if (missingElements.length > 0) {
            Utils.log(`Missing required elements: ${missingElements.join(', ')}`, 'error');
            return;
        }

        // אתחול מודולים
        try {
            StepsManager.init();
            SavingsCalculator.init();
            OptionsManager.init();
            FormSubmission.init();
            
            Utils.log('✅ All modules initialized successfully');
            
            // מעקב אחרי טעינת הדף
            window.addEventListener('beforeunload', () => {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'page_unload', {
                        event_category: 'User Behavior',
                        event_label: `Step ${currentStep}`,
                        custom_map: { metric1: 'time_on_step' }
                    });
                }
            });
            
        } catch (error) {
            Utils.log(`Initialization error: ${error.message}`, 'error');
        }
    }

    // המתנה ל-DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeSystem);
    } else {
        initializeSystem();
    }

})();