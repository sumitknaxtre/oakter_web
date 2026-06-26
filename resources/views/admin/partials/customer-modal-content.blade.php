@php
  $shipping = $record->shipping_snapshot ?? [];
  $billing = $record->billing_snapshot ?? [];

  $formatCityLine = static function (array $address): string {
      $parts = array_filter([
          $address['city'] ?? null,
          trim(($address['state'] ?? '').' '.($address['pincode'] ?? '')),
      ]);

      return $parts !== [] ? implode(', ', $parts) : '—';
  };
@endphp

<div class="admin-customer-modal-sections">
  <section class="admin-customer-modal-section">
    <h3>Contact</h3>
    <dl class="admin-detail-list">
      <div>
        <dt>Name</dt>
        <dd>{{ $record->customer_name ?: '—' }}</dd>
      </div>
      <div>
        <dt>Email</dt>
        <dd>{{ $record->user?->email ?? '—' }}</dd>
      </div>
      <div>
        <dt>Phone</dt>
        <dd>{{ $record->phone ?? '—' }}</dd>
      </div>
    </dl>
  </section>

  <section class="admin-customer-modal-section">
    <h3>Shipping address</h3>
    <dl class="admin-detail-list">
      <div>
        <dt>Address</dt>
        <dd>
          {{ $shipping['address_line1'] ?? '—' }}
          @if (! empty($shipping['address_line2']))
            <br />{{ $shipping['address_line2'] }}
          @endif
        </dd>
      </div>
      <div>
        <dt>City / State / PIN</dt>
        <dd>{{ $formatCityLine($shipping) }}</dd>
      </div>
      <div>
        <dt>Country</dt>
        <dd>{{ $shipping['country'] ?? '—' }}</dd>
      </div>
    </dl>
  </section>

  <section class="admin-customer-modal-section">
    <h3>Billing address</h3>
    @if ($record->billing_same_as_shipping)
      <p class="admin-customer-same-as">Same as shipping address</p>
    @else
      <dl class="admin-detail-list">
        <div>
          <dt>Address</dt>
          <dd>
            {{ $billing['address_line1'] ?? '—' }}
            @if (! empty($billing['address_line2']))
              <br />{{ $billing['address_line2'] }}
            @endif
          </dd>
        </div>
        <div>
          <dt>City / State / PIN</dt>
          <dd>{{ $formatCityLine($billing) }}</dd>
        </div>
        <div>
          <dt>Country</dt>
          <dd>{{ $billing['country'] ?? '—' }}</dd>
        </div>
      </dl>
    @endif
  </section>

  @isset($editUrl)
    <div class="admin-customer-modal-actions">
      <a class="admin-link-button secondary" href="{{ $editUrl }}">Edit customer details</a>
    </div>
  @endisset
</div>
