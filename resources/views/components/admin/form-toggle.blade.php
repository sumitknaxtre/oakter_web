@props([
    'name',
    'label',
    'hint' => null,
    'checked' => false,
])

<label {{ $attributes->class(['admin-toggle-card']) }}>
  <input
    type="checkbox"
    class="admin-toggle-card-input"
    name="{{ $name }}"
    value="1"
    @checked($checked)
  />
  <span class="admin-toggle-card-switch" aria-hidden="true"></span>
  <span class="admin-toggle-card-copy">
    <strong>{{ $label }}</strong>
    @if ($hint)
      <span>{{ $hint }}</span>
    @endif
  </span>
</label>
