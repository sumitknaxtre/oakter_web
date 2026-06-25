<div class="admin-customer-list-cell">
  <strong>{{ $record->customer_name ?: '—' }}</strong>
  <button
    type="button"
    class="admin-customer-trigger"
    data-customer-dialog-target="{{ $dialogId }}"
    data-order-label="{{ $dialogLabel }}"
  >
    View details
  </button>
</div>
