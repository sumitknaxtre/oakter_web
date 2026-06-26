@include('admin.partials.customer-modal-content', [
  'record' => $order,
  'editUrl' => route('admin.orders.customer.edit', $order),
])
