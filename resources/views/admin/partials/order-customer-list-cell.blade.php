<div class="admin-customer-list-cell">
  <strong>{{ $order->customer_name ?: '—' }}</strong>
  <button
    type="button"
    class="admin-customer-trigger"
    data-customer-dialog-target="customer-details-{{ $order->id }}"
    data-order-label="Order #{{ $order->id }}"
  >
    View details
  </button>
</div>
