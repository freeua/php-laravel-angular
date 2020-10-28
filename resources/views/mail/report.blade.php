@if ($categories)
<div><small>{{ implode(',', $categories) }}</small></div>
@endif
<div>{!! $body !!}</div>