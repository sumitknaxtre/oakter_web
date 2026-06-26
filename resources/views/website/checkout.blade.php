@extends('layouts.checkout')

@php
    $amount = $product['amount_paise'] / 100;
    $formattedAmount = number_format($amount, 2);
    $taxAmount = number_format($amount - ($amount / 1.18), 2);
@endphp

@section('title', 'Checkout | '.$product['order_name'])
@section('meta_description', 'Complete your purchase of '.$product['order_name'].' securely with Razorpay.')

@section('content')
  <div class="shopify-checkout">
    <div class="shopify-checkout-main">
      <form
        id="checkout-form"
        class="checkout-form"
        method="post"
        action="{{ route('website.checkout.order', $product['slug']) }}"
        novalidate
      >
        @csrf
        <input type="hidden" name="coupon_code" id="coupon-code" value="{{ old('coupon_code') }}" />

        <section class="checkout-block">
          <div class="checkout-block-head">
            <h2>Contact</h2>
          </div>
          <label class="field is-required">
            <span>Email or mobile phone number</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required />
          </label>
          @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </section>

        <section class="checkout-block">
          <h2>Delivery</h2>
          <label class="field is-required">
            <span>Country/Region</span>
            <select name="country" required>
              <option value="India" @selected(old('country', 'India') === 'India')>India</option>
            </select>
          </label>
          <div class="field-row">
            <label class="field is-required">
              <span>First name</span>
              <input type="text" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" required />
            </label>
            <label class="field is-required">
              <span>Last name</span>
              <input type="text" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" required />
            </label>
          </div>
          <label class="field is-required">
            <span>Address</span>
            <input type="text" name="address_line1" value="{{ old('address_line1') }}" autocomplete="address-line1" required />
          </label>
          <label class="field">
            <span>Apartment, suite, etc. (optional)</span>
            <input type="text" name="address_line2" value="{{ old('address_line2') }}" autocomplete="address-line2" />
          </label>
          <div class="field-row field-row-3">
            <label class="field is-required">
              <span>City</span>
              <input type="text" name="city" value="{{ old('city') }}" autocomplete="address-level2" required />
            </label>
            <label class="field is-required">
              <span>State</span>
              <select name="state" required>
                <option value="" disabled @selected(! old('state'))>State</option>
                @foreach ($states as $state)
                  <option value="{{ $state }}" @selected(old('state') === $state)>{{ $state }}</option>
                @endforeach
              </select>
            </label>
            <label class="field is-required">
              <span>PIN code</span>
              <input type="text" name="pincode" value="{{ old('pincode') }}" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="postal-code" required />
            </label>
          </div>
          <label class="field is-required">
            <span>Phone</span>
            <input type="tel" name="phone" value="{{ old('phone') }}" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" autocomplete="tel" required />
          </label>
        </section>

        <section class="checkout-block">
          <h2>Shipping method</h2>
          <div class="shipping-method">
            <div>
              <strong>Free shipping</strong>
              <span>5 to 7 working days</span>
            </div>
            <em>FREE</em>
          </div>
        </section>

        <section class="checkout-block">
          <h2>Payment</h2>
          <p class="checkout-muted">All transactions are secure and encrypted.</p>
          <div class="payment-method is-selected">
            <div class="payment-method-head">
              <span class="payment-radio" aria-hidden="true"></span>
              <strong>Razorpay Secure (UPI, Cards, Int'l Cards, Wallets)</strong>
            </div>
            <p class="payment-method-copy">
              After you click Pay now, Razorpay will open as a secure payment popup on this page.
            </p>
          </div>
        </section>

        <section class="checkout-block">
          <h2>Billing address</h2>
          <label class="billing-option">
            <input type="radio" name="billing_same_as_shipping" value="1" @checked(old('billing_same_as_shipping', '1') == '1') />
            <span>Same as shipping address</span>
          </label>
          <label class="billing-option">
            <input type="radio" name="billing_same_as_shipping" value="0" @checked(old('billing_same_as_shipping') === '0') />
            <span>Use a different billing address</span>
          </label>

          <div id="billing-fields" class="billing-fields" hidden>
            <div class="field-row">
              <label class="field is-required">
                <span>First name</span>
                <input type="text" name="billing_first_name" value="{{ old('billing_first_name') }}" />
              </label>
              <label class="field is-required">
                <span>Last name</span>
                <input type="text" name="billing_last_name" value="{{ old('billing_last_name') }}" />
              </label>
            </div>
            <label class="field is-required">
              <span>Address</span>
              <input type="text" name="billing_address_line1" value="{{ old('billing_address_line1') }}" />
            </label>
            <label class="field">
              <span>Apartment, suite, etc. (optional)</span>
              <input type="text" name="billing_address_line2" value="{{ old('billing_address_line2') }}" />
            </label>
            <div class="field-row field-row-3">
              <label class="field is-required">
                <span>City</span>
                <input type="text" name="billing_city" value="{{ old('billing_city') }}" />
              </label>
              <label class="field is-required">
                <span>State</span>
                <select name="billing_state">
                  <option value="">State</option>
                  @foreach ($states as $state)
                    <option value="{{ $state }}" @selected(old('billing_state') === $state)>{{ $state }}</option>
                  @endforeach
                </select>
              </label>
              <label class="field is-required">
                <span>PIN code</span>
                <input type="text" name="billing_pincode" value="{{ old('billing_pincode') }}" inputmode="numeric" maxlength="6" />
              </label>
            </div>
            <label class="field is-required">
              <span>Country/Region</span>
              <select name="billing_country">
                <option value="India" @selected(old('billing_country', 'India') === 'India')>India</option>
              </select>
            </label>
          </div>
        </section>

        <p id="checkout-error" class="checkout-error" role="alert" hidden></p>

        <button class="checkout-pay-button" type="submit" id="pay-now-button">Pay now</button>
      </form>
    </div>

    <aside class="shopify-checkout-sidebar" aria-label="Order summary">
      <div class="order-line">
        <div class="order-line-media">
          <img src="{{ asset($product['images']['thumb']) }}" alt="{{ $product['images']['thumb_alt'] }}" />
          <span class="order-qty">1</span>
        </div>
        <div class="order-line-copy">
          <p>{{ $product['order_name'] }}</p>
          <strong>₹{{ $formattedAmount }}</strong>
        </div>
      </div>

      <div class="order-discount">
        <input type="text" id="coupon-input" placeholder="Discount code" value="{{ old('coupon_code') }}" autocomplete="off" />
        <button type="button" id="coupon-apply-button">Apply</button>
      </div>
      <div id="coupon-applied" class="coupon-applied" hidden>
        <span id="coupon-applied-code"></span>
        <button type="button" id="coupon-remove-button" class="coupon-remove-button" aria-label="Remove discount code">&times;</button>
      </div>
      <p id="coupon-message" class="coupon-message" role="status" hidden></p>

      <dl class="order-totals">
        <div>
          <dt>Subtotal</dt>
          <dd id="checkout-subtotal">₹{{ $formattedAmount }}</dd>
        </div>
        <div id="checkout-discount-row" hidden>
          <dt>Discount</dt>
          <dd id="checkout-discount">-₹0.00</dd>
        </div>
        <div>
          <dt>Shipping</dt>
          <dd>FREE</dd>
        </div>
        <div class="order-total">
          <dt>Total</dt>
          <dd>
            <small>INR</small>
            <strong id="checkout-total">₹{{ $formattedAmount }}</strong>
          </dd>
        </div>
      </dl>
      <p class="order-tax-note">Including ₹<span id="checkout-tax">{{ $taxAmount }}</span> in taxes</p>
    </aside>
  </div>
@endsection

@push('head')
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endpush

@push('scripts')
  <script>
    window.oakterCheckout = {
      razorpayKey: @json($razorpayKey),
      lookupUrl: @json(route('website.checkout.lookup')),
      couponUrl: @json(route('website.checkout.coupon', $product['slug'])),
      subtotalPaise: @json($product['amount_paise']),
    };
  </script>
@endpush
