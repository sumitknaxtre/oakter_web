<div class="admin-customer-list-cell">
  <strong>{{ $record->customer_name ?: '—' }}</strong>
  <div class="admin-customer-list-actions">
    <button
      type="button"
      class="admin-customer-trigger"
      data-customer-dialog-target="{{ $dialogId }}"
      data-order-label="{{ $dialogLabel }}"
    >
      View details
    </button>
    @if (! empty($editUrl))
      <a class="admin-customer-edit-link" href="{{ $editUrl }}">Edit</a>
    @endif
  </div>
</div>
