(function () {
    const allInfos = document.querySelectorAll('.wpte-checkout-payment-info')
    const paymentMethodsRadio = document.querySelectorAll('[name=wpte_checkout_paymnet_method]')
    paymentMethodsRadio && paymentMethodsRadio.forEach(el => {
        el.checked && el.parentElement.classList.add('wpte-active-payment-method') && el.parentElement.querySelector('.wpte-checkout-payment-info').removeAttribute('style')
        el.addEventListener('change', e => {
            if (!!allInfos) {
                allInfos.forEach(el => {
                    el.style.display = 'none'
                    el.parentElement.classList.remove('wpte-active-payment-method')
                })
            }
            let parentEl = e.target.parentElement
            parentEl.classList.add('wpte-active-payment-method')
            let infoEl = e.target.parentElement.querySelector('.wpte-checkout-payment-info')
            infoEl && infoEl.removeAttribute('style')
        })
    })

    // Coupon Handler.
    const Coupon =  function(el) {
        if(!el) {
            return
        }
        this.applyBtn = el.querySelector('.wp-travel-engine-coupons-apply-btn')
        this.resetBtn = el.querySelector('.wte-coupon-response-reset-coupon')
        this.tripsField = el.querySelector('input[name="wte_couponse_trip_id"]')
        if ( ! this.tripsField ) {
            throw new Error('No trips Field.')
        }

        this.couponCodeField = el.querySelector('input[name="wp_travel_engine_coupon_code_input"]')
        this.nonceField = document.getElementById('wte_apply_coupon_nonce')
        this.loader = el.querySelector('#price-loader-coupon')
        this.loading = false
        this.summaryTable =  document.querySelector('.wpte-bf-summary-table')
        this.responseHolder = document.getElementById('coupon-response-holder')

        const applyRequest = () => {
            if(WTEAjaxData?.ajaxurl) {
                let formData = new FormData()
                formData.append('action', 'wte_session_cart_apply_coupon')
                formData.append('trip_ids', this.tripsField.value)
                formData.append('CouponCode', this.couponCodeField.value)
                formData.append('trip_id', WTEAjaxData.trip_id)
                formData.append('nonce', this.nonceField.value)
                setTimeout(() => {
                    return new Promise((resolve, reject) =>  resolve(false))
                }, 5000);

                return fetch(WTEAjaxData.ajaxurl, {
                    method: 'POST',
                    body: formData
                })
            }
        }

        function toggleLoader(show = true) {
            if(show && ! this.loading && this.loader) {
                this.loader.style.removeProperty('display')
                if(summaryTable) summaryTable.style.opacity = '.3'
                this.loading = true
            }
            if( ! show && this.loading && this.loader ) {
                this.loader.style.display = 'none'
                if(summaryTable) summaryTable.style.opacity = '1'
                this.loading = false
            }
        }

        if(this.applyBtn) {
            const handleApplyBtnClick = couponInstance => e => {
                e.preventDefault()
                toggleLoader(true)
                applyRequest()
                .then(response => response.json())
                .then((data) => {
                    if (data.success) {
                        location.reload();
                    } else {
                        var template = wp.template('wte-coupon-response'); //tmpl-wte-coupon-response
                        var table_template = wp.template('wte-coupon-response-updated-price'); //tmpl-wte-coupon-response-updated-price
                        if(couponInstance.responseHolder) {
                            let message = ''
                            message = data.data.reduce((acc, curr) => (`${acc}${curr.message}` ), '')
                            couponInstance.responseHolder.innerHTML = template({
                                type: 'error',
                                message
                              })
                        }
                        toggleLoader(false)
                    }
                })
            }
            this.applyBtn.addEventListener('click', handleApplyBtnClick(this))
        }

        const resetRequest = () => {
            if(WTEAjaxData?.ajaxurl) {
                let formData = new FormData()
                formData.append('action', 'wte_session_cart_reset_coupon')
                formData.append('nonce', this.nonceField.value)
                setTimeout(() => {
                    return new Promise((resolve, reject) =>  resolve(false))
                }, 5000);

                return fetch(WTEAjaxData.ajaxurl, {
                    method: 'POST',
                    body: formData
                })
            }
        }

        if(this.resetBtn){
            this.resetBtn.addEventListener('click', function(e) {
                e.preventDefault()
                toggleLoader(!0)
                resetRequest()
                    .then(response =>response.json())
                    .then(data => {
                        if(data.success){
                            window.location.reload()
                        } else {
                            var template = wp.template('wte-coupon-response'); //tmpl-wte-coupon-response
                            var table_template = wp.template('wte-coupon-response-updated-price'); //tmpl-wte-coupon-response-updated-price
                            if(couponInstance.responseHolder) {
                                let message = ''
                                message = data.data.reduce((acc, curr) => (`${acc}${curr.message}` ), '')
                                couponInstance.responseHolder.innerHTML = template({
                                    type: 'error',
                                    message
                                  })
                            }
                            toggleLoader(false)
                        }
                    })
            })
        }
    }

    new Coupon(document.getElementById('wte-checkout-coupon') || null)

})()
