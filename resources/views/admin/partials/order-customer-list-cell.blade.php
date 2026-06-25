@include('admin.partials.customer-list-cell', [
  'record' => $order,
  'dialogId' => 'customer-details-'.$order->id,
  'dialogLabel' => 'Order #'.$order->id,
])
